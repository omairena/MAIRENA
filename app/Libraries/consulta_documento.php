<?php 
	/**
	 * 
	 */
	namespace App\Libraries;

	class Consulta_documentos
	{
		private $xmlCompleto, $array_seguridad, $client_id, $urlApi;
		function __construct($array_xml, $array_seguridad)
		{
			$this->xmlCompleto = $array_xml;
			$this->array_seguridad = $array_seguridad;
			if ($this->array_seguridad['client_id'] === 'api-stag') {
				$this->urlApi = 'https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1/';
			}else{
				$this->urlApi = 'https://api.comprobanteselectronicos.go.cr/recepcion/v1/';
			}
		}

		// Funcion principal que crea el token para poder realizar todas las peticiones
		// esta se alimenta solo con el id_config en BD necesita los parametros user del webservice,
		// clave del ws  y el client_id que si esta en produccion es api-prod y api-stag para sandbox
		function Generar_Token(){
			$string = "username=".$this->array_seguridad['credenciales_conexion']."&password=".urlencode($this->array_seguridad['clave_conexion'])."&grant_type=password&client_id=".$this->array_seguridad['client_id']."";
			$url = '';
			if ($this->array_seguridad['client_id'] === 'api-stag') {
				$url = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/token';
			}else{
				$url = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut/protocol/openid-connect/token';
			}
			$curl = curl_init();
			curl_setopt_array($curl, array(
  				CURLOPT_URL => "".$url,
  				CURLOPT_RETURNTRANSFER => true,
  				CURLOPT_ENCODING => "",
  				CURLOPT_MAXREDIRS => 10,
  				CURLOPT_TIMEOUT => 30,
  				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  				CURLOPT_CUSTOMREQUEST => "POST",
  				CURLOPT_POSTFIELDS => $string,
  				CURLOPT_HTTPHEADER => array(
    				"cache-control: no-cache",
    				"content-type: application/x-www-form-urlencoded",
    				"postman-token: c1016240-cf6f-fe54-67d6-587ad9b11c39"
  				),
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
  				$respuesta = "cURL Error #:" . $err;
  				return  $respuesta;
			}else{
				$response = json_decode($response);
				$respuesta = 'bearer '.$response->{'access_token'};
  				return $respuesta;
			}
		}

		function consultar_documentos($ptoken, $pclave){
			switch ($this->xmlCompleto['tipoDocumento']) {
				case '01':
					$url = $this->urlApi.'recepcion/'.$pclave;
				break;
				case '02':
					$url = $this->urlApi.'recepcion/'.$pclave;
				break;
				case '03':
					$url = $this->urlApi.'recepcion/'.$pclave;
				break;
				case '04':
					$url = $this->urlApi.'recepcion/'.$pclave;
				break;
		 		case '05':
		 			$consecutivo = $this->xmlCompleto['numero_consecutivo'];
		 			$url = $this->urlApi.'recepcion/'.$pclave.'-'.$consecutivo;
		 		break;
		 		case '06':
		 			$consecutivo = $this->xmlCompleto['numero_consecutivo'];
		 			$url = $this->urlApi.'recepcion/'.$pclave.'-'.$consecutivo;
		 		break;
		 		case '07':
		 			$consecutivo = $this->xmlCompleto['numero_consecutivo'];
		 			$url = $this->urlApi.'recepcion/'.$pclave.'-'.$consecutivo;
		 		break;
		 		case '08':
		 			$url = $this->urlApi.'recepcion/'.$pclave;
		 		break;
		 		case '09':
		 			$url = $this->urlApi.'recepcion/'.$pclave;
		 		break;
			} 
			$curl = curl_init();
			curl_setopt_array($curl, array(
  				CURLOPT_URL => "".$url,
  				CURLOPT_RETURNTRANSFER => true,
  				CURLOPT_ENCODING => "",
  				CURLOPT_MAXREDIRS => 10,
  				CURLOPT_TIMEOUT => 30,
		  		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  		CURLOPT_CUSTOMREQUEST => "GET",
  				CURLOPT_HTTPHEADER => array(
    				"authorization: ".$ptoken,
   			 		"cache-control: no-cache",
    				"content-type: application/x-www-form-urlencoded",
    				"postman-token: 65becdcc-f3f2-8598-f38a-9f6d9adb803a"
  				),
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			$info = curl_getinfo($curl, CURLOPT_HTTPHEADER);
			curl_close($curl);
			if ($err) {
				$respuesta = "cURL Error #:" . $err . " Info: " . $info;
  				return  $respuesta;
  			} else {
  				return $response;
			}
		}

		function consulta_generica(){
			$token = $this->Generar_Token();
			$consulta_doc = $this->consultar_documentos($token, $this->xmlCompleto['clave']);
			return $consulta_doc;			
		}	
	}

 ?>