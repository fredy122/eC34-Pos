<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VendedorController extends Controller
{
    public function cambVend(Request $request)
    {
    	$this->validate($request, [
            'c_vend' => 'required'
        ]);

        if (session('org')->k_empr == '2') {
            $this->validate($request, [
                'c_ubic' => 'required',
                'c_mesa' => 'required'
            ]);
        }

        $c_vend = $request->c_vend;

    	/*
    	select top 1 c_vend,RTRIM(l_vend) as l_vend
        from Vendedor
        where c_vend='01' and c_alma='001'
    	*/
    	
        /*
        $vendedor = \DB::table('Vendedor')
    			->select(\DB::raw('c_vend,RTRIM(l_vend) as l_vend'))
    			->where([
                    ['c_vend','=',$c_vend],
                    ['c_alma','=',session('c_alma')]
                ])
    			->first();
                */

        /*
        declare @c_alma varchar(3);
            declare @c_vend varchar(50);
            set @c_alma = :c_alma;
            set @c_vend = :c_vend;

            IF LEN(@c_vend) = 2
                select top 1 c_vend,RTRIM(l_vend) as l_vend
                from Vendedor
                where c_alma=@c_alma and c_vend=@c_vend
            ELSE
                select top 1 c_vend,RTRIM(l_vend) as l_vend
                from Vendedor
                where c_alma=@c_alma and l_clav=@c_vend
        */
        
        
        $vendedor = collect(\DB::select("
            select top 1 c_vend,RTRIM(l_vend) as l_vend
            from Vendedor
            where c_empr=:c_empr and c_alma=:c_alma and l_clav=:c_vend
            ",['c_empr'=>session('c_empr'), 'c_alma'=>session('c_alma'), 'c_vend'=>$c_vend]))->first();

    	if(empty($vendedor)){
    		return response()->json([
                    'errors' => [
                            'c_vend' => ["No existe vendedor para almacen actual !"]
                        ]
                    ],422);
    	}

        if (session('org')->k_empr == '2') {

            // Verificamos si pedido esta siendo editado por otro usuario
            /*$mesa = \DB::table('Mesas')
                        ->select('q_edit','c_vendedit')
                        ->where([
                            ['c_empr','=',session('c_empr')],
                            ['c_ubic','=',$request->c_ubic],
                            ['c_mesa','=',$request->c_mesa],
                            ['c_alma','=',session('c_alma')]
                        ])
                        ->first();

            if ($mesa->q_edit == 1 && $mesa->c_vendedit != $vendedor->c_vend) {
                return response()->json([
                        'errors' => [
                                'c_vendedit' => ['Mesa está siendo editada por otro usuario !!!']
                            ]
                        ],422);
            }

            // Bloqueamos edicion de pedidos para otros usuario
            \DB::update("
                update Mesas set q_edit=1, c_vendedit=:c_vendedit 
                where c_empr=:c_empr and c_alma=:c_alma and c_ubic=:c_ubic and c_mesa=:c_mesa and q_edit=0
                ",['c_vendedit'=>$vendedor->c_vend,'c_empr'=>session('c_empr'),'c_alma'=>session('c_alma'),'c_ubic'=>$request->c_ubic,'c_mesa'=>$request->c_mesa]);*/

            \DB::update("
                update Mesas set c_vendedit=:c_vendedit 
                where c_empr=:c_empr and c_alma=:c_alma and c_ubic=:c_ubic and c_mesa=:c_mesa
                ",['c_vendedit'=>$vendedor->c_vend,'c_empr'=>session('c_empr'),'c_alma'=>session('c_alma'),'c_ubic'=>$request->c_ubic,'c_mesa'=>$request->c_mesa]);
        }

        session('usuario')->c_vend = $vendedor->c_vend;

    	return ['vendedor'=>$vendedor,'pedidos'=>[]/*PedidosController::listPedidos()*/];
    }

    public static function devVendxc_vend($c_vend){
        /*$vendedor = \DB::table('Vendedor')
                ->select(\DB::raw('c_vend,RTRIM(l_vend) as l_vend'))
                ->where('c_vend','=',$c_vend)
                ->first();*/

        $vendedor = collect(\DB::select("
            select top 1 c_vend,RTRIM(l_vend) as l_vend
            from Vendedor
            where c_alma=:c_alma and c_vend=:c_vend
            ",['c_alma'=>session('c_alma'), 'c_vend'=>$c_vend]))->first();

        session('usuario')->c_vend = $c_vend;

        return $vendedor;
    }

    public static function devXclave($l_clav){
        $vendedor = collect(\DB::select("
            select top 1 c_vend,RTRIM(l_vend) as l_vend
            from Vendedor
            where c_alma=:c_alma and l_clav=:l_clav
            ",['c_alma'=>session('c_alma'), 'l_clav'=>$l_clav]))->first();

        return $vendedor;
    }

    public static function listVend($c_empr, $c_alma){
        $vendedor = \DB::select("
            select c_vend, l_vend
            from Vendedor
            where c_empr=:c_empr and c_alma=:c_alma
            order by c_vend
            ",['c_empr'=>$c_empr,'c_alma'=>$c_alma]);

        return ['vendedor'=>$vendedor];
    }

    public static function listVend2(){
        $vendedor = \DB::select("
            select c_vend,l_vend
            from Vendedor
            where c_empr=:c_empr and c_alma=:c_alma
            order by c_vend
            ",['c_empr'=>session('c_empr'),'c_alma'=>session('c_alma')]);

        return ['vendedor'=>$vendedor];
    }
}
