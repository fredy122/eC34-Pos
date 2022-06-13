<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UbicaController extends Controller
{
    public static function listUbica($c_empr)
    {
    	/*
    	select RTRIM(c_ubic) as c_ubic,RTRIM(l_ubic) as l_ubic
        from Ubica
        where c_empr='001' and c_alma='001'
        order by c_ubic
    	*/
    	$listado = \DB::table('Ubica')
    				->select(\DB::raw('RTRIM(c_ubic) as c_ubic,RTRIM(l_ubic) as l_ubic'))
    				->where([
                        ['c_empr','=',$c_empr],
                        ['c_alma','=',session('c_alma')],
                        ['c_ubic','!=','99']
                    ])
                    ->orderBy('c_ubic')
    				->get();

    	return $listado;
    }
}
