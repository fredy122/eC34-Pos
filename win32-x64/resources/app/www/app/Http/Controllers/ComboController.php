<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ComboController extends Controller
{
    public static function listCombos(){
        /*
        select a.c_comb,RTRIM(a.l_comb) as l_comb,sum(b.s_vent) as s_impo
        from Combos as a, CombosDet as b
        where a.c_empr='001' and a.c_sucu='001' and (a.c_alma='001' or a.c_alma='')  and b.c_empr='001' and b.c_sucu='001' and (b.c_alma='001' or b.c_alma='') and a.q_acti=1
        and a.c_comb=b.c_comb
        group by a.c_comb,a.l_comb
        order by a.c_comb asc
        */
    	$combos = DB::table(DB::raw('Combos as a, CombosDet as b'))
    			->select(DB::raw('a.c_comb,RTRIM(a.l_comb) as l_comb,sum(b.s_vent* b.s_cant) as s_impo'))
    			->where([
    				['a.c_empr','=',session('c_empr')],
    				['a.c_sucu','=',session('c_sucu')],
                    // ['a.c_alma','=',session('c_alma')],
                    // ['a.c_alma','=',''],
                    ['b.c_empr','=',session('c_empr')],
                    ['b.c_sucu','=',session('c_sucu')],
                    // ['b.c_alma','=',session('c_alma')],
                    // ['b.c_alma','=',''],
                    ['a.q_acti','=',1]
    			])
                ->where(function($query) {
                    $query->where('a.c_alma', session('c_alma'))
                        ->orWhere('a.c_alma', '');})
                ->where(function($query) {
                    $query->where('b.c_alma', session('c_alma'))
                        ->orWhere('b.c_alma', '');})
                ->whereColumn('a.c_comb','=','b.c_comb')
                ->groupBy('a.c_comb', 'a.l_comb')
                ->orderBy('a.c_comb','asc')
                ->get();

    	return $combos;
    }
}
