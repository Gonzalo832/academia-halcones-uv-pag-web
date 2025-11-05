<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'Asistencia';
    protected $primaryKey = 'id_asistencia';
    public $timestamps = false; 

    protected $fillable = ['id_alumno', 'id_entrenador', 'fecha', 'estado'];
    protected $guarded = [];
    
    public function alumno() { return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno'); }
    public function entrenador() { return $this->belongsTo(Entrenador::class, 'id_entrenador', 'id_entrenador'); }
}
