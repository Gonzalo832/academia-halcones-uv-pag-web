<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PadreTutor extends Model
{
    protected $table = 'PadreTutor';
    protected $primaryKey = 'id_padre';
    public $timestamps = false; 

    protected $fillable = [
        'nombre_completo', 'telefono', 'direccion', 'como_se_entero', 
        'expectativas', 'id_usuario_fk'
    ];
    protected $guarded = [];

    public function usuario() { return $this->belongsTo(Usuario::class, 'id_usuario_fk', 'id_usuario'); }
    public function alumnos() { return $this->hasMany(Alumno::class, 'id_padre', 'id_padre'); }
}