<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'Alumnos';
    protected $primaryKey = 'id_alumno';
    public $timestamps = false; 

    protected $fillable = [
        'id_padre', 'id_categoria', 'id_curso', 'nombre_completo', 'matricula', 
        'edad', 'fecha_nacimiento', 'sexo', 'talla_camiseta', 'estatura', 'peso', 
        'centro_educativo', 'seguro_medico', 'enfermedades', 'alergias', 'equipo_previo', 
        'posicion', 'habilidades', 'qr_code', 'nombre_hermano', 'acta_nacimiento', 
        'certificado_medico', 'foto_nino', 'id_matricula'
    ];
    protected $guarded = [];

    // Relaciones
    public function padre() { return $this->belongsTo(PadreTutor::class, 'id_padre', 'id_padre'); }
    public function categoria() { return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria'); }
    public function curso() { return $this->belongsTo(Curso::class, 'id_curso', 'id_curso'); }
    public function matricula() { return $this->belongsTo(Matricula::class, 'id_matricula', 'id_matricula'); }
}
