<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ComboDetController extends Controller
{
    public static function listCombosDet(){
        /*
        select a.c_comb,a.n_item,RTRIM(a.c_prod) as c_prod,a.k_medi,a.s_cant,a.s_vent,b.k_igv
        from CombosDet as a, Producto as b
        where a.c_empr='001' and a.c_sucu='001' and (a.c_alma='001' or a.c_alma='') and b.c_empr='001' and a.c_prod=b.c_prod
        */
    	$combos = DB::table(DB::raw('CombosDet as a, Producto as b'))
    			->select(DB::raw('a.c_comb,a.n_item,RTRIM(a.c_prod) as c_prod,a.k_medi,a.s_cant,a.s_vent,b.k_igv'))
    			->where([
    				['a.c_empr','=',session('c_empr')],
    				['a.c_sucu','=',session('c_sucu')],
                    // ['a.c_alma','=',session('c_alma')],
                    ['b.c_empr','=',session('c_empr')]
    			])
                ->where(function($query) {
                    $query->where('a.c_alma', session('c_alma'))
                        ->orWhere('a.c_alma', '');})
                ->whereColumn('a.c_prod','=','b.c_prod')
                ->get();

    	return $combos;
    }
}
