<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /** @var string */
    protected $table = 'Usuarios'; 

    /** @var string */
    protected $primaryKey = 'id_usuario'; 

    /** @var bool */
    public $timestamps = true; 

    /** @var array<int, string> */
    protected $fillable = [
        'correo',
        'contrasena',
        'rol',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'contrasena', 
    ];

    /** @var array<string, string> */
    protected $casts = [
        'contrasena' => 'hashed', 
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }
    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'id_administrador', 'id_usuario');
    }

    public function entrenador()
    {
        return $this->hasOne(Entrenador::class, 'id_entrenador', 'id_usuario');
    }
    public function padreTutor()
    {
        return $this->hasOne(PadreTutor::class, 'id_usuario_fk', 'id_usuario');
    }
}