-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-10-2025 a las 01:45:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `data_stack`
--
CREATE DATABASE IF NOT EXISTS `data_stack` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `data_stack`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adscripto`
--

CREATE TABLE `adscripto` (
  `ID_Adscripto` int(11) NOT NULL,
  `ID_Usuario` int(11) DEFAULT NULL,
  `ID_Grupo` int(11) DEFAULT NULL,
  `Turno` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `ID_Alumno` int(11) NOT NULL,
  `Cédula` int(11) NOT NULL,
  `ID_Grupo` int(11) DEFAULT NULL,
  `ID_Usuario` int(11) DEFAULT NULL,
  `Turno` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignatura`
--

CREATE TABLE `asignatura` (
  `ID_Asignatura` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignatura`
--

INSERT INTO `asignatura` (`ID_Asignatura`, `Nombre`) VALUES
(1, 'Mat. CTS'),
(2, 'Sociología'),
(3, 'Ingeniería'),
(4, 'Inglés'),
(5, 'Ciberseguridad'),
(6, 'Sistemas Operativos'),
(7, 'Programación'),
(8, 'Cálculo'),
(9, 'Filosofía'),
(10, 'Física'),
(11, 'Emprendurismo y Gestión'),
(12, 'Proyecto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigos_admin`
--

CREATE TABLE `codigos_admin` (
  `ID_Codigo` int(11) NOT NULL,
  `Codigo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `codigos_admin`
--

INSERT INTO `codigos_admin` (`ID_Codigo`, `Codigo`) VALUES
(2, '1234');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `ID_Curso` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`ID_Curso`, `Nombre`) VALUES
(1, 'Bachillerato Informática'),
(2, 'Bachillerato Robótica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso_tiene_asignaturas`
--

CREATE TABLE `curso_tiene_asignaturas` (
  `ID_Asignatura` int(11) NOT NULL,
  `ID_Curso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso_tiene_asignaturas`
--

INSERT INTO `curso_tiene_asignaturas` (`ID_Asignatura`, `ID_Curso`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente`
--

CREATE TABLE `docente` (
  `ID_Docente` int(11) NOT NULL,
  `ID_Usuario` int(11) DEFAULT NULL,
  `ID_Asignatura` int(11) DEFAULT NULL,
  `Fecha_Ingreso` date DEFAULT NULL,
  `ID_Grupo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docente`
--

INSERT INTO `docente` (`ID_Docente`, `ID_Usuario`, `ID_Asignatura`, `Fecha_Ingreso`, `ID_Grupo`) VALUES
(1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente_asignatura`
--

CREATE TABLE `docente_asignatura` (
  `ID_Usuario` int(11) NOT NULL COMMENT 'ID del Docente (FK a usuario)',
  `ID_Asignatura` int(11) NOT NULL COMMENT 'ID de la Asignatura (FK a asignatura)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docente_asignatura`
--

INSERT INTO `docente_asignatura` (`ID_Usuario`, `ID_Asignatura`) VALUES
(22, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente_grupo`
--

CREATE TABLE `docente_grupo` (
  `ID_Usuario` int(11) NOT NULL COMMENT 'ID del Docente (FK a usuario)',
  `ID_Grupo` int(11) NOT NULL COMMENT 'ID del Grupo (FK a grupo)',
  `ID_Asignatura` int(11) NOT NULL COMMENT 'ID de la Asignatura (FK a asignatura)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docente_grupo`
--

INSERT INTO `docente_grupo` (`ID_Usuario`, `ID_Grupo`, `ID_Asignatura`) VALUES
(22, 5, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `espacio`
--

CREATE TABLE `espacio` (
  `ID_Espacio` int(11) NOT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Capacidad` int(11) DEFAULT NULL,
  `Ubicacion` varchar(100) DEFAULT NULL,
  `ID_Tipo_Espacio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE `evento` (
  `ID_Evento` varchar(50) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `ID_Horario` int(11) DEFAULT NULL,
  `ID_Reserva` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo`
--

CREATE TABLE `grupo` (
  `ID_Grupo` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `ID_Curso` int(11) DEFAULT NULL,
  `ID_Horario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo`
--

INSERT INTO `grupo` (`ID_Grupo`, `Nombre`, `ID_Curso`, `ID_Horario`) VALUES
(5, '3°BC', 1, NULL),
(6, '3°MA', 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_de_limpieza`
--

CREATE TABLE `historial_de_limpieza` (
  `ID_Historial_Limpieza` int(11) NOT NULL,
  `Fecha_Hora_Inicio` datetime NOT NULL,
  `Fecha_Hora_Finalizacion` datetime NOT NULL,
  `Estado_De_Limpieza` varchar(50) NOT NULL,
  `Observaciones` text DEFAULT NULL,
  `ID_Usuario` int(11) NOT NULL,
  `ID_Tipo_Limpieza` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `ID_Horario` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Fecha_Creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`ID_Horario`, `Nombre`, `Fecha_Creacion`) VALUES
(1, 'Horario inicial', '2025-09-03 19:56:28'),
(2, 'Nuevo Horario', '2025-09-03 19:59:39'),
(3, 'Nuevo Horario', '2025-09-03 20:01:10'),
(4, 'Nuevo Horario', '2025-09-03 20:14:31'),
(5, 'Nuevo Horario', '2025-09-08 18:14:08'),
(7, 'holaa', '2025-10-06 16:10:57'),
(8, 'holaa', '2025-10-06 16:13:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario_backup`
--

CREATE TABLE `horario_backup` (
  `ID_Horario` int(11) NOT NULL,
  `ID_Hora` int(11) DEFAULT NULL,
  `ID_Asignatura` int(11) DEFAULT NULL,
  `ID_Dia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario_detalle`
--

CREATE TABLE `horario_detalle` (
  `ID_Detalle` int(11) NOT NULL,
  `ID_Horario` int(11) NOT NULL,
  `ID_Dia` int(11) NOT NULL,
  `ID_Hora` int(11) NOT NULL,
  `ID_Asignatura` int(11) NOT NULL,
  `ID_Grupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horario_detalle`
--

INSERT INTO `horario_detalle` (`ID_Detalle`, `ID_Horario`, `ID_Dia`, `ID_Hora`, `ID_Asignatura`, `ID_Grupo`) VALUES
(1, 7, 1, 2, 8, 5),
(2, 8, 1, 2, 8, 5),
(3, 8, 2, 2, 5, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horas`
--

CREATE TABLE `horas` (
  `ID_Hora` int(11) NOT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Duracion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horas`
--

INSERT INTO `horas` (`ID_Hora`, `Nombre`, `Duracion`) VALUES
(2, '1ª Hora', 45),
(3, '2ª Hora', 45),
(4, '3ª Hora', 45),
(5, '4ª Hora', 45),
(6, '5ª Hora', 45),
(7, '6ª Hora', 45),
(8, '7ª Hora', 45),
(9, '8ª Hora', 45);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `ID_Permiso` int(11) NOT NULL,
  `Nombre_Permiso` varchar(100) NOT NULL,
  `ID_Rol` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos`
--

CREATE TABLE `recursos` (
  `ID_Recurso` int(11) NOT NULL,
  `Nombre` varchar(130) NOT NULL,
  `Ubicacion` varchar(130) DEFAULT NULL,
  `Estado` varchar(130) DEFAULT NULL,
  `Descripcion` text DEFAULT NULL,
  `Ultimo_Mantenimiento` date DEFAULT NULL,
  `ID_Tipo_Recurso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

CREATE TABLE `reporte` (
  `ID_Reporte` int(11) NOT NULL,
  `Fecha_Reporte` date NOT NULL,
  `Estado_Reporte` varchar(130) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `ID_Usuario` int(11) NOT NULL,
  `ID_Tipo_Reporte` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `ID_Reserva` int(11) NOT NULL,
  `Fecha_Creada` datetime NOT NULL,
  `Hora_Inicio` time NOT NULL,
  `Estado` varchar(50) NOT NULL,
  `Descripcion_Motivo` text DEFAULT NULL,
  `ID_Usuario` int(11) DEFAULT NULL,
  `ID_Espacio` int(11) DEFAULT NULL,
  `ID_Recurso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `ID_Rol` int(11) NOT NULL,
  `Nombre_Rol` varchar(255) NOT NULL,
  `ID_Tipo_Rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`ID_Rol`, `Nombre_Rol`, `ID_Tipo_Rol`) VALUES
(1, 'Administrador', 1),
(2, 'Profesor', 2),
(3, 'Estudiante', 2),
(4, 'Adscripto', 3),
(5, 'Auxiliar', 3),
(8, 'Invitado', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semana`
--

CREATE TABLE `semana` (
  `ID_Dia` int(11) NOT NULL,
  `Nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `semana`
--

INSERT INTO `semana` (`ID_Dia`, `Nombre`) VALUES
(1, 'Lunes'),
(2, 'Martes'),
(3, 'Miércoles'),
(4, 'Jueves'),
(5, 'Viernes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_de_limpieza`
--

CREATE TABLE `tipo_de_limpieza` (
  `ID_Tipo_Limpieza` int(11) NOT NULL,
  `Nombre_Tipo_Limpieza` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_de_reporte`
--

CREATE TABLE `tipo_de_reporte` (
  `ID_Tipo_Reporte` int(11) NOT NULL,
  `Nombre_Tipo_Reporte` varchar(130) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_de_rol`
--

CREATE TABLE `tipo_de_rol` (
  `ID_Tipo_Rol` int(11) NOT NULL,
  `Nombre_Tipo_Rol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_de_rol`
--

INSERT INTO `tipo_de_rol` (`ID_Tipo_Rol`, `Nombre_Tipo_Rol`) VALUES
(1, 'Administrativo'),
(2, 'Académico'),
(3, 'Auxiliar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_espacio`
--

CREATE TABLE `tipo_espacio` (
  `ID_Tipo_Espacio` int(11) NOT NULL,
  `Nombre_Tipo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_recursos`
--

CREATE TABLE `tipo_recursos` (
  `ID_Tipo_Recurso` int(11) NOT NULL,
  `Nombre_Tipo_Recurso` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `ID_Usuario` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Documento` int(15) NOT NULL,
  `Apellido` varchar(255) NOT NULL,
  `Correo` varchar(255) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `ID_Rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`ID_Usuario`, `Nombre`, `Documento`, `Apellido`, `Correo`, `Contrasena`, `ID_Rol`) VALUES
(11, 'Damian', 56567557, 'Luberiaga', 'damian@gmail.com', '$2y$10$GG./mXps9wQ.6AcAblo81e2B09lRHNo8j7giAgBVAfeSQilhvGHj2', 1),
(18, 'Juan', 56567554, 'Perez', 'damianluberiaga5656@gmail.com', '$2y$10$HohdghfTMnrK2nT9VprPcuTQIMQbIh8l7/HVIc/NFz1phIIqdbbX2', 2),
(19, 'Juan', 56567557, 'Perez', 'damianluberiaga5656@gmail.com', '$2y$10$EOSbjf1nW43dpUzoB29W5OVPf4AXmu3gz9K3qVpxeBBj.cQk2ys9e', 2),
(20, 'Juan', 56567557, 'Perez', 'damianluberiaga5656@gmail.com', '$2y$10$V5W65qmpiNOPFntGZ8Uq..bF9EzpO.L8s2iuiVx3kKQQXn3g81gXS', 2),
(21, 'Juan', 56567557, 'Perez', 'damianluberiaga5656@gmail.com', '$2y$10$W18imCLSVQc1Ayb1yx/kwuP8K4StfnE1SwhOFbTYQ8jnYhN5e2WQe', 2),
(22, 'santino', 60690972, 'calderon', 'santinopro60662@gmail.com', '$2y$10$AnTXVtLCRKv7v.Z11KhY1umGFodTiIa9n3UAU2wGLg9Y02w/bzh76', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adscripto`
--
ALTER TABLE `adscripto`
  ADD PRIMARY KEY (`ID_Adscripto`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Grupo` (`ID_Grupo`);

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`ID_Alumno`),
  ADD KEY `ID_Grupo` (`ID_Grupo`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Indices de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD PRIMARY KEY (`ID_Asignatura`);

--
-- Indices de la tabla `codigos_admin`
--
ALTER TABLE `codigos_admin`
  ADD PRIMARY KEY (`ID_Codigo`),
  ADD UNIQUE KEY `Codigo` (`Codigo`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`ID_Curso`);

--
-- Indices de la tabla `curso_tiene_asignaturas`
--
ALTER TABLE `curso_tiene_asignaturas`
  ADD PRIMARY KEY (`ID_Asignatura`,`ID_Curso`),
  ADD KEY `ID_Curso` (`ID_Curso`);

--
-- Indices de la tabla `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`ID_Docente`);

--
-- Indices de la tabla `docente_asignatura`
--
ALTER TABLE `docente_asignatura`
  ADD PRIMARY KEY (`ID_Usuario`,`ID_Asignatura`),
  ADD KEY `ID_Asignatura` (`ID_Asignatura`);

--
-- Indices de la tabla `docente_grupo`
--
ALTER TABLE `docente_grupo`
  ADD PRIMARY KEY (`ID_Usuario`,`ID_Grupo`,`ID_Asignatura`),
  ADD KEY `ID_Grupo` (`ID_Grupo`),
  ADD KEY `ID_Asignatura` (`ID_Asignatura`);

--
-- Indices de la tabla `espacio`
--
ALTER TABLE `espacio`
  ADD PRIMARY KEY (`ID_Espacio`),
  ADD KEY `ID_Tipo_Espacio` (`ID_Tipo_Espacio`);

--
-- Indices de la tabla `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`ID_Evento`),
  ADD KEY `ID_Horario` (`ID_Horario`),
  ADD KEY `ID_Reserva` (`ID_Reserva`);

--
-- Indices de la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`ID_Grupo`),
  ADD KEY `ID_Curso` (`ID_Curso`),
  ADD KEY `ID_Horario` (`ID_Horario`);

--
-- Indices de la tabla `historial_de_limpieza`
--
ALTER TABLE `historial_de_limpieza`
  ADD PRIMARY KEY (`ID_Historial_Limpieza`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Tipo_Limpieza` (`ID_Tipo_Limpieza`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`ID_Horario`);

--
-- Indices de la tabla `horario_backup`
--
ALTER TABLE `horario_backup`
  ADD PRIMARY KEY (`ID_Horario`),
  ADD KEY `fk_horario_horas` (`ID_Hora`),
  ADD KEY `fk_dia` (`ID_Dia`);

--
-- Indices de la tabla `horario_detalle`
--
ALTER TABLE `horario_detalle`
  ADD PRIMARY KEY (`ID_Detalle`),
  ADD UNIQUE KEY `horario_grupo_bloque` (`ID_Horario`,`ID_Dia`,`ID_Hora`,`ID_Grupo`),
  ADD KEY `ID_Dia` (`ID_Dia`),
  ADD KEY `ID_Hora` (`ID_Hora`),
  ADD KEY `ID_Asignatura` (`ID_Asignatura`),
  ADD KEY `ID_Grupo` (`ID_Grupo`);

--
-- Indices de la tabla `horas`
--
ALTER TABLE `horas`
  ADD PRIMARY KEY (`ID_Hora`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`ID_Permiso`),
  ADD KEY `ID_Rol` (`ID_Rol`);

--
-- Indices de la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD PRIMARY KEY (`ID_Recurso`),
  ADD KEY `ID_Tipo_Recurso` (`ID_Tipo_Recurso`);

--
-- Indices de la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD PRIMARY KEY (`ID_Reporte`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Tipo_Reporte` (`ID_Tipo_Reporte`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`ID_Reserva`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Espacio` (`ID_Espacio`),
  ADD KEY `ID_Recurso` (`ID_Recurso`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`ID_Rol`),
  ADD KEY `ID_Tipo_Rol` (`ID_Tipo_Rol`);

--
-- Indices de la tabla `semana`
--
ALTER TABLE `semana`
  ADD PRIMARY KEY (`ID_Dia`);

--
-- Indices de la tabla `tipo_de_limpieza`
--
ALTER TABLE `tipo_de_limpieza`
  ADD PRIMARY KEY (`ID_Tipo_Limpieza`);

--
-- Indices de la tabla `tipo_de_reporte`
--
ALTER TABLE `tipo_de_reporte`
  ADD PRIMARY KEY (`ID_Tipo_Reporte`);

--
-- Indices de la tabla `tipo_de_rol`
--
ALTER TABLE `tipo_de_rol`
  ADD PRIMARY KEY (`ID_Tipo_Rol`);

--
-- Indices de la tabla `tipo_espacio`
--
ALTER TABLE `tipo_espacio`
  ADD PRIMARY KEY (`ID_Tipo_Espacio`);

--
-- Indices de la tabla `tipo_recursos`
--
ALTER TABLE `tipo_recursos`
  ADD PRIMARY KEY (`ID_Tipo_Recurso`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD KEY `ID_Rol` (`ID_Rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adscripto`
--
ALTER TABLE `adscripto`
  MODIFY `ID_Adscripto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `alumno`
--
ALTER TABLE `alumno`
  MODIFY `ID_Alumno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  MODIFY `ID_Asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `codigos_admin`
--
ALTER TABLE `codigos_admin`
  MODIFY `ID_Codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `ID_Curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `docente`
--
ALTER TABLE `docente`
  MODIFY `ID_Docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `espacio`
--
ALTER TABLE `espacio`
  MODIFY `ID_Espacio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupo`
--
ALTER TABLE `grupo`
  MODIFY `ID_Grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `historial_de_limpieza`
--
ALTER TABLE `historial_de_limpieza`
  MODIFY `ID_Historial_Limpieza` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `ID_Horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `horario_backup`
--
ALTER TABLE `horario_backup`
  MODIFY `ID_Horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `horario_detalle`
--
ALTER TABLE `horario_detalle`
  MODIFY `ID_Detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `horas`
--
ALTER TABLE `horas`
  MODIFY `ID_Hora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `ID_Permiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recursos`
--
ALTER TABLE `recursos`
  MODIFY `ID_Recurso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reporte`
--
ALTER TABLE `reporte`
  MODIFY `ID_Reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `ID_Reserva` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `ID_Rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tipo_de_limpieza`
--
ALTER TABLE `tipo_de_limpieza`
  MODIFY `ID_Tipo_Limpieza` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_de_reporte`
--
ALTER TABLE `tipo_de_reporte`
  MODIFY `ID_Tipo_Reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_de_rol`
--
ALTER TABLE `tipo_de_rol`
  MODIFY `ID_Tipo_Rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_espacio`
--
ALTER TABLE `tipo_espacio`
  MODIFY `ID_Tipo_Espacio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_recursos`
--
ALTER TABLE `tipo_recursos`
  MODIFY `ID_Tipo_Recurso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adscripto`
--
ALTER TABLE `adscripto`
  ADD CONSTRAINT `adscripto_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`),
  ADD CONSTRAINT `adscripto_ibfk_2` FOREIGN KEY (`ID_Grupo`) REFERENCES `grupo` (`ID_Grupo`);

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`ID_Grupo`) REFERENCES `grupo` (`ID_Grupo`),
  ADD CONSTRAINT `alumno_ibfk_2` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`);

--
-- Filtros para la tabla `curso_tiene_asignaturas`
--
ALTER TABLE `curso_tiene_asignaturas`
  ADD CONSTRAINT `curso_tiene_asignaturas_ibfk_1` FOREIGN KEY (`ID_Asignatura`) REFERENCES `asignatura` (`ID_Asignatura`),
  ADD CONSTRAINT `curso_tiene_asignaturas_ibfk_2` FOREIGN KEY (`ID_Curso`) REFERENCES `curso` (`ID_Curso`);

--
-- Filtros para la tabla `docente_asignatura`
--
ALTER TABLE `docente_asignatura`
  ADD CONSTRAINT `docente_asignatura_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `docente_asignatura_ibfk_2` FOREIGN KEY (`ID_Asignatura`) REFERENCES `asignatura` (`ID_Asignatura`) ON DELETE CASCADE;

--
-- Filtros para la tabla `docente_grupo`
--
ALTER TABLE `docente_grupo`
  ADD CONSTRAINT `docente_grupo_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `docente_grupo_ibfk_2` FOREIGN KEY (`ID_Grupo`) REFERENCES `grupo` (`ID_Grupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `docente_grupo_ibfk_3` FOREIGN KEY (`ID_Asignatura`) REFERENCES `asignatura` (`ID_Asignatura`) ON DELETE CASCADE;

--
-- Filtros para la tabla `espacio`
--
ALTER TABLE `espacio`
  ADD CONSTRAINT `espacio_ibfk_1` FOREIGN KEY (`ID_Tipo_Espacio`) REFERENCES `tipo_espacio` (`ID_Tipo_Espacio`);

--
-- Filtros para la tabla `evento`
--
ALTER TABLE `evento`
  ADD CONSTRAINT `evento_ibfk_1` FOREIGN KEY (`ID_Horario`) REFERENCES `horario_backup` (`ID_Horario`),
  ADD CONSTRAINT `evento_ibfk_2` FOREIGN KEY (`ID_Reserva`) REFERENCES `reserva` (`ID_Reserva`);

--
-- Filtros para la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD CONSTRAINT `grupo_ibfk_1` FOREIGN KEY (`ID_Curso`) REFERENCES `curso` (`ID_Curso`),
  ADD CONSTRAINT `grupo_ibfk_2` FOREIGN KEY (`ID_Horario`) REFERENCES `horario_backup` (`ID_Horario`);

--
-- Filtros para la tabla `historial_de_limpieza`
--
ALTER TABLE `historial_de_limpieza`
  ADD CONSTRAINT `historial_de_limpieza_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`),
  ADD CONSTRAINT `historial_de_limpieza_ibfk_2` FOREIGN KEY (`ID_Tipo_Limpieza`) REFERENCES `tipo_de_limpieza` (`ID_Tipo_Limpieza`);

--
-- Filtros para la tabla `horario_backup`
--
ALTER TABLE `horario_backup`
  ADD CONSTRAINT `fk_dia` FOREIGN KEY (`ID_Dia`) REFERENCES `semana` (`ID_Dia`),
  ADD CONSTRAINT `fk_horario` FOREIGN KEY (`ID_Horario`) REFERENCES `horarios` (`ID_Horario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_horario_horas` FOREIGN KEY (`ID_Hora`) REFERENCES `horas` (`ID_Hora`);

--
-- Filtros para la tabla `horario_detalle`
--
ALTER TABLE `horario_detalle`
  ADD CONSTRAINT `horario_detalle_ibfk_1` FOREIGN KEY (`ID_Horario`) REFERENCES `horarios` (`ID_Horario`) ON DELETE CASCADE,
  ADD CONSTRAINT `horario_detalle_ibfk_2` FOREIGN KEY (`ID_Dia`) REFERENCES `semana` (`ID_Dia`),
  ADD CONSTRAINT `horario_detalle_ibfk_3` FOREIGN KEY (`ID_Hora`) REFERENCES `horas` (`ID_Hora`),
  ADD CONSTRAINT `horario_detalle_ibfk_4` FOREIGN KEY (`ID_Asignatura`) REFERENCES `asignatura` (`ID_Asignatura`),
  ADD CONSTRAINT `horario_detalle_ibfk_5` FOREIGN KEY (`ID_Grupo`) REFERENCES `grupo` (`ID_Grupo`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`ID_Rol`) REFERENCES `rol` (`ID_Rol`);

--
-- Filtros para la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD CONSTRAINT `recursos_ibfk_1` FOREIGN KEY (`ID_Tipo_Recurso`) REFERENCES `tipo_recursos` (`ID_Tipo_Recurso`);

--
-- Filtros para la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD CONSTRAINT `reporte_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`),
  ADD CONSTRAINT `reporte_ibfk_2` FOREIGN KEY (`ID_Tipo_Reporte`) REFERENCES `tipo_de_reporte` (`ID_Tipo_Reporte`);

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`),
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`ID_Espacio`) REFERENCES `espacio` (`ID_Espacio`),
  ADD CONSTRAINT `reserva_ibfk_3` FOREIGN KEY (`ID_Recurso`) REFERENCES `recursos` (`ID_Recurso`);

--
-- Filtros para la tabla `rol`
--
ALTER TABLE `rol`
  ADD CONSTRAINT `rol_ibfk_1` FOREIGN KEY (`ID_Tipo_Rol`) REFERENCES `tipo_de_rol` (`ID_Tipo_Rol`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`ID_Rol`) REFERENCES `rol` (`ID_Rol`);
--
-- Base de datos: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Volcado de datos para la tabla `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2019-10-21 13:37:09', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indices de la tabla `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indices de la tabla `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indices de la tabla `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indices de la tabla `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indices de la tabla `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indices de la tabla `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indices de la tabla `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indices de la tabla `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indices de la tabla `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indices de la tabla `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indices de la tabla `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indices de la tabla `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indices de la tabla `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Base de datos: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
