<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubLineProdController extends Controller
{
    public static function listSubLineProd($c_empr)
    {
    	/*
    	select RTRIM(c_subl) as c_subl,RTRIM(l_subl) as l_subl,RTRIM(c_line) as c_line
        from SubLineProd
        where c_empr='001'
        order by c_subl asc
    	*/
    	$listado = \DB::table('SubLineProd')
			    	->select(\DB::raw('RTRIM(c_subl) as c_subl,RTRIM(l_subl) as l_subl,RTRIM(c_line) as c_line'))
			    	->where('c_empr','=',$c_empr)
                    ->orderBy('c_subl', 'asc')
			    	->get();

		return $listado;
    }
}
