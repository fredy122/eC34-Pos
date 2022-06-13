<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubSectController extends Controller
{
    public static function listSubSect(){
    	/*
		select b.c_subs,RTRIM(b.l_subs) as l_subs,RTRIM(a.l_sect) as l_sect
		from Sector as a, SubSect as b
		where a.c_empr = '001' and b.c_empr = '001' and b.c_sect=a.c_sect
		order by b.c_subs asc
    	*/
    	$response = \DB::table(\DB::raw('Sector as a, SubSect as b'))
    				->select(\DB::raw('b.c_subs,RTRIM(b.l_subs) as l_subs,RTRIM(a.l_sect) as l_sect'))
    				->where([
    					['a.c_empr','=',session('c_empr')],
    					['b.c_empr','=',session('c_empr')]
    				])
    				->whereColumn('b.c_sect','=','a.c_sect')
    				->orderBy('b.c_subs', 'asc')
    				->get();

    	return $response;
    }
}
