<?php

namespace App\Http\Controllers;

// --- Imports de Laravel ---
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

// --- Tus Modelos ---
use App\Models\Usuario;
use App\Models\PadreTutor;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Administrador;
use App\Models\Entrenador;
use App\Models\Pago;
use App\Models\Categoria;
use App\Models\Curso;

class AdminController extends Controller
{
    // Mapeo para los IDs de Categoría y Curso
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

    /**
     * Maneja el formulario de inscripción pública.
     * (Esta función ya estaba perfecta)
     */
    public function handleInscripcion(Request $request)
    {
        // 1. VALIDACIÓN DE DATOS Y ARCHIVOS
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
            // CRÍTICO: El email debe ser único en tu tabla 'Usuarios'
            'email' => 'required|email|unique:Usuarios,correo', 
            'direccion' => 'required|string|max:255',
            // Datos Adicionales
            'comoseentero' => 'required|string',
            'expectativas' => 'required|string',
            'hermanos' => 'required|in:si,no',
            'detalleHermano' => 'nullable|string',
            'habilidades' => 'array',
            'habilidades.*' => 'string',
        ]);

        // 2. CREACIÓN DE CONTRASEÑA Y MATRÍCULA
        $password_temporal = 'HalconesUV2025!'; // Contraseña temporal
        $hashedPassword = Hash::make($password_temporal);
        $matricula = 'HAL-' . time() . rand(10, 99); // Genera una matrícula única

        // 3. INICIO DE LA TRANSACCIÓN
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
                'como_se_entero' => $request->input('comoseentero'),
                'expectativas' => $request->input('expectativas'),
            ]);
            $id_padre = $padre->id_padre;

            // --- 3.3. GESTIÓN DE ARCHIVOS (Storage) ---
            $pathBase = "inscripciones/{$matricula}"; // storage/app/public/inscripciones/...
            
            $acta_nacimiento_path = $request->file('acta_nacimiento')->store($pathBase, 'public'); 
            $certificado_medico_path = $request->file('certificado_medico')->store($pathBase, 'public');
            $foto_nino_path = $request->file('foto_nino')->store($pathBase, 'public');

            // --- 3.4. CREAR EL ALUMNO ---
            $alumno = Alumno::create([
                'id_padre' => $id_padre, // Corregido: id_padre en lugar de id_padre_fk
                'matricula' => $matricula,
                'nombre_completo' => $request->nombre,
                'edad' => $request->edad,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'sexo' => $request->sexo,
                'talla_camiseta' => $request->talla_camiseta,
                'estatura' => $request->estatura,
                'peso' => $request->peso,
                'centro_educativo' => $request->nombredelcentroeducativo, // Corregido
                'seguro_medico' => $request->segurmedico == 'si', // Convertir a boolean
                'enfermedades' => $request->input('detalleAlergiaEnfermedadCondicionMedica', null),
                'alergias' => $request->input('detalleAlergiaMedicamento', null),
                'equipo_previo' => $request->input('equiposparticipando', null),
                'habilidades' => json_encode($request->input('habilidades', [])),
                'comoseentero' => $request->comoseentero, // Añadido
                'expectativas' => $request->expectativas, // Añadido
                'hermanos' => $request->hermanos, // Añadido
                'detalleHermano' => $request->input('detalleHermano', null), // Añadido
                'acta_nacimiento' => $acta_nacimiento_path, // Corregido: Nombres de columna de tu SQL original
                'certificado_medico' => $certificado_medico_path, // Corregido
                'foto_nino' => $foto_nino_path, // Corregido
                'id_categoria' => $this->categoriaMap[$request->categoria] ?? null, // Corregido
                'id_curso' => $this->cursoMap[$request->curso] ?? null, // Corregido
                'qr_code' => 'QR-' . $matricula, // Añadido QR Code
            ]);
            
            // --- 3.5. CREAR LA MATRÍCULA ---
            Matricula::create([
                // 'id_alumno_fk' => $alumno->id_alumno, // Tu tabla Matriculas no tiene id_alumno
                'matricula' => $matricula,
                'fecha_asignacion' => now(),
                'estado' => 'Asignada', // Cambiado de Pendiente a Asignada
            ]);

            // 4. CONFIRMAR LA TRANSACCIÓN
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Inscripción recibida! Se ha creado una cuenta. Matrícula: ' . $matricula,
                'matricula' => $matricula,
            ], 201); // 201 = Creado

        } catch (Exception $e) {
            // 5. REVERTIR LA TRANSACCIÓN Y LOGUEAR ERROR
            DB::rollBack();
            
            if (isset($pathBase)) { Storage::deleteDirectory("public/{$pathBase}"); }
            
            Log::error("Error en la inscripción: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor al procesar la inscripción.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Agrega nuevo personal (Admin o Entrenador).
     * (¡¡ESTA ES LA FUNCIÓN CORREGIDA!!)
     */
public function addPersonal(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        
        // --- 1. VALIDACIÓN CORREGIDA ---
        // 'nombre_completo' SOLO es requerido si el rol es 'Entrenador'
        $request->validate([
            'correo' => 'required|email|unique:Usuarios,correo',
            'contrasena' => 'required|string|min:8',
            'rol' => 'required|in:Administrador,Entrenador',
            'nombre_completo' => 'required_if:rol,Entrenador|string|max:255',
        ]);
        
        DB::beginTransaction();
        try {
            $hashedPassword = Hash::make($request->contrasena);
            $usuario = Usuario::create(['correo' => $request->correo, 'contrasena' => $hashedPassword, 'rol' => $request->rol]);
            $id_usuario = $usuario->id_usuario;

            // --- 2. LÓGICA DE CREACIÓN CORREGIDA ---
            if ($request->rol === 'Administrador') {
                
                // ¡CORREGIDO! Ya no se pasa 'nombre_completo'
                Administrador::create(['id_administrador' => $id_usuario]);
            
            } else if ($request->rol === 'Entrenador') {
                $qr_code = 'ENTRENADOR_' . uniqid(); 
                Entrenador::create([
                    'id_entrenador' => $id_usuario, 
                    'nombre_completo' => $request->nombre_completo, // Aquí SÍ se pasa el nombre
                    'qr_code' => $qr_code
                ]);
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Personal agregado correctamente.'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); 
            \Log::error("Error al agregar personal: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor.'], 500);
        }
    }
    /**
     * Obtener todos los Alumnos (con sus relaciones).
     */
    public function getAlumnos(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador' && auth()->user()->rol !== 'Entrenador') { return response()->json(['message' => 'Acceso denegado.'], 403); }
        $alumnos = Alumno::with(['categoria', 'curso', 'padre'])->get();
        return response()->json($alumnos, 200);
    }
    
    /**
     * Obtener todos los Padres (con su usuario).
     */
    public function getPadres(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador' && auth()->user()->rol !== 'Entrenador') { 
            return response()->json(['message' => 'Acceso denegado.'], 403); 
        }
        $padres = PadreTutor::with('usuario')->get(); 
        return response()->json($padres, 200);
    }

    /**
     * Obtener contadores para el dashboard.
     */
    public function getContadores(Request $request)
    {
        $totalAdministradores = Administrador::count();
        $totalEntrenadores = Entrenador::count();
        return response()->json(['totalAdministradores' => $totalAdministradores, 'totalEntrenadores' => $totalEntrenadores], 200);
    }
    
    /**
     * Obtener todas las Matrículas.
     */
    public function getMatriculas(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        $matriculas = Matricula::all();
        return response()->json($matriculas, 200);
    }

    /**
     * Añadir una nueva matrícula manualmente.
     */
    public function addMatricula(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        
        $validator = Validator::make($request->all(), [
            'matricula' => 'required|string|unique:Matriculas,matricula',
            'id_alumno_fk' => 'nullable|integer|exists:Alumnos,id_alumno',
            'estado' => 'required|in:Disponible,Asignada', // Tu SQL solo tiene estas dos
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            Matricula::create([
                'matricula' => $request->matricula,
                // 'id_alumno_fk' => $request->input('id_alumno_fk', null), // Tu tabla no tiene esta columna
                'estado' => $request->estado,
                'fecha_asignacion' => $request->estado == 'Asignada' ? now() : null,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Matrícula agregada con éxito.'], 201);

        } catch (Exception $e) {
            Log::error("Error al agregar matrícula: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor.'], 500);
        }
    }
    
    /**
     * Subir un comprobante de pago (Ruta de Padre).
     */
    public function uploadComprobante(Request $request)
    {
        if (auth()->user()->rol !== 'Padre') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }

        $validator = Validator::make($request->all(), [
            'id_alumno' => 'required|integer|exists:Alumnos,id_alumno',
            'tipo_pago' => 'required|string|in:Mensual,Anual,Bimestral',
            'monto' => 'required|numeric|min:0',
            'comprobante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $id_usuario_autenticado = auth()->id();
        $padre = PadreTutor::where('id_usuario_fk', $id_usuario_autenticado)->first();
        if (!$padre) { return response()->json(['success' => false, 'message' => 'Padre no encontrado.'], 404); }

        $alumno = Alumno::where('id_alumno', $request->id_alumno)
                        ->where('id_padre', $padre->id_padre) // Corregido: id_padre
                        ->first();

        if (!$alumno) {
            return response()->json(['success' => false, 'message' => 'Alumno no válido para este padre.'], 403);
        }

        try {
            $pathComprobante = $request->file('comprobante')->store("public/comprobantes/{$padre->id_padre}");

            Pago::create([
                'id_padre' => $padre->id_padre,
                'id_alumno' => $request->id_alumno,
                'fecha_pago' => now(),
                'tipo_pago' => $request->tipo_pago,
                'comprobante' => $pathComprobante,
                'estado' => 'Pendiente', 
                'monto' => $request->monto,
            ]);

            return response()->json(['success' => true, 'message' => 'Comprobante subido. En espera de aprobación.'], 201);

        } catch (Exception $e) {
            Log::error("Error al subir comprobante: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor.'], 500);
        }
    }
    
    /**
     * Obtener Hijos del Padre Autenticado.
     */
    public function getPadreHijos(Request $request)
    {
        if (auth()->user()->rol !== 'Padre') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        $id_usuario_autenticado = auth()->id();
        
        $padre = PadreTutor::where('id_usuario_fk', $id_usuario_autenticado)->first();
        if (!$padre) { return response()->json(['success' => false, 'message' => 'Padre no encontrado.'], 404); }
        
        $hijos = $padre->alumnos()->select('id_alumno', 'nombre_completo', 'matricula')->get();
        return response()->json(['success' => true, 'hijos' => $hijos], 200);
    }
    
    /**
     * Obtener Comprobantes del Padre.
     */
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

    /**
     * Actualizar Contraseña del Padre (Admin).
     */
    public function updateParentPassword(Request $request)
    {
        if (auth()->user()->rol !== 'Administrador') { return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403); }
        $request->validate(['id_padre' => 'required|integer|exists:PadreTutor,id_padre', 'contrasena' => 'required|string|min:8']);
        
        try {
            $padre = PadreTutor::find($request->id_padre);
            if (!$padre || !$padre->id_usuario_fk) { return response()->json(['success' => false, 'message' => 'Padre no encontrado o sin usuario asignado.'], 404); }
            
            $hashedPassword = Hash::make($request->contrasena);
            $usuario = Usuario::where('id_usuario', $padre->id_usuario_fk)->first();
            
            $usuario->contrasena = $hashedPassword;
            $usuario->save(); 

            return response()->json(['success' => true, 'message' => 'Contraseña actualizada correctamente.'], 200);
        } catch (Exception $e) {
            Log::error("Error al actualizar la contraseña del padre: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor.'], 500);
        }
    }
}