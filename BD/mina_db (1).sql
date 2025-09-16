-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-09-2025 a las 02:00:28
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
-- Base de datos: `mina_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alerta`
--

CREATE TABLE `alerta` (
  `id_alerta` int(11) NOT NULL,
  `tipo_alerta` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_deposito_DepositoPago` int(11) DEFAULT NULL,
  `id_mov_MovCaja` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `depositopago`
--

CREATE TABLE `depositopago` (
  `id_deposito` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `factura` varchar(50) DEFAULT NULL,
  `concepto` text DEFAULT NULL,
  `id_proveedor_proveedor` int(11) DEFAULT NULL,
  `id_reporte_reporte` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallemov`
--

CREATE TABLE `detallemov` (
  `id_detallemov` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `producto` varchar(100) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `unidad` varchar(50) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `IGVMonto` decimal(5,2) DEFAULT NULL,
  `Total` decimal(10,2) NOT NULL,
  `id_mov_MovCaja` int(11) DEFAULT NULL,
  `id_Familia_Familia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallemov`
--

INSERT INTO `detallemov` (`id_detallemov`, `cantidad`, `producto`, `precio_unitario`, `unidad`, `subtotal`, `IGVMonto`, `Total`, `id_mov_MovCaja`, `id_Familia_Familia`) VALUES
(33, 1, 'PAGOS', 50.00, 'soles', 42.37, 7.63, 50.00, 78, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familia`
--

CREATE TABLE `familia` (
  `id_Familia` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `familia`
--

INSERT INTO `familia` (`id_Familia`, `nombre`) VALUES
(1, 'Mantenimiento'),
(4, 'Efectivo'),
(5, 'pagos'),
(6, 'sabre'),
(7, 'Familia'),
(8, 'asd'),
(9, 'Cocina');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `labor`
--

CREATE TABLE `labor` (
  `id_labor` int(11) NOT NULL,
  `nombre_labor` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `labor`
--

INSERT INTO `labor` (`id_labor`, `nombre_labor`, `descripcion`) VALUES
(1, 'Caravileños', 'Labor Caravileños'),
(2, 'Copacabana', 'Labor Copacabana'),
(3, 'Imparable', 'Labor Imparable'),
(4, 'Lurdes', 'Labor Lurdes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movcaja`
--

CREATE TABLE `movcaja` (
  `id_mov` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `factura` varchar(50) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `IGV` decimal(5,2) DEFAULT NULL,
  `tipo_entrada_salida` enum('Entrada','Salida') NOT NULL,
  `observacion` text DEFAULT NULL,
  `id_reporte_reporte` int(11) DEFAULT NULL,
  `id_proveedor_proveedor` int(11) DEFAULT NULL,
  `id_labor_Labor` int(11) DEFAULT NULL,
  `id_Familia_Familia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movcaja`
--

INSERT INTO `movcaja` (`id_mov`, `fecha`, `factura`, `monto`, `saldo`, `IGV`, `tipo_entrada_salida`, `observacion`, `id_reporte_reporte`, `id_proveedor_proveedor`, `id_labor_Labor`, `id_Familia_Familia`) VALUES
(55, '2025-09-14', '0', 0.00, 0.00, NULL, 'Salida', '', NULL, 16, 2, NULL),
(56, '2025-09-14', '0', 0.00, 0.00, NULL, 'Salida', '', NULL, 16, 2, NULL),
(57, '2025-09-14', '0', 0.00, 0.00, NULL, 'Salida', '', NULL, 16, 2, NULL),
(60, '2025-09-14', '3', 0.00, 0.00, NULL, 'Salida', '', NULL, 16, 2, NULL),
(62, '2025-09-14', 'as', 0.00, 0.00, NULL, 'Salida', '', NULL, 1, 1, NULL),
(77, '2025-09-15', 'asd', 100.00, 100.00, NULL, 'Entrada', '', NULL, 1, 2, 8),
(78, '2025-09-15', '3', 50.00, 50.00, NULL, 'Salida', '', NULL, 19, 2, NULL),
(79, '2025-09-15', '3', 0.00, 50.00, NULL, 'Salida', '', NULL, 20, 2, NULL),
(80, '2025-09-15', '3', 0.00, 50.00, NULL, 'Salida', '', NULL, 20, 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `RUC` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre`, `RUC`) VALUES
(1, 'ProveedorJAJA', 12345678901),
(16, 'Prueba10', 12),
(19, 'jojoletre', 0),
(20, 'asd', 12938);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

CREATE TABLE `reporte` (
  `id_reporte` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total_deposito` decimal(10,2) NOT NULL,
  `total_efectivo` decimal(10,2) NOT NULL,
  `saldo_final` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `password`, `rol`) VALUES
(1, 'admin', '$2y$10$q/EqbDPbT6f5DaQZLyJyxePkdOljSOYVoG8VBxY7XMI4t7KkZuI1m', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alerta`
--
ALTER TABLE `alerta`
  ADD PRIMARY KEY (`id_alerta`),
  ADD KEY `id_deposito_DepositoPago` (`id_deposito_DepositoPago`),
  ADD KEY `id_mov_MovCaja` (`id_mov_MovCaja`);

--
-- Indices de la tabla `depositopago`
--
ALTER TABLE `depositopago`
  ADD PRIMARY KEY (`id_deposito`),
  ADD KEY `id_proveedor_proveedor` (`id_proveedor_proveedor`),
  ADD KEY `id_reporte_reporte` (`id_reporte_reporte`);

--
-- Indices de la tabla `detallemov`
--
ALTER TABLE `detallemov`
  ADD PRIMARY KEY (`id_detallemov`),
  ADD KEY `id_mov_MovCaja` (`id_mov_MovCaja`),
  ADD KEY `id_Familia_Familia` (`id_Familia_Familia`);

--
-- Indices de la tabla `familia`
--
ALTER TABLE `familia`
  ADD PRIMARY KEY (`id_Familia`);

--
-- Indices de la tabla `labor`
--
ALTER TABLE `labor`
  ADD PRIMARY KEY (`id_labor`);

--
-- Indices de la tabla `movcaja`
--
ALTER TABLE `movcaja`
  ADD PRIMARY KEY (`id_mov`),
  ADD KEY `id_reporte_reporte` (`id_reporte_reporte`),
  ADD KEY `id_proveedor_proveedor` (`id_proveedor_proveedor`),
  ADD KEY `id_labor_Labor` (`id_labor_Labor`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD PRIMARY KEY (`id_reporte`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alerta`
--
ALTER TABLE `alerta`
  MODIFY `id_alerta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `depositopago`
--
ALTER TABLE `depositopago`
  MODIFY `id_deposito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detallemov`
--
ALTER TABLE `detallemov`
  MODIFY `id_detallemov` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `familia`
--
ALTER TABLE `familia`
  MODIFY `id_Familia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `labor`
--
ALTER TABLE `labor`
  MODIFY `id_labor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `movcaja`
--
ALTER TABLE `movcaja`
  MODIFY `id_mov` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `reporte`
--
ALTER TABLE `reporte`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alerta`
--
ALTER TABLE `alerta`
  ADD CONSTRAINT `alerta_ibfk_1` FOREIGN KEY (`id_deposito_DepositoPago`) REFERENCES `depositopago` (`id_deposito`),
  ADD CONSTRAINT `alerta_ibfk_2` FOREIGN KEY (`id_mov_MovCaja`) REFERENCES `movcaja` (`id_mov`);

--
-- Filtros para la tabla `depositopago`
--
ALTER TABLE `depositopago`
  ADD CONSTRAINT `depositopago_ibfk_1` FOREIGN KEY (`id_proveedor_proveedor`) REFERENCES `proveedor` (`id_proveedor`),
  ADD CONSTRAINT `depositopago_ibfk_2` FOREIGN KEY (`id_reporte_reporte`) REFERENCES `reporte` (`id_reporte`);

--
-- Filtros para la tabla `detallemov`
--
ALTER TABLE `detallemov`
  ADD CONSTRAINT `detallemov_ibfk_1` FOREIGN KEY (`id_mov_MovCaja`) REFERENCES `movcaja` (`id_mov`),
  ADD CONSTRAINT `detallemov_ibfk_2` FOREIGN KEY (`id_Familia_Familia`) REFERENCES `familia` (`id_Familia`);

--
-- Filtros para la tabla `movcaja`
--
ALTER TABLE `movcaja`
  ADD CONSTRAINT `movcaja_ibfk_1` FOREIGN KEY (`id_reporte_reporte`) REFERENCES `reporte` (`id_reporte`),
  ADD CONSTRAINT `movcaja_ibfk_2` FOREIGN KEY (`id_proveedor_proveedor`) REFERENCES `proveedor` (`id_proveedor`),
  ADD CONSTRAINT `movcaja_ibfk_3` FOREIGN KEY (`id_labor_Labor`) REFERENCES `labor` (`id_labor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
