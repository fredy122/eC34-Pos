create database ec34_pedidos DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;

use ec34_pedidos;

create table clientes(
	n_ruc varchar(11) not null,
	l_clien varchar(100) not null,
	l_serv varchar(255) not null
)engine=myisam;

/*
	1 = con opciones para seleccionar Ubicacion y mesas(para restaurante)
	2 = sin opciones para seleccionar Ubicacion y mesas(para otro tipo de negocios)
*/
ALTER TABLE clientes ADD k_tipnegoc int(1) unsigned not null;

/*
	1 = Puede cambiar de vendedor
	0 = No puede cambiar vendedor
*/
ALTER TABLE clientes ADD q_cambvend int(1) unsigned not null;

-- Base de datos multiempresa
ALTER TABLE clientes ADD q_memp decimal(1,0) not null DEFAULT 0;

ALTER TABLE clientes ADD c_empr varchar(3) not null DEFAULT "";


ALTER TABLE clientes ADD CONSTRAINT PK_clientes PRIMARY KEY (n_ruc,c_empr);
