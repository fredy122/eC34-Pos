<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductoController extends Controller
{
	public function listProdLineProdSubLineProd(){
		return [
			'LineProd' => LineProdController::listLineProd(session('c_empr')),
    		'SubLineProd' => SubLineProdController::listSubLineProd(session('c_empr')),
    		'ProductosXMediprod' => ProductoController::listProductosXMediprod(session('c_empr'))
    	];
	}

	public static function listProductosXMediprod($c_empr)
	{
		/*
		select RTRIM(a.c_line) as c_line,RTRIM(a.c_subl) as c_subl,RTRIM(a.c_prod) as c_prod,RTRIM(a.l_prod) as l_prod,a.k_mone,a.k_igv,a.q_coci,a.l_impr1,a.l_impr2,RTRIM(b.k_medi) as k_medi,RTRIM(c.l_abre) as l_abre,b.s_pre1,b.s_pre2,b.s_pre3,b.s_pre4,b.s_pre5
			from Producto a, Mediprod b, Tipounid c
			where a.c_empr='001' and a.q_most=1 and
				b.c_empr=a.c_empr and 
				c.c_empr=a.c_empr and 
				a.c_prod=b.c_prod and 
				b.k_medi=c.k_medi
			order by CASE 
					WHEN LEN(RTRIM(l_cod1)) = 0 THEN 9999999999
					ELSE l_cod1
					END, c_line,l_prod
		*/

		// $listado = [];
		/*if (session('org')->k_empr != '2') {
			$listado = \DB::table(\DB::raw('Producto a, Mediprod b, Tipounid c'))
						->select(\DB::raw('RTRIM(a.c_line) as c_line,RTRIM(a.c_subl) as c_subl,RTRIM(a.c_prod) as c_prod,RTRIM(a.l_prod) as l_prod,a.k_mone,a.k_igv,a.q_coci,RTRIM(b.k_medi) as k_medi,RTRIM(c.l_abre) as l_abre,b.s_pre1,b.s_pre2,b.s_pre3,b.s_pre4,b.s_pre5'))
						->where([
							['a.c_empr','=',$c_empr],
							['a.q_most','=','1'],
							['b.c_empr','=',$c_empr],
							['c.c_empr','=',$c_empr]
						])
						->whereColumn('a.c_prod','=','b.c_prod')
						->whereColumn('b.k_medi','=','c.k_medi')
						->orderBy('a.l_prod', 'asc')
						->get();
		}else{*/
			/*$listado = \DB::select("
				select RTRIM(a.c_line) as c_line,RTRIM(a.c_subl) as c_subl,RTRIM(a.c_prod) as c_prod,RTRIM(a.l_prod) as l_prod,a.k_mone,a.k_igv,a.q_coci,a.l_impr1,a.l_impr2,RTRIM(b.k_medi) as k_medi,RTRIM(c.l_abre) as l_abre,b.s_pre1,b.s_pre2,b.s_pre3,b.s_pre4,b.s_pre5
				from Producto a, Mediprod b, Tipounid c
				where a.c_empr=:c_empr and a.q_most=1 and
					b.c_empr=a.c_empr and 
					c.c_empr=a.c_empr and 
					a.c_prod=b.c_prod and 
					b.k_medi=c.k_medi
				order by CASE 
						WHEN LEN(RTRIM(l_cod1)) = 0 THEN 9999999999
						ELSE l_cod1
						END, c_line,l_prod
						",["c_empr"=>$c_empr]);*/

			// Listado de productos con unidades de medida desde procedimientos almacenados
			$listado = \DB::select("exec ListProdMedi :c_empr",["c_empr"=>$c_empr]);
		// }

		return $listado;
	}

	public function devProd(Request $request)
	{
		$c_empr = session('c_empr');

		$producto = collect(\DB::select("
			select top 1 q_acti
			from Producto
			where c_empr=:c_empr and c_prod=:c_prod
			",["c_empr"=>$c_empr, "c_prod"=>$request->c_prod]))->first();

		return [ 'prod' => $producto];

	}
}
