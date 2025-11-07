<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'Administradores';
    protected $primaryKey = 'id_administrador';
    public $timestamps = false; 
    
    protected $fillable = ['id_administrador']; 
    protected $guarded = [];

    public function usuario() { return $this->belongsTo(Usuario::class, 'id_administrador', 'id_usuario'); }
}