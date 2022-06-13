<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class CambMozoPrinc extends Controller
{
    public function index(Request $request){
    	$Vendedor = VendedorController::listVend2();

    	return ["Vendedor"=>$Vendedor["vendedor"]];
    }

    public function grabar(Request $request){
    	$this->validate($request, [
    		'n_comp' => 'required',
            'c_vend' => 'required',
        ]);

        $c_empr = session('c_empr');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');

        DB::update("
        	update Pedidos set c_vend=:c_vend where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and c_comp='60' and n_comp=:n_comp
        ",[
        	"c_vend"=>$request->c_vend,
			"c_empr"=>$c_empr,
			"c_sucu"=>$c_sucu,
			"c_alma"=>$c_alma,
			"n_comp"=>$request->n_comp,
        ]);

        return ['success'=>'Vendedor se cambio correctamente'];
    }
}
