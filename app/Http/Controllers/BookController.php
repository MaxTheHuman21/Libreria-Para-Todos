<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Funcion para mostrar el listado de los libros (Con paginacion de 10)
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if($request->has('search') && $request->search !=''){
            $query->where('nombre', 'LIKE', '%' . $request->search . '%')
            ->orWhere('clave', $request->search);
        }

        $books = $query->paginate(10);

        return response()->json([
            'mensaje' => 'Listado de inventario obtenido con exito', 
            'total_registros'=> $books->total(),
            'pagina_actual' => $books->currentPage(),
            'libros' => $books->items()
        ], 200);
    }

    /**
     * Este no se va utilizar por el momento
     */
    public function create()
    {
        //
    }

    /**
     * Guarda un nuevo libro en la base de datos
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre'=> 'required|string|max:255',
            'clave'=> 'required|string|unique:books,clave',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $book = Book::create($validatedData);

        return response()->json([
            'mensaje' => 'Libro registrado exitosamente en la libreria', 
            'libro_creado  ' => $book
            ], 201);
    }

    /**
     * Muestra un libro especifico por su ID
     */
    public function show(string $id)
    {
        $book = Book::find($id);

        if(!$book) {
            return response()->json(['error' => 'El libro solicitado no existe'], 404);
        }

        return response()->json($book, 200);
    }

    /**
     * function EDIT no se utilizara por el momento
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza un libro existencia en la base de datos
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);
        if(!$book) {
            return response()->json(['error'=> 'El libro que quieres actualizar no existe'], 404);
        }

        $validatedData = $request->validate([
            'nombre'=> 'required|string|max:255',
            'clave' => 'required|string|unique:books,clave,' . $id,
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $book->update($validatedData);
        return response()->json([
            'mensaje'=> 'Libro actualizado correctamente!',
            'libro_actualizado' => $book
        ], 200);
    }

    /**
     * Elminar un libro del inventario
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);
        if(!$book) {
            return response()->json(['error'=> 'El libro que quieres eliminar no existe'], 404);
        }

        $book->delete();

        return response()->json([
            'mensaje' => 'El libro fue eliminado exitosamente!'
        ], 200);
    }
}
