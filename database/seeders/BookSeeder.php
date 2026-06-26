<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('books')->insert([
            ['nombre' => 'Harry Potter', 'clave' => 'LIB-001', 'precio' => 250.00, 'stock' => 15],
            ['nombre' => 'El señor de los anillos', 'clave' => 'LIB-002', 'precio' => 550.00, 'stock' => 10],
            ['nombre' => 'Cien años de soledad', 'clave' => 'LIB-003', 'precio' => 290.00, 'stock' => 25],
            ['nombre' => 'Dracula', 'clave' => 'LIB-004', 'precio' => 50.00, 'stock' => 10],
            ['nombre' => 'El Alquimista', 'clave' => 'LIB-005', 'precio' => 350.00, 'stock' => 15],
            ['nombre' => 'Pedro Paramo', 'clave' => 'LIB-006', 'precio' => 150.00, 'stock' => 19],
            ['nombre' => 'Coleccion Clave', 'clave' => 'LIB-007', 'precio' => 750.00, 'stock' => 5],
            ['nombre' => 'Codigo Facilito', 'clave' => 'LIB-008', 'precio' => 1550.00, 'stock' => 1],
            ['nombre' => 'Ficcion', 'clave' => 'LIB-009', 'precio' => 140.00, 'stock' => 15],
            ['nombre' => 'Frankenstein', 'clave' => 'LIB-010', 'precio' => 200.00, 'stock' => 15],
            ['nombre' => 'El Hobbit', 'clave' => 'LIB-011', 'precio' => 2000.00, 'stock' => 15],
            ['nombre' => 'Juego de Tronos', 'clave' => 'LIB-012', 'precio' => 190.00, 'stock' => 15],
            ['nombre' => 'La Casa del Dragon', 'clave' => 'LIB-013', 'precio' => 500.00, 'stock' => 15],
            ['nombre' => 'Cañitas', 'clave' => 'LIB-014', 'precio' => 250.00, 'stock' => 15],
            ['nombre' => 'Ciencia Ficcion', 'clave' => 'LIB-015', 'precio' => 250.00, 'stock' => 1],
        ]);
    }
}
