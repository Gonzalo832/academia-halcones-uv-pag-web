<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'Pagos';
    protected $primaryKey = 'id_pago';
    public $timestamps = false; 

    protected $fillable = [
        'id_padre', 'id_alumno', 'fecha_pago', 'tipo_pago', 'comprobante', 'estado'
    ];
    protected $guarded = [];
    
    public function padre() { return $this->belongsTo(PadreTutor::class, 'id_padre', 'id_padre'); }
    public function alumno() { return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno'); }
}