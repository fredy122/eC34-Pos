<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class TipcomprobController extends Controller
{
    public static function devTipcomprobKTPreCuenta(){
    	/*
    	select top 1 l_prg1
		from Tipcomprob
		where c_empr='001' and c_comp='KT'
    	*/

    	$kt = DB::table('Tipcomprob')
    		->select('l_prg1')
    		->where([
    			['c_empr','=',session('c_empr')],
    			['c_comp','=','KT']
    		])
    		->first();

    	return $kt;

        //$kt = \DB::table('sisprop')->select(\DB::raw('despre5 as l_prg1'))->first();

        //return $kt;
    }
}
