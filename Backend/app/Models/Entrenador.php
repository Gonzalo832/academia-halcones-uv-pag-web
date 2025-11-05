<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrenador extends Model
{
    protected $table = 'Entrenadores';
    protected $primaryKey = 'id_entrenador';
    public $timestamps = false; 

    protected $fillable = [
        'id_entrenador', 'nombre_completo', 'edad', 'fecha_nacimiento', 'sexo', 
        'direccion', 'qr_code'
    ];
    protected $guarded = [];

    public function usuario() { return $this->belongsTo(Usuario::class, 'id_entrenador', 'id_usuario'); }
    public function asistencias() { return $this->hasMany(Asistencia::class, 'id_entrenador', 'id_entrenador'); }
}