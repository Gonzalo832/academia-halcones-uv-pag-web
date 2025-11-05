import React, { useState } from 'react';
import { Routes, Route, Link, useLocation } from 'react-router-dom';

import './App.css';
import HomePage from './pages/HomePage';
import Inscripcion from './pages/inscripcion'; 
import Login from './pages/login';      

function App() {
  const [menuOpen, setMenuOpen] = useState(false);
  
  const location = useLocation();
  
  const showHeaderFooter = location.pathname !== '/login';

  const handleLinkClick = () => {
    setMenuOpen(false);
  };

  return (
    <>
      {showHeaderFooter && (
        <nav className="navbar">
          <div className="container">
            <div className="logo">
              <h1>Halcones UV</h1>
            </div>

            <button className="menu-toggle" onClick={() => setMenuOpen(!menuOpen)}>
              {menuOpen ? '✕' : '☰'}
            </button>

            <div className={`nav-links ${menuOpen ? 'open' : ''}`}>
              
              <Link to="/#hero" onClick={handleLinkClick}>Inicio</Link>
              <Link to="/#info" onClick={handleLinkClick}>Información</Link>
              <Link to="/#cursos" onClick={handleLinkClick}>Cursos</Link>
              <Link to="/#entrenadores" onClick={handleLinkClick}>Entrenadores</Link>
              <Link to="/#ubicacion" onClick={handleLinkClick}>Ubicación</Link>
              
              <Link to="/inscripcion" onClick={handleLinkClick}>Inscripción</Link>
              <Link to="/login" onClick={handleLinkClick}>Acceso a la plataforma</Link>
            </div>
          </div>
        </nav>
      )}

      <main>
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/inscripcion" element={<Inscripcion />} />
          <Route path="/login" element={<Login />} /> 
        </Routes>
      </main>

      {showHeaderFooter && (
        <footer className="footer">
          <div className="container">
            <p>&copy; 2025 Academia de Fútbol Halcones UV. Todos los derechos reservados.</p>
          </div>
        </footer>
      )}
    </>
  );
}

export default App;