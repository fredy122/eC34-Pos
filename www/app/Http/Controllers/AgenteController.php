<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgenteController extends Controller
{
    public function busCliente($n_docu)
   	{
   		$n_docu = trim($n_docu);
        $agente = null;

        if( $n_docu == '100' ){
            /*
            select RTRIM(c_docu) as c_docu,RTRIM(n_docu) as n_docu,RTRIM(l_agen) as l_agen,RTRIM(l_dire) as l_dire,c_vend
            from Agentes
            where c_empr='001' and c_tipa='1' and n_docu='100'
            */
            $agente = \DB::table('Agentes')
                    ->select(\DB::raw('RTRIM(c_docu) as c_docu,RTRIM(n_docu) as n_docu,RTRIM(l_agen) as l_agen,RTRIM(l_dire) as l_dire,c_vend'))
                    ->where([
                        ['c_empr','=',session('c_empr')],
                        ['c_tipa','=','1'],
                        ['n_docu','=','100']
                    ])->first();

            if( empty($agente)){
                return response()->json([
                        'errors' => [
                                'n_docu' => ['No Existe registro(s), para dato ingresado...!']
                            ]
                        ],422);
            }

        }else if ( is_numeric($n_docu) && (strlen($n_docu)== 11 || strlen($n_docu)== 8) ) {
       		/*
       		select RTRIM(c_docu) as c_docu,RTRIM(n_docu) as n_docu,RTRIM(l_agen) as l_agen,RTRIM(l_dire) as l_dire,c_vend
            from Agentes
            where c_empr='001' and c_tipa='1' and n_docu='100'
            */
            $agente = \DB::table('Agentes')
            		->select(\DB::raw('RTRIM(c_docu) as c_docu,RTRIM(n_docu) as n_docu,RTRIM(l_agen) as l_agen,RTRIM(l_dire) as l_dire,c_vend'))
            		->where([
            			['c_empr','=',session('c_empr')],
            			['c_tipa','=','1'],
            			['n_docu','=',$n_docu]
            		])->first();

            if( empty($agente) && (strlen($n_docu) == 8 || strlen($n_docu) == 11) ){

        		// Nº RUC/DNI no existe... desea registrar?
                return ['status' => '1'];

            }/*else if( $agente->c_vend != session('usuario')->c_vend ){

                return response()->json([
                        'errors' => [
                                'n_docu' => ['Cliente no pertenece a vendedor actual...!']
                            ]
                        ],422);
            }*/
            
        }else{
            /*
            select top 5 RTRIM(c_docu) as c_docu,RTRIM(n_docu) as n_docu,RTRIM(l_agen) as l_agen,RTRIM(l_dire) as l_dire,c_vend
            from Agentes
            where c_empr='001' and c_tipa='1' and c_vend='01' and (n_docu like '20%' or l_agen like '%20%')
            order by n_docu,l_agen asc
            */
            $agente = \DB::table('Agentes')
                    ->select(\DB::raw('top 5 RTRIM(c_docu) as c_docu,RTRIM(n_docu) as n_docu,RTRIM(l_agen) as l_agen,RTRIM(l_dire) as l_dire,c_vend'))
                    ->where([
                        ['c_empr','=',session('c_empr')],
                        ['c_tipa','=','1'],
                        ['c_vend','=',session('usuario')->c_vend]
                    ])
                    ->where(function($query) use ($n_docu){
                        $query->where('n_docu','like',"$n_docu%")->orWhere('l_agen','like',"%$n_docu%");
                    })
                    ->orderBy('n_docu','asc')
                    ->orderBy('l_agen','asc')
                    ->get();

            if( count($agente) == 0 ){
                return response()->json([
                        'errors' => [
                                'n_docu' => ['No Existe registro(s), para dato ingresado...!']
                            ]
                        ],422);
            }
        }

        return ['cliente' => $agente];
    }

    public function grabarCliente(Request $request){
        $this->validate($request, [
            'n_docu' => 'required|numeric',
            'l_agen' => 'required|string|max:100',
            'l_dire' => 'max:100',
            'c_rutaa' => 'max:5',
            'n_celu' => 'max:30',
        ]);

        $buscliente = \DB::table('Agentes')
                        ->select('n_docu')
                        ->where([
                            ['c_empr','=',session('c_empr')],
                            ['c_tipa','=','1'],
                            ['n_docu','=',$request->n_docu]
                        ])
                        ->first();

        if (!empty($buscliente)) {
            return response()->json([
                    'errors' => [
                            'n_docu2' => ['Nº de dcto. existe... no se puede registrar...!']
                        ]
                    ],422);
        }

        \DB::table('Agentes')->insert([
            'c_empr' => session('c_empr'),
            'c_docu' => $request->c_docu,
            'n_docu' => mb_strtoupper($request->n_docu, 'UTF-8'),
            'l_agen' => mb_strtoupper($request->l_agen, 'UTF-8'),
            'l_dire' => mb_strtoupper($request->l_dire, 'UTF-8'),
            'c_tipa' => '1',
            'c_vend' => session('usuario')->c_vend,
            'c_rutaa' => mb_strtoupper($request->c_rutaa, 'UTF-8'),
            'n_celu' => mb_strtoupper($request->n_celu, 'UTF-8')
            
        ]);

        return ['success' => 'Agente se registro exitosamente'];
    }

    // Buscar RUC en webservices c34
    public function postBuscSunat(Request $request)
    {
        $this->validate($request, [
            'n_docu' => 'required|numeric|digits:11'
        ]);

        $resul = json_decode(file_get_contents("http://c34ws.sistemac34.com/consultaruc/KdQc1kevVih4LXzTk7/".$request->n_docu));

        if(empty($resul))
        {
            return response()->json([
                        'errors' => [
                                'l_agen' => ['No Existe registro, para RUC ingresado...!']
                            ]
                        ],422);
        }

        return ['agente'=>$resul];

    }
}
