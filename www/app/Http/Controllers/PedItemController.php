<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PedItemController extends Controller
{
    public static function devListPedItemsXPedido($n_comp){
        /*
        select RTRIM(n_item) as n_item,RTRIM(a.c_prod) as c_prod,RTRIM(a.k_medi) as k_medi,a.s_cant,a.s_vent,a.n_prec,RTRIM(a.c_indi) as c_indi,a.q_coci,a.q_envic,a.q_preparado,RTRIM(a.l_obse) as l_obse,c_comb
        from PedItem a
        where a.c_empr='001' and a.c_sucu='001' and a.c_comp='60' and a.n_comp='2712170012'
        order by CONVERT(INT, n_item) asc
        
    	$list = \DB::table(\DB::raw('PedItem a'))
    			->select(\DB::raw('RTRIM(n_item) as n_item,RTRIM(a.c_prod) as c_prod,RTRIM(a.k_medi) as k_medi,a.s_cant,a.s_vent,a.n_prec,RTRIM(a.c_indi) as c_indi,a.q_coci,a.q_envic,a.q_preparado,RTRIM(a.l_obse) as l_obse,c_comb'))
    			->where([
    				['a.c_empr','=',session('c_empr')],
    				['a.c_sucu','=',session('c_sucu')],
    				['a.c_comp','=','60'],
    				['a.n_comp','=',$n_comp]
    			])
                ->orderBy(\DB::raw('CONVERT(INT, n_item)'), 'asc')
    			->get();

    	return $list;
        */



        /*
        select RTRIM(c_comb) as c_comb,max(RTRIM(n_item)) as n_item,max(RTRIM(c_prod)) as c_prod,max(RTRIM(k_medi)) as k_medi,max(s_cant)  as s_cant,max(s_vent) as s_vent,max(n_prec) as n_prec,max(RTRIM(c_indi)) as c_indi,max(q_coci) as q_coci,max(q_envic) as q_envic,max(q_preparado) as q_preparado,max(RTRIM(l_obse)) as l_obse, max(c_vend) as c_vend
        from PedItem
        where c_empr='001' and c_sucu='001' and c_comp='60' and n_seri='0001' and n_comp='2607180001'
        group by c_comb,n_item
        order by CONVERT(INT, n_item) asc
        */

        /*
        $list = \DB::table(\DB::raw('PedItem'))
                ->select(\DB::raw('RTRIM(c_comb) as c_comb,max(RTRIM(n_item)) as n_item,max(RTRIM(c_prod)) as c_prod,max(RTRIM(k_medi)) as k_medi,max(s_cant)  as s_cant,max(s_vent) as s_vent,max(n_prec) as n_prec,max(RTRIM(c_indi)) as c_indi,max(q_coci) as q_coci,max(q_envic) as q_envic,max(q_preparado) as q_preparado,max(RTRIM(l_obse)) as l_obse, max(c_vend) as c_vend'))
                ->where([
                    ['c_empr','=',session('c_empr')],
                    ['c_sucu','=',session('c_sucu')],
                    ['c_comp','=','60'],
                    ['n_seri','=',str_pad(session('c_alma'), 4, '0', STR_PAD_LEFT)],
                    ['n_comp','=',$n_comp]
                ])
                ->groupBy('c_comb', 'n_item')
                ->orderBy(\DB::raw('CONVERT(INT, n_item)'), 'asc')
                ->get();
        */



        /*
        select RTRIM(a.c_comb) as c_comb,max(RTRIM(a.n_item)) as n_item,max(RTRIM(a.c_prod)) as c_prod,max(RTRIM(a.k_medi)) as k_medi,max(a.s_cant)  as s_cant,max(a.s_vent) as s_vent,max(a.n_prec) as n_prec,max(RTRIM(a.c_indi)) as c_indi,max(a.q_coci) as q_coci,max(a.q_envic) as q_envic,max(a.q_preparado) as q_preparado,max(RTRIM(a.l_obse)) as l_obse, max(a.c_vendd) as c_vend, max(b.l_vend) as l_vend
        from PedItem a left join Vendedor b on b.c_empr=a.c_empr and b.c_alma='001' and b.c_vend=a.c_vendd
        where a.c_empr='001' and a.c_sucu='001' and a.c_comp='60' and a.n_seri='0001' and a.n_comp='2607180001'
        group by a.c_comb,a.n_item
        order by CONVERT(INT, a.n_item) asc
        */

        if (session('org')->k_empr==5) {
            // Con stock, para zapatillas
            $list = DB::select("
                select a.c_comb,a.n_item,a.c_prod,d.n_stok,max(RTRIM(b.k_medi)) as k_medi,max(b.s_cant)  as s_cant,max(b.s_vent) as s_vent,max(b.n_prec) as n_prec,max(RTRIM(b.c_indi)) as c_indi,max(b.q_coci) as q_coci,max(b.q_envic) as q_envic,max(b.q_preparado) as q_preparado,max(RTRIM(b.l_obse)) as l_obse, max(b.c_vendd) as c_vend, max(c.l_vend) as l_vend,FORMAT(MAX(b.f_digi),'hh:mm tt') as f_digi
                from
                (select a.c_empr,a.c_sucu,a.c_comp,a.n_seri,a.n_comp,RTRIM(a.c_comb) as c_comb,max(RTRIM(a.n_item)) as n_item,max(RTRIM(a.c_prod)) as c_prod
                from PedItem a 
                where a.c_empr=:c_empr and a.c_sucu=:c_sucu and a.c_comp=:c_comp and a.n_seri=:n_seri and a.n_comp=:n_comp
                group by a.c_empr,a.c_sucu,a.c_comp,a.n_seri,a.n_comp,a.c_comb,n_item) a inner join PedItem b on a.c_empr=b.c_empr and a.c_sucu=b.c_sucu and a.c_comp=b.c_comp and a.n_seri=b.n_seri and a.n_comp=b.n_comp and a.c_comb=b.c_comb and a.n_item=b.n_item and a.c_prod=b.c_prod
                left join Vendedor c on c.c_empr=b.c_empr and c.c_alma=:c_alma and c.c_vend=b.c_vendd
                left join ProdStock d on d.c_empr=a.c_empr and d.c_sucu=a.c_sucu and d.c_alma=:c_alma1 and d.c_prod=a.c_prod
                group by a.c_comb,a.n_item,a.c_prod,d.n_stok
                order by CONVERT(INT, a.n_item) asc
                ",
                [
                    'c_empr'=>session('c_empr'),
                    'c_sucu'=>session('c_sucu'),
                    'c_alma'=>session('c_alma'),
                    'c_alma1'=>session('c_alma'),
                    'c_comp'=>'60',
                    'n_seri'=>str_pad(session('c_alma'), 4, '0', STR_PAD_LEFT),
                    'n_comp'=>$n_comp
                ]);
        }else{
            $list = DB::select("
                select a.c_comb,a.n_item,a.c_prod,max(RTRIM(b.k_medi)) as k_medi,max(b.s_cant)  as s_cant,max(b.s_vent) as s_vent,max(b.n_prec) as n_prec,max(RTRIM(b.c_indi)) as c_indi,max(b.q_coci) as q_coci,max(b.q_envic) as q_envic,max(b.q_preparado) as q_preparado,max(RTRIM(b.l_obse)) as l_obse, max(b.c_vendd) as c_vend, max(c.l_vend) as l_vend,FORMAT(MAX(b.f_digi),'hh:mm tt') as f_digi
                from
                (select a.c_empr,a.c_sucu,a.c_comp,a.n_seri,a.n_comp,RTRIM(a.c_comb) as c_comb,max(RTRIM(a.n_item)) as n_item,max(RTRIM(a.c_prod)) as c_prod
                from PedItem a 
                where a.c_empr=:c_empr and a.c_sucu=:c_sucu and a.c_comp=:c_comp and a.n_seri=:n_seri and a.n_comp=:n_comp
                group by a.c_empr,a.c_sucu,a.c_comp,a.n_seri,a.n_comp,a.c_comb,n_item) a inner join PedItem b on a.c_empr=b.c_empr and a.c_sucu=b.c_sucu and a.c_comp=b.c_comp and a.n_seri=b.n_seri and a.n_comp=b.n_comp and a.c_comb=b.c_comb and a.n_item=b.n_item and a.c_prod=b.c_prod
                left join Vendedor c on c.c_empr=b.c_empr and c.c_alma=:c_alma and c.c_vend=b.c_vendd
                group by a.c_comb,a.n_item,a.c_prod
                order by CONVERT(INT, a.n_item) asc
                ",
                [
                    'c_empr'=>session('c_empr'),
                    'c_sucu'=>session('c_sucu'),
                    'c_alma'=>session('c_alma'),
                    'c_comp'=>'60',
                    'n_seri'=>str_pad(session('c_alma'), 4, '0', STR_PAD_LEFT),
                    'n_comp'=>$n_comp
                ]);
        }


        return $list;
    }

    public function elimItem(Request $request){
        $c_empr = session('c_empr');
        $c_año = session('c_anio');
        $c_alma = session('c_alma');
        $c_sucu = session('c_sucu');
        $n_seri = str_pad($c_alma, 4, '0', STR_PAD_LEFT);
        $n_comp = $request->_Pedido['n_comp'];
        // $c_vend = session('usuario')->c_vend;

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
            return response()->json(['errors' => ['d_anul' => ['Pedido fue eliminado, no se puede anular producto !!!']]],422);
        }else if($pedido->q_pago == '1'){
            return response()->json(['errors' => ['q_pago' => ['Pedido ha sido pagado, no se puede anular producto !!!']]],422);
        }else if ($pedido->d_anul == '1') {
            return response()->json(['errors' => ['d_anul' => ['Pedido ha sido anulado, no se puede anular producto !!!']]],422);
        }elseif ($pedido->q_pago == '2' || $pedido->q_divi == '1') {
            return response()->json(['errors' => ['q_ocup' => ['Pedido tiene cuentas divididas, no se puede anular producto !!!']]],422);
        }

        // Grabamos en Base de Datos
        foreach ($request->_PedItem as $key => $value) {
            if ($value['s_cant1'] == $value['s_cant']) {
                // Peditem a modificar
                $PedItem = [];
            }else{
                // Peditem a modificar
                $PedItem = $value;
                $PedItem['s_cant'] = $PedItem['s_cant'] - $value['s_cant1'];
                $PedItem['s_bimp'] = $PedItem['s_cant'] * $PedItem['s_vent'];
            }

            // Grabamos en Peditem
            if( count($PedItem) == 0 ){
                // Items anulados por completo
                DB::transaction(function () use ($c_empr,$c_sucu,$c_alma,$n_seri,$request,$value) {
                    DB::insert("
                        select c_empr,c_almp as c_alma,c_comp,n_seri,n_comp,c_prod,n_item,k_medi,s_cant,s_vent,n_prec,c_indi,c_comb,c_usua,f_digi,q_coci,q_envic,q_preparado,l_obse,c_vendd into #tmp from PedItem where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp and n_item=:n_item
                        update #tmp set c_alma=IIF(rtrim(c_alma)='',:c_alma,c_alma)
                        insert into PedItemAnul select *,dbo._Getdate(),:l_obse as l_anul from #tmp 
                        ", ["c_empr"=>$c_empr, "c_sucu"=>$c_sucu, "n_seri"=>$n_seri, "n_comp"=>$request->_Pedido['n_comp'], "n_item"=>$value['n_item'],"l_obse"=>mb_strtoupper(trim($request->PedItem1["l_obse"]),'UTF-8'),"c_alma"=>$c_alma]);
                    \DB::delete("delete from PedItem where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp and n_item=:n_item
                        ", ["c_empr"=>$c_empr, "c_sucu"=>$c_sucu, "n_seri"=>$n_seri, "n_comp"=>$request->_Pedido['n_comp'], "n_item"=>$value['n_item']]);
                });
            }else{
                // Items anulados parcialmente
                DB::transaction(function () use ($c_empr,$c_sucu,$c_alma,$n_seri,$request,$value,$PedItem) {
                    DB::insert("
                        select c_empr,c_almp as c_alma,c_comp,n_seri,n_comp,c_prod,n_item,k_medi,s_cant,s_vent,n_prec,c_indi,c_comb,c_usua,f_digi,q_coci,q_envic,q_preparado,l_obse,c_vendd into #tmp from PedItem where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp and n_item=:n_item
                        update #tmp set c_alma=IIF(rtrim(c_alma)='',:c_alma,c_alma),s_cant=:s_cant
                        insert into PedItemAnul select *,dbo._Getdate(),:l_obse as l_anul from #tmp 
                        ", ["c_empr"=>$c_empr, "c_sucu"=>$c_sucu, "n_seri"=>$n_seri, "n_comp"=>$request->_Pedido['n_comp'], "n_item"=>$value['n_item'],"l_obse"=>mb_strtoupper(trim($request->PedItem1["l_obse"]),'UTF-8'),"s_cant"=>$value['s_cant1'],"c_alma"=>$c_alma]);
                    \DB::update("update PedItem set s_cant=:s_cant where c_empr=:c_empr and c_sucu=:c_sucu and n_seri=:n_seri and n_comp=:n_comp and n_item=:n_item
                    ",['s_cant'=> $PedItem['s_cant'],'c_empr'=>$c_empr,'c_sucu'=>$c_sucu,'n_seri'=>$n_seri,'n_comp'=> $request->_Pedido['n_comp'],'n_item'=> $PedItem['n_item']]);
                });  
            }
        }
        return ['success'=>'Pedido actualizado correctamente'];
    }
}
