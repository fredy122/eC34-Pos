<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;

class PedidosController extends Controller
{
    public function getPedidos()
    {
    	return view('stock.pedidos');
    }

    public function devDatosIndex(Request $request)
    {
       	return [
    		'LineProd' => LineProdController::listLineProd(session('c_empr')),
    		'SubLineProd' => SubLineProdController::listSubLineProd(session('c_empr')),
    		'ProductosXMediprod' => ProductoController::listProductosXMediprod(session('c_empr')),
    		'Ubica' => UbicaController::listUbica(session('c_empr')),
    		'Mesas' => MesasController::listMesas(session('c_empr')),
            'Pedidos' => [],//$this->listPedidos(),
            'SubSect' => SubSectController::listSubSect(),
            'Combos' => ComboController::listCombos(),
            'CombosDet' => ComboDetController::listCombosDet(),
            'impresorakt' => TipcomprobController::devTipcomprobKTPreCuenta(),
            'ProdObse' => ProdObseController::listProdObse(session('c_empr'),session('c_alma'))
    	];
    }

    public static function listPedidos()
    {
        if (session('org')->k_empr == '2') {
            return [];
        }
        else{
            // Consultamos fecha de apertura de caja
            $f_proc = (DB::table('Apertcaja')
                        ->select('f_proc')
                        ->where([
                            ['c_empr','=',session('c_empr')],
                            ['c_sucu','=',session('c_sucu')],
                            ['c_alma','=',session('c_alma')],
                            ['q_esta','=','0']
                        ])
                        ->first())->f_proc;

            // Traemos listado de pedidos
            /*
            select d_anul,n_comp,q_pago,c_ubic,c_mesa
            from Pedidos
            where CONVERT(VARCHAR(10),f_comp,110)=CONVERT(VARCHAR(10),GETDATE(),110) and
                c_empr='001' and c_sucu='001' and c_alma='001' and c_comp='60' and c_vend='01'
            order by n_comp desc
            */
            $listado = \DB::table('Pedidos')
                        ->select('d_anul','n_comp','q_pago','c_ubic','c_mesa','q_aten')
                        ->where([
                            //[\DB::raw('CONVERT(VARCHAR(10),f_comp,110)'),'=',\DB::raw('CONVERT(VARCHAR(10),GETDATE(),110)')],
                            ['f_comp','=',$f_proc],
                            ['c_empr','=',session('c_empr')],
                            ['c_sucu','=',session('c_sucu')],
                            ['c_alma','=',session('c_alma')],
                            ['c_comp','=','60'],
                            ['c_vend','=',session('usuario')->c_vend]
                        ])
                        ->orderBy('n_comp', 'desc')
                        ->get();

            return $listado;            
        }
    }

    public function buscPedidos(Request $request)
    {
        $this->validate($request, [
            'c_vend' => 'required'
        ]);

        $c_empr = session('c_empr');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');

        // Buscamos codigo de vendedor
        $vend = collect(DB::select("
            select top 1 c_vend
            from Vendedor
            where c_empr=:c_empr and c_alma=:c_alma and l_clav=:l_clav
            ",['c_empr'=>$c_empr,'c_alma'=>$c_alma,'l_clav'=>$request->c_vend]))->first();

        if(empty($vend)){
            return response()->json([
                            'errors' => [
                                    'c_vend' => ['No existe vendedor para almacen actual !']
                                ]
                            ],422);
        }

        // Consultamos fecha de apertura de caja
        $apertcaja = collect(DB::select("
            select top 1 Convert(varchar,f_proc,3) as f_proc
            from Apertcaja
            where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and q_esta=:q_esta
            ", ['c_empr'=>$c_empr,'c_sucu'=>$c_sucu,'c_alma'=>$c_alma,'q_esta'=>0]))->first();

        if(empty($apertcaja)){
            return response()->json([
                        'errors' => [
                                'q_esta' => ['No existe caja aperturada... Revise por favor!']
                            ]
                        ],422);
        }
        $f_comp = str_replace('/', '', $apertcaja->f_proc);

        $pedidos = DB::select("
            select a.n_comp, RTRIM(b.l_ubic) as l_ubic, a.c_ubic, RTRIM(c.l_mesa) as l_mesa, a.c_mesa, (CASE WHEN a.c_vend = :q_vend THEN 1 ELSE 0 END) as q_vend
            from Pedidos a, Ubica b, Mesas c
            where q_pago=:q_pago and (n_comp in 
            (select n_comp
            from PedItem
            where c_empr=:c_empr and c_comp=:c_comp and n_seri=:n_seri and SUBSTRING(n_comp, 1, 6)=:f_comp and d_anul=0 and c_vendd=:c_vendd
            group by n_comp) or a.c_vend=:c_vendd1) and a.c_empr=:c_empr1 and a.c_alma=:c_alma and SUBSTRING(a.n_comp, 1, 6)=:f_comp1 and a.d_anul=0 and a.q_pago=0 and
            a.c_empr=b.c_empr and a.c_alma=b.c_alma and a.c_ubic=b.c_ubic and 
            a.c_empr=c.c_empr and a.c_alma=c.c_alma and a.c_ubic=c.c_ubic and a.c_mesa=c.c_mesa
            order by a.n_comp
            ",[
                'c_empr'=>$c_empr,'c_empr1'=>$c_empr,
                'c_comp'=>'60', 
                'n_seri'=>str_pad($c_alma, 4, '0', STR_PAD_LEFT),
                'f_comp'=>$f_comp,'f_comp1'=>$f_comp,
                'q_pago'=>0,
                'c_vendd'=>$vend->c_vend,'c_vendd1'=>$vend->c_vend,
                'q_vend'=>$vend->c_vend,
                'c_alma'=>$c_alma,
            ]);

        return ['pedidos'=>$pedidos];
    }

    public function buscPedidos2(Request $request)
    {
        $this->validate($request, [
            'c_vend' => 'required'
        ]);

        $c_empr = session('c_empr');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');

        
        // Consultamos fecha de apertura de caja
        $apertcaja = collect(DB::select("
            select top 1 Convert(varchar,f_proc,3) as f_proc
            from Apertcaja
            where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and q_esta=:q_esta
            ", ['c_empr'=>$c_empr,'c_sucu'=>$c_sucu,'c_alma'=>$c_alma,'q_esta'=>0]))->first();

        if(empty($apertcaja)){
            return response()->json([
                        'errors' => [
                                'q_esta' => ['No existe caja aperturada... Revise por favor!']
                            ]
                        ],422);
        }
        $f_comp = str_replace('/', '', $apertcaja->f_proc);

        $pedidos = DB::select("
            select a.n_comp, RTRIM(b.l_ubic) as l_ubic, a.c_ubic, RTRIM(c.l_mesa) as l_mesa, a.c_mesa, (CASE WHEN a.c_vend = :q_vend THEN 1 ELSE 0 END) as q_vend
            from Pedidos a, Ubica b, Mesas c
            where q_pago=:q_pago and (n_comp in 
            (select n_comp
            from PedItem
            where c_empr=:c_empr and c_comp=:c_comp and n_seri=:n_seri and SUBSTRING(n_comp, 1, 6)=:f_comp and d_anul=0 and c_vendd=:c_vendd
            group by n_comp) or a.c_vend=:c_vendd1) and a.c_empr=:c_empr1 and a.c_alma=:c_alma and SUBSTRING(a.n_comp, 1, 6)=:f_comp1 and a.d_anul=0 and a.q_pago=0 and
            a.c_empr=b.c_empr and a.c_alma=b.c_alma and a.c_ubic=b.c_ubic and 
            a.c_empr=c.c_empr and a.c_alma=c.c_alma and a.c_ubic=c.c_ubic and a.c_mesa=c.c_mesa
            order by a.n_comp
            ",[
                'c_empr'=>$c_empr,'c_empr1'=>$c_empr,
                'c_comp'=>'60', 
                'n_seri'=>str_pad($c_alma, 4, '0', STR_PAD_LEFT),
                'f_comp'=>$f_comp,'f_comp1'=>$f_comp,
                'q_pago'=>0,
                'c_vendd'=>$request->c_vend,'c_vendd1'=>$request->c_vend,
                'q_vend'=>$request->c_vend,
                'c_alma'=>$c_alma,
            ]);

        return ['pedidos'=>$pedidos];
    }

    public function buscPedidosxNcomp(Request $request){
        $this->validate($request, [
            'n_comp' => 'required'
        ]);

        $c_empr = session('c_empr');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');

        
        // Consultamos fecha de apertura de caja
        $apertcaja = collect(DB::select("
            select top 1 Convert(varchar,f_proc,3) as f_proc
            from Apertcaja
            where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and q_esta=:q_esta
            ", ['c_empr'=>$c_empr,'c_sucu'=>$c_sucu,'c_alma'=>$c_alma,'q_esta'=>0]))->first();

        if(empty($apertcaja)){
            return response()->json([
                        'errors' => [
                                'q_esta' => ['No existe caja aperturada... Revise por favor!']
                            ]
                        ],422);
        }
        $f_comp = str_replace('/', '', $apertcaja->f_proc);

        // Consulta DB
        $pedidos = DB::select("
            select top 1 a.n_comp, RTRIM(b.l_ubic) as l_ubic, a.c_ubic, RTRIM(c.l_mesa) as l_mesa, a.c_mesa
            from Pedidos a, Ubica b, Mesas c
            where a.c_empr=:c_empr and a.c_alma=:c_alma and a.c_comp=:c_comp and a.n_comp=:n_comp and d_anul=0 and a.q_pago=:q_pago and 
            a.c_empr=b.c_empr and a.c_alma=b.c_alma and a.c_ubic=b.c_ubic and 
            a.c_empr=c.c_empr and a.c_alma=c.c_alma and a.c_ubic=c.c_ubic and a.c_mesa=c.c_mesa
            order by a.n_comp
            ",['c_empr'=>$c_empr,'c_alma'=>$c_alma,'c_comp'=>'60','n_comp'=>$f_comp.str_pad($request->n_comp, 4, "0", STR_PAD_LEFT),'q_pago'=>0]);

        return ['pedidos'=>$pedidos];
    }

    public function listTotPedVends()
    {
        $c_empr = session('c_empr');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');

        // Consultamos fecha de apertura de caja
        $apertcaja = collect(DB::select("
            select top 1 Convert(varchar,f_proc,3) as f_proc
            from Apertcaja
            where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and q_esta=:q_esta
            ", ['c_empr'=>$c_empr,'c_sucu'=>$c_sucu,'c_alma'=>$c_alma,'q_esta'=>0]))->first();

        if(empty($apertcaja)){
            return response()->json([
                        'errors' => [
                                'q_esta' => ['No existe caja aperturada... Revise por favor!']
                            ]
                        ],422);
        }
        $f_comp = str_replace('/', '', $apertcaja->f_proc);

        // Consulta
        /*$resul = DB::select("
            select c_vendd, COUNT(c_vendd) as n_tota
            from (select b.n_comp,b.c_vendd
            from Pedidos a, PedItem b
            where a.c_empr=:c_empr and a.c_alma=:c_alma and SUBSTRING(a.n_comp, 1, 6)=:f_comp and a.q_pago=:q_pago and 
                b.c_empr=a.c_empr and b.n_seri=a.n_seri and b.n_comp=a.n_comp
            group by b.n_comp,b.c_vendd) pedidos
            group by c_vendd
            ",['c_empr'=>$c_empr, 'c_alma'=>$c_alma, 'f_comp'=>$f_comp, 'q_pago'=>0]);

        return ['resul'=>$resul] ;*/

        $resul = \DB::select("
            SET NOCOUNT ON;
            EXEC _posListTotPedsVends :c_empr, :c_alma, :f_comp
        ",['c_empr'=>$c_empr,'c_alma'=>$c_alma,'f_comp'=>$f_comp]);

        return ['resul'=>$resul];
    }

    public function devPedidoXC_ubicC_mesa($c_ubic,$c_mesa,Request $request){
        // Consultamos fecha de apertura de caja
        $apertcaja = collect(DB::select("
            select top 1 f_proc
            from Apertcaja
            where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and q_esta=:q_esta
            ", ['c_empr'=>session('c_empr'),'c_sucu'=>session('c_sucu'),'c_alma'=>session('c_alma'),'q_esta'=>0]))->first();


        if(empty($apertcaja)){
            return response()->json([
                        'errors' => [
                                'q_esta' => ['No existe caja aperturada... Revise por favor!']
                            ]
                        ],422);
        }
        // 

        // Ejecutamos consulta del Pedido
        /*
        select top 1 a.d_anul,RTRIM(c.l_vend) as l_vend,RTRIM(a.c_vend) as c_vend,RTRIM(a.n_comp) as n_comp,a.s_tota,a.c_docu,RTRIM(a.n_docu) as n_docu,RTRIM(b.l_agen) as l_agen,RTRIM(b.l_dire) as l_dire,RTRIM(a.c_compgen) as c_compgen,RTRIM(a.c_ubic) as c_ubic,RTRIM(a.c_mesa) as c_mesa,a.q_pago,k_page,a.l_obse,n_pers
        from Pedidos a, Agentes b, Vendedor c
        where a.c_empr='001' and a.c_año='2018' and a.c_sucu='001' and a.c_alma='001' and a.c_comp='60' and a.c_ubic='01' and a.c_mesa='01' and a.q_pago='0' and d_anul='0'
        and b.c_empr='001' and c_tipa='1' and b.n_docu=a.n_docu and
        c.c_empr=a.c_empr and c.c_alma=a.c_alma and c.c_vend=a.c_vend
        */
        $pedido = \DB::table(\DB::raw('Pedidos a, Agentes b, Vendedor c'))
                    ->select(\DB::raw('a.d_anul,RTRIM(c.l_vend) as l_vend,RTRIM(a.c_vend) as c_vend,RTRIM(a.n_comp) as n_comp,a.s_tota,a.c_docu,RTRIM(a.n_docu) as n_docu,RTRIM(b.l_agen) as l_agen,RTRIM(b.l_dire) as l_dire,RTRIM(a.c_compgen) as c_compgen,RTRIM(a.c_ubic) as c_ubic,RTRIM(a.c_mesa) as c_mesa,a.q_pago,k_page,a.l_obse,n_pers,a.ad_plac'))
                    ->where([
                        //Pedidos
                        ['a.c_empr','=',session('c_empr')],
                        ['a.c_año','=',session('c_anio')],
                        ['a.c_sucu','=',session('c_sucu')],
                        ['a.c_alma','=',session('c_alma')],
                        ['a.c_comp','=','60'],
                        ['a.f_comp','=',$apertcaja->f_proc],
                        ['a.c_ubic','=',$c_ubic],
                        ['a.c_mesa','=',$c_mesa],
                        // ['a.q_pago','=','0'],
                        ['a.d_anul','=','0'],
                        //Agentes
                        ['b.c_empr','=',session('c_empr')],
                        ['c_tipa','=','1']
                    ])
                    ->whereIn('a.q_pago', [0, 2])
                    ->whereColumn([
                        ['b.n_docu','=','a.n_docu'],
                        ['c.c_empr','=','a.c_empr'],
                        ['c.c_alma','=','a.c_alma'],
                        ['c.c_vend','=','a.c_vend']
                    ])
                    ->first();

        // Verificamos si existe pedido
        if (empty($pedido)) {
            // Liberamos mesa si no se encuentra el pedido(el pedido puede no encontrarse cuando se elimina desde el sistema y este no libero la mesa)
            // ---Consulta SQL
            // ---update Mesas set q_ocup=1 where c_empr='001' and c_alma='001' and c_ubic='01' and c_mesa='01'
            DB::table('Mesas')->where(['c_empr'=>session('c_empr'),'c_alma'=>session('c_alma'),'c_ubic'=>$c_ubic,'c_mesa'=>$c_mesa])->update(['q_ocup'=>0]);

            // Retornamos mensaje
            return response()->json([
                            'errors' => [
                                    'n_comp' => ['No se encuentra pedido en esta mesa!!!']
                                ]
                            ],422);
        }

        // Para edicion de mesa
        if ($request->q_prcue != 1) {
            $c_empr = session('c_empr');
            $c_alma = session('c_alma');
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
        // ======================================================

        // Retornamos Pedidos
        return ['pedido' => $pedido,
                'peditem' => PedItemController::devListPedItemsXPedido($pedido->n_comp),
                'vendedor' => VendedorController::devVendxc_vend($pedido->c_vend),
                'vendedit' => $request->q_prcue != 1 ? [] : VendedorController::devXclave($request->l_clav)
                ];
    }

    public function devPedidoXn_comp($n_comp){
        /*select top 1 a.d_anul,RTRIM(a.n_comp) as n_comp,a.s_tota,a.c_docu,RTRIM(a.n_docu) as n_docu,RTRIM(b.l_agen) as l_agen,RTRIM(b.l_dire) as l_dire,RTRIM(a.c_compgen) as c_compgen,RTRIM(a.c_ubic) as c_ubic,RTRIM(a.c_mesa) as c_mesa,a.q_pago,k_page,a.l_obse,n_pers
        from Pedidos a, Agentes b
        where a.c_empr='001' and a.c_año='2018' and a.c_sucu='001' and a.c_alma='001' and a.c_comp='60' and a.c_vend='01' and a.n_comp='2401180001'
        and b.c_empr='001' and c_tipa='1' and b.n_docu=a.n_docu
        */
        $pedido = \DB::table(\DB::raw('Pedidos a, Agentes b'))
                    ->select(\DB::raw('a.d_anul,RTRIM(a.n_comp) as n_comp,a.s_tota,a.c_docu,RTRIM(a.n_docu) as n_docu,RTRIM(b.l_agen) as l_agen,RTRIM(b.l_dire) as l_dire,RTRIM(a.c_compgen) as c_compgen,RTRIM(a.c_ubic) as c_ubic,RTRIM(a.c_mesa) as c_mesa,a.q_pago,k_page,a.l_obse,n_pers,a.ad_plac,a.q_aten'))
                    ->where([
                        //Pedidos
                        ['a.c_empr','=',session('c_empr')],
                        ['a.c_año','=',session('c_anio')],
                        ['a.c_sucu','=',session('c_sucu')],
                        ['a.c_alma','=',session('c_alma')],
                        ['a.c_comp','=','60'],
                        // ['a.c_vend','=',session('usuario')->c_vend],
                        ['a.n_comp','=',$n_comp],
                        //Agentes
                        ['b.c_empr','=',session('c_empr')],
                        ['c_tipa','=','1']
                    ])
                    ->whereColumn('b.n_docu','=','a.n_docu')
                    ->first();

        return $pedido;
    }

    public function devPedidoPedItemsXn_comp($n_comp){
        return [
            'pedido' => $this->devPedidoXn_comp($n_comp),
            'peditem' => PedItemController::devListPedItemsXPedido($n_comp)
        ];
    }

    public function grabarPedido(Request $request){
        $c_empr = session('c_empr');
        $c_año = session('c_anio');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');
        $n_seri = str_pad($c_alma, 4, '0', STR_PAD_LEFT);
        $c_vend = session('usuario')->c_vend;
        //Request
        $s_tota = $request->pedido['s_tota'];
        $s_icbper = $request->pedido['s_icbper'];
        $c_ubic = $request->pedido['c_ubic'];
        $c_mesa = $request->pedido['c_mesa'];
        $n_pers = $request->pedido['n_pers'];
        //datos de cliente
        $tipdctos = $request->pedido['c_docu'];
        $c_compgen = $request->pedido['c_compgen'];
        $n_docu = $request->pedido['n_docu'];

        // Peditem para zapatillas
        if (session('org')->k_empr == '5') {
            $n_item = 0;
            $PedItem = [];
            $s_tota1 = 0;
            foreach ($request->peditem as $key => $value) {
                foreach ($value["tallas"] as $key => $value) {
                    if ($value != null && (double)$value['s_cant'] > 0) {
                        $n_item++;
                        $value['n_item'] = $n_item;
                        array_push($PedItem, $value);

                        $s_tota1 += (double)$value['s_vent'] * (double)$value['s_cant'];
                    }
                }
            }
            $request->peditem = $PedItem;

            if ($s_tota1 <= 0) {
                return response()->json(['errors' => ['s_tota' => ['Importe Total debe ser mayor a CERO...!']]],422);
            }
        }
        // 

        // Generamos numero de comprobante
        //$f_now =(new \Datetime)->format('dmy'); // Anterior fecha que extraímos del servidor

        /*select top 1 Convert(varchar,f_proc,3) as f_proc
        from Apertcaja
        where c_empr='001' and c_sucu='001' and c_alma='001' and q_esta='0'*/
        $f_now = DB::table('Apertcaja')
                    ->select(DB::raw('Convert(varchar,f_proc,3) as f_proc, f_proc as f_comp'))
                    ->where([
                        ['c_empr','=',$c_empr],
                        ['c_sucu','=',$c_sucu],
                        ['c_alma','=',$c_alma],
                        ['q_esta','=','0']
                    ])
                    ->first();

        if (empty($f_now)) {
            return response()->json([
                    'errors' => [
                            'q_esta' => ['No existe caja aperturada... Revise por favor!']
                        ]
                    ],422);
        }else{
            $f_comp = \DateTime::createFromFormat('Y-m-d H:i:s.u',$f_now->f_comp)->format('Ymd');//$f_comp = $f_now->f_comp;
            $f_now = str_replace('/', '', $f_now->f_proc);
        }

        $n_comp = DB::select(DB::raw(
            "select top 1 SUBSTRING(n_comp,7,4)n_comp
            from Pedidos
            where SUBSTRING(n_comp,1,6) = '$f_now' and c_alma='$c_alma'
            order by n_comp desc"
        ));

        $n_comp = sizeof($n_comp) > 0 ? $f_now . str_pad((int)$n_comp[0]->n_comp + 1, 4, '0', STR_PAD_LEFT) : $f_now.'0001';

        // Verificamos si mesa sigue descocupada solo cuando el tipo de empresa es 2
        /*
        select top 1 q_ocup
        from Mesas
        where c_empr='001' and c_ubic='01' and c_mesa='01'
        */
        if (session('org')->k_empr == '2') {
            $q_ocup = (DB::table('Mesas')
                        ->select('q_ocup')
                        ->where([
                            ['c_empr','=',$c_empr],
                            ['c_ubic','=',$c_ubic],
                            ['c_mesa','=',$c_mesa],
                            ['c_alma','=',$c_alma]
                        ])
                        ->first())->q_ocup;

            if ($q_ocup == '1') {
                return response()->json([
                        'errors' => [
                                'q_ocup' => ['Mesa se encuentra ocupada, por favor eliga otra !!!']
                            ]
                        ],422);
            }elseif ($q_ocup == '2') {
                return response()->json([
                        'errors' => [
                                'q_ocup' => ['Mesa se encuentra con cuenta dividida, no se puede grabar... !!!']
                            ]
                        ],422);
            }
        }


        // Verifica si pedido fue registrado anteriormente
        $search_pedido = DB::table('Pedidos')->where([
            'c_empr' => $c_empr,
            'c_año' => $c_año,
            'c_sucu' => $c_sucu,
            'c_alma' => $c_alma,
            'c_comp' => '60',
            // 'c_vend' => $c_vend,
            'n_comp' => $n_comp
        ])->select('n_comp')->first();

        if (!empty($search_pedido)) {
            return \Response::json("Pedido {$n_comp} ya fue registrado anteriormente", 404);
        }

        // Graba pedido
        DB::table('Pedidos')->insert([
            'c_empr' => $c_empr,
            'c_año' => $c_año,
            'c_sucu' => $c_sucu,
            'c_alma' => $c_alma,
            'c_comp' => '60',
            'n_seri' => $n_seri,
            'n_comp' => $n_comp,
            'f_comp' => $f_comp,
            'k_mone' => '0',
            's_tipc' => '1',
            'k_pago' => '1',
            'k_page' => $request->pedido['k_page'],
            's_exons' => '0',
            's_tota' => $s_tota,
            's_icbper' => $s_icbper,
            'c_vend' => $c_vend,
            'c_ubic' => $c_ubic,
            'c_mesa' => trim($c_mesa),
            'c_compgen' => $c_compgen,
            'c_docu' => $tipdctos,
            'n_docu' => $n_docu,
            'q_pago' => 0,
            'q_coti' => 1,
            'l_obse' => mb_strtoupper($request->pedido['l_obse'],'UTF-8'),
            'n_pers' => $n_pers,
            'ad_plac' => mb_strtoupper($request->pedido['ad_plac'],'UTF-8')
        ]);

        // Cambiamos estado de mesa solo cuando el tipo de empresa sea restaurant=2
        /*
        update Mesas set q_ocup='1' where c_empr='001' and c_ubic='01' and c_mesa='02'
        */
        if (session('org')->k_empr == '2') {
            DB::table('Mesas')->where([
                'c_empr' => $c_empr,
                'c_ubic' => $c_ubic,
                'c_mesa' => $c_mesa,
                'c_alma' => $c_alma
            ])->update([
                'q_ocup' => '1',
                'q_edit' => '0',
                'c_vendedit' => '',
                'l_dat0' => mb_strtoupper($request->pedido['ad_plac'],'UTF-8')
            ]);
        }

        //Graba peditem
        //$n_item = 0;
        foreach ($request->peditem as $key => $value) {
            $value = (object)$value;
            //$n_item++;
            DB::table('Peditem')->insert(
                [
                    "c_empr" => $c_empr,
                    "c_sucu" => $c_sucu,
                    'c_comp' => '60',
                    'n_seri' => $n_seri,
                    'n_comp' => $n_comp,
                    "c_prod" => $value->c_prod,
                    'n_item' => str_pad($value->n_item, 4, '0', STR_PAD_LEFT),
                    "k_medi" => $value->k_medi,
                    "s_cant" => $value->s_cant,
                    's_vent' => $value->s_vent,
                    'n_prec' => $value->n_prec,
                    'c_indi' => $value->c_indi == '1' ? 'E' : '',
                    'q_coci' => $value->q_coci,
                    'q_envic' => $value->q_envic,
                    'q_preparado' => $value->q_preparado,
                    'l_obse' => mb_strtoupper(trim($value->l_obse),'UTF-8'),
                    'c_comb' => trim($value->c_comb),
                    'c_vendd' => $c_vend,
                    'c_almp' => $c_alma,
                ]
            );
        }
        
        //Grabar o actualizat numero correlativo de comprobante 60
        $orgserie = DB::table('Orgserie')->where([
            'c_empr' => $c_empr,
            'c_comp' => '60',
            'n_seri' => $n_seri,
        ])->select('n_comp')->first();


        if (!empty($orgserie)) {
            DB::table('Orgserie')->where([
                'c_empr' => $c_empr,
                'c_comp' => '60',
                'n_seri' => $n_seri,
            ])->update([
                'n_comp'=> $n_comp
            ]);
        }
        else
        {
            DB::table('Orgserie')->insert([
                'c_empr' => $c_empr,
                'c_comp' => '60',
                'n_seri' => $n_seri,
                'n_comp' => $n_comp
            ]);
        }

        //Respuesta
        return ['n_comp'=>$n_comp];
    }

    public function actPedido(Request $request){
        $c_empr = session('c_empr');
        $c_año = session('c_anio');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');
        $n_seri = str_pad($c_alma, 4, '0', STR_PAD_LEFT);
        $c_vend = session('usuario')->c_vend;
        //Request
        $n_comp = $request->pedido['n_comp'];
        $s_tota = $request->pedido['s_tota'];
        $s_icbper = $request->pedido['s_icbper'];
        $c_ubic = $request->pedido['c_ubic'];
        $c_mesa = $request->pedido['c_mesa'];
        $n_pers = $request->pedido['n_pers'];
        //datos de cliente
        $tipdctos = $request->pedido['c_docu'];
        $c_compgen = $request->pedido['c_compgen'];
        $n_docu = $request->pedido['n_docu'];

        if($c_compgen=='01' && strlen(trim($n_docu)) != '11' ){
            return response()->json([
                    'errors' => [
                            'n_docu' => ['Nº de RUC es incorrecto, para tipo de comprobante... Revise por favor!']
                        ]
                    ],422);
        }

        // Verificamos si pedido esta siendo editado por otro usuario
        /*$mesa = DB::table('Mesas')
                    ->select('q_edit','c_vendedit')
                    ->where([
                        ['c_empr','=',$c_empr],
                        ['c_ubic','=',$c_ubic],
                        ['c_mesa','=',$c_mesa],
                        ['c_alma','=',$c_alma]
                    ])
                    ->first();

        if ($mesa->q_edit == 1 && $mesa->c_vendedit != $c_vend) {
            return response()->json([
                    'errors' => [
                            'c_vendedit' => ['Mesa está siendo editada por otro usuario, no se puede grabar !!!']
                        ]
                    ],422);
        }*/


        // Validamos si el pedido fue pagado o anulado
        /*
        select top 1 q_pago,d_anul
        from Pedidos
        where c_empr='001' and c_año='2017' and c_sucu='001' and c_alma='001' and c_comp='60' and c_vend='01' and n_comp='2112170001'
        */
        $pedido = DB::table('Pedidos')
        ->select('q_pago','d_anul','q_divi')
        ->where([
            ['c_empr','=',$c_empr],
            ['c_año','=',$c_año],
            ['c_sucu','=',$c_sucu],
            ['c_alma','=',$c_alma],
            ['c_comp','=','60'],
            // ['c_vend','=',$c_vend],
            ['n_comp','=',$n_comp]
        ])
        ->first();

        if (empty($pedido)) {
            return ['info'=>'d_anul'];
        }else if($pedido->q_pago == '1'){
            return ['info'=>'q_pago'];
        }else if ($pedido->d_anul == '1') {
            return ['info'=>'d_anul'];
        }/*elseif ($pedido->q_pago == '2' || $pedido->q_divi == '1') {
            return response()->json([
                    'errors' => [
                            'q_ocup' => ['Pedido tiene cuentas divididas, no se puede grabar !!!']
                        ]
                    ],422);
        }*/

        // Peditem para zapatillas
        if (session('org')->k_empr == '5') {
            $n_item = 0;
            $PedItem = [];
            $s_tota1 = 0;
            foreach ($request->peditem as $key => $value) {
                foreach ($value["tallas"] as $key => $value) {
                    if ($value != null && (double)$value['s_cant'] > 0) {
                        $n_item++;
                        $value['n_item'] = $n_item;
                        array_push($PedItem, $value);

                        $s_tota1 += (double)$value['s_vent'] * (double)$value['s_cant'];
                    }
                }
            }
            $request->peditem = $PedItem;

            if ($s_tota1 <= 0) {
                return response()->json(['errors' => ['s_tota' => ['Importe Total debe ser mayor a CERO...!']]],422);
            }
        }
        // 

        // Recopilamos items que no fueron enviados a cocina
        $arrayNewsPeditems = array();
        $peditemNItems = array();
        foreach ($request->peditem as $key => $value) {
            // return [$value];

            if ($value['q_envic']=='0') {
                // Peditems a registrar
                $c_indi = '';
                if ($value['c_indi'] == '1' || $value['c_indi'] === 'E') {
                    $c_indi = 'E';
                }

                $item_insert = [
                    'c_empr' => $c_empr,
                    'c_sucu' => $c_sucu,
                    'c_comp' => '60',
                    'n_seri' => $n_seri,
                    'n_comp' => $n_comp,
                    'c_prod' => $value['c_prod'],
                    'n_item' => str_pad($value['n_item'], 4, '0', STR_PAD_LEFT),
                    'k_medi' => $value['k_medi'],
                    's_cant' => (double)$value['s_cant'],
                    's_vent' => (double)$value['s_vent'],
                    'n_prec' => $value['n_prec'],
                    'c_indi' => $c_indi,
                    'q_coci' => $value['q_coci'],
                    'q_envic' => $value['q_envic'],
                    'q_preparado' => $value['q_preparado'],
                    'l_obse' => mb_strtoupper(trim($value['l_obse']),'UTF-8'),
                    'c_comb' => trim($value['c_comb']),
                    'c_vendd' => array_key_exists('c_vendd',$value) ? $value['c_vendd'] : $c_vend,
                    'c_almp' => $c_alma,
                ];
                array_push($arrayNewsPeditems, $item_insert);
                array_push($peditemNItems, $item_insert['n_item']);
            }
        };

        // Verificamos si ya se enviaron items
        $totalItemEnviados = DB::table('PedItem')
                            ->where([
                                ['c_empr','=',$c_empr],
                                ['c_sucu','=',$c_sucu],
                                ['c_comp','=','60'],
                                ['n_seri','=',$n_seri],
                                ['n_comp','=',$n_comp],
                                ['q_envic','=','1']
                            ])
                            ->whereIn('n_item', $peditemNItems)
                            ->count();

        if( $totalItemEnviados > 0 ){
            return response()->json([
                    'errors' => [
                            'peditem' => ['Item(s) de pedido ya fueron modificados y/o enviados, por favor vuelva a ingresar a pedido para verificar.']
                        ]
                    ],422);
        }

        // Actualizamos Pedido e Items
        DB::transaction(function () use ($c_empr,$c_año,$c_sucu,$c_alma,$c_vend,$n_seri,$n_comp,$request,$s_tota,$s_icbper,$c_ubic,$c_mesa,$tipdctos,$c_compgen,$n_docu,$n_pers,$arrayNewsPeditems) {
            //Modificando Pedido
            DB::table('Pedidos')->where([
                'c_empr' => $c_empr,
                'c_año' => $c_año,
                'c_sucu' => $c_sucu,
                'c_alma' => $c_alma,
                'c_comp' => '60',
                // 'c_vend' => $c_vend,
                'n_comp' => $n_comp
            ])->update([
                'k_mone' => '0',
                's_tipc' => '1',
                'k_pago' => '1',
                'k_page' => $request->pedido['k_page'],
                's_exons' => '0',
                's_tota' => $s_tota,
                's_icbper' => $s_icbper,
                'c_ubic' => $c_ubic,
                'c_mesa' => trim($c_mesa),
                'c_docu' => $tipdctos,
                'c_compgen' => $c_compgen,
                'n_docu' => $n_docu,
                'l_obse' => mb_strtoupper($request->pedido['l_obse'],'UTF-8'),
                'n_pers' => $n_pers,
                'ad_plac' => mb_strtoupper($request->pedido['ad_plac'],'UTF-8'),
            ]);
            // Eliminamos items que no han sido enviados a cocina
            DB::table('PedItem')
            ->where([
                ['c_empr','=',$c_empr],
                ['c_sucu','=',$c_sucu],
                ['c_comp','=','60'],
                ['n_seri','=',$n_seri],
                ['n_comp','=',$n_comp],
                ['q_envic','=','0']
            ])
            ->delete();
            // Insertamos items que no han sido enviados a cocina
            DB::table('PedItem')
            ->insert($arrayNewsPeditems);

            // Liberamos edicion de mesa
            DB::update("
                update Mesas set q_edit=0, c_vendedit='', l_dat0=:l_dat0
                where c_empr=:c_empr and c_alma=:c_alma and c_ubic=:c_ubic and c_mesa=:c_mesa
                ",['c_empr'=>$c_empr,'c_alma'=>$c_alma,'c_ubic'=>$c_ubic,'c_mesa'=>$c_mesa, 'l_dat0'=>mb_strtoupper($request->pedido['ad_plac'],'UTF-8')]);
        });

        //Respuesta
        return ['success'=>'Pedido actualizado correctamente'];
    }

    public function anulPedido(Request $request){
        $c_empr = session('c_empr');
        $c_año = session('c_anio');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');
        $c_vend = session('usuario')->c_vend;

        // Validamos si el pedido fue pagado o anulado
        /*
        select top 1 q_pago,d_anul
        from Pedidos
        where c_empr='001' and c_año='2017' and c_sucu='001' and c_alma='001' and c_comp='60' and c_vend='01' and n_comp='2112170001'
        */
        $pedido = DB::table('Pedidos')
        ->select('q_pago','d_anul')
        ->where([
            ['c_empr','=',$c_empr],
            ['c_año','=',$c_año],
            ['c_sucu','=',$c_sucu],
            ['c_alma','=',$c_alma],
            ['c_comp','=','60'],
            // ['c_vend','=',$c_vend],
            ['n_comp','=',$request->n_comp]
        ])
        ->first();

        if($pedido->q_pago == '1'){
            return ['info'=>'q_pago'];
        }else if ($pedido->d_anul == '1') {
            return ['info'=>'d_anul'];
        }

        // Anulamos pedido
        DB::table('Pedidos')->where([
            'c_empr' => $c_empr,
            'c_año' => $c_año,
            'c_sucu' => $c_sucu,
            'c_alma' => $c_alma,
            'c_comp' => '60',
            // 'c_vend' => $c_vend,
            'n_comp' => $request->n_comp 
        ])->update([
            'd_anul' => '1',
            'f_anul' => DB::raw('GETDATE()')
        ]);

        /*
        update Mesas set q_ocup='0' where c_empr='001' and c_ubic='01' and c_mesa='02'
        */
        if (session('org')->k_empr == '2') {
            // Cambiamos estado de mesa solo cuando el tipo de empresa sea restaurant=2
            DB::table('Mesas')->where([
                'c_empr' => $c_empr,
                'c_ubic' => $request->c_ubic,
                'c_mesa' => $request->c_mesa,
                'c_alma' => $c_alma
            ])->update([
                'q_ocup' => '0'
            ]);

            // Liberamos edición de mesa
            MesasController::libEdicMesa1($request->c_ubic, $request->c_mesa);
        }

        return ['success'=>'Pedido anulado correctamente'];
    }

    public function cerrarPedido(Request $request){
        $c_empr = session('c_empr');
        $c_año = session('c_anio');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');
        $n_seri = str_pad($c_alma, 4, '0', STR_PAD_LEFT);
        $c_vend = session('usuario')->c_vend;

        // Validamos si el pedido fue pagado o anulado
        /*
        select top 1 q_pago,d_anul
        from Pedidos
        where c_empr='001' and c_año='2017' and c_sucu='001' and c_alma='001' and c_comp='60' and c_vend='01' and n_comp='2112170001'
        */
        $pedido = DB::table('Pedidos')
        ->select('q_pago','d_anul')
        ->where([
            ['c_empr','=',$c_empr],
            ['c_año','=',$c_año],
            ['c_sucu','=',$c_sucu],
            ['c_alma','=',$c_alma],
            ['c_comp','=','60'],
            // ['c_vend','=',$c_vend],
            ['n_comp','=',$request->n_comp]
        ])
        ->first();

        if($pedido->q_pago == '1'){
            return ['info'=>'q_pago'];
        }else if ($pedido->d_anul == '1') {
            return ['info'=>'d_anul'];
        }

        // Verificamos si hay items para enviar a cocina
        $totalItemsNoEnviados = DB::table('PedItem')
                            ->where([
                                ['c_empr','=',$c_empr],
                                ['c_sucu','=',$c_sucu],
                                ['c_comp','=','60'],
                                ['n_seri','=',$n_seri],
                                ['n_comp','=',$request->n_comp],
                                ['q_envic','=','0']
                            ])
                            ->count();

        if ( $totalItemsNoEnviados == 0 ) {
            return ['success'=>'Pedido cerrado correctamente'];
            /*return response()->json([
                    'errors' => [
                            'peditem' => ['Item(s) de pedido ya fueron enviados, por favor vuelva a ingresar a pedido para verificar.']
                        ]
                    ],422);*/
        }

        //Modificando peditem
        DB::table('peditem')->where([
            'c_empr' => $c_empr,
            'c_sucu' => $c_sucu,
            'c_comp' => '60',
            'n_comp' => $request->n_comp
        ])->update([
            'q_envic' => '1',
        ]);

        return ['success'=>'Pedido cerrado correctamente'];
    }

    // Cambiar Mesa
    public function cambMesa(Request $request){
        $c_empr = session('c_empr');
        $c_año = session('c_anio');
        $c_sucu = session('c_sucu');
        $c_alma = session('c_alma');
        $c_vend = session('usuario')->c_vend;
        // Request
        $n_comp = $request->n_comp;
        $c_ubic = $request->c_ubic;
        $c_mesa = $request->c_mesa;
        $pedc_ubic = $request->pedc_ubic;
        $pedc_mesa = $request->pedc_mesa;

        // Verificamos si mesa esta siendo editada
        $mesa = DB::table('Mesas')
                    ->select('q_ocup','q_edit','c_vendedit')
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
                            'c_vendedit' => ["No se puede cambiar a Mesa destino, está siendo editado por otro usuario $l_vend"]
                        ]
                    ],422);
        }elseif ($mesa->q_ocup == '1') {
            return response()->json([
                'errors' => [
                        'q_ocup' => ['Mesa destino se encuentra ocupada, por favor eliga otra !!!']
                    ]
                ],422);
        }elseif ($mesa->q_ocup == 2) {
            return response()->json(['errors' => ['q_edit' => ['No se puede cambiar a Mesa destino, tiene cuentas divididas !!!']]],422);
        }

        // Verificamos si mesa que queremos cambiar se encuentra ocupada
        /*$q_ocup = (DB::table('Mesas')
                    ->select('q_ocup')
                    ->where([
                        ['c_empr','=',$c_empr],
                        ['c_ubic','=',$c_ubic],
                        ['c_mesa','=',$c_mesa],
                        ['c_alma','=',$c_alma]
                    ])
                    ->first())->q_ocup;

        if ($q_ocup == '1') {
            return response()->json([
                'errors' => [
                        'q_ocup' => ['Mesa destino se encuentra ocupada, por favor eliga otra !!!']
                    ]
                ],422);
        }elseif ($q_ocup == 2) {
            return response()->json(['errors' => ['q_edit' => ['No se puede cambiar a Mesa destino, tiene cuentas divididas !!!']]],422);
        }*/

        // Validamos si el pedido fue pagado o anulado
        $pedido = DB::table('Pedidos')
        ->select('q_pago','d_anul','q_divi')
        ->where([
            ['c_empr','=',$c_empr],
            ['c_año','=',$c_año],
            ['c_sucu','=',$c_sucu],
            ['c_alma','=',$c_alma],
            ['c_comp','=','60'],
            // ['c_vend','=',$c_vend],
            ['n_comp','=',$n_comp]
        ])
        ->first();

        if (empty($pedido)) {
            return response()->json(['errors' => ['n_comp' => ['Pedido actual fue eliminado, no se puede cambiar mesa !!!']]],422);
        }elseif($pedido->q_pago == '1'){
            return response()->json(['errors' => ['q_ocup' => ['Pedido actual fue pagado, no se puede cambiar mesa !!!']]],422);
        }elseif ($pedido->d_anul == '1') {
            return response()->json(['errors' => ['q_ocup' => ['Pedido actual fue anulado, no se puede cambiar mesa !!!']]],422);
        }elseif ($pedido->q_pago == '2' || $pedido->q_divi == '1') {
            return response()->json(['errors' => ['q_divi' => ['Pedido actual tiene cuentas divididas, no se puede cambiar mesa !!!']]],422);
        }

        // Cambiamos mesa y liberamos la mesa anterior
        DB::transaction(function () use ($c_empr,$c_año,$c_sucu,$c_alma,$c_vend,$n_comp,$c_ubic,$c_mesa,$pedc_ubic,$pedc_mesa) {
            DB::table('Pedidos')->where([
                'c_empr' => $c_empr,
                'c_año' => $c_año,
                'c_sucu' => $c_sucu,
                'c_alma' => $c_alma,
                'c_comp' => '60',
                // 'c_vend' => $c_vend,
                'n_comp' => $n_comp
            ])->update([
                'c_ubic' => $c_ubic,
                'c_mesa' => $c_mesa
            ]);

            DB::table('Mesas')->where([
                'c_empr' => $c_empr,
                'c_alma' => $c_alma,
                'c_ubic' => $c_ubic,
                'c_mesa' => $c_mesa,
            ])->update([
                'q_ocup' => '1',
                'q_edit' => '1',
                'c_vendedit' => $c_vend
            ]);

            DB::table('Mesas')->where([
                'c_empr' => $c_empr,
                'c_alma' => $c_alma,
                'c_ubic' => $pedc_ubic,
                'c_mesa' => $pedc_mesa,
            ])->update([
                'q_ocup' => '0',
                'q_edit' => '0',
                'c_vendedit' => ''
            ]);
        });

        return ['success'=>'Mesa se cambio correctamente.'];
    }

    // Movimiento de item
    public function moverItem(Request $request)
    {
        // Validaciones
         $validator = \Validator::make($request->PedItem1, [
            // 's_cant' => 'required|numeric',
            'c_ubic' => 'required',
            'c_mesa' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' =>$validator->errors()], 422);
        }

        $c_empr = session('c_empr');
        $c_año = session('c_anio');
        $c_alma = session('c_alma');
        $c_sucu = session('c_sucu');
        $n_seri = str_pad($c_alma, 4, '0', STR_PAD_LEFT);
        $c_vend = session('usuario')->c_vend;
        $n_comp = $request->_Pedido['n_comp'];

        // Validamos si el pedido fue pagado o anulado
        $pedido = DB::table('Pedidos')
        ->select('q_pago','d_anul','q_divi')
        ->where([
            ['c_empr','=',$c_empr],
            ['c_año','=',$c_año],
            ['c_sucu','=',$c_sucu],
            ['c_alma','=',$c_alma],
            ['c_comp','=','60'],
            // ['c_vend','=',$c_vend],
            ['n_comp','=',$n_comp]
        ])
        ->first();

        if (empty($pedido)) {
            return response()->json(['errors' => ['d_anul' => ['Pedido fue eliminado, no se puede mover producto !!!']]],422);
        }else if($pedido->q_pago == '1'){
            return response()->json(['errors' => ['q_pago' => ['Pedido ha sido pagado, no se puede mover producto !!!']]],422);
        }else if ($pedido->d_anul == '1') {
            return response()->json(['errors' => ['d_anul' => ['Pedido ha sido anulado, no se puede mover producto !!!']]],422);
        }elseif ($pedido->q_pago == '2' || $pedido->q_divi == '1') {
            return response()->json(['errors' => ['q_ocup' => ['Pedido tiene cuentas divididas, no se puede mover producto !!!']]],422);
        }

        // Verificamos si mesa esta siendo editada
        $mesa = DB::table('Mesas')
                    ->select('q_edit','c_vendedit')
                    ->where([
                        ['c_empr','=',$c_empr],
                        ['c_ubic','=',$request->PedItem1['c_ubic']],
                        ['c_mesa','=',$request->PedItem1['c_mesa']],
                        ['c_alma','=',$c_alma]
                    ])
                    ->first();

        if ($mesa->q_edit == 1) {
            // Mensaje si mesa esta siendo editada
            $l_vend = $mesa->c_vendedit == 'CA' ? 'CAJA' : $mesa->c_vendedit;
            return response()->json([
                    'errors' => [
                            'c_vendedit' => ["No se puede mover producto, Mesa destino está siendo editado por otro usuario $l_vend"]
                        ]
                    ],422);
        }


        // Verificamos cantidad a eliminar
        /*if($request->PedItem1['s_cant'] <= 0){
            return response()->json(['errors' => ['s_cant' => ['Cantidad a eliminar no puede ser menor a 0']]],422);
        }
        else if ($request->PedItem1['s_cant'] > $request->_PedItem['s_cant']) {
            return response()->json(['errors' => ['s_cant' => ['Cantidad a mover no puede ser mayor a '.(double)$request->_PedItem['s_cant']]]],422);
        }*/

        if ( $request->PedItem1['c_ubic'] == $request->_Pedido['c_ubic'] && $request->PedItem1['c_mesa'] == $request->_Pedido['c_mesa'] ) {
            return response()->json(['errors' => ['c_ubic' => ['No se puede mover item a la misma mesa']]],422);
        }        

        // Validamos que mesa se encuentre ocupada para realizar la transferencia del item
        $mesa = collect(\DB::select("
            select top 1 q_ocup
            from mesas
            where c_empr=:c_empr and c_alma=:c_alma and c_ubic=:c_ubic and c_mesa=:c_mesa
            ",["c_empr"=>$c_empr, "c_alma"=>$c_alma, "c_ubic"=>$request->PedItem1['c_ubic'], "c_mesa"=>$request->PedItem1['c_mesa']]))->first();

        if($mesa->q_ocup == "0")
        {
            return response()->json(['errors' => ['c_mesa' => ["Mesa no se encuentra ocupada, primero registre pedido en mesa de destino para mover item"]]],422);
        }

        // Obtenemos numero de pedido de mesa destino y numero de item
        $pedido = collect(\DB::select("
            select top 1 n_comp,q_pago,d_anul,q_divi
            from Pedidos
            where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and n_seri=:n_seri and SUBSTRING(n_comp, 1, 6)=:f_comp and c_ubic=:c_ubic and c_mesa=:c_mesa and q_pago in (0,2)
            ",["c_empr"=>$c_empr, "c_sucu"=>$c_sucu, "c_alma"=>$c_alma, "n_seri"=>$n_seri, "f_comp"=>substr($request->_Pedido['n_comp'], 0, 6), "c_ubic"=>$request->PedItem1['c_ubic'], "c_mesa"=>$request->PedItem1['c_mesa']]))->first();

        if (empty($pedido)) {
            return response()->json(['errors' => ['d_anul' => ['Pedido destino fue eliminado, no se puede mover producto !!!']]],422);
        }else if($pedido->q_pago == '1'){
            return response()->json(['errors' => ['q_pago' => ['Pedido destino ha sido pagado, no se puede mover producto !!!']]],422);
        }else if ($pedido->d_anul == '1') {
            return response()->json(['errors' => ['d_anul' => ['Pedido destino ha sido anulado, no se puede mover producto !!!']]],422);
        }elseif ($pedido->q_pago == '2' || $pedido->q_divi == '1') {
            return response()->json(['errors' => ['q_ocup' => ['Pedido destino tiene cuentas divididas, no se puede mover producto !!!']]],422);
        }

        $peditem = collect(\DB::select("
            select top 1 n_item
            from PedItem
            where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp
            order by n_item desc
            ",["c_empr"=>$c_empr, "c_sucu"=>$c_sucu, "n_seri"=>$n_seri, "n_comp"=>$pedido->n_comp]))->first();
        // $n_item = count($peditem) == 0 ? 1 : (int)$peditem->n_item + 1;
        $sig_n_item = empty($peditem) ? 0 : (int)$peditem->n_item;

        // Grabamos en Base de Datos
        foreach ($request->_PedItem as $key => $value) {
            $sig_n_item++;
            $n_item = str_pad($sig_n_item, 4, '0', STR_PAD_LEFT);

            if ($value['s_cant1'] == $value['s_cant']) {
                // Peditem a modificar
                $PedItem = [];

                // Peditem destino
                $PedItem1 = $value;
                $PedItem1['n_item'] = $n_item;
            }else{
                // Peditem a modificar
                $PedItem = $value;
                $PedItem['s_cant'] = $PedItem['s_cant'] - $value['s_cant1'];
                $PedItem['s_bimp'] = $PedItem['s_cant'] * $PedItem['s_vent'];

                // Peditem destino
                $PedItem1 = $value;
                $PedItem1['s_cant'] = $value['s_cant1'];
                $PedItem1['s_bimp'] = $PedItem1['s_cant'] * $PedItem1['s_vent'];
                $PedItem1['n_item'] = $n_item;
            }

            // Grabamos en Peditem
            if( count($PedItem) == 0 ){
                \DB::delete("
                    delete
                    from PedItem
                    where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp and n_item=:n_item
                    ", ["c_empr"=>$c_empr, "c_sucu"=>$c_sucu, "n_seri"=>$n_seri, "n_comp"=>$request->_Pedido['n_comp'], "n_item"=>$value['n_item']]);
            }else{
                \DB::update("
                    update PedItem set s_cant=:s_cant
                        where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp and n_item=:n_item
                    ",['s_cant'=> $PedItem['s_cant'],'c_empr'=>$c_empr,'c_sucu'=>$c_sucu,'n_seri'=>$n_seri,'n_comp'=> $request->_Pedido['n_comp'],'n_item'=> $PedItem['n_item']]);
            }

            \DB::insert("
                insert into PedItem(c_empr,c_sucu,c_comp,n_seri,n_comp,c_prod,n_item,k_medi,s_cant,s_vent,n_prec,c_indi,q_coci,q_envic,q_preparado,l_obse,c_comb,c_vendd) 
                values(:c_empr,:c_sucu,:c_comp,:n_seri,:n_comp,:c_prod,:n_item,:k_medi,:s_cant,:s_vent,:n_prec,:c_indi,:q_coci,:q_envic,:q_preparado,:l_obse,:c_comb,:c_vend)
                ",[
                    "c_empr" => $c_empr,
                    "c_sucu" => $c_sucu,
                    "c_comp" => "60",
                    "n_seri" => $n_seri,
                    "n_comp" => $pedido->n_comp,
                    "c_prod" => $PedItem1['c_prod'],
                    "n_item" => $PedItem1['n_item'],
                    "k_medi" => $PedItem1['k_medi'],
                    "s_cant" => $PedItem1['s_cant'],
                    "s_vent" => $PedItem1['s_vent'],
                    "n_prec" => $PedItem1['n_prec'],
                    "c_indi" => $PedItem1['c_indi'] == '1' ? 'E' : '',
                    "q_coci" => $PedItem1['q_coci'],
                    "q_envic" => $PedItem1['q_envic'],
                    "q_preparado" => $PedItem1['q_preparado'],
                    "l_obse"=> mb_strtoupper(trim($PedItem1['l_obse']),'UTF-8'),
                    "c_comb"=> trim($PedItem1['c_comb']),
                    'c_vend' => $PedItem1['c_vend']
                ]
            );

            // Pedido Origen
            DB::update("
                update Pedidos 
                set q_movitem=1 
                where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and c_comp=:c_comp and n_seri=:n_seri and n_comp=:n_comp
            ",["c_empr"=>$c_empr,"c_sucu"=>$c_sucu,"c_alma"=>$c_alma,"c_comp"=>"60","n_seri"=>$n_seri,"n_comp"=>$request->_Pedido['n_comp']]);

            // Pedido Destino
            DB::update("
                update Pedidos 
                set q_movitem=1 
                where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and c_comp=:c_comp and n_seri=:n_seri and n_comp=:n_comp
            ",["c_empr"=>$c_empr,"c_sucu"=>$c_sucu,"c_alma"=>$c_alma,"c_comp"=>"60","n_seri"=>$n_seri,"n_comp"=>$pedido->n_comp]);
        }

        return ['success'=>'Pedido actualizado correctamente'];

        // Modificamos items para grabar        
        /*if ($request->PedItem1['s_cant'] == $request->_PedItem['s_cant']) {
            // Peditem a modificar
            $PedItem = [];

            // Peditem destino
            $PedItem1 = $request->_PedItem;
            $PedItem1['n_item'] = $n_item;
        }else{
            // Peditem a modificar
            $PedItem = $request->_PedItem;
            $PedItem['s_cant'] = $PedItem['s_cant'] - $request->PedItem1['s_cant'];
            $PedItem['s_bimp'] = $PedItem['s_cant'] * $PedItem['s_vent'];

            // Peditem destino
            $PedItem1 = $request->_PedItem;
            $PedItem1['s_cant'] = $request->PedItem1['s_cant'];
            $PedItem1['s_bimp'] = $PedItem1['s_cant'] * $PedItem1['s_vent'];
            $PedItem1['n_item'] = $n_item;
        }

        // Grabamos en Peditem
        if( count($PedItem) == 0 ){
            \DB::delete("
                delete
                from PedItem
                where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp and n_item=:n_item
                ", ["c_empr"=>$c_empr, "c_sucu"=>$c_sucu, "n_seri"=>$n_seri, "n_comp"=>$request->_Pedido['n_comp'], "n_item"=>$request->_PedItem['n_item']]);
        }else{
            \DB::update("
                update PedItem set s_cant=:s_cant
                    where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp and n_item=:n_item
                ",['s_cant'=> $PedItem['s_cant'],'c_empr'=>$c_empr,'c_sucu'=>$c_sucu,'n_seri'=>$n_seri,'n_comp'=> $request->_Pedido['n_comp'],'n_item'=> $PedItem['n_item']]);
        }

        \DB::insert("
            insert into PedItem(c_empr,c_sucu,c_comp,n_seri,n_comp,c_prod,n_item,k_medi,s_cant,s_vent,n_prec,c_indi,q_coci,q_envic,q_preparado,l_obse,c_comb,c_vendd) 
            values(:c_empr,:c_sucu,:c_comp,:n_seri,:n_comp,:c_prod,:n_item,:k_medi,:s_cant,:s_vent,:n_prec,:c_indi,:q_coci,:q_envic,:q_preparado,:l_obse,:c_comb,:c_vend)
            ",[
                "c_empr" => $c_empr,
                "c_sucu" => $c_sucu,
                "c_comp" => "60",
                "n_seri" => $n_seri,
                "n_comp" => $pedido->n_comp,
                "c_prod" => $PedItem1['c_prod'],
                "n_item" => $PedItem1['n_item'],
                "k_medi" => $PedItem1['k_medi'],
                "s_cant" => $PedItem1['s_cant'],
                "s_vent" => $PedItem1['s_vent'],
                "n_prec" => $PedItem1['n_prec'],
                "c_indi" => $PedItem1['c_indi'] == '1' ? 'E' : '',
                "q_coci" => $PedItem1['q_coci'],
                "q_envic" => $PedItem1['q_envic'],
                "q_preparado" => $PedItem1['q_preparado'],
                "l_obse"=> mb_strtoupper(trim($PedItem1['l_obse']),'UTF-8'),
                "c_comb"=> trim($PedItem1['c_comb']),
                'c_vend' => $PedItem1['c_vend']
            ]
        );

        // Pedido Origen
        DB::update("
            update Pedidos 
            set q_movitem=1 
            where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and c_comp=:c_comp and n_seri=:n_seri and n_comp=:n_comp
        ",["c_empr"=>$c_empr,"c_sucu"=>$c_sucu,"c_alma"=>$c_alma,"c_comp"=>"60","n_seri"=>$n_seri,"n_comp"=>$request->_Pedido['n_comp']]);

        // Pedido Destino
        DB::update("
            update Pedidos 
            set q_movitem=1 
            where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and c_comp=:c_comp and n_seri=:n_seri and n_comp=:n_comp
        ",["c_empr"=>$c_empr,"c_sucu"=>$c_sucu,"c_alma"=>$c_alma,"c_comp"=>"60","n_seri"=>$n_seri,"n_comp"=>$pedido->n_comp]);

        return ['PedItem'=>(object)$PedItem];*/
    }

    public function imprPedAgrup(Request $request){
        $n_car = 40; // Numero de caracteres
        $l_txt = ""; // Para impresion en ticket desde archivo de texto

        $fpdf = new Fpdf('P','mm', 'A4');
        $fpdf->AddPage();
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->SetFillColor(255,255,255);

        // Organizacion
        $fpdf->SetXY(10,15);
        $fpdf->SetFont('Arial', 'B', 10);
        $fpdf->Cell(105, 4, utf8_decode(session('org')->l_empr),0,1,"L");
        $fpdf->Ln(2);
        $fpdf->SetFont('Arial', '', 10);
        $fpdf->Cell(105, 4, utf8_decode(session('org')->l_dire),0,0,"L");
        $fpdf->Cell(95, 4, utf8_decode(""),0,0,"L",1);
        
        // Numero de pedido
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->RoundedRect(120, 10, 80, 10, 1, '12', 'D');
        $fpdf->SetXY(120,10);
        $fpdf->Cell(80, 10, utf8_decode("Pedido N° ".substr($request->pedido['n_comp'],6,10)),1,0,"C",1);

        // Fecha pedido
        $f_comp = substr($request->pedido['n_comp'],0,6);
        $f_comp = \DateTime::createFromFormat('jmy', $f_comp,new \DateTimeZone('America/Bogota'));

        $fpdf->SetXY(120,20);
        $fpdf->Cell(40, 10, utf8_decode("Fecha: ".$f_comp->format('d/m/Y')),1,0,"C",1);

        // Hora pedido
        $fpdf->SetXY(160,20);
        $fpdf->Cell(40, 10, utf8_decode("Hora: ".$f_comp->format('H:i:s')),1,0,"C",1);

        // Informacion de cliente
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->SetXY(10,37);
        $fpdf->Cell(15, 4, utf8_decode("Señor(es):"),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(115, 4, utf8_decode($request->pedido['l_agen']),"B",0,"L");
        $fpdf->Cell(70, 4, utf8_decode(""),0,0,"L",1);

        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->SetXY(10,45);
        $fpdf->Cell(14, 4, utf8_decode("Dirección:"),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(116, 4, utf8_decode($request->pedido['l_dire']),"B",0,"L");
        $fpdf->Cell(70, 4, utf8_decode(""),0,0,"L",1);

        $fpdf->SetXY(10,53);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(10, 4, utf8_decode("R.U.C:"),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(55, 4, utf8_decode(strlen($request->pedido['n_docu']) == 11 ? $request->pedido['n_docu'] : ""),"B",0,"L");

        $fpdf->SetXY(75,53);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(10, 4, utf8_decode("D.N.I:"),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(55, 4, utf8_decode(strlen($request->pedido['n_docu']) != 11 ? $request->pedido['n_docu'] : ""),"B",0,"L");

        // Comprobante
        $fpdf->SetXY(150,37);
        $fpdf->Cell(5, 5, $request->pedido['c_compgen'] == "01" ? "X" : "",1,0,"C");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(20, 5, utf8_decode("Factura"),0,0,"L");

        $fpdf->Cell(5, 5, $request->pedido['c_compgen'] == "03" ? "X" : "",1,0,"C");
        $fpdf->Cell(20, 5, utf8_decode("Boleta"),0,0,"L");

        $fpdf->SetXY(150,44.5);
        $fpdf->Cell(5, 5, $request->pedido['c_compgen'] == "12" ? "X" : "",1,0,"C");
        $fpdf->Cell(20, 5, utf8_decode("Venta Interna"),0,0,"L");

        $fpdf->SetXY(150,52.5);
        $fpdf->Cell(5, 5, $request->pedido['c_compgen'] == "55" ? "X" : "",1,0,"C");
        $fpdf->Cell(20, 5, utf8_decode("Ticket"),0,1,"L");

        // Detalle pedido
        $fpdf->Ln(3);
        $fpdf->SetFillColor(60,60,60);
        $fpdf->SetTextColor(255,255,255);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(20, 5, utf8_decode("CODIGO"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("SERIE"),1,0,"C",1);
        // w = 120
        $tallas = [];
        foreach ($request->tallas as $key => $value) {
            if ($value === 0) {
                array_push($tallas, $key);
            }
        }

        $wtallas = (120 / count($tallas));
        foreach ($tallas as $key => $value) {
            $fpdf->Cell($wtallas, 5, utf8_decode($value),1,0,"C",1);
        }

        $fpdf->Cell(15, 5, utf8_decode("PARES"),1,0,"C",1);
        $fpdf->Cell(20, 5, utf8_decode("TOTAL"),1,1,"C",1);

        $fpdf->RoundedRect(10, 65.5, 190, 170, 1, '34', 'D');

        $fpdf->SetFont('Arial', '', 8);
        $fpdf->SetTextColor(0,0,0);
        $i = 1;
        $n_totpares = 0;
        $s_tota = 0;

        // Analizamos que items se mostraranm, solo los que tienen almenos 1 cantidad en su pedido
        $prods = [];
        foreach ($request->peditem as $key => $value) {
            $prods[utf8_decode($value["c_prod"]).utf8_decode($value["l_seri"])] = true;

            foreach ($tallas as $key1 => $value1) {
                $s_cant = 0;
                foreach ($value["tallas"] as $key2 => $value2) {
                    $s_cant = (double)$s_cant + (double)$value2["s_cant"];
                }

                if ($s_cant > 0) {
                    $prods[utf8_decode($value["c_prod"]).utf8_decode($value["l_seri"])] = true;
                }else{
                    $prods[utf8_decode($value["c_prod"]).utf8_decode($value["l_seri"])] = false;
                }
            }
        }
        // 

        // Añadimos al PDF
        $l_items = ""; // Para impresion desde archivo de texto
        foreach ($request->peditem as $key => $value) {
            if($prods[utf8_decode($value["c_prod"]).utf8_decode($value["l_seri"])] == true){
                $fpdf->SetFont('Arial', 'B', 8);
                $fpdf->Cell(5, 5, utf8_decode($i),($i == 34 ? 0 : 1),0,"C");
                $fpdf->SetFont('Arial', '', 8);
                $fpdf->Cell(15, 5, utf8_decode($value["c_prod"]),1,0,"C");
                $fpdf->Cell(15, 5, utf8_decode($value["l_seri"]),1,0,"C");

                // Para impresion desde archivo de texto
                $l_items .= PHP_EOL.str_pad(utf8_decode($value["c_prod"])."-".utf8_decode($value["l_seri"]), 14, " ")." ";
                $l_tall = "";
                // 

                // Tallas
                $n_totpares1 = 0;
                $s_tota1 = 0;
                foreach ($tallas as $key1 => $value1) {
                    $exist = false;
                    foreach ($value["tallas"] as $key2 => $value2) {
                        if ($value1 == $key2) {
                            $exist = true;
                            $fpdf->Cell($wtallas, 5, $value2 == null || (int)$value2["s_cant"] == 0 ? "" : $value2["s_cant"],1,0,"C",0);
                            $n_totpares1 += (int)$value2["s_cant"];
                            $s_tota1 += ((int)$value2["s_cant"]) * (double)$value2["s_vent"];

                            // Para impresion desde archivo de texto
                            $l_tall .= $value2 == null || (int)$value2["s_cant"] == 0 ? "" : $value1.":".(int)$value2["s_cant"].",";
                            /*if(strlen($l_tall) > 9){
                                $l_items .= $l_tall.PHP_EOL;
                            }else{
                                $l_items .= str_pad($l_tall, 9, " ", STR_PAD_LEFT);
                            }*/
                            // 
                        }
                    }

                    if ($exist == false) {
                        $fpdf->Cell($wtallas, 5, $exist,1,0,"C",0);
                    }
                }
                $n_totpares += $n_totpares1;
                $s_tota += $s_tota1;
                $fpdf->Cell(15, 5, $n_totpares1,1,0,"C");
                // 
                $fpdf->Cell(20, 5, number_format($s_tota1,2,'.',','),($i == 34 ? 0 : 1),1,"R");
                $i++;

                // Para impresion desde archivo de texto
                $l_tall .= $value2 == null || (int)$value2["s_cant"] == 0 ? "" : $value1.":".(int)$value2["s_cant"].",";
                if(strlen($l_tall) > 9){
                    $l_items .= substr($l_tall, 0, 25).PHP_EOL;

                    $l_tall = substr($l_tall, 25, strlen($l_tall));
                    if (strlen($l_tall)>0) {
                        foreach (str_split($l_tall,40) as $key => $value) {
                            $l_items .= $value.PHP_EOL;
                        }
                    }

                    $l_items .= str_pad($n_totpares1, 30, " ", STR_PAD_LEFT);
                    $l_items .= str_pad($s_tota, 10, " ", STR_PAD_LEFT);
                }else{
                    $l_items .= str_pad($l_tall, 8, " ", STR_PAD_LEFT);
                    $l_items .= str_pad($n_totpares1, 7, " ", STR_PAD_LEFT);
                    $l_items .= str_pad($s_tota, 10, " ", STR_PAD_LEFT);
                }
                // 
            }
        }
        // 
        
        // Observación
        $fpdf->RoundedRect(10, 237, 85, 30, 1, '12', 'D');

        $fpdf->SetXY(10,237.5);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(20, 4, utf8_decode("Observación: "),0,0,"L");

        $fpdf->SetXY(30,237.5);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->MultiCell(65, 4, utf8_decode($request->pedido['l_obse']),0,"L");

        // Vendedor
        $fpdf->RoundedRect(10, 267, 85, 4, 1, '34', 'D');
        $fpdf->SetXY(10,267);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(14, 4, utf8_decode("Vendedor: "),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(71, 4, utf8_decode(session('usuario')->l_vend),0,0,"L");

        // Total Pares
        $fpdf->RoundedRect(97.5, 237, 50, 10, 1, '1234', 'D');
        $fpdf->SetXY(97.5, 237);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(25, 10, utf8_decode("TOTAL PARES:"),"R",0,"R");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(25, 10, $n_totpares,0,0,"L");

        // SUB TOTAL
        $fpdf->SetFillColor(0,0,0);
        $fpdf->RoundedRect(150, 237, 25, 10, 1, '14', 'FD');
        $fpdf->RoundedRect(175, 237, 25, 10, 1, '23', 'D');
        $fpdf->SetXY(150, 237);

        $fpdf->SetTextColor(255,255,255);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(25, 10, utf8_decode("S-TOTAL"),0,0,"C");

        $fpdf->SetTextColor(0,0,0);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(25, 10, number_format($s_tota,2,'.',','),0,0,"C");

        $file = base64_encode($fpdf->output("S"));

        // Para archivo de texto
        $l_txt .= "----------------------------------------".PHP_EOL.str_pad("Pedido N: ".substr($request->pedido['n_comp'],6,10), 26, " ", STR_PAD_LEFT).PHP_EOL;
        $l_txt .= "Vendedor: ".utf8_decode(session('usuario')->l_vend).PHP_EOL;
        $l_txt .= "Fecha: ".$f_comp->format('d/m/Y')." ".$f_comp->format('H:i:s').PHP_EOL;
        $l_txt .= "----------------------------------------".PHP_EOL."Codigo-Serie   Talla(s)  Pares   Importe".PHP_EOL."----------------------------------------";

        $l_txt .= $l_items.PHP_EOL."----------------------------------------".PHP_EOL;
        $l_txt .= "Pares: ".$n_totpares.PHP_EOL;
        $l_txt .= str_pad("Total:", 25, " ", STR_PAD_LEFT).str_pad(number_format($s_tota,2,'.',','), 15, " ", STR_PAD_LEFT).PHP_EOL."----------------------------------------".PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
        // 

        // Respuesta
        return ["pdf"=>$file, "l_txt"=>$l_txt];
    }

    public function devProdStock1(Request $request){
        $this->validate($request, [
            'c_prods' => 'required'
        ]);

        $resul = DB::table("ProdStock")
                ->where(["c_empr"=>session('c_empr'),"c_sucu"=>session('c_sucu'),"c_alma"=>session('c_alma')])
                ->whereIn('c_prod',$request->c_prods)
                ->get();

        return ["resul"=>$resul];
    }

    public function modPedAten(Request $request){
        $this->validate($request, [
            'n_comp' => 'required'
        ]);

        DB::update("update Pedidos set q_aten=1, c_usuat=SYSTEM_USER, f_aten=dbo._Getdate() where c_empr=:c_empr and c_sucu=:c_sucu and c_alma=:c_alma and c_comp='60' and n_comp=:n_comp",
            ["c_empr"=>session('c_empr'),"c_sucu"=>session('c_sucu'),"c_alma"=>session('c_alma'),"n_comp"=>$request->n_comp]);

        return ["resul"=>"Pedido modificado correctamente."];
    }

    public function envEmail(Request $request){
        $this->validate($request, [
            'l_email' => 'required',
            'l_pdf' => 'required',
            'pedido' => 'required',
        ]);

        $organizacion = session('org');

        $l_mensaje = 'Estimado cliente, le enviamos adjunto el PDF de su Pedido N° '.substr($request->pedido["n_comp"], 5,5);

        \Mail::raw($l_mensaje, function ($message) use ($request,$organizacion){
            $message->from('no-reply@sistemac34.com',$organizacion->l_empr);
            $message->subject("Pedido N° ".substr($request->pedido["n_comp"], 5,5));
            $message->to($request->l_email);
            $message->attachData( base64_decode($request->l_pdf), "pdf.pdf", ['mime' => 'application/pdf']);
        });
    }
}
