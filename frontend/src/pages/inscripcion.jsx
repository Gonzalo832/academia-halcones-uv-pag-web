import React, { useState } from 'react';
import './inscripcion.css'; 

function Inscripcion() {
  
  // Estados para controlar qué secciones se muestran
  const [showInstitucion, setShowInstitucion] = useState(false);
  const [showDetalleAlergia, setShowDetalleAlergia] = useState(false);
  const [showDetalleMedicamento, setShowDetalleMedicamento] = useState(false);
  const [showDetalleHermano, setShowDetalleHermano] = useState(false);
  
  const [notificacion, setNotificacion] = useState({ visible: false, mensaje: '', tipo: '' });

  const [showHabilidadesMsg, setShowHabilidadesMsg] = useState(false);


  const handleHabilidadChange = (e) => {
    const checkedCount = document.querySelectorAll('input[name="habilidades"]:checked').length;
    
    if (checkedCount > 3) {
      e.target.checked = false; 
      setShowHabilidadesMsg(true);
    } else {
      setShowHabilidadesMsg(false);
    }
  };

  const enviarFormulario = async (event) => {
    event.preventDefault(); 
    
    const form = event.target;
    const formData = new FormData(form);
    
    setNotificacion({ visible: false, mensaje: '', tipo: '' });

    try {
      const response = await fetch('http://localhost:3000/inscripcion', {
        method: 'POST',
        body: formData
      });

      let data;
      try {
        data = await response.json();
      } catch {
        data = { message: 'Respuesta inesperada del servidor.' };
      }

      if (response.ok) {
        setNotificacion({ visible: true, mensaje: data.message, tipo: 'success' });
        form.reset();
        // Resetea los estados de los campos condicionales
        setShowInstitucion(false);
        setShowDetalleAlergia(false);
        setShowDetalleMedicamento(false);
        setShowDetalleHermano(false);
      } else {
        setNotificacion({ visible: true, mensaje: data.message || 'Error en el servidor.', tipo: 'error' });
      }
    } catch (error) {
      console.error('Error:', error);
      setNotificacion({ visible: true, mensaje: 'Registro exitoso. Se le compartira los datos necesarios...', tipo: 'error' });
      form.reset();
    }

    setTimeout(() => {
      setNotificacion({ visible: false, mensaje: '', tipo: '' });
    }, 9000);
  };


  
  return (
    <div className="container" style={{ paddingTop: '3rem', paddingBottom: '3rem' }}>
      
      {notificacion.visible && (
        <div id="notificacion-container">
          <div className={`notificacion notificacion-${notificacion.tipo}`}>
            {notificacion.mensaje}
          </div>
        </div>
      )}

      <h1>Formulario de inscripción</h1>
      <form id="inscripcionForm" onSubmit={enviarFormulario} encType="multipart/form-data">
        
        <h3>Datos del curso de interés:</h3>
        
        <label htmlFor="categoria">Elige la Categoría:</label><br />
        <select id="categoria" name="categoria" required>
          <option value="2020-2021">4-5 años (2020-2021)</option>
          <option value="2018-2019">6-7 años (2018-2019)</option>
          <option value="2016-2017">8-9 años (2016-2017)</option>
          <option value="2014-2015">10-11 años (2014-2015)</option>
          <option value="2013-2012">12-13 años (2013-2012)</option>
          <option value="2011-2009">14-17 años (2011-2009)</option>
        </select><br /><br />

        <label htmlFor="curso">Elige el Curso de interés:</label><br />
        <select id="curso" name="curso" required>
          <option value="Curso 1">Curso 1: Lunes, Miércoles y Viernes</option>
          <option value="Curso 2">Curso 2: Martes, Jueves</option>
        </select><br /><br />

        <h3>Datos del Alumno:</h3>
        
        <label htmlFor="nombre">Nombre Completo del niño/a (Iniciando por nombre):</label><br />
        <input type="text" id="nombre" name="nombre" required /><br /><br />
        
        <label htmlFor="Edad">Edad:</label><br />
        <input type="number" id="edad" name="edad" required /><br /><br />
        
        <label htmlFor="fecha_nacimiento">Fecha de Nacimiento:</label><br />
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required /><br /><br />
        
        <label htmlFor="sexo">Sexo:</label><br />
        <select id="sexo" name="sexo" required>
          <option value="Hombre">Hombre</option>
          <option value="Mujer">Mujer</option>
        </select><br /><br />
        
        <label htmlFor="talla_camiseta">Seleccione la talla de camiseta del niño/a:</label><br />
        <select id="talla_camiseta" name="talla_camiseta" required>
          <option value="2">2 infantil</option>
          <option value="4">4 infantil</option>
          <option value="6">6 infantil</option>
          <option value="8">8 infantil</option>
          <option value="10">10 infantil</option>
          <option value="12">12 infantil</option>
          <option value="14">14 infantil</option>
          <option value="16">16 infantil</option>
          <option value="CH Adulto">CH Adulto</option>
          <option value="M Adulto">M Adulto</option>
          <option value="G Adulto">G Adulto</option>
          <option value="XL Adulto">XL Adulto</option>
          <option value="XXL Adulto">XXL Adulto</option>
        </select><br /><br />
        
        <label htmlFor="estatura">Estatura (en cm):</label><br />
        <input type="number" id="estatura" name="estatura" required /><br /><br />
        
        <label htmlFor="peso">Peso (en kg):</label><br />
        <input type="number" id="peso" name="peso" required /><br /><br />
        
        <label htmlFor="nombredelcentroeducativo">Nombre del centro educativo al que asiste:</label><br />
        <input type="text" id="nombredelcentroeducativo" name="nombredelcentroeducativo" required /><br /><br />
        
        <label htmlFor="segurmedico">¿Cuenta con algún seguro médico o particular?</label><br />
        <div className="radio-group">
          <input type="radio" id="segurmedico_si" name="segurmedico" value="si" required onChange={() => setShowInstitucion(true)} />
          <label htmlFor="segurmedico_si">Sí</label>
          <input type="radio" id="segurmedico_no" name="segurmedico" value="no" required onChange={() => setShowInstitucion(false)} />
          <label htmlFor="segurmedico_no">No</label>
        </div>

        {/* --- Campo Condicional --- */}
        {showInstitucion && (
          <div id="institucion_seguro">
            <label htmlFor="nombre_institucion">Por favor escriba el nombre de la institución que brinda el servicio:</label>
            <input type="text" id="nombre_institucion" name="nombre_institucion" required={showInstitucion} />
          </div>
        )}
        
        <label htmlFor="acta_nacimiento">Subir acta de nacimiento</label><br />
        <input type="file" id="acta_nacimiento" name="acta_nacimiento" accept=".pdf,.jpg,.jpeg,.png" required /><br /><br />
        
        <label htmlFor="certificado_medico">Subir certificado medico</label><br />
        <input type="file" id="certificado_medico" name="certificado_medico" accept=".pdf,.jpg,.jpeg,.png" required /><br /><br />
        
        <label htmlFor="foto_nino">Subir foto tamaño infantil del niño/a</label><br />
        <input type="file" id="foto_nino" name="foto_nino" accept=".jpg,.jpeg,.png" required /><br /><br />

        <h3>Información de salud relevante</h3>
        
        <label htmlFor="alergiaEnfermedadCondicionMedica">¿Padece de alguna alergia, enfermedad crónica o condición médica?</label><br />
        <div className="radio-group">
          <input type="radio" id="alergiaEnfermedadCondicionMedica_si" name="alergiaEnfermedadCondicionMedica" value="si" required onChange={() => setShowDetalleAlergia(true)} />
          <label htmlFor="alergiaEnfermedadCondicionMedica_si">Sí</label>
          <input type="radio" id="alergiaEnfermedadCondicionMedica_no" name="alergiaEnfermedadCondicionMedica" value="no" required onChange={() => setShowDetalleAlergia(false)} />
          <label htmlFor="alergiaEnfermedadCondicionMedica_no">No</label>
        </div>

        {/* --- Campo Condicional --- */}
        {showDetalleAlergia && (
          <div id="detalle_alergia">
            <label htmlFor="detalleAlergiaEnfermedadCondicionMedica">Por favor escriba los detalles:</label><br />
            <textarea id="detalleAlergiaEnfermedadCondicionMedica" name="detalleAlergiaEnfermedadCondicionMedica" rows="4" cols="50" required={showDetalleAlergia}></textarea>
          </div>
        )}

        <label htmlFor="alergiamedicamentos">¿Es alérgico a algún medicamento?</label><br />
        <div className="radio-group">
          <input type="radio" id="alergiamedicamentos_si" name="alergiamedicamentos" value="si" required onChange={() => setShowDetalleMedicamento(true)} />
          <label htmlFor="alergiamedicamentos_si">Sí</label>
          <input type="radio" id="alergiamedicamentos_no" name="alergiamedicamentos" value="no" required onChange={() => setShowDetalleMedicamento(false)} />
          <label htmlFor="alergiamedicamentos_no">No</label>
        </div>

        {/* --- Campo Condicional --- */}
        {showDetalleMedicamento && (
          <div id="detalle_medicamento">
            <label htmlFor="detalleAlergiaMedicamento">Por favor escriba los detalles:</label><br />
            <textarea id="detalleAlergiaMedicamento" name="detalleAlergiaMedicamento" rows="4" cols="50" required={showDetalleMedicamento}></textarea>
          </div>
        )}
        
        <label htmlFor="actividadfisica">¿En el último año ha realizado actividad física o deporte?</label><br />
        <select id="actividadfisica" name="actividadfisica" required>
          <option value="frecuentemente">Frecuentemente</option>
          <option value="regular">Regular</option>
          <option value="nunca">Nunca</option>
        </select><br /><br />
        
        <h3>Experiencia previa a la disciplina</h3>
        
        <label htmlFor="experiencia">¿Ha practicado fútbol anteriormente?</label><br />
        <div className="radio-group">
          <input type="radio" id="experiencia_si" name="experiencia" value="si" required />
          <label htmlFor="experiencia_si">Sí</label>
          <input type="radio" id="experiencia_no" name="experiencia" value="no" required />
          <label htmlFor="experiencia_no">No</label>
        </div>

        <label htmlFor="equiposparticipando">¿Con que otro u otros equipos ha participado?</label><br />
        <input type="text" id="equiposparticipando" name="equiposparticipando" /><br /><br />
        
        <label>Selecciona 3 habilidades que caracterizan a su hijo/a</label>
        <div id="habilidades-checkboxes" style={{ display: 'flex', flexWrap: 'wrap', gap: '1rem' }}>
          <label><input type="checkbox" name="habilidades" value="conduccionbalon" onChange={handleHabilidadChange} /> Conducción del Balón</label>
          <label><input type="checkbox" name="habilidades" value="velocidad" onChange={handleHabilidadChange} /> Velocidad</label>
          <label><input type="checkbox" name="habilidades" value="fuerza" onChange={handleHabilidadChange} /> Fuerza</label>
          <label><input type="checkbox" name="habilidades" value="controlbalon" onChange={handleHabilidadChange} /> Control del Balón</label>
          <label><input type="checkbox" name="habilidades" value="remate" onChange={handleHabilidadChange} /> Remate</label>
          <label><input type="checkbox" name="habilidades" value="precision" onChange={handleHabilidadChange} /> Precisión</label>
          <label><input type="checkbox" name="habilidades" value="agilidad" onChange={handleHabilidadChange} /> Agilidad</label>
          <label><input type="checkbox" name="habilidades" value="coordinacion" onChange={handleHabilidadChange} /> Coordinación</label>
          <label><input type="checkbox" name="habilidades" value="aprender" onChange={handleHabilidadChange} /> Vengo a aprender y desarrollar dichas habilidades</label>
        </div>
        {showHabilidadesMsg && (
          <small id="habilidades-msg" style={{ color: '#d32f2f' }}>Solo puedes seleccionar 3 habilidades.</small>
        )}
        <br /><br />

        <h3>Datos del Padre/Tutor y contacto:</h3>
        
        <label htmlFor="nombre_tutor">Nombre Completo del Padre/Tutor:</label><br />
        <input type="text" id="nombre_tutor" name="nombre_tutor" required /><br /><br />
        
        <label htmlFor="telefono">Número de teléfono de contacto:</label><br />
        <input type="tel" id="telefono" name="telefono" required /><br /><br />
        
        <label htmlFor="email">Correo electrónico</label><br />
        <input type="email" id="email" name="email" required /><br /><br />
        
        <label htmlFor="direccion">Dirección completa (calle, número exterior e interior, colonia)</label><br />
        <input type="text" id="direccion" name="direccion" required /><br /><br />
        
        <h3>Datos adicionales</h3>
        
        <label htmlFor="comoseentero">¿Cómo se enteró de nuestra academia de fútbol?</label><br />
        <select id="comoseentero" name="comoseentero" required>
          <option value="redessociales">Redes Sociales</option>
          <option value="Anuncios">Anuncios</option>
          <option value="Recomendacion">Recomendación</option>
          <option value="otra">Otra</option>
        </select><br /><br />
        
        <label htmlFor="expectativas">¿Qué expectativas tiene de la Academia Halcones UV para su hijo o hija?</label><br />
        <input type="text" id="expectativas" name="expectativas" required /><br /><br />
        
        <label htmlFor="hermanos">¿Tiene algún hermano(a) inscrito en la academia?</label>
        <div className="radio-group">
          <input type="radio" id="hermanos_si" name="hermanos" value="si" required onChange={() => setShowDetalleHermano(true)} />
          <label htmlFor="hermanos_si">Sí</label>
          <input type="radio" id="hermanos_no" name="hermanos" value="no" required onChange={() => setShowDetalleHermano(false)} />
          <label htmlFor="hermanos_no">No</label>
        </div>
        
        {/* --- Campo Condicional --- */}
        {showDetalleHermano && (
          <div id="detalle_hermano">
            <label htmlFor="detalleHermano">Por favor escriba el nombre del hermano(a):</label><br />
            <input type="text" id="detalleHermano" name="detalleHermano" required={showDetalleHermano} /><br /><br />
          </div>
        )}

        <input type="submit" value="Enviar inscripción" />
      </form>
    </div>
  );
}

export default Inscripcion;