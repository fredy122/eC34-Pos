<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccesoController extends Controller
{
    public function getLogin()
    {
    	return view('acceso.acceso');
    }

    public function postLogin(Request $request)
    {
        // APP_TYPE = local | web
        $l_serv = '';
        $k_tipnegoc = '';
        $q_cambvend = '';
        $request->l_pass = mb_strtoupper(trim($request->l_pass),'UTF-8');

        $Usuario = $request->Usuario;
        $l_pass = $request->l_pass;

        $cliente = (object)['q_memp'=>0];

        if ( env('APP_TYPE') == 'web' ){
        	$this->validate($request, [
                'n_ruc' => 'required',
                'Usuario' => 'required',
                'l_pass' => 'required'
            ]);

            $n_ruc = "";
            $c_empr = "";
            if (strlen(trim($request->n_ruc))==14) {
                $n_ruc = substr($request->n_ruc, 0,11);
                $c_empr = substr($request->n_ruc, 11,3);
            }
            if (!empty($n_ruc) && !empty($c_empr)) {
                $request->n_ruc = $n_ruc;
            }

            // Consultando Usuario en Mysql
            if (!empty($c_empr)) {
                $cliente = \DB::table('clientes')->select('l_serv','k_tipnegoc','q_cambvend','q_memp','c_empr')->where(['n_ruc'=>$request->n_ruc,'c_empr'=>$c_empr])->first();
            }else{
                $cliente = \DB::table('clientes')->select('l_serv','k_tipnegoc','q_cambvend','q_memp','c_empr')->where('n_ruc',$request->n_ruc)->first();

                if($cliente->c_empr){
                    return response()->json([
                        'errors' => [
                                'n_ruc' => ['Numero de RUC y Empresa no coincide con nuestros registros.']
                            ]
                        ],422);
                }
            }
            
            if( ! $cliente )
            {
                return response()->json([
                    'errors' => [
                            'n_ruc' => ['Numero de RUC no coincide con nuestros registros.']
                        ]
                    ],422);
            }elseif ($cliente->q_memp == 1) {
                $request->n_ruc = $request->n_ruc=="1042654310X" ? "10426543104" : $request->n_ruc;
                $Usuario = "_" . $request->n_ruc . $c_empr . mb_strtoupper(trim($request->Usuario),'UTF-8');
                $l_pass = strtr($l_pass, 'ijtuvwx.%()*+FGHIJK-/012345klmnopqrs6789:;=@ABCDE"LMNOPXYZ[]abcdeQRSTUVWfghyz{}', ')*+FGHjtuIJK-/01klmnopqivwCDE"LMNOPXYZ[]abc2345deQRST.UVWfghx%(rs6789:;=@AByz{}');
            }

            // Conexión SQL SERVER
            \Config::set('database.default', 'sqlsrv');
            \Config::set('database.connections.sqlsrv.host', $cliente->l_serv);
            \Config::set('database.connections.sqlsrv.port', '1433');
            \Config::set('database.connections.sqlsrv.username', $Usuario);
            \Config::set('database.connections.sqlsrv.password', $l_pass);
            \Config::set('database.connections.sqlsrv.database', 'eC34');

            $l_serv = $cliente->l_serv;
            $k_tipnegoc = $cliente->k_tipnegoc;
            $q_cambvend = $cliente->q_cambvend;

        }else if(env('APP_TYPE') == 'local'){
            $this->validate($request, [
                'Usuario' => 'required',
                'l_pass' => 'required'
            ]);

            // Conexión SQL SERVER
            \Config::set('database.default', 'sqlsrv');
            \Config::set('database.connections.sqlsrv.port', '1433');
            \Config::set('database.connections.sqlsrv.username', $request->Usuario);
            \Config::set('database.connections.sqlsrv.password', $request->l_pass);
            \Config::set('database.connections.sqlsrv.database', 'eC34');

            $l_serv = env('DB_HOST');
            $k_tipnegoc = env('APP_K_TIPNEGOC');
            $q_cambvend = env('APP_Q_CAMBVEND');
        }

        // Consultamos usuario en SQL SERVER
        try {
            /*
            select top 1 RTRIM(a.Usuario) as Usuario,RTRIM(a.NomUsuario) as NomUsuario,a.c_sucu,a.c_alma,k_tipp,p_pudefecto, RTRIM(a.c_vend) as c_vend, RTRIM(l_vend) as l_vend
            from Usuarios as a, Vendedor as b
            where a.Usuario='SUPERVISOR' and a.c_vend=b.c_vend
            */
            if ($cliente->q_memp == 1) {
                // Cuando es multiempresa se valida con el numero de ruc
                if (!empty($c_empr)) {
                    $usuario = \DB::table(\DB::raw('Usuarios as a inner join organizacion c on a.c_empr=c.c_empr,Vendedor as b'))
                                ->select(\DB::raw('a.c_empr,RTRIM(a.Usuario) as Usuario,RTRIM(a.NomUsuario) as NomUsuario,a.c_sucu,a.c_alma,k_tipp,p_pudefecto, a.q_movitem,a.q_elimitem,a.q_reimpitems, RTRIM(a.c_vend) as c_vend, RTRIM(l_vend) as l_vend, q_editpv,c_mesac,q_anulped,q_camvend,a.q_vera'))
                                ->where('a.Usuario',$request->Usuario)
                                ->where('c.n_ruc',$request->n_ruc)
                                ->where('c.c_empr',$c_empr)
                                ->whereColumn('a.c_empr','=','b.c_empr')
                                ->whereColumn('a.c_vend','=','b.c_vend')
                                ->first();
                }else{
                    $usuario = \DB::table(\DB::raw('Usuarios as a inner join organizacion c on a.c_empr=c.c_empr,Vendedor as b'))
                                ->select(\DB::raw('a.c_empr,RTRIM(a.Usuario) as Usuario,RTRIM(a.NomUsuario) as NomUsuario,a.c_sucu,a.c_alma,k_tipp,p_pudefecto, a.q_movitem,a.q_elimitem,a.q_reimpitems, RTRIM(a.c_vend) as c_vend, RTRIM(l_vend) as l_vend, q_editpv,c_mesac,q_anulped,q_camvend,a.q_vera'))
                                ->where('a.Usuario',$request->Usuario)
                                ->where('c.n_ruc',$request->n_ruc)
                                ->whereColumn('a.c_empr','=','b.c_empr')
                                ->whereColumn('a.c_vend','=','b.c_vend')
                                ->first();
                }
            }else{
                $usuario = \DB::table(\DB::raw('Usuarios as a,Vendedor as b'))
                            ->select(\DB::raw('a.c_empr,RTRIM(a.Usuario) as Usuario,RTRIM(a.NomUsuario) as NomUsuario,a.c_sucu,a.c_alma,k_tipp,p_pudefecto, a.q_movitem,a.q_elimitem,a.q_reimpitems, RTRIM(a.c_vend) as c_vend, RTRIM(l_vend) as l_vend, q_editpv,c_mesac,q_anulped,q_camvend,a.q_vera'))
                            ->where('a.Usuario',$request->Usuario)
                            ->whereColumn('a.c_empr','=','b.c_empr')
                            ->whereColumn('a.c_vend','=','b.c_vend')
                            ->first();
            }

            // Validamos si el usuario de sql server se encuentra en la base de datos
            if( empty($usuario) )
            {
                return response()->json([
                    'errors' => [
                            'Usuario' => ['Usuario no se encuentra registrado en su sistema. Por favor, revisa e inténtalo de nuevo']
                        ]
                ],422);
            }

            // Validamos si al usuario se le asigno un vendedor
            if( trim($usuario->c_vend) == '' )
            {
                return response()->json([
                    'errors' => [
                            'Usuario' => ['Debe asignar Vendedor a Usuario!']
                        ]
                ],422);
            }

            // Validamos si al usuario se le asigno un almacen(esto funciona como sucursal)
            if( trim($usuario->c_alma) == '' )
            {
                return response()->json([
                    'errors' => [
                            'Usuario' => ['Debe asignar Almacen a Usuario!']
                        ]
                ],422);
            }

            // Recuperamos el campo ped_gencomp de SISPROP (solo debe haber una fila en la tabla sisprop)
            //$sisprop = \DB::table('sisprop')->select('EmprDefa','ped_gencomp')->where('c_empr','=','001')->first();
            /*
            select top 1 RTRIM(EmprDefa) as EmprDefa,RTRIM(ped_gencomp) as ped_gencomp, q_npers, q_pregimpcue, q_pregenvped
            from Sisprop
            */
            $sisprop = \DB::table('sisprop')->select(\DB::raw('c_empr,RTRIM(EmprDefa) as EmprDefa,RTRIM(ped_gencomp) as ped_gencomp, q_npers, q_pregimpcue, q_pregenvped, icbper_v'))
                ->where('c_empr',$usuario->c_empr)
                ->first();

            if( empty($sisprop) ){
                return response()->json([
                    'errors' => [
                            'Usuario' => ['No se pudo recuperar "ped_gencomp" de sisprop']
                        ]
                ],422);
            }elseif (strlen(trim($sisprop->c_empr)) == 0) {
                 return response()->json([
                    'errors' => [
                            'Usuario' => [ 'No tiene empresa por defecto, debe asignar!!!' ]
                        ]
                ],422);
            }

            $SispropPDV = collect(\DB::select("
                select top 1 pq_punit
                from SispropPDV
                where c_empr=:c_empr
            ",['c_empr'=>$usuario->c_empr]))->first();

            if(empty($SispropPDV)){
                return response()->json([
                    'errors' => ['Usuario' => ['No se pudo recuperar configuracion PDV']]],422);
            }

            // Verificamos si hay caja aperturada
            $Apertcaja = \DB::table('Apertcaja')
                    ->select(\DB::raw("FORMAT(f_proc,'dd/MM/yyyy') as f_proc"))
                    ->where([
                        ['c_empr','=',$sisprop->c_empr],
                        ['c_sucu','=',$usuario->c_sucu],
                        ['c_alma','=',$usuario->c_alma],
                        ['q_esta','=','0']
                    ])
                    ->first();

            if (empty($Apertcaja)) {
                return response()->json([
                    'errors' => [
                            'f_proc' => [ 'No existe caja aperturada... Revise por favor !!!' ]
                        ]
                ],422);
            }/*else if ($totalApertcaja > 1) {
                return response()->json([
                    'errors' => [
                            'f_proc' => [ "Existen $totalApertcaja cajas aperturadas para almacén, revise por favor !!!" ]
                        ]
                ],422);
            }*/

            // Devolvemos tipo de empresa
            /*select k_empr
            from organizacion
            where c_empr='001'*/

            $org = \DB::table('organizacion')->select('k_empr','l_empr','l_dire')->where('c_empr','=',$sisprop->c_empr)->first();
            
            // Creamos la session para el usuario
            session([
                'authenticated' => true,
                'usuario' => $usuario,
                'sisprop' => $sisprop,
                'SispropPDV' => $SispropPDV,
                'c_anio' => (new \Datetime())->format('Y'),
                'org' => $org,
                'c_empr' => $sisprop->c_empr,
                'c_sucu' => $usuario->c_sucu,
                'c_alma' => $usuario->c_alma,
                'k_tipnegoc' => $k_tipnegoc,
                'q_cambvend' => $q_cambvend,
                'connection' => (object)[
                    'host' => $l_serv,
                    'username' => $Usuario,
                    'password' => $l_pass,
                ]
            ]);

            // Mensaje satisfactorio
            return ['usuario' => $usuario, 'sisprop' => $sisprop, 'SispropPDV' => $SispropPDV, 'org' => $org, 'Apertcaja' => $Apertcaja];

        } catch (\Exception $e) {
            // 28000 => Login failed
            // HYT00 => Tiempo de espera de la operación de espera agotado
            // 08001 => Host desconocido
            if ($e->getCode() == '28000') {
                return response()->json([
                    'errors' => [
                            'Usuario' => ['El nombre de usuario o la contraseña que ingresaste son incorrectos. Por favor, revisa e inténtalo de nuevo.']
                        ]
                    ],422);
            }else if ($e->getCode() == '08001') {
                return response()->json([
                    'errors' => [
                            'Usuario' => ['Error de conexión, servidor desconocido.']
                        ]
                    ],422);
            }

            return response()->json([
                'errors' => [
                        'Usuario' => [$e->getMessage()]
                    ]
                ],422);

            return response()->json([
                'errors' => [
                        'Usuario' => ['No se puede conectar a Servidor... revise por favor!']
                    ]
                ],422);
        }

    }

    public function getLogout()
    {
        session()->flush();
        return redirect('/');
    }
}
