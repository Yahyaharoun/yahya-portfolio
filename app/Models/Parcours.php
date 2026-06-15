<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcours extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'localisation',
        'nom_proprietaire',
        'image',
        'description',
        'lien',
    ];
}
