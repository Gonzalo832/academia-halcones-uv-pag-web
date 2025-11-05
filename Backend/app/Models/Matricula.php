<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    protected $table = 'Matriculas';
    protected $primaryKey = 'id_matricula';
    public $timestamps = false; 

    protected $fillable = [
        'matricula', 'estado', 'fecha_asignacion',
    ];
    protected $guarded = [];
    
    public function alumno() { return $this->hasOne(Alumno::class, 'id_matricula', 'id_matricula'); }
}