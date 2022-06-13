<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineProdController extends Controller
{
    public static function listLineProd($c_empr)
    {
    	/*
    	select RTRIM(c_line) as c_line,RTRIM(l_line) as l_line,RTRIM(l_impr) as l_impr
        from LineProd
        where c_empr='001'
        order by c_line asc
    	*/
    	/*$listado = \DB::table('LineProd')
			    	->select(\DB::raw('RTRIM(c_line) as c_line,RTRIM(l_line) as l_line,RTRIM(l_impr) as l_impr'))
			    	->where('c_empr','=',$c_empr)
                    ->orderBy('c_line', 'asc')
			    	->get();*/

        $listado = \DB::select("
            select RTRIM(c_line) as c_line,RTRIM(l_line) as l_line,RTRIM(l_impr) as l_impr
            from LineProd
            where c_empr=:c_empr
            order by  
            CASE 
                WHEN n_orde = 0 THEN 99999
                ELSE n_orde
                END, c_line
            ",["c_empr"=>$c_empr]);

		return $listado;
    }
}
