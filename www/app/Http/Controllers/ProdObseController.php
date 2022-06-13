<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ProdObseController extends Controller
{
    public static function listProdObse($c_empr,$c_alma)
    {
    	$ProdObse = \DB::select("
				select RTRIM(c_line) as c_line,RTRIM(l_obse) as l_obse
				from ProdObse
				where c_empr=:c_empr and c_alma in (:c_alma,'')
				order by c_obse
	    	", ['c_empr'=>$c_empr, 'c_alma'=>$c_alma]);

    	return $ProdObse;
    }
}
