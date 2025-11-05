import React from 'react';

import UbiImage from '../assets/ubicacion.png'; 

function HomePage() {
  return (
    <>
      <section id="hero">
        <div className="hero-content">
          <h2>Únete a la Academia de Fútbol Halcones UV</h2>
          <p>Formando talentos y futuros campeones. Descubre nuestros programas de entrenamiento de élite para todas las edades.</p>
          <a href="#info" className="btn">Conoce más</a>
        </div>
      </section>

      <section id="info">
        <div className="container">
          <h3>Nuestra Academia</h3>
          <div className="cards-grid">
            <div className="card">
              <h4>Misión</h4>
              <p>Fomentar el desarrollo integral de jóvenes atletas a través del deporte, inculcando valores como la disciplina, el trabajo en equipo y el respeto.</p>
            </div>
            <div className="card">
              <h4>Visión</h4>
              <p>Ser un referente en la formación de futbolistas a nivel estatal y nacional, reconocida por la calidad de sus entrenadores y su metodología innovadora.</p>
            </div>
            <div className="card">
              <h4>Valores</h4>
              <p>Disciplina, Respeto, Pasión, Trabajo en Equipo y Compromiso.</p>
            </div>
          </div>
        </div>
      </section>

      <section id="cursos">
        <div className="container">
          <h3>Cursos y Categorías</h3>
          <p style={{ textAlign: 'center', marginBottom: '2rem' }}>Ofrecemos programas diseñados para cada etapa de desarrollo de nuestros atletas.</p>
          <div className="cards-grid">
            <div className="card">
              <h4>Cursos</h4>
              <ul>
                <li><p><strong>Curso 1:</strong> Lunes, Miércoles y Viernes</p></li>
                <li><p><strong>Curso 2:</strong> Martes y Jueves</p></li>
              </ul>
            </div>
            <div className="card">
              <h4>Costos</h4>
              <ul>
                <li><p><strong>Curso 1:</strong> $600</p></li>
                <li><p><strong>Curso 2:</strong> $450</p></li>
              </ul>
            </div>
            <div className="card">
              <h4>Horarios</h4>
              <ul>
                <li><p><strong>Curso 1:</strong> 16:00 a 18:00 hrs</p></li>
                <li><p><strong>Curso 2:</strong> 16:00 a 18:00 hrs</p></li>
              </ul>
            </div>
            <div className="card">
              <h4>Categorías</h4>
              <ul>
                <li><p><strong>2020-2021:</strong> (Cancha 1)</p></li>
                <li><p><strong>2018-2019:</strong> (Cancha 1)</p></li>
                <li><p><strong>2016-2017:</strong> (Cancha Infantil)</p></li>
                <li><p><strong>2014-2015:</strong> (Campo Mixto)</p></li>
                <li><p><strong>2013-2012:</strong> (Campo 1)</p></li>
                <li><p><strong>2011-2009:</strong> (Campo 1)</p></li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <section id="entrenadores">
        <div className="container">
          <h3>Nuestros Entrenadores</h3>
          <p style={{ textAlign: 'center', marginBottom: '2rem' }}>Contamos con un equipo de profesionales altamente calificados para guiar a nuestros jugadores.</p>
          <div className="cards-grid">
            <div className="card">
              <h4>Jonathan</h4>
              <p>Entrenador Principal</p>
            </div>
            <div className="card">
              <h4>Aaron</h4>
              <p>Entrenador Categoría Infantil</p>
            </div>
            <div className="card">
              <h4>Manolo</h4>
              <p>Entrenador Categoría Juvenil</p>
            </div>
            <div className="card">
              <h4>Mario</h4>
              <p>Preparador Físico</p>
            </div>
            <div className="card">
              <h4>Karina</h4>
              <p>Entrenadora</p>
            </div>
            <div className="card">
              <h4>Cynthia</h4>
              <p>Asistente</p>
            </div>
          </div>
        </div>
      </section>

      <section id="ubicacion">
        <div className="container">
          <h3>Ubicación</h3>
          <div className="card">
            <p>Nuestras instalaciones se encuentran en la Unidad de Servicios Bibliotecarios y de Información (USBI) de la Universidad Veracruzana en la ciudad de Xalapa.</p>
            <br />
            <img src={UbiImage} alt="Ubicación de la Academia de Fútbol Halcones UV" className="map-frame" />
            <br />
          </div>
        </div>
      </section>
    </>
  );
}

export default HomePage;