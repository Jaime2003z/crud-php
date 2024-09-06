-- Crear base de datos
CREATE DATABASE crudphp;

-- Usar la base de datos
USE crudphp

-- Tabla de productos
CREATE TABLE Producto (
    codigo_producto INT PRIMARY KEY,
    nombre_producto VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    cantidad_disponible INT NOT NULL,
    estado ENUM('en stock', 'agotado') NOT NULL
);

-- Tabla de clientes
CREATE TABLE Cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nombre_cliente VARCHAR(100) NOT NULL,
    cedula_nit VARCHAR(50) NOT NULL UNIQUE,
    tipo_cliente ENUM('Natural', 'Empresa') NOT NULL
);

-- Tabla de ventas
CREATE TABLE Venta (
    id_venta INT PRIMARY KEY AUTO_INCREMENT,
    fecha_venta DATE NOT NULL,
    total_venta DECIMAL(10, 2) NOT NULL,
    iva DECIMAL(10, 2) NOT NULL DEFAULT 0.19,
    total_final DECIMAL(10, 2) AS (total_venta + (total_venta * iva)) PERSISTENT
);

-- Tabla detalle de ventas
CREATE TABLE DetalleVenta (
    id_detalle INT PRIMARY KEY AUTO_INCREMENT,
    fk_id_venta INT,
    fk_codigo_producto INT,
    cantidad_vendida INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (fk_id_venta) REFERENCES Venta(id_venta),
    FOREIGN KEY (fk_codigo_producto) REFERENCES Producto(codigo_producto)
);

-- Tabla de facturas
CREATE TABLE Factura (
    id_factura INT PRIMARY KEY AUTO_INCREMENT,
    fk_id_venta INT,
    fk_id_cliente INT,
    FOREIGN KEY (fk_id_venta) REFERENCES Venta(id_venta),
    FOREIGN KEY (fk_id_cliente) REFERENCES Cliente(id_cliente)
);
