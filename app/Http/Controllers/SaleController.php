<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function store(Request $request)
    {    
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:books,id',
            'productos.*.cantidad' => 'required|integer|min:1'
        ]);

        try {
            $ventaProcesada = DB::transaction(function () use ($request){
                $totalVenta = 0;
                $librosAProcesar = [];

                foreach($request ->productos as $item){
                    $libro = Book::find($item['id']);

                    if($libro -> stock < $item['cantidad']){
                        throw new \Exception("Stock Insuficiente para '{$libro->nombre}'. Disponibles:  '{$libro->stock}'");
                    }

                    $subTotal = $libro -> precio * $item["cantidad"];
                    $totalVenta += $subTotal;

                    $librosAProcesar[] = [
                        'libro' => $libro, 
                        'cantidad' => $item['cantidad'],
                        'precio_unitario'=> $libro -> precio,
                    ];
                }

                $venta = Sale::create([
                    'numero_recibo' => 'REC-' . time() . rand (10, 99),
                    'fecha' => now() -> toDateString(),
                    'total' => $totalVenta,
                    'user_id' => Auth::user()
                ]);

                foreach($librosAProcesar as $itemProcesado){
                    $libro = $itemProcesado['libro'];

                    $libro -> decrement('stock', $itemProcesado['cantidad']);

                    $venta -> books()->atach($libro->id, [
                        'cantidad'=> $itemProcesado['cantidad'],
                        'precio_unitario' => $itemProcesado['precio_unitario'],
                    ]);
                }

                return $venta;
        });

        return response()->json([
            'mensaje' => 'Venta Procesada con exito! Stock Actualizado',
            'venta'=> $ventaProcesada->load('books')
        ], 201);
    } catch (\Exception $e) {
    return response()->json([
        'error'=> $e->getMessage()
    ], 400);
        }
}
}