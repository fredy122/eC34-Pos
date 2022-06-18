<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class MesasController extends Controller
{
    public static function listMesas($c_empr)
    {
    	/*
    	select RTRIM(c_ubic) as c_ubic,RTRIM(c_mesa) as c_mesa,RTRIM(l_mesa) as l_mesa, n_ejex, n_ejey
        from Mesas
        where c_empr='001'
        order by c_ubic,c_mesa
    	*/
    	$listado = \DB::table('Mesas')
    				->select(\DB::raw('RTRIM(c_ubic) as c_ubic,RTRIM(c_mesa) as c_mesa,RTRIM(l_mesa) as l_mesa, n_ejex, n_ejey,q_ocup'))
    				->where([
                        ['c_empr','=',$c_empr],
                        ['c_alma','=',session('c_alma')]
                    ])
                    ->orderBy('c_ubic', 'asc')
                    ->orderBy('c_mesa', 'asc')
    				->get();

    	return $listado;
    }

    public static function grabaHubicMesas(Request $request){
        foreach($request->mesas as $key => $value){
            \DB::table('Mesas')
                ->where([
                    'c_empr' => session('c_empr'),
                    'c_ubic' => $value['c_ubic'],
                    'c_mesa' => $value['c_mesa'],
                    'c_alma' => session('c_alma')
                ])
                ->update([
                    'n_ejex' => $value['n_ejex'],
                    'n_ejey' => $value['n_ejey']
                ]);
        }

        return ['success'=>'Hubicacion de Mesas actualizado correctamente'];
    }

    public function listMesasXUbic($c_ubic){
        /*select RTRIM(c_ubic) as c_ubic,RTRIM(c_mesa) as c_mesa,RTRIM(l_mesa) as l_mesa, n_ejex, n_ejey,q_ocup
        from Mesas
        where c_empr='001' and c_ubic='01' and c_alma='001'
        order by c_ubic,c_mesa*/
        $c_empr = session('c_empr');

        $listado = \DB::table('Mesas')
                ->select(\DB::raw('RTRIM(c_ubic) as c_ubic,RTRIM(c_mesa) as c_mesa,RTRIM(l_mesa) as l_mesa, n_ejex, n_ejey,q_ocup,l_dat0'))
                ->where([
                    ['c_empr','=',$c_empr],
                    ['c_alma','=',session('c_alma')]
                    /*,
                    ['c_ubic','=',$c_ubic]*/
                ])
                ->orderBy('c_ubic', 'asc')
                ->orderBy('c_mesa', 'asc')
                ->get();

        return $listado;
    }

    public function libEdicMesa(Request $request)
    {
        $this->validate($request, [
            'c_ubic' => 'required',
            'c_mesa' => 'required',
            // 'c_vend' => 'required'
        ]);
        
        $c_empr = session('c_empr');
        $c_alma = session('c_alma');

        // Liberamos edicion de mesa
        $this->libEdicMesa1($request->c_ubic, $request->c_mesa);

        return ['success' => 'Modificación se grabo'];
    }

    public static function libEdicMesa1($c_ubic, $c_mesa)
    {        
        $c_empr = session('c_empr');
        $c_alma = session('c_alma');

        // Liberamos edicion de mesa
        \DB::update("
            update Mesas set q_edit=0, c_vendedit='' 
            where c_empr=:c_empr and c_alma=:c_alma and c_ubic=:c_ubic and c_mesa=:c_mesa and q_edit=1
            ",['c_empr'=>$c_empr,'c_alma'=>$c_alma,'c_ubic'=>$c_ubic,'c_mesa'=>$c_mesa]);
    }

    public static function postSetOcupEdicMesa(Request $request){
        $c_empr = session('c_empr');
        $c_alma = session('c_alma');
        $c_ubic = $request->c_ubic;
        $c_mesa = $request->c_mesa;

        // Verificamos si mesa esta siendo editada
        $mesa = DB::table('Mesas')
                    ->select('q_edit','c_vendedit')
                    ->where([
                        ['c_empr','=',$c_empr],
                        ['c_ubic','=',$c_ubic],
                        ['c_mesa','=',$c_mesa],
                        ['c_alma','=',$c_alma]
                    ])
                    ->first();

        if ($mesa->q_edit == 1) {
            // Mensaje si mesa esta siendo editada
            $l_vend = $mesa->c_vendedit == 'CA' ? 'CAJA' : $mesa->c_vendedit;
            return response()->json([
                    'errors' => [
                            'c_vendedit' => ["Mesa está siendo editada por otro usuario $l_vend, no puede ingresar !!!"]
                        ]
                    ],422);
        }else{
            // Cambiamos a true edicion de mesa
            DB::update("
                update Mesas set q_edit=1 
                where c_empr=:c_empr and c_alma=:c_alma and c_ubic=:c_ubic and c_mesa=:c_mesa
                ",['c_empr'=>session('c_empr'),'c_alma'=>session('c_alma'),'c_ubic'=>$c_ubic,'c_mesa'=>$c_mesa]);
        }

    }

    public function devMesa(Request $request){
        $c_empr = session('c_empr');
        $c_alma = session('c_alma');

        $this->validate($request, [
            'c_ubic' => 'required',
            'c_mesa' => 'required',
            'c_vend' => 'required',
            'l_vend' => 'required',
        ]);

        session('usuario')->c_vend = $request->c_vend;
        session('usuario')->l_vend = $request->l_vend;

        $mesa = collect(\DB::select("
            select top 1 l_mesa, q_ocup, q_edit, c_vendedit
            from Mesas
            where c_empr=:c_empr and c_alma=:c_alma and c_ubic=:c_ubic and c_mesa=:c_mesa
            ",['c_empr'=>$c_empr,'c_alma'=>$c_alma,'c_ubic'=>$request->c_ubic,'c_mesa'=>$request->c_mesa]))->first();

        if ($mesa->q_edit == 1) {
            // Mensaje si mesa esta siendo editada
            $l_vend = $mesa->c_vendedit == 'CA' ? 'CAJA' : $mesa->c_vendedit;
            return response()->json([
                    'errors' => [
                            'c_vendedit' => ["Mesa está siendo editada por otro usuario $l_vend, no puede ingresar !!!"]
                        ]
                    ],422);
        }

        return ['mesa'=>$mesa];
    }
}
