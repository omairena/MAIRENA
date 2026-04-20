<?php
	/**
	 *
	 */
	function Enviar_documentos($xml, $seguridad)
	{
        if(\Auth::check()){

            $envio = new Consulta_documentos($xml,$seguridad);
		    $doc = $envio->consulta_generica();
		    return $doc;

        } else {

            $envio = new Consulta_documentos($xml,$seguridad);
		    $doc = $envio->consulta_generica();
        }

	}

	class Consulta_documentos
	{
		private $xmlCompleto, $array_seguridad, $client_id, $urlApi;
		function __construct($array_xml, $array_seguridad)
		{
			$this->xmlCompleto = $array_xml;
			$this->array_seguridad = $array_seguridad;
			if ($this->array_seguridad['client_id'] === 'api-stag') {
			//	$this->urlApi = 'https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1/';
				$this->urlApi = 'https://api-sandbox.comprobanteselectronicos.go.cr/recepcion/v1/';
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
				if(!empty($response->{'access_token'})){
				$respuesta = 'bearer '.$response->{'access_token'};
					return $respuesta;
				}

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
                case '10':
                    $consecutivo = $this->xmlCompleto['numero_consecutivo'];
					$url = $this->urlApi.'recepcion/'.$pclave;
					//$url = $this->urlApi.'recepcion/'.$pclave.'-'.$consecutivo;
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
			$consult = json_decode($consulta_doc);
		//	dd( $token);
            if(\Auth::check()){

                $idconfigfact = \Auth::user()->idconfigfact;

            } else {

                $idconfigfact =  $this->array_seguridad['idconfigfact'];
            }
			if (!is_null($consult)) {
				file_put_contents('prueba.json', $consult);
				if (isset($consult->{'ind-estado'})) {
					switch ($consult->{'ind-estado'}) {
					case 'aceptado':
					switch ($this->xmlCompleto['tipoDocumento']) {
						case '01':
							$respuesta_xml = "./XML/".$idconfigfact."/Facturas/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/Facturas/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);

							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						//	App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
						//	);
						if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
						case '02':
							$respuesta_xml = "./XML/".$idconfigfact."/NotaDebito/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/NotaDebito/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							//App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
						//	);
						if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
						case '03':
							$respuesta_xml = "./XML/".$idconfigfact."/NotaCredito/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/NotaCredito/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							//App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							//);
							if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
						case '04':
							$respuesta_xml = "./XML/".$idconfigfact."/Tiquete/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/Tiquete/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);

							if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}

						break;
						case '05':

                            $respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/aceptados/".$consult->{'clave'}."_respuesta.xml";

                            if ($this->xmlCompleto['comando'] == '1') {
                                file_put_contents(public_path("./XML/".$idconfigfact."/DocReceptor/Respuesta/aceptados/".$consult->{'clave'}."_respuesta.xml"), base64_decode($consult->{'respuesta-xml'}));
							    $strContents = file_get_contents(public_path($respuesta_xml));
                            } else {
							    file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/aceptados/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							    $strContents = file_get_contents($respuesta_xml);
                            }
                            $strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'aceptado', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
                        break;
						case '06':
							$respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/parcialAceptados/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/parcialAceptados/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'aceptado', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
						break;
						case '07':
							$respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/rechazados/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/rechazados/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'aceptado', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
						break;
						case '08':
							$respuesta_xml = "./XML/".$idconfigfact."/FacturaCompra/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/FacturaCompra/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							);
						break;
						case '09':
							$respuesta_xml = "./XML/".$idconfigfact."/FacturaExportacion/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/FacturaExportacion/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							//App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							//);
							if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
						case '10':
							$respuesta_xml 	= "./XML/".$idconfigfact."/ReciboPago/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/ReciboPago/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents 	= file_get_contents($respuesta_xml);
							$strDatas 		= $this->Xml2Array($strContents);
							if (isset($strDatas['MensajeHacienda'])) {
								$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
								App\Facelectron::where('clave', $this->xmlCompleto['clave'])
								->update(['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
								$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);
								App\Facelectron::where('clave', $this->xmlCompleto['clave'])
								->update(['estatushacienda' => 'aceptado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
					}
				break;
				case 'recibido':
					switch ($this->xmlCompleto['tipoDocumento']) {
    case '01':
        $respuesta_xml = "./XML/" . $idconfigfact . "/Facturas/Respuesta/" . $consult->{'clave'} . "_respuesta.xml";

        // Verifica si 'respuesta-xml' existe y no está vacío antes de intentar decodificar y guardar
        if (isset($consult->{'respuesta-xml'}) && !empty($consult->{'respuesta-xml'})) {
            // Decodificar y almacenar el archivo
            file_put_contents($respuesta_xml, base64_decode($consult->{'respuesta-xml'}));

            // Leer el contenido del archivo recién creado
            $strContents = file_get_contents($respuesta_xml);
            $strDatas = $this->Xml2Array($strContents);
            $DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);

            // Actualizar la base de datos
            App\Facelectron::where('clave', $this->xmlCompleto['clave'])
                ->update([
                    'estatushacienda' => 'recibido',
                    'mensajehacienda' => $DetalleMensaje,
                    'respuesta_xml' => $respuesta_xml,
                    'pendiente' => 1
                ]);
        } else {
            // Manejar la falta de información de respuesta-xml
            // Por ejemplo, registrar un error o lanzar una excepción
            error_log("No se recibió 'respuesta-xml' para la clave: " . $consult->{'clave'});
            // O podrías lanzar una excepción
            //throw new Exception("No se recibió respuesta del API para la clave: " . $consult->{'clave'});
        }
        break;
						case '02':
							$respuesta_xml = "./XML/".$idconfigfact."/NotaDebito/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/NotaDebito/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'recibido', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							);
						break;
						case '03':
						$respuesta_xml = "./XML/".$idconfigfact."/NotaCredito/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							//file_put_contents("./XML/".$idconfigfact."/NotaCredito/Respuesta/respuesta-#".$consult->{'clave'}.".xml", base64_decode($consult->{'respuesta-xml'}));
							//$strContents = file_get_contents($respuesta_xml);
							//$strDatas = $this->Xml2Array($strContents);
							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							//App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'recibido', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							//);
							// Verifica si 'respuesta-xml' existe y no está vacío antes de intentar decodificar y guardar
        if (isset($consult->{'respuesta-xml'}) && !empty($consult->{'respuesta-xml'})) {
            // Decodificar y almacenar el archivo
            file_put_contents($respuesta_xml, base64_decode($consult->{'respuesta-xml'}));

            // Leer el contenido del archivo recién creado
            $strContents = file_get_contents($respuesta_xml);
            $strDatas = $this->Xml2Array($strContents);
            $DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);

            // Actualizar la base de datos
            App\Facelectron::where('clave', $this->xmlCompleto['clave'])
                ->update([
                    'estatushacienda' => 'recibido',
                    'mensajehacienda' => $DetalleMensaje,
                    'respuesta_xml' => $respuesta_xml,
                    'pendiente' => 1
                ]);
        } else {
            // Manejar la falta de información de respuesta-xml
            // Por ejemplo, registrar un error o lanzar una excepción
            error_log("No se recibió 'respuesta-xml' para la clave: " . $consult->{'clave'});
            // O podrías lanzar una excepción
            //throw new Exception("No se recibió respuesta del API para la clave: " . $consult->{'clave'});
        }
						break;
						case '04':
							$respuesta_xml = "./XML/".$idconfigfact."/Tiquete/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							//file_put_contents("./XML/".$idconfigfact."/Tiquete/Respuesta/respuesta-#".$consult->{'clave'}.".xml", base64_decode($consult->{'respuesta-xml'}));
							//$strContents = file_get_contents($respuesta_xml);
							//$strDatas = $this->Xml2Array($strContents);
							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							//App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'recibido', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							//);
							// Verifica si 'respuesta-xml' existe y no está vacío antes de intentar decodificar y guardar
        if (isset($consult->{'respuesta-xml'}) && !empty($consult->{'respuesta-xml'})) {
            // Decodificar y almacenar el archivo
            file_put_contents($respuesta_xml, base64_decode($consult->{'respuesta-xml'}));

            // Leer el contenido del archivo recién creado
            $strContents = file_get_contents($respuesta_xml);
            $strDatas = $this->Xml2Array($strContents);
            $DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);

            // Actualizar la base de datos
            App\Facelectron::where('clave', $this->xmlCompleto['clave'])
                ->update([
                    'estatushacienda' => 'recibido',
                    'mensajehacienda' => $DetalleMensaje,
                    'respuesta_xml' => $respuesta_xml,
                    'pendiente' => 1
                ]);
        } else {
            // Manejar la falta de información de respuesta-xml
            // Por ejemplo, registrar un error o lanzar una excepción
            error_log("No se recibió 'respuesta-xml' para la clave: " . $consult->{'clave'});
            // O podrías lanzar una excepción
            //throw new Exception("No se recibió respuesta del API para la clave: " . $consult->{'clave'});
        }
						break;
						case '05':
						$respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/aceptados/".$consult->{'clave'}."_respuesta.xml";
						file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/aceptados/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
						$strContents = file_get_contents($respuesta_xml);
						$strDatas = $this->Xml2Array($strContents);
						$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'recibido', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
						break;
						case '06':
						$respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/parcialAceptados/".$consult->{'clave'}."_respuesta.xml";
						file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/parcialAceptados/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
						$strContents = file_get_contents($respuesta_xml);
						$strDatas = $this->Xml2Array($strContents);
						$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'recibido', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
						break;
						case '07':
						$respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/rechazados/".$consult->{'clave'}."_respuesta.xml";
						file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/rechazados/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
						$strContents = file_get_contents($respuesta_xml);
						$strDatas = $this->Xml2Array($strContents);
						$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'recibido', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
						break;
						case '08':
						$respuesta_xml = "./XML/".$idconfigfact."/FacturaCompra/Respuesta/".$consult->{'clave'}."_respuesta.xml";
						file_put_contents("./XML/".$idconfigfact."/FacturaCompra/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
						$strContents = file_get_contents($respuesta_xml);
						$strDatas = $this->Xml2Array($strContents);
						$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'recibido', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							);
						break;
						case '09':
							$respuesta_xml = "./XML/".$idconfigfact."/FacturaExportacion/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/FacturaExportacion/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'recibido', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							);
						break;
						case '10':
							$respuesta_xml = "./XML/" . $idconfigfact . "/ReciboPago/Respuesta/" . $consult->{'clave'} . "_respuesta.xml";
							file_put_contents("./XML/" . $idconfigfact . "/ReciboPago/Respuesta/" . $consult->{'clave'} . "_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(['estatushacienda' => 'recibido', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]);
						break;
					}
				break;
				case 'rechazado':
					switch ($this->xmlCompleto['tipoDocumento']) {
						case '01':
							$find = App\Facelectron::where('clave', $this->xmlCompleto['clave'])->get();
							$sale = App\Sales::find($find[0]->idsales);
							if ($sale->condicion_venta === '02') {
								$cli_cxcobrar = App\Cxcobrar::where('idcliente', $sale->idcliente)->get();
								$restando = $cli_cxcobrar[0]->saldo_cuenta - $sale->total_comprobante;
								$restando2 = $cli_cxcobrar[0]->cantidad_dias - $sale->p_credito;
                				$mcxcobrar = App\Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $restando,'cantidad_dias' => $restando2]);
                				App\Mov_cxcobrar::where('idmovcxcobrar', $sale->idmovcxcobrar)->update(['estatus_mov' => 3]);
							}
							$respuesta_xml = "./XML/".$idconfigfact."/Facturas/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/Facturas/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);

							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							//App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							//);
							if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
						case '02':
							$respuesta_xml = "./XML/".$idconfigfact."/NotaDebito/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/NotaDebito/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							//App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							//);
							if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
						case '03':
						$respuesta_xml = "./XML/".$idconfigfact."/NotaCredito/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/NotaCredito/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						//	App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							//);
							if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
						case '04':
							$find = App\Facelectron::where('clave', $this->xmlCompleto['clave'])->get();
							$sale = App\Sales::find($find[0]->idsales);
							if ($sale->condicion_venta === '02') {
								$cli_cxcobrar = App\Cxcobrar::where('idcliente', $sale->idcliente)->get();
								$restando = $cli_cxcobrar[0]->saldo_cuenta - $sale->total_comprobante;
								$restando2 = $cli_cxcobrar[0]->cantidad_dias - $sale->p_credito;
                				$mcxcobrar = App\Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $restando,'cantidad_dias' => $restando2]);
                				App\Mov_cxcobrar::where('idmovcxcobrar', $sale->idmovcxcobrar)->update(['estatus_mov' => 3]);
							}
							$respuesta_xml = "./XML/".$idconfigfact."/Tiquete/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/Tiquete/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							//App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							//->update(
							//	['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							//);
							if (isset($strDatas['MensajeHacienda'])) {
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}else{
							    	$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['ns2:MensajeHacienda']['ns2:DetalleMensaje']);

							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]	);
							}
						break;
						//case '05':
						//$respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/aceptados/respuesta-#".$consult->{'clave'}.".xml";
						//file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/aceptados/respuesta-#".$consult->{'clave'}.".xml", base64_decode($consult->{'respuesta-xml'}));
						//$strContents = file_get_contents($respuesta_xml);
						//$strDatas = $this->Xml2Array($strContents);
						//$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						//App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'rechazado', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
						//	break;
							case '05':

                            $respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/aceptados/".$consult->{'clave'}."_respuesta.xml";

                            if ($this->xmlCompleto['comando'] == '1') {
    $rutaXml = public_path("XML/$idconfigfact/DocReceptor/Respuesta/aceptados/{$consult->{'clave'}}.xml");
    $contenido = base64_decode($consult->{'respuesta-xml'});

    file_put_contents($rutaXml, $contenido);

    if (file_exists($rutaXml)) {
        $strContents = file_get_contents($rutaXml);
    } else {
        // fallback si no se creó
        $strContents = ''; // o algún valor predeterminado
    }
} else {
    $rutaXml = public_path("./XML/$idconfigfact/DocReceptor/Respuesta/aceptados/{$consult->{'clave'}}.xml");
    $contenido = base64_decode($consult->{'respuesta-xml'});

    file_put_contents($rutaXml, $contenido);

    if (file_exists($rutaXml)) {
        $strContents = file_get_contents($rutaXml);
    } else {
        $strContents = ''; // fallback
    }
}
                            $strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'rechazado', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
                        break;


						case '06':
						$respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/parcialAceptados/".$consult->{'clave'}."_respuesta.xml";
						file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/parcialAceptados/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
						$strContents = file_get_contents($respuesta_xml);
						$strDatas = $this->Xml2Array($strContents);
						$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'rechazado', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
						break;
						case '07':
						$respuesta_xml = "./XML/".$idconfigfact."/DocReceptor/Respuesta/rechazados/".$consult->{'clave'}."_respuesta.xml";
						file_put_contents("./XML/".$idconfigfact."/DocReceptor/Respuesta/rechazados/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
						$strContents = file_get_contents($respuesta_xml);
						$strDatas = $this->Xml2Array($strContents);
						$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['xml_respuesta' => $respuesta_xml, 'estatus_hacienda' => 'rechazado', 'respuesta_hacienda' => $DetalleMensaje, 'pendiente' => 1]);
						break;
						case '08':
						$respuesta_xml = "./XML/".$idconfigfact."/FacturaCompra/Respuesta/".$consult->{'clave'}."_respuesta.xml";
						file_put_contents("./XML/".$idconfigfact."/FacturaCompra/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
						$strContents = file_get_contents($respuesta_xml);
						$strDatas = $this->Xml2Array($strContents);
						$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
						App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							);
						break;
						case '09':
							$respuesta_xml = "./XML/".$idconfigfact."/FacturaExportacion/Respuesta/".$consult->{'clave'}."_respuesta.xml";
							file_put_contents("./XML/".$idconfigfact."/FacturaExportacion/Respuesta/".$consult->{'clave'}."_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]
							);
						break;
						case '10':
							$respuesta_xml = "./XML/" . $idconfigfact . "/ReciboPago/Respuesta/" . $consult->{'clave'} . "_respuesta.xml";
							file_put_contents("./XML/" . $idconfigfact . "/ReciboPago/Respuesta/" . $consult->{'clave'} . "_respuesta.xml", base64_decode($consult->{'respuesta-xml'}));
							$strContents = file_get_contents($respuesta_xml);
							$strDatas = $this->Xml2Array($strContents);
							$DetalleMensaje = $this->Limpiar_Mensaje($strDatas['MensajeHacienda']['DetalleMensaje']);
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(['estatushacienda' => 'rechazado', 'mensajehacienda' => $DetalleMensaje, 'respuesta_xml' => $respuesta_xml, 'pendiente' => 1]);
						break;
					}
				break;
				case 'procesando':
					switch ($this->xmlCompleto['tipoDocumento']) {
						case '01':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'procesando']
							);
						break;
						case '02':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'procesando']
							);
						break;
						case '03':
						App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'procesando']
							);
						break;
						case '04':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'procesando']
							);
						break;
						case '05':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'procesando']);
						break;
						case '06':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'procesando']);
						break;
						case '07':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'procesando']);
						break;
						case '08':
						App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'procesando']
							);
						break;
						case '09':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'procesando']
							);
						break;
						case '10':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'procesando']
							);
						break;
					}
				break;
				default:
					switch ($this->xmlCompleto['tipoDocumento']) {
						case '01':
							$find = App\Facelectron::where('clave', $this->xmlCompleto['clave'])->get();
							$sale = App\Sales::find($find[0]->idsales);
							if ($sale->condicion_venta === '02') {
								$cli_cxcobrar = App\Cxcobrar::where('idcliente', $sale->idcliente)->get();
								$restando = $cli_cxcobrar[0]->saldo_cuenta - $sale->total_comprobante;
								$restando2 = $cli_cxcobrar[0]->cantidad_dias - $sale->p_credito;
                				$mcxcobrar = App\Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $restando,'cantidad_dias' => $restando2]);
                				App\Mov_cxcobrar::where('idmovcxcobrar', $sale->idmovcxcobrar)->update(['estatus_mov' => 3]);
							}
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '02':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '03':
						App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '04':
							$find = App\Facelectron::where('clave', $this->xmlCompleto['clave'])->get();
							$sale = App\Sales::find($find[0]->idsales);
							if ($sale->condicion_venta === '02') {
								$cli_cxcobrar = App\Cxcobrar::where('idcliente', $sale->idcliente)->get();
								$restando = $cli_cxcobrar[0]->saldo_cuenta - $sale->total_comprobante;
								$restando2 = $cli_cxcobrar[0]->cantidad_dias - $sale->p_credito;
                				$mcxcobrar = App\Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $restando,'cantidad_dias' => $restando2]);
                				App\Mov_cxcobrar::where('idmovcxcobrar', $sale->idmovcxcobrar)->update(['estatus_mov' => 3]);
							}
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '05':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'pendiente']);
						break;
						case '06':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'pendiente']);
						break;
						case '07':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'pendiente']);
						break;
						case '08':
						App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '09':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '10':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;	
					}
				break;
			}
			}else{
				switch ($this->xmlCompleto['tipoDocumento']) {
						case '01':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '02':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '03':
						App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '04':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '05':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'pendiente']);
						break;
						case '06':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'pendiente']);
						break;
						case '07':
						App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])->update(['estatus_hacienda' => 'pendiente']);
						break;
						case '08':
						App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '09':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
						break;
						case '10':
							App\Facelectron::where('clave', $this->xmlCompleto['clave'])
							->update(
								['estatushacienda' => 'pendiente']
							);
							break;
					}
			}

			}else{
				$consulta_doc = 'Error en consulta, consulta esta vacio';
			}
			return $consulta_doc;
		}

		function Xml2Array($contents, $get_attributes=1, $priority = 'tag') {
    		if(!$contents) return array();

    		if(!function_exists('xml_parser_create')) {
        		//print "'xml_parser_create()' function not found!";
        		return array();
    		}

    		//Get the XML parser of PHP - PHP must have this module for the parser to work
    		$parser = xml_parser_create('');
    		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    		xml_parse_into_struct($parser, trim($contents), $xml_values);
    		xml_parser_free($parser);

    		if(!$xml_values) return;//Hmm...

    		//Initializations
    		$xml_array = array();
    		$parents = array();
    		$opened_tags = array();
    		$arr = array();

   			$current = &$xml_array; //Refference

    		//Go through the tags.
    		$repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    		foreach($xml_values as $data) {
        		unset($attributes,$value);//Remove existing values, or there will be trouble

        		//This command will extract these variables into the foreach scope
        		// tag(string), type(string), level(int), attributes(array).
        		extract($data);//We could use the array by itself, but this cooler.

        		$result = array();
        		$attributes_data = array();

        		if(isset($value)) {
            		if($priority == 'tag') $result = $value;
           	 		else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        		}

        	//Set the attributes too.
        	if(isset($attributes) and $get_attributes) {
            	foreach($attributes as $attr => $val) {
                	if($priority == 'tag') $attributes_data[$attr] = $val;
                	else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
           	 	}
        	}

        	//See tag status and do the needed.
        	if($type == "open") {//The starting of the tag '<tag>'
            	$parent[$level-1] = &$current;
            	if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                	$current[$tag] = $result;
                	if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                		$repeated_tag_index[$tag.'_'.$level] = 1;

                		$current = &$current[$tag];

           	 		} else { //There was another element with the same tag name

                		if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    		$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    		$repeated_tag_index[$tag.'_'.$level]++;
                		} else {//This section will make the value an array if multiple tags with the same name appear together
                   	 		$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    		$repeated_tag_index[$tag.'_'.$level] = 2;

                   			if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        		$current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        		unset($current[$tag.'_attr']);
                    		}

                		}
                		$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                		$current = &$current[$tag][$last_item_index];
            		}

        	} elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            	//See if the key is already taken.
            	if(!isset($current[$tag])) { //New Key
                	$current[$tag] = $result;
                	$repeated_tag_index[$tag.'_'.$level] = 1;
                	if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
            	} else { //If taken, put all things inside a list(array)
                	if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                    	// ...push the new element into that array.
                    	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                    	if($priority == 'tag' and $get_attributes and $attributes_data) {
                        	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    	}
                    	$repeated_tag_index[$tag.'_'.$level]++;

                	} else { //If it is not an array...
                    	$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    	$repeated_tag_index[$tag.'_'.$level] = 1;
                    	if($priority == 'tag' and $get_attributes) {
                        	if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            	unset($current[$tag.'_attr']);
                       	 	}

                        	if($attributes_data) {
                            	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        	}
                    	}
                    	$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                	}
            	}

        	} elseif($type == 'close') { //End of tag '</tag>'
            	$current = &$parent[$level-1];
        	}
    	}
   			return($xml_array);
		}
function Limpiar_Mensaje($string){

 		if (!empty($string)) {

    	$string = trim($string);

    	$string = str_replace(
        	array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        	array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        	$string
    	);

    	$string = str_replace(
        	array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        	array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        	$string
    	);

   	 	$string = str_replace(
        	array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        	array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        	$string
    	);

    	$string = str_replace(
        	array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        	array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        	$string
    	);

    	$string = str_replace(
        	array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        	array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        	$string
    	);

    	$string = str_replace(
        	array('ñ', 'Ñ', 'ç', 'Ç'),
        	array('n', 'N', 'c', 'C',),
        	$string
    	);

    	$string = str_replace(
        	array("\\", "¨", "º", "-", "~",
             	"#", "@", "|", "!", "\"",
             	"·", "$", "%", "&", "/",
             	"(", ")", "?", "'", "¡",
             	"¿", "[", "^", "<code>", "]",
             	"+", "}", "{", "¨", "´",
             	">", "< ", ";", ",", ":",
             	".", " "),
        	' ',
        	$string
    	);
		return $string;
		}else{
			$string = 'Documento sin detalle mensaje';
			return $string;
		}
		}

	}

 ?>
