-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2025 a las 13:13:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `base_impresora`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentador_pagina`
--

CREATE TABLE `cuentador_pagina` (
  `id_contador_pagina` int(12) NOT NULL,
  `id_impresora` int(12) NOT NULL,
  `fecha_registro` date NOT NULL,
  `numero_paginas` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_actual_impresora`
--

CREATE TABLE `estado_actual_impresora` (
  `id_estado_actual_impresora` int(2) NOT NULL,
  `tipo_estado` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_actual_impresora`
--

INSERT INTO `estado_actual_impresora` (`id_estado_actual_impresora`, `tipo_estado`) VALUES
(1, 'activa'),
(2, 'alerta'),
(3, 'inactiva'),
(4, 'Requiere mantenimneto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos_falla_mantenimiento`
--

CREATE TABLE `eventos_falla_mantenimiento` (
  `id_eventos_falla_mantenimiento` int(12) NOT NULL,
  `id_impresora` int(12) NOT NULL,
  `fecha_evento` date NOT NULL,
  `id_tipo_evento` int(9) NOT NULL,
  `descripcion_problema` text NOT NULL,
  `diagnostico_ia_sugerido` text NOT NULL,
  `solucion_aplicada` text NOT NULL,
  `piezas_reemplazadas` text NOT NULL,
  `contador_paginas_evento` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_impresiones`
--

CREATE TABLE `historial_impresiones` (
  `id_impresion` int(11) NOT NULL,
  `cantidad_hojas` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_impresora` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_impresiones`
--

INSERT INTO `historial_impresiones` (`id_impresion`, `cantidad_hojas`, `id_usuario`, `id_impresora`, `fecha`) VALUES
(1, 5, 4, 1, '2025-07-03'),
(2, 10, 4, 2, '2025-07-04'),
(3, 40, 5, 1, '2025-07-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impresora`
--

CREATE TABLE `impresora` (
  `id_impresora` int(11) NOT NULL,
  `id_usuario` int(12) NOT NULL,
  `numero_serie` varchar(50) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `id_tipo_impresora` int(9) NOT NULL,
  `fecha_puesta_en_servicio` date NOT NULL,
  `contador_paginas_actual` int(6) NOT NULL,
  `toner_negro` int(100) NOT NULL,
  `toner_cian` int(100) NOT NULL,
  `toner_magenta` int(100) NOT NULL,
  `toner_amarillo` int(100) NOT NULL,
  `estado_actual` int(11) NOT NULL,
  `ubicacion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `impresora`
--

INSERT INTO `impresora` (`id_impresora`, `id_usuario`, `numero_serie`, `marca`, `modelo`, `id_tipo_impresora`, `fecha_puesta_en_servicio`, `contador_paginas_actual`, `toner_negro`, `toner_cian`, `toner_magenta`, `toner_amarillo`, `estado_actual`, `ubicacion`) VALUES
(1, 4, '10', 'hp', 'nueva', 4, '2025-06-18', 20, 80, 20, 50, 10, 1, 'Departamento tecnologia'),
(2, 4, '11', 'HP', 'LaserJet Piña488', 4, '2025-06-18', 50, 100, 100, 100, 100, 3, 'departamento marketing'),
(3, 5, 'M428fdw', 'HP', 'LaserJet Pro MFP M428fdw', 4, '0000-00-00', 0, 100, 15, 40, 70, 2, 'departamento de marketing'),
(4, 5, 'ET-4800', 'Epson', 'EcoTank ET-4800', 7, '0000-00-00', 300, 8, 40, 60, 35, 1, 'departamento de finanzas'),
(5, 5, 'TM-305', 'Canon', 'imagePROGRAF', 6, '0000-00-00', 200, 40, 50, 70, 30, 1, 'departamento de arte'),
(6, 5, 'HL-L2390DW', 'Brother', 'HL-L2390DW', 4, '0000-00-00', 100, 75, 20, 40, 60, 3, 'departamento de estadística'),
(7, 5, '6515/DNI', 'Xerox', 'WorkCentre 6515/DNI', 1, '0000-00-00', 250, 100, 70, 30, 10, 2, 'Departamento de recursos humanos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_evento`
--

CREATE TABLE `tipos_evento` (
  `id_tipo_evento` int(2) NOT NULL,
  `tipo_evento` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_evento`
--

INSERT INTO `tipos_evento` (`id_tipo_evento`, `tipo_evento`) VALUES
(1, 'Problema reportado'),
(2, 'Mantenimiento Preventivo'),
(3, 'Mantenimiento Correctivo'),
(4, 'Inspeccion'),
(5, 'Instalacion Hardware'),
(6, 'Actualizacion Hardware'),
(7, 'Instalacion Software'),
(8, 'Actualizacion Software'),
(9, 'Otro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_impresora`
--

CREATE TABLE `tipo_impresora` (
  `id_tipo_impresora` int(11) NOT NULL,
  `tipo_impresora` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_impresora`
--

INSERT INTO `tipo_impresora` (`id_tipo_impresora`, `tipo_impresora`) VALUES
(1, 'Inyeccion'),
(2, '3D'),
(3, 'Termica'),
(4, 'Láser'),
(5, 'Multifunción'),
(6, 'Plotters'),
(7, 'Tanque de tinta'),
(8, 'De impacto'),
(9, 'Otro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `alias` varchar(30) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `alias`, `contraseña`, `fecha_registro`) VALUES
(4, '', '', 'MangoSeco32', '$2y$10$ahcLx4jajcQF2/YySlRe0uujjNhQ7ut3NsvjPhJhRY7CZJ7mN81ou', '0000-00-00'),
(5, 'Daniel', 'Palacios', 'MrEleki', '$2y$10$91Hs3d5ZnR6SHrOw3ZodTOCbKL5FLGI6j.NL3k4mojMIX3yVnXU7C', '2025-07-03');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cuentador_pagina`
--
ALTER TABLE `cuentador_pagina`
  ADD PRIMARY KEY (`id_contador_pagina`),
  ADD KEY `id_impresora` (`id_impresora`);

--
-- Indices de la tabla `estado_actual_impresora`
--
ALTER TABLE `estado_actual_impresora`
  ADD PRIMARY KEY (`id_estado_actual_impresora`);

--
-- Indices de la tabla `eventos_falla_mantenimiento`
--
ALTER TABLE `eventos_falla_mantenimiento`
  ADD PRIMARY KEY (`id_eventos_falla_mantenimiento`),
  ADD KEY `eventos_falla_mantenimiento_ibfk_1` (`id_impresora`),
  ADD KEY `id_tipo_evento` (`id_tipo_evento`);

--
-- Indices de la tabla `historial_impresiones`
--
ALTER TABLE `historial_impresiones`
  ADD PRIMARY KEY (`id_impresion`),
  ADD KEY `usuario` (`id_usuario`),
  ADD KEY `impresora` (`id_impresora`);

--
-- Indices de la tabla `impresora`
--
ALTER TABLE `impresora`
  ADD PRIMARY KEY (`id_impresora`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_tipo_impresora` (`id_tipo_impresora`),
  ADD KEY `estado_actual` (`estado_actual`);

--
-- Indices de la tabla `tipos_evento`
--
ALTER TABLE `tipos_evento`
  ADD PRIMARY KEY (`id_tipo_evento`);

--
-- Indices de la tabla `tipo_impresora`
--
ALTER TABLE `tipo_impresora`
  ADD PRIMARY KEY (`id_tipo_impresora`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cuentador_pagina`
--
ALTER TABLE `cuentador_pagina`
  MODIFY `id_contador_pagina` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_actual_impresora`
--
ALTER TABLE `estado_actual_impresora`
  MODIFY `id_estado_actual_impresora` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `eventos_falla_mantenimiento`
--
ALTER TABLE `eventos_falla_mantenimiento`
  MODIFY `id_eventos_falla_mantenimiento` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_impresiones`
--
ALTER TABLE `historial_impresiones`
  MODIFY `id_impresion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `impresora`
--
ALTER TABLE `impresora`
  MODIFY `id_impresora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipos_evento`
--
ALTER TABLE `tipos_evento`
  MODIFY `id_tipo_evento` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tipo_impresora`
--
ALTER TABLE `tipo_impresora`
  MODIFY `id_tipo_impresora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuentador_pagina`
--
ALTER TABLE `cuentador_pagina`
  ADD CONSTRAINT `cuentador_pagina_ibfk_1` FOREIGN KEY (`id_impresora`) REFERENCES `impresora` (`id_impresora`);

--
-- Filtros para la tabla `eventos_falla_mantenimiento`
--
ALTER TABLE `eventos_falla_mantenimiento`
  ADD CONSTRAINT `eventos_falla_mantenimiento_ibfk_1` FOREIGN KEY (`id_impresora`) REFERENCES `impresora` (`id_impresora`),
  ADD CONSTRAINT `eventos_falla_mantenimiento_ibfk_2` FOREIGN KEY (`id_tipo_evento`) REFERENCES `tipos_evento` (`id_tipo_evento`);

--
-- Filtros para la tabla `historial_impresiones`
--
ALTER TABLE `historial_impresiones`
  ADD CONSTRAINT `impresora` FOREIGN KEY (`id_impresora`) REFERENCES `impresora` (`id_impresora`),
  ADD CONSTRAINT `usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `impresora`
--
ALTER TABLE `impresora`
  ADD CONSTRAINT `impresora_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `impresora_ibfk_2` FOREIGN KEY (`id_tipo_impresora`) REFERENCES `tipo_impresora` (`id_tipo_impresora`),
  ADD CONSTRAINT `impresora_ibfk_3` FOREIGN KEY (`estado_actual`) REFERENCES `estado_actual_impresora` (`id_estado_actual_impresora`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
