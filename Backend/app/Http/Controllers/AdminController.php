<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Usuario;
use App\Models\PadreTutor;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Administrador;
use App\Models\Entrenador;
use App\Models\Pago;
use App\Models\Categoria;
use App\Models\Curso;


// backend/app/Http/Controllers/AdminController.php

class AdminController extends Controller
{
    // --- CORRECCIÓN DE SINTAXIS DE PHP: Usar => en lugar de : ---
    private $categoriaMap = [
        '2020-2021' => 1, 
        '2018-2019' => 2, 
        '2016-2017' => 3,
        '2014-2015' => 4, 
        '2013-2012' => 5, 
        '2011-2009' => 6,
    ];
    
    private $cursoMap = [
        'Curso 1' => 1, 
        'Curso 2' => 2
    ];
// backend/app/Http/Controllers/AdminController.php (SOLO EL MÉTODO handleInscripcion)

public function handleInscripcion(Request $request)
{
    // 1. VALIDACIÓN DE DATOS Y ARCHIVOS (CRÍTICO para la Seguridad)
    $request->validate([
        // Datos del Curso
        'categoria' => 'required|string',
        'curso' => 'required|string',
        // Datos del Alumno
        'nombre' => 'required|string|max:255',
        'edad' => 'required|integer',
        'fecha_nacimiento' => 'required|date',
        'sexo' => 'required|in:Hombre,Mujer',
        'talla_camiseta' => 'required|string',
        'estatura' => 'required|numeric',
        'peso' => 'required|numeric',
        'nombredelcentroeducativo' => 'required|string|max:255',
        'segurmedico' => 'required|in:si,no',
        'nombre_institucion' => 'nullable|string|max:255',
        // Archivos (Subida segura)
        'acta_nacimiento' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096', 
        'certificado_medico' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        'foto_nino' => 'required|file|mimes:jpg,jpeg,png|max:4096',
        // Datos de Salud
        'alergiaEnfermedadCondicionMedica' => 'required|in:si,no',
        'detalleAlergiaEnfermedadCondicionMedica' => 'nullable|string',
        'alergiamedicamentos' => 'required|in:si,no',
        'detalleAlergiaMedicamento' => 'nullable|string',
        'actividadfisica' => 'required|string',
        // Experiencia
        'experiencia' => 'required|in:si,no',
        // Datos del Tutor
        'nombre_tutor' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'email' => 'required|email|unique:Usuarios,correo', // CRÍTICO: El email no debe existir ya
        'direccion' => 'required|string|max:255',
        // Datos Adicionales
        'comoseentero' => 'required|string',
        'expectativas' => 'required|string',
        'hermanos' => 'required|in:si,no',
        'detalleHermano' => 'nullable|string',
        // Habilidades
        'habilidades' => 'array',
        'habilidades.*' => 'string', // Valida que cada elemento del array sea string
    ]);

    // 2. CREACIÓN DE CONTRASEÑA Y MATRÍCULA
    // Se usa una contraseña por defecto que será enviada al correo (en la implementación real)
    $password_temporal = 'MySecurePass!';
    $hashedPassword = Hash::make($password_temporal);
    $matricula = 'HALCON' . time() . rand(100, 999); // Genera una matrícula única

    // 3. INICIO DE LA TRANSACCIÓN (Garantiza que todo se guarde o que nada se guarde)
    DB::beginTransaction();

    try {
        // --- 3.1. CREAR EL USUARIO (Padre/Tutor) ---
        $usuario = Usuario::create([
            'correo' => $request->email,
            'contrasena' => $hashedPassword,
            'rol' => 'Padre', // Rol fijo para las inscripciones
        ]);
        $id_usuario = $usuario->id_usuario;

        // --- 3.2. CREAR EL PADRE/TUTOR ---
        $padre = PadreTutor::create([
            'id_usuario_fk' => $id_usuario,
            'nombre_completo' => $request->nombre_tutor,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ]);
        $id_padre = $padre->id_padre;

        // --- 3.3. GESTIÓN DE ARCHIVOS (Subida Segura con Storage) ---
        $pathBase = "inscripciones/{$matricula}"; // La carpeta se creará dentro de storage/app/public/
        
        // Uso de Storage::putFile para evitar inyección y generar un nombre único seguro
        $acta_nacimiento_path = $request->file('acta_nacimiento')->store($pathBase, 'public'); 
        $certificado_medico_path = $request->file('certificado_medico')->store($pathBase, 'public');
        $foto_nino_path = $request->file('foto_nino')->store($pathBase, 'public');

        // --- 3.4. CREAR EL ALUMNO ---
        $alumno = Alumno::create([
            'id_padre_fk' => $id_padre,
            'matricula' => $matricula,
            'nombre_completo' => $request->nombre,
            'edad' => $request->edad,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'talla_camiseta' => $request->talla_camiseta,
            'estatura' => $request->estatura,
            'peso' => $request->peso,
            'nombredelcentroeducativo' => $request->nombredelcentroeducativo,
            // Salud, etc.
            'segurmedico' => $request->segurmedico,
            'nombre_institucion' => $request->input('nombre_institucion', null), // Usar null si no aplica
            'alergiaEnfermedadCondicionMedica' => $request->alergiaEnfermedadCondicionMedica,
            'detalleAlergiaEnfermedadCondicionMedica' => $request->input('detalleAlergiaEnfermedadCondicionMedica', null),
            'alergiamedicamentos' => $request->alergiamedicamentos,
            'detalleAlergiaMedicamento' => $request->input('detalleAlergiaMedicamento', null),
            'actividadfisica' => $request->actividadfisica,
            'experiencia' => $request->experiencia,
            'equiposparticipando' => $request->input('equiposparticipando', null),
            'habilidades' => json_encode($request->input('habilidades', [])), // Guarda las habilidades como JSON
            'comoseentero' => $request->comoseentero,
            'expectativas' => $request->expectativas,
            'hermanos' => $request->hermanos,
            'detalleHermano' => $request->input('detalleHermano', null),
            // Rutas de los Archivos
            'acta_nacimiento_path' => $acta_nacimiento_path,
            'certificado_medico_path' => $certificado_medico_path,
            'foto_nino_path' => $foto_nino_path,
            // IDs de Mapeo
            'id_categoria_fk' => $this->categoriaMap[$request->categoria],
            'id_curso_fk' => $this->cursoMap[$request->curso],
        ]);
        
        // --- 3.5. CREAR LA MATRÍCULA ---
        Matricula::create([
            'id_alumno_fk' => $alumno->id_alumno,
            'matricula' => $matricula,
            'fecha_creacion' => now(),
            'estado' => 'Pendiente', // Estado inicial
        ]);

        // 4. CONFIRMAR LA TRANSACCIÓN
        DB::commit();

        return response()->json([
            'message' => '¡Inscripción recibida! Revise el correo para su contraseña temporal. Matrícula: ' . $matricula,
            'matricula' => $matricula,
        ], 200);

    } catch (\Exception $e) {
        // 5. REVERTIR LA TRANSACCIÓN Y LOGUEAR ERROR
        DB::rollBack();
        
        // Eliminar archivos subidos si la BD falló (aunque la transacción es la clave)
        if (isset($pathBase)) { Storage::deleteDirectory("public/{$pathBase}"); }
        
        \Log::error("Error en la inscripción: " . $e->getMessage());
        return response()->json(['message' => 'Error en el servidor al procesar la inscripción. Detalle: ' . $e->getMessage()], 500);
    }
}
    public function addPersonal(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'correo' => 'required|email|unique:Usuarios,correo',
            'contrasena' => 'required|string|min:8',
            'rol' => 'required|in:Administrador,Entrenador',
        ]);
        
        DB::beginTransaction();
        try {
            $hashedPassword = Hash::make($request->contrasena);
            $usuario = Usuario::create(['correo' => $request->correo, 'contrasena' => $hashedPassword, 'rol' => $request->rol]);
            $id_usuario = $usuario->id_usuario;

            if ($request->rol === 'Administrador') {
                Administrador::create(['id_administrador' => $id_usuario, 'nombre_completo' => $request->nombre_completo]);
            } else if ($request->rol === 'Entrenador') {
                $qr_code = 'ENTRENADOR_' . uniqid(); 
                Entrenador::create(['id_entrenador' => $id_usuario, 'nombre_completo' => $request->nombre_completo, 'qr_code' => $qr_code]);
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Personal agregado correctamente.'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); 
            \Log::error("Error al agregar personal: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor.'], 500);
        }
    }
    
    // Obtener Alumnos (Lectura Segura con Relaciones)
    public function getAlumnos(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador' && auth()->user()->rol !== 'Entrenador') { return response()->json(['message' => 'Acceso denegado.'], 403); }
        $alumnos = Alumno::with(['categoria', 'curso', 'padre'])->get();
        return response()->json($alumnos, 200);
    }
    
    // Obtener Contadores
    public function getContadores(Request $request)
    {
        $totalAdministradores = Administrador::count();
        $totalEntrenadores = Entrenador::count();
        return response()->json(['totalAdministradores' => $totalAdministradores, 'totalEntrenadores' => $totalEntrenadores], 200);
    }
    
    // Obtener Matrículas
    public function getMatriculas(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        $matriculas = Matricula::all();
        return response()->json($matriculas, 200);
    }
    
    // ----------------------------------------------------
    // Rutas de Padre
    // ----------------------------------------------------
    
    // Obtener Hijos del Padre Autenticado (Migrado en el paso anterior)
    public function getPadreHijos(Request $request)
    {
        if (auth()->user()->rol !== 'Padre') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        $id_usuario_autenticado = auth()->id();
        
        $padre = PadreTutor::where('id_usuario_fk', $id_usuario_autenticado)->first();
        if (!$padre) { return response()->json(['success' => false, 'message' => 'Padre no encontrado.'], 404); }
        
        $hijos = $padre->alumnos()->select('id_alumno', 'nombre_completo', 'matricula')->get();
        return response()->json(['success' => true, 'hijos' => $hijos], 200);
    }
    
    // Obtener Comprobantes del Padre
    public function getComprobantes(Request $request)
    {
        if (auth()->user()->rol !== 'Padre') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        $id_usuario_autenticado = auth()->id();
        
        $padre = PadreTutor::where('id_usuario_fk', $id_usuario_autenticado)->first();
        if (!$padre) { return response()->json(['success' => false, 'message' => 'Padre no encontrado.'], 404); }
        
        $comprobantes = Pago::where('id_padre', $padre->id_padre)
                             ->select('id_pago', 'fecha_pago', 'comprobante', 'estado')
                             ->orderBy('fecha_pago', 'desc')
                             ->get();
        return response()->json(['success' => true, 'comprobantes' => $comprobantes], 200);
    }

    // Actualizar Contraseña del Padre (Ruta Crítica de Seguridad)
    public function updateParentPassword(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        $request->validate(['id_padre' => 'required|integer|exists:PadreTutor,id_padre', 'contrasena' => 'required|string|min:8']);
        
        try {
            $padre = PadreTutor::find($request->id_padre);
            if (!$padre || !$padre->id_usuario_fk) { return response()->json(['success' => false, 'message' => 'Padre no encontrado.'], 404); }
            
            $hashedPassword = Hash::make($request->contrasena);
            $usuario = Usuario::where('id_usuario', $padre->id_usuario_fk)->first();
            
            $usuario->contrasena = $hashedPassword;
            $usuario->save(); 

            return response()->json(['success' => true, 'message' => 'Contraseña actualizada correctamente.'], 200);
        } catch (\Exception $e) {
            \Log::error("Error al actualizar la contraseña del padre: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor.'], 500);
        }
    }
    
}