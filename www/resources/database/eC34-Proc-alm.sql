-- Listado de Productos con Mediprod para POS
if(OBJECT_ID('ListProdMedi') is not null)
	DROP PROCEDURE ListProdMedi
go
CREATE PROCEDURE ListProdMedi
	@c_empr varchar(3)  
AS   
	select RTRIM(a.c_line) as c_line,RTRIM(a.c_subl) as c_subl,RTRIM(a.c_prod) as c_prod,RTRIM(a.l_prod) as l_prod,a.k_mone,a.k_igv,a.q_coci,a.l_impr1,a.l_impr2,a.q_icbper,RTRIM(b.k_medi) as k_medi,RTRIM(c.l_abre) as l_abre,b.s_pre1,b.s_pre2,b.s_pre3,b.s_pre4,b.s_pre5
	from Producto a, Mediprod b, Tipounid c
	where a.c_empr=@c_empr and a.q_most=1 and
		b.c_empr=@c_empr and 
		c.c_empr=@c_empr and 
		a.c_prod=b.c_prod and 
		b.k_medi=c.k_medi
	order by CASE WHEN LEN(RTRIM(l_cod1)) = 0 THEN 9999999999 ELSE l_cod1 END,c_line,l_prod
GO

-- Listado de total de predidos creados y editados de vendedores para POS
if(OBJECT_ID('_posListTotPedsVends') is not null)
	DROP PROCEDURE _posListTotPedsVends
go
CREATE PROCEDURE _posListTotPedsVends
	@c_empr varchar(3),
	@c_alma varchar(3),
	@f_comp varchar(10)
AS  
	select c_vend, COUNT(a.c_vend) as n_totac
	into #tmpPedidos1
	from Pedidos a
	where a.c_empr=@c_empr and a.c_alma=@c_alma and SUBSTRING(a.n_comp, 1, 6)=@f_comp and a.d_anul=0 and a.q_pago=0 
	group by c_vend

	select c_vendd, count(n_comp) as n_totae
	into #tmpPedidos2
	from 
	(select b.n_comp, b.c_vendd
	from Pedidos a, PedItem as b
	where a.c_empr=@c_empr and a.c_alma=@c_alma and SUBSTRING(a.n_comp, 1, 6)=@f_comp and a.d_anul=0 and a.q_pago=0 and
		b.c_empr=a.c_empr and b.n_seri=a.n_seri and b.n_comp=a.n_comp and a.c_vend != b.c_vendd
	group by b.n_comp, b.c_vendd) as Pedidos
	group by c_vendd


	select a.c_vend, a.l_vend, b.n_totac, c.n_totae
	from Vendedor a left join #tmpPedidos1 b on a.c_vend=b.c_vend left join #tmpPedidos2 c on a.c_vend=c.c_vendd
	where a.c_empr=@c_empr
	order by ISNULL(b.n_totac, 0)+ISNULL(c.n_totae, 0) desc, l_vend
GO