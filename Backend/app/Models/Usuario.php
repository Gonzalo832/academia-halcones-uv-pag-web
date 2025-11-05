<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'Usuarios'; 
    protected $primaryKey = 'id_usuario';
    public $timestamps = false; 

    protected $fillable = [
        'correo',
        'contrasena',
        'rol',
    ];

    protected $hidden = [
        'contrasena',
    ];
    
    protected $guarded = [];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Relaciones
    public function administrador() { return $this->hasOne(Administrador::class, 'id_administrador', 'id_usuario'); }
    public function entrenador() { return $this->hasOne(Entrenador::class, 'id_entrenador', 'id_usuario'); }
    public function padreTutor() { return $this->hasOne(PadreTutor::class, 'id_usuario_fk', 'id_usuario'); }
}