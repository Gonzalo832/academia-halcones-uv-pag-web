<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'Cursos';
    protected $primaryKey = 'id_curso';
    public $timestamps = false; 

    protected $fillable = ['nombre_curso', 'dias'];
    protected $guarded = [];

    public function alumnos() { return $this->hasMany(Alumno::class, 'id_curso', 'id_curso'); }
}
