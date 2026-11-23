<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    public const ESTADOS = ['disponible', 'asignado', 'mantenimiento'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return ['fecha_adquisicion' => 'date'];
    }
}
