<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'Categorias';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false; 

    protected $fillable = ['rango_edad'];
    protected $guarded = [];
    
    public function alumnos() { return $this->hasMany(Alumno::class, 'id_categoria', 'id_categoria'); }
}