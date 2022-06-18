--drop table mesas
-- Crea Tabla Mesas
create table Mesas(
	c_año varchar(4) not null default(''),
	c_empr varchar(3) not null default(''),
	c_mesa varchar(2) not null default(''),
	l_mesa varchar(100) not null default(''),
	c_usua varchar(15) not null default('SUPERVISOR'),
	f_digi datetime not null default(GETDATE())
)
go

-- Ubicacion Mesas
ALTER TABLE Mesas
ADD c_ubic char(2) not null DEFAULT '';
go

-- Posicion de Mesas en mapa
ALTER TABLE Mesas
ADD n_ejex numeric(11,0) not null DEFAULT 0;
go

ALTER TABLE Mesas
ADD n_ejey numeric(11,0) not null DEFAULT 0;
go

-- Estado de mesas
ALTER TABLE Mesas
ADD q_ocup numeric(1,0) not null DEFAULT 0;
go

ALTER TABLE LineProd
ADD l_impr varchar(50) not null DEFAULT '';
go

ALTER TABLE Combos
ADD q_acti numeric(1,0) not null DEFAULT 0;
go
-----Ultima modificacion

-- Añade c_alma a tabla ubica
ALTER TABLE Ubica
ADD c_alma varchar(3) not null DEFAULT '001';
go

-- Añade c_alma a tabla Mesas
ALTER TABLE Mesas
ADD c_alma varchar(3) not null DEFAULT '001';
go

-- Añade c_alma a tabla Combos
ALTER TABLE Combos
ADD c_alma varchar(3) not null DEFAULT '001';
go

-- Añade c_alma a tabla CombosDet
ALTER TABLE CombosDet
ADD c_alma varchar(3) not null DEFAULT '001';
go

--Tabla Pedidos
ALTER TABLE Pedidos
ADD n_pers int not null DEFAULT '0';
go

---------------
-- 26/07/2018 -
---------------

-- Tabla Producto
ALTER TABLE Producto
ADD l_impr1 varchar(50) not null DEFAULT '';
go

ALTER TABLE Producto
ADD l_impr2 varchar(50) not null DEFAULT '';
go

-- Tabla Usuarios
ALTER TABLE Usuarios
ADD q_movitem numeric(1,0) not null DEFAULT 0;
go

-- Tabla Peditem
ALTER TABLE PedItem
ADD c_vendd char(2) not null DEFAULT '';
go

-- Tabla Pedidos
ALTER TABLE Pedidos
ADD q_movitem numeric(1,0) not null DEFAULT 0;
go

---------------
-- 31/07/2018 -
---------------

-- Tabla Vendedor
ALTER TABLE Vendedor
ADD l_clav varchar(15) not null DEFAULT '';
go
Update Vendedor SET l_clav = c_vend where l_clav=''
go
ALTER TABLE Vendedor  
ADD CONSTRAINT AK_Vendedor UNIQUE (c_empr,c_alma,l_clav);   
GO

-- Tabla Producto
ALTER TABLE Producto
ADD q_acti numeric(1,0) not null DEFAULT 1;
go

---------------
-- 06/08/2018 -
---------------

-- Tabla Usuarios
ALTER TABLE Usuarios
ADD q_elimitem numeric(1,0) not null DEFAULT 0;
go


---------------
-- 08/08/2018 -
---------------

-- Cramos tabla para las observaciones de los producto
-- ProdObse
create table ProdObse(
	c_empr varchar(3) not null default(''),
	c_alma varchar(3) not null default(''),
	c_line varchar(5) not null default(''),
	c_obse varchar(3) not null default(''),
	l_obse varchar(50) not null default(''),
	c_usua varchar(15) not null default(suser_sname()),
	f_digi datetime not null default(GETDATE())
)
go

---------------
-- 10/08/2018 -
---------------

-- Sisprop
---Campo para habilitar o deshabilitar ingreso de numero de personas
ALTER TABLE Sisprop
ADD q_npers numeric(1,0) not null DEFAULT 1;
GO

-- LineProd
--- Agregar numero de orden al listar lineas
ALTER TABLE LineProd
ADD n_orde numeric(5,0) not null DEFAULT 0;
go

---------------
-- 13/08/2018 -
---------------

---Campo para preguntar impresion de pre-cuenta
ALTER TABLE Sisprop
ADD q_pregimpcue numeric(1,0) not null DEFAULT 1;
GO

-- Aumentar observaciones de Peditem
ALTER TABLE PedItem ALTER COLUMN l_obse VARCHAR(200);

---------------
-- 13/08/2018 -
---------------

--Sisprop
---Campo para preguntar al grabar
ALTER TABLE Sisprop
ADD q_pregenvped numeric(1,0) not null DEFAULT 1;
GO


---------------
-- 13/08/2018 -
---------------
-- Tabla mesas
--- Para union de mesas
ALTER TABLE Mesas
ADD c_mesa1 varchar(2) not null default('');
go
ALTER TABLE Pedidos
ADD q_unim numeric(1,0) not null DEFAULT 0;
go

--- Para bloquear edicion de mesas
ALTER TABLE Mesas
ADD q_edit numeric(1,0) not null DEFAULT 0;
go
ALTER TABLE Mesas
ADD c_vendedit varchar(2) not null default('');
go

---------------
--13/08/2018  -
---------------
-- Tabla Usuarios
---Para permitir reimpresion de items
ALTER TABLE Usuarios
ADD q_reimpitems numeric(1,0) not null DEFAULT 1;
go


---------------
-- 01/10/2018 -
---------------
ALTER TABLE Mesas
ADD l_dat0 varchar(100) not null DEFAULT '';
go

---------------
-- 20/03/2019 -
---------------
-- Tabla Usuarios
---Para asignar mesa de pedido rapido a cajeros
ALTER TABLE Usuarios
ADD c_mesac varchar(2) not null default('')
go

---------------
--13/08/2018  -
---------------
-- Tabla Usuarios
---Para permitir anular pedido
ALTER TABLE Usuarios
ADD q_anulped numeric(1,0) not null DEFAULT 0;
go

---------------
--12/06/2019  -
---------------
-- Tabla Pedidos
---Al anular que grabe la fecha de anulación
ALTER TABLE Pedidos
	ADD f_anul datetime;
go

ALTER TABLE Usuarios
ADD q_camvend numeric(1,0) not null DEFAULT 0;
go

ALTER TABLE Pedidos ADD q_aten numeric(1,0) not null DEFAULT 0;
go
ALTER TABLE Pedidos ADD c_usuat varchar(30) not null DEFAULT '';
go
ALTER TABLE Pedidos ADD f_aten datetime;
go
ALTER TABLE Usuarios ADD q_vera numeric(1,0) not null DEFAULT 0;
go
