-- Alterar campo para not null
ALTER TABLE
  Pedidos
ALTER COLUMN
  c_empr char(3) NOT NULL;

ALTER TABLE
  Pedidos
ALTER COLUMN
  n_seri char(4) NOT NULL;

ALTER TABLE
  Pedidos
ALTER COLUMN
  n_comp char(10) NOT NULL;

-- Asignamos llaves primarias
ALTER TABLE Pedidos
ADD CONSTRAINT PK_Pedidos PRIMARY KEY (c_empr,n_seri,n_comp)

--borrar constraint de llave primaria
/*ALTER TABLE Pedidos
DROP CONSTRAINT PK_Pedidos;*/