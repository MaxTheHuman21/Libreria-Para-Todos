<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sale extends Model
{
    protected $fillable = ['numero_recibo', 'fecha', 'total', 'user_id'];

        public function books(): BelongsToMany{
            return $this->belongsToMany(Book::class)
                    ->withPivot('cantidad', 'precio_unitario')
                    ->withTimestamps();
        }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }   

}
