<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ProdStockController extends Controller
{
    public function devProdStock(Request $request){
    	$this->validate($request, [
            'c_prod' => 'required',
        ]);

    	$stock = collect(DB::select("select top 1 n_stok
							from ProdStock
							where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and c_prod=:c_prod",
							["c_empr"=>session('c_empr'),"c_sucu"=>session('c_sucu'),"c_alma"=>session('c_alma'),"c_prod"=>$request->c_prod]))->first();

    	return ["stock"=>$stock];
    }
}
