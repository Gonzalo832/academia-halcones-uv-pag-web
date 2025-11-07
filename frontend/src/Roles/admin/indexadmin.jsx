import React, { useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import './indexadmin.css'; // Importamos los estilos

function IndexAdmin() {
  const navigate = useNavigate();

  // --- GUARDIA DE RUTA ---
  useEffect(() => {
    const userRole = localStorage.getItem('userRole');
    
    if (userRole !== 'Administrador') {
      navigate('/login');
    }
  }, [navigate]);

  // --- FUNCIÓN DE LOGOUT ---
  const handleLogout = () => {
    localStorage.clear();
    navigate('/login');
  };

  return (
    <div className="admin-dashboard"> 
      <nav className="admin-navbar">
        <div className="container">
          <div className="logo">
            <h2>Panel Administrador</h2>
          </div>
          <div className="nav-links">
            <button onClick={handleLogout} className="logout-btn">
              Cerrar sesión
            </button>
          </div>
        </div>
      </nav>

      <main>
        <section className="dashboard">
          <div className="dashboard-container container">
            <h1>Bienvenido al Panel de Administración</h1>
            <div className="cards">
              
              <div className="card">
                <h3>Gestión de alumnos</h3>
                <p>Administra los alumnos inscritos.</p>
                <Link to="/admin/gestion-alumnos" className="btn">
                  Ir a Alumnos
                </Link>
              </div>

              <div className="card">
                <h3>Gestión de Padres-Tutores</h3>
                <p>Administra los padres/tutores registrados.</p>
                <Link to="/admin/gestion-padres" className="btn">
                  Ir a Padres/Tutor
                </Link>
              </div>

              <div className="card">
                <h3>Reportes</h3>
                <p>Gestion de reportes en excel.</p>
                <Link to="/admin/reportes" className="btn">
                  Ver Reportes
                </Link>
              </div>

              <div className="card">
                <h3>Gestion de pagos</h3>
                <p>Gestionar pagos.</p>
                <Link to="/admin/gestion-pagos" className="btn">
                  Ir a pagos
                </Link>
              </div>

              <div className="card">
                <h3>Gestion de matriculas</h3>
                <p>Gestionar matriculas.</p>
                <Link to="/admin/gestion-matriculas" className="btn">
                  Ir a matriculas
                </Link>
              </div>

              <div className="card">
                <h3>Gestion del personal</h3>
                <p>Gestionar el personal.</p>
                <Link to="/admin/gestion-personal" className="btn">
                  Ir a personal
                </Link>
              </div>

            </div>
          </div>
        </section>
      </main>

      <footer className="footer">
        <div className="container">
          <p>&copy; 2025 Academia de Fútbol Halcones UV. Todos los derechos reservados.</p>
        </div>
      </footer>
    </div>
  );
}

export default IndexAdmin;