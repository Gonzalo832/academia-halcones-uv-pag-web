import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import './login.css'; 

function Login() {
  const [user, setUser] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState(''); 
  const navigate = useNavigate(); 

  const handleSubmit = async (event) => {
    event.preventDefault();
    setError(''); 

    try {
      const response = await fetch('http://localhost:3000/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ user, password })
      });
      const data = await response.json();

      if (data.success) {
        localStorage.setItem('userRole', data.rol);
        localStorage.setItem('isLoggedIn', 'true');
        localStorage.setItem('userEmail', user);
        
        if (data.rol === 'Administrador') {
          navigate('/admin/inicio'); 
        } else if (data.rol === 'Padre') {
          navigate('/padre/inicio'); 
        } else {
          navigate('/'); 
        }
      } else {
        localStorage.removeItem('userRole');
        localStorage.removeItem('isLoggedIn');
        setError(data.message || 'Usuario o contraseña incorrectos.');
      }
    } catch (error) {
      console.error('Error al conectar con el servidor:', error);
      setError('Error de conexión con el servidor.');
    }
  };

  return (
    <div className="login-page-wrapper">
      
      <nav className="nav-login">
        <Link to="/">Volver al Inicio</Link>
      </nav>

      <div className="login-container">
        <h2>Iniciar Sesión</h2>
        
        <div id="error-message" className="error">{error}</div>
        
        <form id="login-form" onSubmit={handleSubmit}>
          <label htmlFor="user">Usuario (Email):</label>
          <input 
            type="text" 
            id="user" 
            name="user" 
            required 
            value={user}
            onChange={(e) => setUser(e.target.value)}
          />
          
          <label htmlFor="password">Contraseña:</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            required 
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
          
          <label htmlFor="nuevo">¿Eres Nuevo?</label>
          <Link to="/inscripcion">Registro de inscripción</Link><br />
          
          <label htmlFor="olvido">¿Olvidaste tu contraseña?</label>
          <Link to="/recuperar-password">Recuperar contraseña</Link>
          
          <button type="submit">Ingresar</button>
        </form>
      </div>
    </div>
  );
}

export default Login;