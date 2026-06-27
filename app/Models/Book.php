<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $fillable = ['nombre', 'clave', 'precio', 'stock'];

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class)
        ->withPivot('cantidad', 'precio_unitario')
        ->withTimestamps();
    }
}
