<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;

class SunatController extends Controller
{

    public function getCaptchaBase64()
    {
    	$_ch = curl_init();
        curl_setopt ($_ch, CURLOPT_RETURNTRANSFER, TRUE);
        $_ch = $_ch;

        curl_setopt ($_ch, CURLOPT_URL , 'http://www.sunat.gob.pe/cl-ti-itmrconsruc/captcha?accion=image' );
        curl_setopt ($_ch, CURLOPT_HEADER, 1);

        $result = curl_exec ($_ch);
        curl_close($_ch);
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        session(['cookies' => $cookies]);
        $xs = explode('Transfer-Encoding: chunked',$result);

        if( array_key_exists(1,$xs) == false ){
        	return response()->json([
                'errors' => [
                        'l_captcha' => ['No se pudo cargar captcha']
                    ]
                ],422);
        }

        $code = trim($xs[1]);
        $base64captcha = base64_encode($code);

        return ["base64captcha"=>$base64captcha];
    }

    public static function buscRuc($captcha,$n_ruc){
    	$captcha = mb_strtoupper($captcha, 'UTF-8');

        $_ch = curl_init();
        curl_setopt ($_ch, CURLOPT_RETURNTRANSFER, TRUE);
        $_ch = $_ch;

        //variables
        $captcha=$captcha;
        $ruc=$n_ruc;

        $_ch = $_ch;
        $vars = [
            'accion'=>'consPorRuc'
            ,'razSoc'=>''
            ,'nroRuc'=> $ruc
            ,'nrodoc'=>''
            ,'contexto'=>'ti-it'
            ,'modo'=>'1'
            ,'rbtnTipo'=>'1'
            ,'search1'=> $ruc
            ,'tipdoc'=>'1'
            ,'search2'=>''
            ,'search3'=>''
            ,'codigo'=> $captcha
            ];
        $qry_str = '';      
        $qry = [];
        foreach($vars as $key => $val ){$qry[] = $key.'='.$val; }
        $qry_str = implode('&',$qry);
        $cookie_str = '';
        $cks = [];
        foreach( session('cookies') as $key => $val ){ $cks[] = $key.'='.$val; }
        $cookie_str = implode('&',$cks);
         
        curl_setopt ($_ch, CURLOPT_URL , 'http://www.sunat.gob.pe/cl-ti-itmrconsruc/jcrS03Alias' );
        curl_setopt ($_ch, CURLOPT_POST, 1);
        curl_setopt ($_ch, CURLOPT_POSTFIELDS, $qry_str );
        curl_setopt ($_ch, CURLOPT_HTTPHEADER, array("Cookie: ".$cookie_str));
        $result = curl_exec($_ch);
        curl_close($_ch);
        $html = $result;

        //echo $html;

        //mis modificaciones
        $dom = HtmlDomParser::str_get_html($html);

        //buscando errores
        $error = $dom->find('body .cuerpo .error');
        if(count($error)>0){
            //return $error[0]->plaintext;
            //return \Response::json($error[0]->plaintext, 404);
            return response()->json([
                'errors' => [
                        'l_captcha' => [$error[0]->plaintext]
                    ]
                ],422);
        }

        //si no hay errores pasara a esta seccion del codigo
        $divs = $dom->find('body .container .row .list-group-item');
        $data = [];
        $n=0;

        foreach($divs as $div){
            $n++;
            if ($n==1) {
                //contenedor datos
                $h4s = $div->find('h4.list-group-item-heading');

                //etiqueta
                $tag=explode(':',$h4s[0]->plaintext);
                $tag=strtoupper(utf8_encode(trim($tag[0])));

                //contenido
                $cont = explode(' - ',$h4s[1]->plaintext);
                $value=utf8_encode(trim($cont[0]));

                //agregando a array data
                $data[ $tag ] = $value;
                $data['RAZON SOCIAL']=utf8_encode(trim($cont[1]));

            }else{
                //contenedor etiqueta item
                $title = $div->find('.list-group-item-heading');
                $tag=explode(':',$title[0]->plaintext);
                $tag=strtoupper(utf8_encode(trim($tag[0])));

                //contenedor contenido
                $value = $div->find('.list-group-item-text');
                $value = count($value)>0 ? $value[0]->plaintext : "" ;
                $value=substr(utf8_encode(trim($value)),0,100);

                if ($tag=="ESTADO") {
                    if ($value=="ACTIVO") {
                        $value="1";
                    }else{
                        $value="0";
                    }
                }elseif ($tag=="CONDICI&OACUTE;N") {
                    if ($value=="HABIDO") {
                        $value="1";
                    }else{
                        $value="0";
                    }
                }

                //agregando a array data
                $data[ $tag ] = preg_replace("/\s+/", " ", $value);//quitando espacion dobles
            }
        }

        if (count($data)==0) {
            return response()->json([
                'errors' => [
                        'l_captcha' => ['El número de RUC consultado no es válido. Verificar!!!']
                    ]
                ],422);
        }
        //return $data;
        return ['info'=>$data];
    }
}
