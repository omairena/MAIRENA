<?php
/**
 *
 */

function Timbrar_documentos($xml, $seguridad)
{
	$facturar = new Facelectron506($xml,$seguridad);
	$envio_doc = $facturar->envia_documento();
	return $envio_doc;
}

function Timbrar_receptor($xml, $seguridad)
{
	$receptor = new Facelectron506($xml,$seguridad);
	$envio_doc = $receptor->envia_documento_receptor();
	return $envio_doc;
}
class Facelectron506
{
	private $xmlCompleto, $array_seguridad, $config, $data, $certs, $name_space, $urlApi;
	function __construct($array_xml, $array_seguridad, array $config = [])
	{
		$this->xmlCompleto = $array_xml;
		$this->array_seguridad = $array_seguridad;
		if ($this->array_seguridad['client_id'] === 'api-stag') {
		//$this->urlApi = 'https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1/';
		$this->urlApi = 'https://api-sandbox.comprobanteselectronicos.go.cr/recepcion/v1/';
		//vieja url
		}else{
			$this->urlApi = 'https://api.comprobanteselectronicos.go.cr/recepcion/v1/';
		}

		if (!$config) {
		   	$config = [];
   		}

   		switch ($this->xmlCompleto['tipoDocumento']) {
      		case '01':
                $this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/facturaElectronica';
      		break;
      		case '02':
           		$this->name_space =  'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/notaDebitoElectronica';
      		break;
      		case '03':
           		$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/notaCreditoElectronica';
      		break;
      		case '04':
                $this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/tiqueteElectronico';
      		break;
      		case '05':
      			$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/mensajeReceptor';
        	break;
        	case '06':
        		$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/mensajeReceptor';
        	break;
        	case '07':
        		$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/mensajeReceptor';
        	break;
        	case '08':
      			$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/facturaElectronicaCompra';
        	break;
        	case '09':
      			$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/facturaElectronicaExportacion';
        	break;
            case '10':
                $this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/reciboElectronicoPago';
            break;
    	}
		//Esta parte se debe traer de base de datos la ruta de donde esta guardado el certificado .p12 para poder adquirir lo que tiene dentro si se crea una carpeta en el servidor.
			//	dd($array_seguridad);
			$data = file_get_contents($array_seguridad['certificado']);
		   	$this->config = array_merge([
							   'file' => '',
							   'pass' => ''.$array_seguridad['clave_certificado'],
							   'data' => ''.$data,
   							], $config);
		   // cargar firma electrónica desde el contenido del archivo .p12 si no
		   // se pasaron como datos del arreglo de configuración
   		   if (!$this->config['data'] and $this->config['file']) {
   				if (is_readable($this->config['file'])) {
   					$this->config['data'] = file_get_contents($this->config['file']);
   					//$this->config['data'] = file_get_contents(public_path('archivo.pem'));
   				} else {
   					return $this->error('Archivo de la firma electrónica '.basename($this->config['file']).' no puede ser leído');
   				}
   			}
   			// leer datos de la firma electrónica
            //dd(openssl_x509_parse(openssl_x509_read($this->config['data']), $this->config['pass']) );

   			if ($this->config['data'] and openssl_pkcs12_read($this->config['data'], $this->certs, $this->config['pass'])===false) {
   				//return $this->exception('No fue posible leer los datos de la firma electrónica (verificar la contraseña)');
   				throw new \Exception('No fue posible leer los datos de la firma electrónica (verificar la contraseña)');
   			}
   			$this->data = openssl_x509_parse($this->certs['cert']);
   			$this->serial = $this->data['serialNumber'];
   			// quitar datos del contenido del archivo de la firma
   			unset($this->config['data']);
	}
	//Comienzo de funciones Genericas necesarias para todo el proceso de firmado y facturacion

	public function getModulus(){
	   	$details = openssl_pkey_get_details(openssl_pkey_get_private($this->certs['pkey']));
	   	return base64_encode($details['rsa']['n']);
	}

	public function getExponent(){
	   	$details = openssl_pkey_get_details(openssl_pkey_get_private($this->certs['pkey']));
	   	return base64_encode($details['rsa']['e']);
	}


	public function sign($data, $signature_alg = "sha256WithRSAEncryption"){
		if (openssl_sign($data, $signature, $this->certs['pkey'], $signature_alg)==false) {
		   	return $this->error('No fue posible firmar los datos');
		}
		return base64_encode($signature);
   	}

	function getGUID(){
    	if (function_exists('com_create_guid')){
        	return com_create_guid();
    	}else{
        	mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        	$charid = strtoupper(md5(uniqid(rand(), true)));
        	$hyphen = chr(45);// "-"
        	$uuid = chr(123)// "{"
            	.substr($charid, 0, 8).$hyphen
            	.substr($charid, 8, 4).$hyphen
            	.substr($charid,12, 4).$hyphen
            	.substr($charid,16, 4).$hyphen
            	.substr($charid,20,12)
            	.chr(125);// "}"
        	return $uuid;
    	}
	}

	function LinealizarOutput($output){
		$output = preg_replace('/\t{1,}/', ' ', $output);
		$output = preg_replace('/\n{1,}/', ' ', $output);
		$output = preg_replace('/\r{1,}/', ' ', $output);
		$output = preg_replace('/\s{1,}/', ' ', $output);
		$output = str_replace('> <', '><', $output);
		$output = str_replace(' >', '>', $output);
		return $output;
	}

	function Normalizar_Certificado(){
        $envio_cert = str_replace("-----BEGIN CERTIFICATE-----".chr(10), '', $this->certs['cert']);
        $envio_cert = str_replace(chr(10)."-----END CERTIFICATE-----", '', $envio_cert);
        $envio_cert = str_replace(chr(10), '', $envio_cert);
        return $envio_cert;
    }

    /*function ModificarEntero($pvalor){
		$valor = number_format($pvalor,5,',','.');
		$valor = str_replace('.','',$valor);
		$valor = str_replace(',','.', $valor);
		return $valor;
	}*/

	function ModificarEntero($pvalor){
		// Convertimos a float
		$pvalor = floatval($pvalor);
		
		// Formateamos con 5 decimales
		$valor = number_format($pvalor, 5, ',', '.');
		
		// Eliminamos los puntos y reemplazamos la coma por punto
		$valor = str_replace('.', '', $valor);
		$valor = str_replace(',', '.', $valor);
		
		return $valor;
	}

	function ConviertePhone($output){
		$search = array('+', '(', ')', ' ');
		$replace = array('', '','','');
		$phone = str_replace($search, $replace, $output);
		return $phone;
	}

    function validarCorreosElectronicos(array $correos) {
		$maxCorreos = 4;
		$correosValidos = [];

		// Expresión regular para validar el correo electrónico
		$patron = '/^\s*(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()\[\]\.,;:\s@\"]+\.)+[^<>()\[\]\.,;:\s@\"]{0,})\s*$/';

		foreach ($correos as $correo) {
			if (count($correosValidos) >= $maxCorreos) {
				break; // Ya alcanzamos el máximo permitido
			}
			if (preg_match($patron, $correo)) {
				$correosValidos[] = $correo; // Agregar solo correos válidos
			} else {
				// Aquí puedes manejar el error según sea necesario
				throw new InvalidArgumentException("El correo '{$correo}' no cumple con el formato permitido.");
			}
		}

		return $correosValidos;
	}

    public function validarDetalleServicio($tipoOtrosCargos)
	{
		// Códigos que eximen la obligatoriedad
		$codigosExentos  = ['04', '08', '09', '10'];
		$nodoOtrosCargos = $this->xmlCompleto['usaOtrosCargos'];
		// Verificamos si el tipo de documento es uno de los exentos
		if (in_array($this->xmlCompleto['tipoDocumento'], ['FE', 'TE', 'NC', 'ND'])) {
			// Verificamos si hay un nodo de "Otros Cargos" y el tipo corresponde a los códigos exentos
			if ($nodoOtrosCargos && in_array($tipoOtrosCargos, $codigosExentos)) {
				// Si no hay líneas de servicio o producto, la validación se cumple
				if (isset($this->xmlCompleto['DetalleServicio']) && empty($this->xmlCompleto['DetalleServicio'])) {
					return true; // La validación se cumple, no se requiere el campo
				}
			}
		}
		// Si no se cumple alguna de las condiciones anteriores, la validación no se cumple
		return false; // El campo es obligatorio
	}

	//Funcion para crear la clave dinamica recibe varios parametros
	function Crea_clave_old(){

		$pais = '506'; //Corresponde al pais donde se esta ejecutando la clave
		$documento = $this->xmlCompleto['numeroFactura'];
		$cod_tipodoc = $this->xmlCompleto['tipoDocumento'];
		$emision = $this->xmlCompleto['fechaEmision'];
		if ($this->xmlCompleto['tipoDocumento'] != '08') {
		    	if ($this->xmlCompleto['tipoDocumento'] != '03') {
			$emisor = $this->xmlCompleto['Emisor']['Identificacion']['Numero'];
		    	}else{
		    	    if($this->xmlCompleto['InformacionReferencia']['TipoDocIR'] === '17'){
		    	        $emisor = $this->xmlCompleto['Receptor']['Identificacion']['Numero'];
		    	    }else{
		    	       	$emisor = $this->xmlCompleto['Emisor']['Identificacion']['Numero'];
		    	    }

		    	}
		}else{
			$emisor = $this->xmlCompleto['Receptor']['Identificacion']['Numero'];
		}

		$sucursal = $this->xmlCompleto['sucursal']; // tamaño 3 digitos correspondientes a la sucursal de donde proviene el documento
		$puntoVenta = $this->xmlCompleto['puntoVenta']; //tamaño 5 digitos correspondientes al terminal o punto de venta
		$situacion = $this->xmlCompleto['situacionComprobante']; // tamaño 1 digito corresponde a la situacion del documento 1 Normal 2 Contingencia y 3 Sin internet
		list($fecha,$restante)=explode("T",$emision);
		list($year,$mes,$dia)=explode("-",$fecha);
		$string = $pais.''.$dia.''.$mes.''.substr($year,2).''.str_pad($emisor, 12, "0", STR_PAD_LEFT).''.$sucursal.''.$puntoVenta.''.$cod_tipodoc.''.$documento.''.$situacion.''.substr($documento,2,10);
		return $string;
	}


	function Crea_clave() {
    $pais = '506'; // Corresponde al país donde se está ejecutando la clave
    $documento = $this->xmlCompleto['numeroFactura'];
    $cod_tipodoc = $this->xmlCompleto['tipoDocumento'];
    $emision = $this->xmlCompleto['fechaEmision'];
    
    if ($this->xmlCompleto['tipoDocumento'] != '08') {
        if ($this->xmlCompleto['tipoDocumento'] != '03') {
            $emisor = $this->xmlCompleto['Emisor']['Identificacion']['Numero'];
        } else {
            if ($this->xmlCompleto['InformacionReferencia']['TipoDocIR'] === '17') {
                $emisor = $this->xmlCompleto['Receptor']['Identificacion']['Numero'];
            } else {
                $emisor = $this->xmlCompleto['Emisor']['Identificacion']['Numero'];
            }
        }
    } else {
        $emisor = $this->xmlCompleto['Receptor']['Identificacion']['Numero'];
    }

    $sucursal = $this->xmlCompleto['sucursal']; // Tamaño 3 dígitos correspondientes a la sucursal
    $puntoVenta = $this->xmlCompleto['puntoVenta']; // Tamaño 5 dígitos del terminal o punto de venta
    $situacion = $this->xmlCompleto['situacionComprobante']; // Tamaño 1 dígito de situación del documento
    $codigo_seguridad = $this->generateSecurityCode($documento, $emision, 6571); // Generar código de seguridad con PIN
    list($fecha, $restante) = explode("T", $emision);
    list($year, $mes, $dia) = explode("-", $fecha);
    
    // Construir la cadena final
    $string = $pais . '' . $dia . '' . $mes . '' . substr($year, 2) 
            . '' . str_pad($emisor, 12, "0", STR_PAD_LEFT) 
            . '' . $sucursal . '' . $puntoVenta 
            . '' . $cod_tipodoc . '' . $documento 
            . '' . $situacion . '' . $codigo_seguridad;

    return $string;
}
private function generateSecurityCode($documento, $emision, $pin) {
    // Combina valores para crear una semilla única, incluyendo el PIN
    $seed = $documento . $emision . $pin; // Combinamos el documento, la emisión y el PIN
    mt_srand(crc32($seed)); // Inicializa el generador de números aleatorios con una semilla
    $randomNumber = mt_rand(10000000, 99999999); // Generar un número aleatorio de 8 dígitos
    return $randomNumber;
}

//$expectedCode = $this->generateSecurityCode($documento, $emision, 6571);
//if ($expectedCode === $codigoRecibido) {
    // Código válido
//} else {
    // Código no válido
//}


	function armar_consecutivo(){
		$sucursal = $this->xmlCompleto['sucursal']; // tamaño 3 digitos correspondientes a la sucursal de donde proviene el documento
		$puntoVenta = $this->xmlCompleto['puntoVenta']; //tamaño 5 digitos correspondientes al terminal o punto de venta
		$tipoDocumento = $this->xmlCompleto['tipoDocumento'];
		$numeroFactura = $this->xmlCompleto['numeroFactura'];
		$consecutivo = $sucursal.''.$puntoVenta.''.$tipoDocumento.''.$numeroFactura;
		return $consecutivo;
	}
    //Final creacion de variables Dinamicas para facturacion y firmado

    //Funciones CURL de consultas y envio contra Hacienda todos los procesos necesarios para hacienda

    // ************************** Comienzo de Funciones CURL ***********************

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
	//Envio del documento para Hacienda mediante CURL nuevamente
	function Envia_Doc($json,$token,$clave){
		$url = $this->urlApi."recepcion";
		$curl = curl_init();
			curl_setopt_array($curl, array(
  				CURLOPT_URL => $url,
  				CURLOPT_RETURNTRANSFER => true,
  				CURLOPT_ENCODING => "",
  				CURLOPT_MAXREDIRS => 10,
  				CURLOPT_TIMEOUT => 30,
  				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  				CURLOPT_CUSTOMREQUEST => "POST",
  				CURLOPT_POSTFIELDS => $json,
  				CURLOPT_HTTPHEADER => array(
    				"authorization: ".$token,
   					"cache-control: no-cache",
   					"content-type: application/json",
    				"postman-token: 689c8b8b-789b-94a3-ba89-607cb3338a5d"
  				),
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				$respuesta = "cURL Error #:".$err;
  				return $respuesta;
			}else{
				return $response;
			}
	}
	function Envia_Doc_Emisor($json,$token,$clave){
		$url = $this->urlApi."recepcion";
		$header = array(
			'Authorization: '.$token,
			'Content-Type: application/json'
		);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

		$respuesta = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$arrayResp = array(
			"Status" => $status,
			"text" => explode("\n", $respuesta)
		);
		curl_close($curl);
    	if ($status==400) {
      		return  $status;
    	} else {
			return $respuesta;
    	}
	}

	// Comienzo de proceso de logout
	function logout($ptoken){
	    if(!empty($ptoken)){
		$curl = curl_init();
		if ($this->array_seguridad['client_id'] === 'api-stag') {
			$url = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/logout';
			$client_id = "api-stag";
		}else{
			$url = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-prod/protocol/openid-connect/logout';

			$client_id = "api-prod";
		}
		$token = explode("bearer ",$ptoken);
		$post = "client_id=".$client_id."&refresh_token=".$token[1]."";
		curl_setopt_array($curl, array(
  			CURLOPT_URL => "".$url,
  			CURLOPT_RETURNTRANSFER => true,
  			CURLOPT_ENCODING => "",
  			CURLOPT_MAXREDIRS => 10,
  			CURLOPT_TIMEOUT => 30,
  			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  			CURLOPT_CUSTOMREQUEST => "POST",
  			CURLOPT_POSTFIELDS => $post,
  			CURLOPT_HTTPHEADER => array(
    			"Content-Type: application/x-www-form-urlencoded",
    			"Postman-Token: d9d25658-c3b0-47c9-a30e-11681b86f0b7",
    			"cache-control: no-cache"
  			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
  			return "cURL Error #:" . $err;
		} else {
  			return $response;
		}
	    }
	}
	// Fin del proceso del logout
    // Proceso a enviar el documento
	function envia_documento(){
		$clave = (string) $this->Crea_clave();
        if ($this->xmlCompleto['tipoDocumento'] != '10') {
		    $xml_ws = $this->armar_xml($clave);
        } else {
            $xml_ws = $this->armarRecibo($clave);
        }
		$array = new \stdClass();
		$array->clave =$clave;
		$array->fecha = (string) $this->xmlCompleto['fechaEmision'];
        $array->emisor = array(
			"tipoIdentificacion" => (string) $this->xmlCompleto['Emisor']['Identificacion']['Tipo'],
			"numeroIdentificacion" => (string) str_pad($this->xmlCompleto['Emisor']['Identificacion']['Numero'], 12, "0", STR_PAD_LEFT)
		);
		if ($this->xmlCompleto['tipoDocumento'] == '01' or $this->xmlCompleto['tipoDocumento'] == '08') {
			$array->receptor = array(
				"tipoIdentificacion" 	=> (string)  $this->xmlCompleto['Receptor']['Identificacion']['Tipo'],
				"numeroIdentificacion" 	=> (string) str_pad($this->xmlCompleto['Receptor']['Identificacion']['Numero'], 12, "0", STR_PAD_LEFT)
			);
		}

		$array->comprobanteXml = $xml_ws;
		$json                  = json_encode($array, true);
		$token                 = $this->Generar_Token();
		$curl_envio            = $this->Envia_Doc($json,$token,$clave);
		$this->logout($token);
		$this->Guardar($clave);
		return $curl_envio;
	}

	function Guardar($pclave)
	{
		$tipodocumento = $this->xmlCompleto['tipoDocumento'];
		$numero_documento = $this->xmlCompleto['numeroFactura'];
		$sales_id = $this->xmlCompleto['sales_id'];
		$idconfigfact = $this->xmlCompleto['idconfigfact'];

		switch ($this->xmlCompleto['tipoDocumento']) {
			case '01':
				$rutaxml = "./XML/".$idconfigfact."/Facturas/Envio/factura#".$pclave.".xml";
				$envcorreo=1;
			break;
			case '02':
				$rutaxml = "./XML/".$idconfigfact."/NotaDebito/Envio/NotaDebito#".$pclave.".xml";
				$envcorreo=1;
			break;
			case '03':
				$rutaxml = "./XML/".$idconfigfact."/NotaCredito/Envio/NotaCredito#".$pclave.".xml";
				$envcorreo=1;
			break;
			case '04':
				$rutaxml = "./XML/".$idconfigfact."/Tiquete/Envio/Tiquete#".$pclave.".xml";
				$envcorreo=1;
			break;
			case '08':
				$rutaxml = "./XML/".$idconfigfact."/FacturaCompra/Envio/FacturaCompra#".$pclave.".xml";
				$envcorreo=2;
			break;
			case '09':
				$rutaxml = "./XML/".$idconfigfact."/FacturaExportacion/Envio/FacturaExportacion#".$pclave.".xml";
				$envcorreo=1;
			break;
            case '09':
				$rutaxml = "./XML/".$idconfigfact."/FacturaExportacion/Envio/FacturaExportacion#".$pclave.".xml";
				$envcorreo=1;
			break;
            case '10':
				$rutaxml = "./XML/".$idconfigfact."/ReciboPago/Envio/ReciboPago#".$pclave.".xml";
				$envcorreo=1;
			break;
		}
		$fechahora = $this->xmlCompleto['fechaEmision'];
		$consecutivo = $this->armar_consecutivo();
		$cons = DB::select("SELECT * FROM `facelectron` WHERE idsales = '$sales_id' AND idconfigfact = '$idconfigfact'");
		if (empty($cons)) {
			$attributes = array(
            	'idsales' => $sales_id,
            	'idconfigfact' => $idconfigfact,
            	'tipodoc' => $tipodocumento,
            	'consecutivo' => $consecutivo,
            	'numdoc' => $numero_documento,
            	'clave' => $pclave,
            	'codigoHTTP' => '200',
            	'rutaxml' => $rutaxml,
            	'fechahora' => $fechahora,
            	'enviado_correo' => $envcorreo
        	);
        	App\Facelectron::create($attributes);
        	return TRUE;
		}else{
			return false;
		}

	}
	function Guardar_receptor($pclave)
	{
		$tipodocumento = $this->xmlCompleto['tipoDocumento'];
		$numero_documento = $this->xmlCompleto['numeroFactura'];
		$idconfigfact = $this->xmlCompleto['idconfigfact'];
		switch ($this->xmlCompleto['tipoDocumento']) {
			case '05':
				$rutaxml = "./XML/".$idconfigfact."/DocReceptor/Envio/aceptados/MensajeR#".$pclave.".xml";
			break;
			case '06':
				$rutaxml = "./XML/".$idconfigfact."/DocReceptor/Envio/parcialAceptados/MensajeR#".$pclave.".xml";
			break;
			case '07':
				$rutaxml = "./XML/".$idconfigfact."/DocReceptor/Envio/rechazados/MensajeR#".$pclave.".xml";
			break;
		}
		$consecutivo = $this->armar_consecutivo();
		App\Receptor::where('idreceptor', $this->xmlCompleto['idreceptor'])
			->update(
						['clave' => $pclave, 'consecutivo' => $consecutivo, 'xml_envio' => $rutaxml]
					);
        return TRUE;
	}
	// Proceso de envio de documento para mensaje receptor
	function envia_documento_receptor(){
		if ($this->xmlCompleto['comando'] == '1') {
			$xml = simplexml_load_file(public_path($this->xmlCompleto['rutaxml']));

		} else {
            $xml = simplexml_load_file($this->xmlCompleto['rutaxml']);
		}
		$fecha = $this->xmlCompleto['fechaEmision'];
		$tipoIdEmisor= $xml->Emisor->Identificacion->Tipo;
		$cedEmisor= $xml->Emisor->Identificacion->Numero;
		$tipoIdReceptor= $xml->Receptor->Identificacion->Tipo;
		$cedReceptor= $xml->Receptor->Identificacion->Numero;
		$clave_doc =  $xml->Clave;
		$consecutivo = $this->armar_consecutivo();
		$url= $this->urlApi.'recepcion/';
		$xml_ws = $this->armar_xml_receptor($clave_doc, $consecutivo);

		$array = new \stdClass();
		$array->clave = "".$clave_doc;
		$array->fecha = "".$fecha;
		$array->emisor = array(
			"tipoIdentificacion" => ''.$tipoIdEmisor,
			"numeroIdentificacion" => ''.$cedEmisor
		);
		$array->receptor = array(
			"tipoIdentificacion" => ''.$tipoIdReceptor,
			"numeroIdentificacion" => ''.$cedReceptor
		);
		$array->consecutivoReceptor = $consecutivo;
		$array->comprobanteXml = $xml_ws;
		$mensaje = json_encode($array, true);
		$token = $this->Generar_Token();
		$curl_envio = $this->Envia_Doc_Emisor($mensaje,$token,$clave_doc);
		$guardado = $this->Guardar_receptor($clave_doc);
		$logout = $this->logout($token);
		return $curl_envio;
	}
	// Fin del proceso de envio del documento
	// Armar xml de recibo de pago
	function armarRecibo($clave) {
        $xmlDoc = new DOMDocument('1.0' , 'UTF-8');
        libxml_use_internal_errors(true);
        $recibo = $xmlDoc->appendChild($xmlDoc->createElement("ReciboElectronicoPago"));
        $recibo->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
        $xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/reciboElectronicoPago'));
        $recibo->appendChild($xmlDoc->createAttribute("xmlns:xsd"))->appendChild(
			$xmlDoc->createTextNode('http://www.w3.org/2001/XMLSchema'));
		$recibo->appendChild($xmlDoc->createAttribute("xmlns:vc"))->appendChild(
   			$xmlDoc->createTextNode('http://www.w3.org/2007/XMLSchema-versioning'));

        //Agrego la clave al recibo de pago
        $recibo->appendChild($xmlDoc->createElement("Clave", $clave));

        //4.4 Se debe indicar el número de cedula de identificación del proveedor de sistemas que esté utilizando para la emisión de comprobantes electrónicos
        $recibo->appendChild($xmlDoc->createElement("ProveedorSistemas", $this->xmlCompleto['ProveedorSistemas']));

		//Numeración consecutiva del comprobante
		$recibo->appendChild($xmlDoc->createElement("NumeroConsecutivo", $this->armar_consecutivo()));

        //Fecha de Emision
		$recibo->appendChild($xmlDoc->createElement("FechaEmision", $this->xmlCompleto['fechaEmision']));

        //Comienzo del Nodo del Emisor
		$emisor = $recibo->appendChild($xmlDoc->createElement("Emisor"));

        //Datos Globales de la Seccion del Emisor aca comienza to do lo refente al emisor
		$emisor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Emisor']['Nombre']));

		//Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
		$identificacion_emisor = $emisor->appendChild($xmlDoc->createElement("Identificacion"));
		$identificacion_emisor->appendChild($xmlDoc->createElement("Tipo",  $this->xmlCompleto['Emisor']['Identificacion']['Tipo']));
		$identificacion_emisor->appendChild($xmlDoc->createElement("Numero",  $this->xmlCompleto['Emisor']['Identificacion']['Numero']));
        // Correos electronicos, ahora permite un array de 4, creo una funcion que cumpla con lo solicitado
        $correos = $this->xmlCompleto['Emisor']['CorreoElectronico']; // Suponiendo que es un array
        $correosValidos = $this->validarCorreosElectronicos($correos);

        foreach ($correosValidos as $correo) {
            $emisor->appendChild($xmlDoc->createElement("CorreoElectronico", $correo));
        }

        $receptor = $recibo->appendChild($xmlDoc->createElement("Receptor"));

        //Datos Globales de la Seccion del Receptor aca comienza todo lo refente al receptor
        $receptor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Receptor']['Nombre']));

        //Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
        $identificacion_receptor = $receptor->appendChild($xmlDoc->createElement("Identificacion"));
        $identificacion_receptor->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['Receptor']['Identificacion']['Tipo']));
        $identificacion_receptor->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['Receptor']['Identificacion']['Numero']));
        //Fin de los tipo de identificacion

		//Comienzo de condicion venta 4.4
		$recibo->appendChild($xmlDoc->createElement("CondicionVenta", $this->xmlCompleto['CondicionVenta']));
        if (isset($this->xmlCompleto['PlazoCredito']) && !empty($this->xmlCompleto['PlazoCredito'])) {
			$recibo->appendChild($xmlDoc->createElement("PlazoCredito", $this->xmlCompleto['PlazoCredito']));
        }
        $detalle_servicio = $recibo->appendChild($xmlDoc->createElement("DetalleServicio"));
		//Inicio las lineas detalles en 1
		$numeroLinea = 1;
		for ($i=0; $i < count($this->xmlCompleto['DetalleServicio']); $i++) {
			$linea_detalle = $detalle_servicio->appendChild($xmlDoc->createElement("LineaDetalle"));
			$linea_detalle->appendChild($xmlDoc->createElement("NumeroLinea", $numeroLinea));
            //Detalle texto informativo del producto
			$linea_detalle->appendChild($xmlDoc->createElement("Detalle", $this->xmlCompleto['DetalleServicio'][$i]['Detalle']));
            $linea_detalle->appendChild($xmlDoc->createElement("MontoTotal", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['MontoTotal'])));
			//Subtotal del detalle
			$linea_detalle->appendChild($xmlDoc->createElement("SubTotal", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['SubTotal'])));
            //Inicio de Impuesto
            if (!empty($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'])) {
                for ($impto=0; $impto < count($this->xmlCompleto['DetalleServicio'][$i]['Impuesto']); $impto++) {
                    $impuesto = $linea_detalle->appendChild($xmlDoc->createElement("Impuesto"));
                    $impuesto->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo']));

                    //Validacion de si el codigo de impuesto es 99 describir el codigo preciso
                    if ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo'] == '99') {
                        $impuesto->appendChild($xmlDoc->createElement("CodigoImpuestoOTRO", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoImpuestoOTRO']));
                    }
                    //Validacion dependiendo del codigo de impuesto se aplica la tarifa IVA solo aplica 01 07
                    switch ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo']) {
                        case '01':
                            $impuesto->appendChild($xmlDoc->createElement("CodigoTarifaIVA", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoTarifaIVA']));
                        break;
                        case '07':
                            $impuesto->appendChild($xmlDoc->createElement("CodigoTarifaIVA", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoTarifaIVA']));
                        break;
                    }
                    if (!empty($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Tarifa'])) {
                        //Tarifa siempre la agregamos
                        $impuesto->appendChild($xmlDoc->createElement("Tarifa", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Tarifa']));
                    }
                    if (!empty($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Tarifa'])) {

                        //Validacion para cuando sea 08 el tipo de impuesto
                        if ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo'] === '08') {
                            $impuesto->appendChild($xmlDoc->createElement("FactorCalculoIVA", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['FactorCalculoIVA']));
                        }
                    }
                    $impuesto->appendChild($xmlDoc->createElement("Monto", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Monto'])));
                }
            }

            $linea_detalle->appendChild($xmlDoc->createElement("ImpuestoNeto", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['ImpuestoNeto'])));
            $linea_detalle->appendChild($xmlDoc->createElement("MontoTotalLinea", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['MontoTotalLinea'])));
            $numeroLinea++;
        }

        //Comienzo de los totales, Toda la informacion del resumen de los diferentes tipos de documentos
		//Nivel Principal resumen_factura
		$resumen_factura = $recibo->appendChild($xmlDoc->createElement("ResumenFactura"));
        //Nivel 2 para codigo moneda
        $codigo_tipo_moneda = $resumen_factura->appendChild($xmlDoc->createElement("CodigoTipoMoneda"));
        $codigo_tipo_moneda->appendChild($xmlDoc->createElement("CodigoMoneda", $this->xmlCompleto['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']));
        $codigo_tipo_moneda->appendChild($xmlDoc->createElement("TipoCambio", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'])));
        //Fin Nivel 2 para codigo de moneda
		$resumen_factura->appendChild($xmlDoc->createElement("TotalVenta", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalVenta'])));
		$resumen_factura->appendChild($xmlDoc->createElement("TotalVentaNeta", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalVentaNeta'])));

        //Comienzo del desglose de impuestos, ahora es un ciclo array de 1 a  1000 que es cada impuesto cobrado.
        if (!empty($this->xmlCompleto['ResumenFactura']['TotalDesgloseImpuesto']) && isset($this->xmlCompleto['ResumenFactura']['TotalDesgloseImpuesto'])) {
            // Inicializa un array para acumular los montos por Codigo y CodigoTarifaIVA
            $montosAcumulados = [];

            // Acumula los montos por Codigo y CodigoTarifaIVA
            foreach ($this->xmlCompleto['ResumenFactura']['TotalDesgloseImpuesto'] as $tdi) {
                $codigo = $tdi['Codigo'];
                $codigoTarifa = $tdi['CodigoTarifaIVA'];
                $montoImpuesto = (float)$tdi['TotalMontoImpuesto'];

                // Crea una clave única para cada combinación de Codigo y CodigoTarifaIVA
                $clave = $codigo . '-' . $codigoTarifa;

                // Si la clave ya existe, suma el monto
                if (isset($montosAcumulados[$clave])) {
                    $montosAcumulados[$clave]['TotalMontoImpuesto'] += $montoImpuesto;
                } else {
                    // Si no existe, inicializa el monto
                    $montosAcumulados[$clave] = [
                        'Codigo' => $codigo,
                        'CodigoTarifaIVA' => $codigoTarifa,
                        'TotalMontoImpuesto' => $montoImpuesto,
                    ];
                }
            }

            // Genera el XML a partir de los montos acumulados
            foreach ($montosAcumulados as $item) {
                $total_desglose_impuesto = $resumen_factura->appendChild($xmlDoc->createElement("TotalDesgloseImpuesto"));

                // Agrega el Código (obligatorio)
                $total_desglose_impuesto->appendChild($xmlDoc->createElement("Codigo", $item['Codigo']));

                // Agrega el CodigoTarifaIVA (opcional)
                if (!empty($item['CodigoTarifaIVA'])) {
                    $total_desglose_impuesto->appendChild($xmlDoc->createElement("CodigoTarifaIVA", $item['CodigoTarifaIVA']));
                }

                // Agrega el TotalMontoImpuesto (obligatorio)
                $total_desglose_impuesto->appendChild($xmlDoc->createElement("TotalMontoImpuesto", number_format($item['TotalMontoImpuesto'], 5, '.', ''))); // Formato decimal
            }
        }
        //fin del desglose de impuestos, ahora es un ciclo array de 1 a  1000 que es cada impuesto cobrado.
		//Este nodo pasa a ser la sumatoria de montos del desglose realizado
		$resumen_factura->appendChild($xmlDoc->createElement("TotalImpuesto", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalImpuesto'])));
        //hasta un maximo de 4 medios de pago segun version 4.4 ahora va en los totales
        if (is_array($this->xmlCompleto['ResumenFactura']['MedioPago']) && sizeof($this->xmlCompleto['ResumenFactura']['MedioPago']) > 0) {
            for ($medio_pago=0; $medio_pago < count($this->xmlCompleto['ResumenFactura']['MedioPago']); $medio_pago++) {
                $medio_pago_linea = $resumen_factura->appendChild($xmlDoc->createElement("MedioPago"));
                $medio_pago_linea->appendChild($xmlDoc->createElement("TipoMedioPago", $this->xmlCompleto['ResumenFactura']['MedioPago'][$medio_pago]['TipoMedioPago']));
                if ($this->xmlCompleto['ResumenFactura']['MedioPago'][$medio_pago]['TipoMedioPago'] == '99') {
                    $medio_pago_linea->appendChild($xmlDoc->createElement("MedioPagoOtros", $this->xmlCompleto['ResumenFactura']['MedioPago'][$medio_pago]['MedioPagoOtros']));
                }
                $medio_pago_linea->appendChild($xmlDoc->createElement("TotalMedioPago", $this->xmlCompleto['ResumenFactura']['MedioPago'][$medio_pago]['TotalMedioPago']));
            }
        }else{
            $medio_pago_linea = $resumen_factura->appendChild($xmlDoc->createElement("MedioPago"));
            $medio_pago_linea->appendChild($xmlDoc->createElement("TipoMedioPago", $this->xmlCompleto['ResumenFactura']['MedioPago']['TipoMedioPago']));
            if ($this->xmlCompleto['ResumenFactura']['MedioPago']['TipoMedioPago'] == '99') {
                $medio_pago_linea->appendChild($xmlDoc->createElement("MedioPagoOtros", $this->xmlCompleto['ResumenFactura']['MedioPago']['MedioPagoOtros']));
            }
            $medio_pago_linea->appendChild($xmlDoc->createElement("TotalMedioPago", $this->xmlCompleto['ResumenFactura']['MedioPago']['TotalMedioPago']));
        }

        $resumen_factura->appendChild($xmlDoc->createElement("TotalComprobante", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalComprobante'])));
        //Fin de resumen de totales para la 4.4

        // Normativa que va en codigo duro esto porque forma parte de la documentacion de Hacienda
        $InformacionRe = $recibo->appendChild($xmlDoc->createElement("InformacionReferencia"));
        $InformacionRe->appendChild($xmlDoc->createElement("TipoDocIR", $this->xmlCompleto['InformacionReferencia']['TipoDocIR']));
        $InformacionRe->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['InformacionReferencia']['Numero']));
        $InformacionRe->appendChild($xmlDoc->createElement("FechaEmisionIR", $this->xmlCompleto['InformacionReferencia']['FechaEmisionIR']));
        $InformacionRe->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['InformacionReferencia']['Codigo']));
        $InformacionRe->appendChild($xmlDoc->createElement("Razon", $this->xmlCompleto['InformacionReferencia']['Razon']));
        $xmlDoc->formatOutput = true;
		$xml_sin_firma = $xmlDoc->saveXML();
		//Aca envio a firmar el documento que se acaba de guardar
		$firma = $this->armar_firmado($xml_sin_firma);
		//Luego de retornar la firma sin el documento hay que agregarlo al documento que se va a enviar contra el webservice de hacienda
		//Aca cargo el documento de la firma a un Nodo
		$dom = new DOMDocument();
		$dom_2 = new DOMDocument();
		//cargo el documento sin firma
		$doc_sin_firma = $dom->LoadXML($xml_sin_firma);
		// cargo la firma del documento canonicalizado probado hasta el 19-01-2018
	    $dom_2->LoadXML($firma);
		//canonicalizo la firm
		$valor_2 = $dom_2->documentElement;
		$dom_2->formatOutput = true;
		$dom->documentElement->appendChild($dom->importNode($valor_2, true));
		$documento_con_firma = $dom->C14N($doc_sin_firma);
		$xml_save = '<?xml version="1.0" encoding="utf-8"?>'.$documento_con_firma;
        $ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/ReciboPago/Envio/ReciboPago#".$this->Crea_clave().".xml";
        file_put_contents($ruta, $xml_save);

        return base64_encode($xml_save);
    }
	function armar_xml($clave){
		// Comienzo del XML DINAMICO con todos los datos referenciales empresa, cliente, productos.
		// El documento creado a travez del Domdocument de PHP content UTF-8 version 1.0 solicitado por la documentacion de Hacienda
		// Realizado por Luis D. Carreño - para el pais de Costa Rica
		$xmlDoc = new DOMDocument('1.0' , 'UTF-8');
		libxml_use_internal_errors(true);
		switch ($this->xmlCompleto['tipoDocumento']) {
			case '01':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("FacturaElectronica"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild($xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/facturaElectronica'));
			break;
			case '02':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("NotaDebitoElectronica"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/notaDebitoElectronica'));
			break;
			case '03':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("NotaCreditoElectronica"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/notaCreditoElectronica'));
			break;
			case '04':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("TiqueteElectronico"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/tiqueteElectronico'));
			break;
			case '08':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("FacturaElectronicaCompra"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/facturaElectronicaCompra'));
			break;
			case '09':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("FacturaElectronicaExportacion"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/facturaElectronicaExportacion'));
			break;


		}
		$facturacion->appendChild($xmlDoc->createAttribute("xmlns:xsd"))->appendChild(
			$xmlDoc->createTextNode('http://www.w3.org/2001/XMLSchema'));
		$facturacion->appendChild($xmlDoc->createAttribute("xmlns:xsi"))->appendChild(
   			$xmlDoc->createTextNode('http://www.w3.org/2001/XMLSchema-instance'));

		//La clave generada por la libreria
		$clave = $facturacion->appendChild($xmlDoc->createElement("Clave", $clave));

		//4.4 Se debe indicar el número de cedula de identificación del proveedor de sistemas que esté utilizando para la emisión de comprobantes electrónicos
		$facturacion->appendChild($xmlDoc->createElement("ProveedorSistemas", $this->xmlCompleto['ProveedorSistemas']));

		//4.4 Se debe de indicar el código de la actividad económica inscrita a la cual corresponde el comprobante que se está generando
		if ($this->xmlCompleto['tipoDocumento'] != '08'){
		$facturacion->appendChild($xmlDoc->createElement("CodigoActividadEmisor", $this->xmlCompleto['CodigoActividadEmisor']));
		}else{
		//$facturacion->appendChild($xmlDoc->createElement("CodigoActividadReceptor", $this->xmlCompleto['CodigoActividadReceptor']));
		}

		//Validacion ya que no debe existir ni en tiquete ni en FEE ni en Recibo
		if (!in_array($this->xmlCompleto['tipoDocumento'], ['04', '09', '10'])) {
			//Si no es uno de los comprobantes validados verifico que no este vacio
			if (isset($this->xmlCompleto['CodigoActividadReceptor']) && !empty($this->xmlCompleto['CodigoActividadReceptor'])) {
				//Se debe de indicar el código de la actividad económica inscrita del receptor a la cual corresponden los bienes o servicios que se le están facturando
				$facturacion->appendChild($xmlDoc->createElement("CodigoActividadReceptor", $this->xmlCompleto['CodigoActividadReceptor']));
			}
		}

		//Numeración consecutiva del comprobante
		$facturacion->appendChild($xmlDoc->createElement("NumeroConsecutivo", $this->armar_consecutivo()));

		//Fecha de Emision
		$facturacion->appendChild($xmlDoc->createElement("FechaEmision", $this->xmlCompleto['fechaEmision']));

		//Comienzo del Nodo del Emisor
		$emisor = $facturacion->appendChild($xmlDoc->createElement("Emisor"));

			//Datos Globales de la Seccion del Emisor aca comienza todo lo refente al emisor
			$emisor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Emisor']['Nombre']));

			//Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
			$identificacion_emisor = $emisor->appendChild($xmlDoc->createElement("Identificacion"));
				$identificacion_emisor->appendChild($xmlDoc->createElement("Tipo",  $this->xmlCompleto['Emisor']['Identificacion']['Tipo']));
				$identificacion_emisor->appendChild($xmlDoc->createElement("Numero",  $this->xmlCompleto['Emisor']['Identificacion']['Numero']));

				//Validacion para cuando sea emisor de bebidas alcoholicas
			if (isset($this->xmlCompleto['Emisor']['Registrofiscal8707']) && !empty($this->xmlCompleto['Emisor']['Registrofiscal8707'])) {
				//Se convierte en carácter obligatorio cuando se estén facturando códigosCAByS de bebidas alcohólicas según la Ley 8707
				$emisor->appendChild($xmlDoc->createElement("Registrofiscal8707", $this->xmlCompleto['Emisor']['Registrofiscal8707']));
			}

			//Nombre comercial del emisor
			$emisor->appendChild($xmlDoc->createElement("NombreComercial", $this->xmlCompleto['Emisor']['NombreComercial']));

			//Comienzo de la Ubicacion para el emisor debe tener Provincia, Canton, Distrito, Barrio
			if ($this->xmlCompleto['tipoDocumento'] != '08'){
			$ubicacion_emisor = $emisor->appendChild($xmlDoc->createElement("Ubicacion"));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Provincia", $this->xmlCompleto['Emisor']['Ubicacion']['Provincia']));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Canton", $this->xmlCompleto['Emisor']['Ubicacion']['Canton']));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Distrito", $this->xmlCompleto['Emisor']['Ubicacion']['Distrito']));
				if (isset($this->xmlCompleto['Emisor']['Ubicacion']['Barrio']) && !empty($this->xmlCompleto['Emisor']['Ubicacion']['Barrio'])) {
					$ubicacion_emisor->appendChild($xmlDoc->createElement("Barrio", $this->xmlCompleto['Emisor']['Ubicacion']['Barrio']));
				}
				if (isset($this->xmlCompleto['Emisor']['Ubicacion']['OtrasSenas']) && !empty($this->xmlCompleto['Emisor']['Ubicacion']['OtrasSenas'])) {
					$ubicacion_emisor->appendChild($xmlDoc->createElement("OtrasSenas", $this->xmlCompleto['Emisor']['Ubicacion']['OtrasSenas']));
				}
			}else{//ubicacion en FEC
			    if($this->xmlCompleto['Emisor']['Identificacion']['Tipo'] != '05' && $this->xmlCompleto['tipoDocumento'] == '08'){
			$ubicacion_emisor = $emisor->appendChild($xmlDoc->createElement("Ubicacion"));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Provincia", $this->xmlCompleto['Emisor']['Ubicacion']['Provincia']));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Canton", $this->xmlCompleto['Emisor']['Ubicacion']['Canton']));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Distrito", $this->xmlCompleto['Emisor']['Ubicacion']['Distrito']));
				if (isset($this->xmlCompleto['Emisor']['Ubicacion']['Barrio']) && !empty($this->xmlCompleto['Emisor']['Ubicacion']['Barrio'])) {
					$ubicacion_emisor->appendChild($xmlDoc->createElement("Barrio", $this->xmlCompleto['Emisor']['Ubicacion']['Barrio']));
				}
				if (isset($this->xmlCompleto['Emisor']['Ubicacion']['OtrasSenas']) && !empty($this->xmlCompleto['Emisor']['Ubicacion']['OtrasSenas'])) {
					$ubicacion_emisor->appendChild($xmlDoc->createElement("OtrasSenas", $this->xmlCompleto['Emisor']['Ubicacion']['OtrasSenas']));
				}
			}else{
			  //Validacion para otras senas extranjero aplica solo para comprobantes 08 y tipo de documento 05 segun 4.4
			if($this->xmlCompleto['Emisor']['Identificacion']['Tipo'] == '05' && $this->xmlCompleto['tipoDocumento'] == '08'){
				//Debe de indicarse lo mas exacta posible. Es de uso exclusivo para cuanto se selecciona el código 05 en el campo denominado Tipo de identificación del emisor.
				if (isset($this->xmlCompleto['Emisor']['OtrasSenasExtranjero']) && !empty($this->xmlCompleto['Emisor']['OtrasSenasExtranjero'])) {
					$emisor->appendChild($xmlDoc->createElement("OtrasSenasExtranjero", $this->xmlCompleto['Emisor']['OtrasSenasExtranjero']));
				}
			}
			}
			}
			//Fin de la Ubicacion



			//Comienzo del Numero del Emisor
			$telefono_emisor = $emisor->appendChild($xmlDoc->createElement("Telefono"));
				$telefono_emisor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Emisor']['Telefono']['CodigoPais']));
				$telefono_emisor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Emisor']['Telefono']['NumTelefono']));
			//Fin del Numero del Emisor

			// Correos electronicos, ahora permite un array de 4, creo una funcion que cumpla con lo solicitado
			$correos = $this->xmlCompleto['Emisor']['CorreoElectronico']; // Suponiendo que es un array
			$correosValidos = $this->validarCorreosElectronicos($correos);

			foreach ($correosValidos as $correo) {
				$emisor->appendChild($xmlDoc->createElement("CorreoElectronico", $correo));
			}
		//Fin de toda la seccion del Emisor

			if ($this->xmlCompleto['tipoDocumento'] == '01' or $this->xmlCompleto['tipoDocumento'] == '08' or $this->xmlCompleto['tipoDocumento'] == '03') {
			//Comienzo de los datos del Receptor
			 if ($this->xmlCompleto['tipoDocumento'] == '03'){
			 if( substr($this->xmlCompleto['InformacionReferencia']['Numero'], 29, 2)== '01' or substr($this->xmlCompleto['InformacionReferencia']['Numero'], 29, 2)== '08'){
			   $receptor = $facturacion->appendChild($xmlDoc->createElement("Receptor"));

				//Datos Globales de la Seccion del Receptor aca comienza todo lo refente al receptor
				$receptor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Receptor']['Nombre']));

				//Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
				$identificacion_receptor = $receptor->appendChild($xmlDoc->createElement("Identificacion"));
				$identificacion_receptor->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['Receptor']['Identificacion']['Tipo']));
				$identificacion_receptor->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['Receptor']['Identificacion']['Numero']));
				//Fin de los tipo de identificacion

				$receptor->appendChild($xmlDoc->createElement("NombreComercial", $this->xmlCompleto['Receptor']['NombreComercial']));

				if ($this->xmlCompleto['tipoDocumento'] != '09') {
					//Comienzo de la Ubicacion para el receptor debe tener Provincia, Canton, Distrito, Barrio
					$ubicacion_receptor = $receptor->appendChild($xmlDoc->createElement("Ubicacion"));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Provincia", $this->xmlCompleto['Receptor']['Ubicacion']['Provincia']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Canton", $this->xmlCompleto['Receptor']['Ubicacion']['Canton']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Distrito", $this->xmlCompleto['Receptor']['Ubicacion']['Distrito']));
					if (isset($this->xmlCompleto['Receptor']['Ubicacion']['Barrio']) && !empty($this->xmlCompleto['Receptor']['Ubicacion']['Barrio'])) {
						$ubicacion_receptor->appendChild($xmlDoc->createElement("Barrio", $this->xmlCompleto['Receptor']['Ubicacion']['Barrio']));
					}
					if (isset($this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas']) && !empty($this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas'])) {
						$ubicacion_receptor->appendChild($xmlDoc->createElement("OtrasSenas", $this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas']));
					}
					//Fin de la ubicacion
				}

				//Validacion para otras senas extranjero aplica solo para comprobantes 08 y tipo de documento 05 segun 4.4
				if($this->xmlCompleto['Receptor']['Identificacion']['Tipo'] == '05' && $this->xmlCompleto['tipoDocumento'] == '08'){
					//Debe de indicarse lo mas exacta posible. Es de uso exclusivo para cuanto se selecciona el código 05 en el campo denominado Tipo de identificación del receptor.
					if (isset($this->xmlCompleto['Receptor']['OtrasSenasExtranjero']) && !empty($this->xmlCompleto['Receptor']['OtrasSenasExtranjero'])) {
						$receptor->appendChild($xmlDoc->createElement("OtrasSenasExtranjero", $this->xmlCompleto['Receptor']['OtrasSenasExtranjero']));
					}
				}

				//Comienzo del Numero del receptor
				$telefono_receptor = $receptor->appendChild($xmlDoc->createElement("Telefono"));
				$telefono_receptor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Receptor']['Telefono']['CodigoPais']));
				$telefono_receptor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Receptor']['Telefono']['NumTelefono']));
				//Fin del numero del Receptor

				// Correo electronico Solo permite 1
				$receptor->appendChild($xmlDoc->createElement("CorreoElectronico", $this->xmlCompleto['Receptor']['CorreoElectronico']));

			//Final de los datos del Receptor para el XML
			 }
			 } else {

			     $receptor = $facturacion->appendChild($xmlDoc->createElement("Receptor"));

				//Datos Globales de la Seccion del Receptor aca comienza todo lo refente al receptor
				$receptor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Receptor']['Nombre']));

				//Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
				$identificacion_receptor = $receptor->appendChild($xmlDoc->createElement("Identificacion"));
				$identificacion_receptor->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['Receptor']['Identificacion']['Tipo']));
				$identificacion_receptor->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['Receptor']['Identificacion']['Numero']));
				//Fin de los tipo de identificacion

				$receptor->appendChild($xmlDoc->createElement("NombreComercial", $this->xmlCompleto['Receptor']['NombreComercial']));

				if ($this->xmlCompleto['tipoDocumento'] != '09') {
					//Comienzo de la Ubicacion para el receptor debe tener Provincia, Canton, Distrito, Barrio
					$ubicacion_receptor = $receptor->appendChild($xmlDoc->createElement("Ubicacion"));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Provincia", $this->xmlCompleto['Receptor']['Ubicacion']['Provincia']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Canton", $this->xmlCompleto['Receptor']['Ubicacion']['Canton']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Distrito", $this->xmlCompleto['Receptor']['Ubicacion']['Distrito']));
					if (isset($this->xmlCompleto['Receptor']['Ubicacion']['Barrio']) && !empty($this->xmlCompleto['Receptor']['Ubicacion']['Barrio'])) {
						$ubicacion_receptor->appendChild($xmlDoc->createElement("Barrio", $this->xmlCompleto['Receptor']['Ubicacion']['Barrio']));
					}
					if (isset($this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas']) && !empty($this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas'])) {
						$ubicacion_receptor->appendChild($xmlDoc->createElement("OtrasSenas", $this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas']));
					}
					//Fin de la ubicacion
				}

				//Validacion para otras senas extranjero aplica solo para comprobantes 08 y tipo de documento 05 segun 4.4
				if($this->xmlCompleto['Receptor']['Identificacion']['Tipo'] == '05' && $this->xmlCompleto['tipoDocumento'] == '08'){
					//Debe de indicarse lo mas exacta posible. Es de uso exclusivo para cuanto se selecciona el código 05 en el campo denominado Tipo de identificación del receptor.
					if (isset($this->xmlCompleto['Receptor']['OtrasSenasExtranjero']) && !empty($this->xmlCompleto['Receptor']['OtrasSenasExtranjero'])) {
						$receptor->appendChild($xmlDoc->createElement("OtrasSenasExtranjero", $this->xmlCompleto['Receptor']['OtrasSenasExtranjero']));
					}
				}

				//Comienzo del Numero del receptor
				$telefono_receptor = $receptor->appendChild($xmlDoc->createElement("Telefono"));
				$telefono_receptor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Receptor']['Telefono']['CodigoPais']));
				$telefono_receptor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Receptor']['Telefono']['NumTelefono']));
				//Fin del numero del Receptor

				// Correo electronico Solo permite 1
				$receptor->appendChild($xmlDoc->createElement("CorreoElectronico", $this->xmlCompleto['Receptor']['CorreoElectronico']));
			 }

		} else {

			if (!empty($this->xmlCompleto['Receptor'])) {
				//Comienzo de los datos del Receptor
				$receptor = $facturacion->appendChild($xmlDoc->createElement("Receptor"));

					//Datos Globales de la Seccion del Receptor aca comienza todo lo refente al receptor
					$receptor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Receptor']['Nombre']));

					//Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
					$identificacion_receptor = $receptor->appendChild($xmlDoc->createElement("Identificacion"));
					$identificacion_receptor->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['Receptor']['Identificacion']['Tipo']));
					$identificacion_receptor->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['Receptor']['Identificacion']['Numero']));
					//Fin de los tipo de identificacion

					$receptor->appendChild($xmlDoc->createElement("NombreComercial", $this->xmlCompleto['Receptor']['NombreComercial']));

					if ($this->xmlCompleto['tipoDocumento'] != '09') {
						//Comienzo de la Ubicacion para el receptor debe tener Provincia, Canton, Distrito, Barrio
						$ubicacion_receptor = $receptor->appendChild($xmlDoc->createElement("Ubicacion"));
						$ubicacion_receptor->appendChild($xmlDoc->createElement("Provincia", $this->xmlCompleto['Receptor']['Ubicacion']['Provincia']));
						$ubicacion_receptor->appendChild($xmlDoc->createElement("Canton", $this->xmlCompleto['Receptor']['Ubicacion']['Canton']));
						$ubicacion_receptor->appendChild($xmlDoc->createElement("Distrito", $this->xmlCompleto['Receptor']['Ubicacion']['Distrito']));
						if (isset($this->xmlCompleto['Receptor']['Ubicacion']['Barrio']) && !empty($this->xmlCompleto['Receptor']['Ubicacion']['Barrio'])) {
							$ubicacion_receptor->appendChild($xmlDoc->createElement("Barrio", $this->xmlCompleto['Receptor']['Ubicacion']['Barrio']));
						}
						if (isset($this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas']) && !empty($this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas'])) {
							$ubicacion_receptor->appendChild($xmlDoc->createElement("OtrasSenas", $this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas']));
						}
						//Fin de la ubicacion
					}

					//Validacion para otras senas extranjero aplica solo para comprobantes 08 y tipo de documento 05 segun 4.4
					if($this->xmlCompleto['Receptor']['Identificacion']['Tipo'] == '05' && $this->xmlCompleto['tipoDocumento'] == '08'){
						//Debe de indicarse lo mas exacta posible. Es de uso exclusivo para cuanto se selecciona el código 05 en el campo denominado Tipo de identificación del receptor.
						if (isset($this->xmlCompleto['Receptor']['OtrasSenasExtranjero']) && !empty($this->xmlCompleto['Receptor']['OtrasSenasExtranjero'])) {
							$receptor->appendChild($xmlDoc->createElement("OtrasSenasExtranjero", $this->xmlCompleto['Receptor']['OtrasSenasExtranjero']));
						}
					}

					//Comienzo del Numero del receptor
					$telefono_receptor = $receptor->appendChild($xmlDoc->createElement("Telefono"));
					$telefono_receptor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Receptor']['Telefono']['CodigoPais']));
					$telefono_receptor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Receptor']['Telefono']['NumTelefono']));
					//Fin del numero del Receptor

					// Correo electronico Solo permite 1
					$receptor->appendChild($xmlDoc->createElement("CorreoElectronico", $this->xmlCompleto['Receptor']['CorreoElectronico']));

				//Final de los datos del Receptor para el XML
			}
		}

		//Comienzo de condicion venta 4.4
		$facturacion->appendChild($xmlDoc->createElement("CondicionVenta", $this->xmlCompleto['CondicionVenta']));

		//Validacion para cuando sea condicion venta y seleccione OTROS debe aparecer este nodo
		if ($this->xmlCompleto['CondicionVenta'] === '99') {
			$facturacion->appendChild($xmlDoc->createElement("CondicionVentaOtros", $this->xmlCompleto['CondicionVentaOtros']));
		}

		//Validacion para cuando sea credito muestre el plazo
		if ($this->xmlCompleto['CondicionVenta'] === '02') {
			$facturacion->appendChild($xmlDoc->createElement("PlazoCredito", $this->xmlCompleto['PlazoCredito']));
		} elseif (isset($this->xmlCompleto['PlazoCredito']) && !empty($this->xmlCompleto['PlazoCredito'])) {
			$facturacion->appendChild($xmlDoc->createElement("PlazoCredito", $this->xmlCompleto['PlazoCredito']));
        }

		if (!empty($this->xmlCompleto['DetalleServicio'])) {

			//Comienzo de el detalle de Servicio Todos los arrays que existan en linea detalle
			$detalle_servicio = $facturacion->appendChild($xmlDoc->createElement("DetalleServicio"));
			//Inicio las lineas detalles en 1
			$numeroLinea = 1;

			for ($i=0; $i < count($this->xmlCompleto['DetalleServicio']); $i++) {

				$linea_detalle = $detalle_servicio->appendChild($xmlDoc->createElement("LineaDetalle"));
				$linea_detalle->appendChild($xmlDoc->createElement("NumeroLinea", $numeroLinea));

				if ($this->xmlCompleto['tipoDocumento'] === '09') {
				     if ($this->xmlCompleto['DetalleServicio'][$i]['PartidaArancelaria']>0) {
					$linea_detalle->appendChild($xmlDoc->createElement("PartidaArancelaria", $this->xmlCompleto['DetalleServicio'][$i]['PartidaArancelaria']));
				}
				}

				$linea_detalle->appendChild($xmlDoc->createElement("CodigoCABYS", $this->xmlCompleto['DetalleServicio'][$i]['CodigoCABYS']));
				// Asegúrate de que 'CodigoComercial' es un array y recorre sus elementos
                if (isset($this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']) && is_array($this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial'])) {
					if(isset($this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']['Codigo'])){
                        $codigo_c = $linea_detalle->appendChild($xmlDoc->createElement("CodigoComercial"));
                        // Asegúrate de que el elemento 'Tipo' y 'Codigo' existen en el array
                        if (isset($this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']['Tipo'])) {
                            $codigo_c->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']['Tipo']));
                        }

                        if (isset($this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']['Codigo'])) {
                            $codigo_c->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']['Codigo']));
                        }
                    } else {
                        for ($codigo_comercial=0; $codigo_comercial < count($this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']); $codigo_comercial++) {
                            // Crea el nodo CodigoComercial
                            $codigo_c = $linea_detalle->appendChild($xmlDoc->createElement("CodigoComercial"));
                            // Asegúrate de que el elemento 'Tipo' y 'Codigo' existen en el array
                            if (isset($this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial'][$codigo_comercial]['Tipo'])) {
                                $codigo_c->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial'][$codigo_comercial]['Tipo']));
                            }

                            if (isset($this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial'][$codigo_comercial]['Codigo'])) {
                                $codigo_c->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial'][$codigo_comercial]['Codigo']));
                            }
                        }
                    }
				}

				$linea_detalle->appendChild($xmlDoc->createElement("Cantidad", $this->xmlCompleto['DetalleServicio'][$i]['Cantidad']));
				$linea_detalle->appendChild($xmlDoc->createElement("UnidadMedida", $this->xmlCompleto['DetalleServicio'][$i]['UnidadMedida']));

				//Tipo de transaccion nuevo para version 4.4 Nota 22
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['TipoTransaccion']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['TipoTransaccion'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("TipoTransaccion", $this->xmlCompleto['DetalleServicio'][$i]['TipoTransaccion']));
				}

				//Se verifica si existe y si no esta vacio para agregarlo
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['UnidadMedidaComercial']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['UnidadMedidaComercial'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("UnidadMedidaComercial", $this->xmlCompleto['DetalleServicio'][$i]['UnidadMedidaComercial']));
				}

				//Detalle texto informativo del producto
				$linea_detalle->appendChild($xmlDoc->createElement("Detalle", $this->xmlCompleto['DetalleServicio'][$i]['Detalle']));

				//Se verifica si existe y si no esta vacio para agregarlo
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['NumeroVINoSerie']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['NumeroVINoSerie'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("NumeroVINoSerie", $this->xmlCompleto['DetalleServicio'][$i]['NumeroVINoSerie']));
				}

				//Se verifica si existe y si no esta vacio para agregarlo
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['RegistroMedicamento']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['RegistroMedicamento'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("RegistroMedicamento", $this->xmlCompleto['DetalleServicio'][$i]['RegistroMedicamento']));
				}

				//Se verifica si existe y si no esta vacio para agregarlo
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['FormaFarmaceutica']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['FormaFarmaceutica'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("FormaFarmaceutica", $this->xmlCompleto['DetalleServicio'][$i]['FormaFarmaceutica']));
				}

				//Se verifica si existe y si no esta vacio para agregarlo
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['FormaFarmaceutica']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['FormaFarmaceutica'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("FormaFarmaceutica", $this->xmlCompleto['DetalleServicio'][$i]['FormaFarmaceutica']));
				}

				// Manejo del nodo Detalle Surtido
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido']) && is_array($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'])) {
					$detalleSurtidoNode = $linea_detalle->appendChild($xmlDoc->createElement("DetalleSurtido"));
					$numeroLineaSurtido = 1;

					for ($surtido=0; $surtido < count($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido']); $surtido++) {
						// Crea un nodo para cada producto en Detalle Surtido
						$productoNode = $detalleSurtidoNode->appendChild($xmlDoc->createElement("LineaDetalleSurtido", $numeroLineaSurtido));

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['CodigoCABYSSurtido'])) {
							$productoNode->appendChild($xmlDoc->createElement("CodigoCABYSSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['CodigoCABYSSurtido']));
						}

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['CodigoComercialSurtido']) && is_array($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['CodigoComercialSurtido'])) {
							// ccs = codigo comercial surtido para identificar el arreglo
							for ($ccs=0; $ccs < count($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['CodigoComercialSurtido']); $ccs++) {
								$codigo_comercial_surtido = $productoNode->appendChild($xmlDoc->createElement("CodigoComercialSurtido"));
								$codigo_comercial_surtido->appendChild($xmlDoc->createElement("TipoSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['CodigoComercialSurtido'][$ccs]['TipoSurtido']));
								$codigo_comercial_surtido->appendChild($xmlDoc->createElement("CodigoSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['CodigoComercialSurtido'][$ccs]['CodigoSurtido']));
							}
						}

						$productoNode->appendChild($xmlDoc->createElement("CantidadSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['CantidadSurtido']));
						$productoNode->appendChild($xmlDoc->createElement("UnidadMedidaSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['UnidadMedidaSurtido']));

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['UnidadMedidaComercialSurtido'])) {
							$productoNode->appendChild($xmlDoc->createElement("UnidadMedidaComercialSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['UnidadMedidaComercialSurtido']));
						}
						$productoNode->appendChild($xmlDoc->createElement("DetalleSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['DetalleSurtido']));
						$productoNode->appendChild($xmlDoc->createElement("MontoTotalSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['MontoTotalSurtido']));

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['DescuentoSurtido']) && is_array($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['DescuentoSurtido'])) {
							// ccs = codigo descuento surtido para identificar el arreglo
							for ($cds=0; $cds < count($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['DescuentoSurtido']); $cds++) {
								$codigo_descuento_surtido = $productoNode->appendChild($xmlDoc->createElement("DescuentoSurtido"));
								$codigo_descuento_surtido->appendChild($xmlDoc->createElement("TipoSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['DescuentoSurtido'][$cds]['MontoDescuentoSurtido']));
								$codigo_descuento_surtido->appendChild($xmlDoc->createElement("CodigoSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['DescuentoSurtido'][$cds]['CodigoDescuentoSurtido']));
							}
						}
						$productoNode->appendChild($xmlDoc->createElement("SubTotalSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['SubTotalSurtido']));

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['IVACobradoFabricaSurtido'])) {
							$productoNode->appendChild($xmlDoc->createElement("IVACobradoFabricaSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['IVACobradoFabricaSurtido']));
						}

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['BaseImponibleSurtido'])) {
							$productoNode->appendChild($xmlDoc->createElement("BaseImponibleSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['BaseImponibleSurtido']));
						}

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido']) && is_array($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'])) {
							// impuesto_surtido = impuesto surtido para identificar el arreglo
							for ($impuesto_surtido=0; $impuesto_surtido < count($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido']); $impuesto_surtido++) {
								$codigo_impuesto_surtido = $productoNode->appendChild($xmlDoc->createElement("ImpuestoSurtido"));
								$codigo_impuesto_surtido->appendChild($xmlDoc->createElement("CodigoImpuestoSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['CodigoImpuestoSurtido']));

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['CodigoImpuestoOTROSurtido'])) {
									$codigo_impuesto_surtido->appendChild($xmlDoc->createElement("CodigoImpuestoOTROSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['CodigoImpuestoOTROSurtido']));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['CodigoTarifaIVASurtido'])) {
									$codigo_impuesto_surtido->appendChild($xmlDoc->createElement("CodigoTarifaIVASurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['CodigoTarifaIVASurtido']));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['TarifaSurtido'])) {
									$codigo_impuesto_surtido->appendChild($xmlDoc->createElement("TarifaSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['TarifaSurtido']));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido'])) {
									$impuesto_especifico_surtido = $codigo_impuesto_surtido->appendChild($xmlDoc->createElement("DatosImpuestoEspecíficoSurtido"));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['CantidadUnidadMedidaSurtido'])) {
									$impuesto_especifico_surtido->appendChild($xmlDoc->createElement("CantidadUnidadMedidaSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['CantidadUnidadMedidaSurtido']));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['PorcentajeSurtido'])) {
									$impuesto_especifico_surtido->appendChild($xmlDoc->createElement("PorcentajeSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['PorcentajeSurtido']));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['ProporcionSurtido'])) {
									$impuesto_especifico_surtido->appendChild($xmlDoc->createElement("ProporcionSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['ProporcionSurtido']));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['VolumenUnidadConsumoSurtido'])) {
									$impuesto_especifico_surtido->appendChild($xmlDoc->createElement("VolumenUnidadConsumoSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['VolumenUnidadConsumoSurtido']));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['VolumenUnidadConsumoSurtido'])) {
									$impuesto_especifico_surtido->appendChild($xmlDoc->createElement("VolumenUnidadConsumoSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['DatosImpuestoEspecíficoSurtido']['VolumenUnidadConsumoSurtido']));
								}

								if (isset($this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['MontoImpuestoSurtido'])) {
									$codigo_impuesto_surtido->appendChild($xmlDoc->createElement("MontoImpuestoSurtido", $this->xmlCompleto['DetalleServicio'][$i]['DetalleSurtido'][$surtido]['ImpuestoSurtido'][$impuesto_surtido]['MontoImpuestoSurtido']));
								}
							}
						}
						$numeroLineaSurtido++;
					}
				}
				//Comienzo de Flujo de Validacion para nuevos nodos tanto de farmacia como de transporte
				$linea_detalle->appendChild($xmlDoc->createElement("PrecioUnitario", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['PrecioUnitario'])));
				$linea_detalle->appendChild($xmlDoc->createElement("MontoTotal", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['MontoTotal'])));

				//Validacion necesaria en caso exista algun descuento
				if (!empty($this->xmlCompleto['DetalleServicio'][$i]['Descuento'])) {
					for ($linea_descuento=0; $linea_descuento < count($this->xmlCompleto['DetalleServicio'][$i]['Descuento']); $linea_descuento++) {
						$descuento = $linea_detalle->appendChild($xmlDoc->createElement("Descuento"));
						$descuento->appendChild($xmlDoc->createElement("MontoDescuento", $this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$linea_descuento]['MontoDescuento']));
						$descuento->appendChild($xmlDoc->createElement("CodigoDescuento", $this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$linea_descuento]['CodigoDescuento']));

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$linea_descuento]['CodigoDescuentoOTRO']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$linea_descuento]['CodigoDescuentoOTRO'])) {
							$descuento->appendChild($xmlDoc->createElement("CodigoDescuentoOTRO", $this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$linea_descuento]['CodigoDescuentoOTRO']));
						}

						if (isset($this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$linea_descuento]['NaturalezaDescuento']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$linea_descuento]['NaturalezaDescuento'])) {
							$descuento->appendChild($xmlDoc->createElement("NaturalezaDescuento", $this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$linea_descuento]['NaturalezaDescuento']));
						}
					}
				}
				//Subtotal del detalle
				$linea_detalle->appendChild($xmlDoc->createElement("SubTotal", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['SubTotal'])));

				//Si existe el IVAcobradoFabrica
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['BaseImponible']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['BaseImponible'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("BaseImponible", $this->xmlCompleto['DetalleServicio'][$i]['BaseImponible']));
				}
				//Si existe la Base Imponible
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['IVACobradoFabrica']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['IVACobradoFabrica'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("IVACobradoFabrica", $this->xmlCompleto['DetalleServicio'][$i]['IVACobradoFabrica']));
				}

				//Inicio de Impuesto
				if (!empty($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'])) {
					for ($impto=0; $impto < count($this->xmlCompleto['DetalleServicio'][$i]['Impuesto']); $impto++) {
						$impuesto = $linea_detalle->appendChild($xmlDoc->createElement("Impuesto"));
						$impuesto->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo']));

						//Validacion de si el codigo de impuesto es 99 describir el codigo preciso
						if ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo'] == '99') {
							$impuesto->appendChild($xmlDoc->createElement("CodigoImpuestoOTRO", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoImpuestoOTRO']));
						}
						//Validacion dependiendo del codigo de impuesto se aplica la tarifa IVA solo aplica 01 07
						switch ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo']) {
							case '01':
								$impuesto->appendChild($xmlDoc->createElement("CodigoTarifaIVA", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoTarifaIVA']));
							break;
							case '07':
								$impuesto->appendChild($xmlDoc->createElement("CodigoTarifaIVA", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoTarifaIVA']));
							break;
						}
						//Tarifa siempre la agregamos
						$impuesto->appendChild($xmlDoc->createElement("Tarifa", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Tarifa']));

						//Validacion para cuando sea 08 el tipo de impuesto
						if ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo'] === '08') {
							$impuesto->appendChild($xmlDoc->createElement("FactorCalculoIVA", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['FactorCalculoIVA']));
						}

						//Objeto nuevo de impuesto solo cuando sea el codigo de impuesto
						$codigos_permitidos = ['03', '04', '05', '06'];
						if (in_array($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo'], $codigos_permitidos)) {
							$datos_impuestos_especificos = $impuesto->appendChild($xmlDoc->createElement("DatosImpuestoEspecifico"));
							$datos_impuestos_especificos->appendChild($xmlDoc->createElement("CantidadUnidadMedida", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['DatosImpuestoEspecifico']['CantidadUnidadMedida']));
							$datos_impuestos_especificos->appendChild($xmlDoc->createElement("Porcentaje", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['DatosImpuestoEspecifico']['Porcentaje']));
							$datos_impuestos_especificos->appendChild($xmlDoc->createElement("Proporcion", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['DatosImpuestoEspecifico']['Proporcion']));
							$datos_impuestos_especificos->appendChild($xmlDoc->createElement("VolumenUnidadConsumo", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['DatosImpuestoEspecifico']['VolumenUnidadConsumo']));
							$datos_impuestos_especificos->appendChild($xmlDoc->createElement("ImpuestoUnidad", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['DatosImpuestoEspecifico']['ImpuestoUnidad']));
						}

						$impuesto->appendChild($xmlDoc->createElement("Monto", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Monto'])));

						//Monto exportacion solo cuando sea documento 09
						if ($this->xmlCompleto['tipoDocumento'] === '09') {
							$impuesto->appendChild($xmlDoc->createElement("MontoExportacion", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['MontoExportacion']));
						}
						//Estas dos validaciones son aplicadas para verificar si es nota de credito o debito provenientes de una factura de exportacion
						if ($this->xmlCompleto['tipoDocumento'] === '02') {
							if ($this->xmlCompleto['InformacionReferencia']['TipoDocIR'] === '09') {
								$impuesto->appendChild($xmlDoc->createElement("MontoExportacion", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['MontoExportacion']));
							}
						}

						if ($this->xmlCompleto['tipoDocumento'] === '03') {
							if ($this->xmlCompleto['InformacionReferencia']['TipoDocIR'] === '09') {
								$impuesto->appendChild($xmlDoc->createElement("MontoExportacion", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['MontoExportacion']));
							}
						}

						//Dentro de los impuestos existe la exoneracion que dependera de la empresa a la cual se le esta facturando, algunas de ellas cuentan con este adicional que es exoneracion
						if ($this->xmlCompleto['tipoDocumento'] != '09') {
							if ($this->xmlCompleto['DetalleServicio'][$i]['EsExoneracion'] === '01') {
								$exoneracion = $impuesto->appendChild($xmlDoc->createElement("Exoneracion"));
									$exoneracion->appendChild($xmlDoc->createElement("TipoDocumentoEX1", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['TipoDocumentoEX1']));
									if ($this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['TipoDocumentoEX1'] == '99') {
										$exoneracion->appendChild($xmlDoc->createElement("TipoDocumentoOTRO", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['TipoDocumentoOTRO']));
									}
									//Numero de la exoneracion
									$exoneracion->appendChild($xmlDoc->createElement("NumeroDocumento", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['NumeroDocumento']));
									//Articulo para exoneracion solo aplica en los codigos de exoneracion permitidos
									$codigos_exoneracion_permitidos = ['02','03', '06', '07', '08'];
									if (in_array($this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['TipoDocumentoEX1'], $codigos_exoneracion_permitidos)) {
										$exoneracion->appendChild($xmlDoc->createElement("Articulo", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['Articulo']));
									}
									//Inciso para exoneracion
									if (isset($this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['Inciso']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['Inciso'])) {
										$exoneracion->appendChild($xmlDoc->createElement("Inciso", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['Inciso']));
									}
									$exoneracion->appendChild($xmlDoc->createElement("NombreInstitucion", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['NombreInstitucion']));
									if ($this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['TipoDocumentoEX1'] == '99') {
										$exoneracion->appendChild($xmlDoc->createElement("NombreInstitucionOtros", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['NombreInstitucionOtros']));
									}
									$exoneracion->appendChild($xmlDoc->createElement("FechaEmisionEX", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['FechaEmisionEX']));
									$exoneracion->appendChild($xmlDoc->createElement("TarifaExonerada", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['TarifaExonerada']));
									$exoneracion->appendChild($xmlDoc->createElement("MontoExoneracion", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['MontoExoneracion'])));
							}
						}
					}
				}
				if (isset($this->xmlCompleto['DetalleServicio'][$i]['ImpuestoAsumidoEmisorFabrica']) && !empty($this->xmlCompleto['DetalleServicio'][$i]['ImpuestoAsumidoEmisorFabrica'])) {
					$linea_detalle->appendChild($xmlDoc->createElement("ImpuestoAsumidoEmisorFabrica", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['ImpuestoAsumidoEmisorFabrica'])));
				}
				if ($this->xmlCompleto['tipoDocumento'] != '09') {
					$linea_detalle->appendChild($xmlDoc->createElement("ImpuestoNeto", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['ImpuestoNeto'])));
			}
				$linea_detalle->appendChild($xmlDoc->createElement("MontoTotalLinea", $this->ModificarEntero($this->xmlCompleto['DetalleServicio'][$i]['MontoTotalLinea'])));
				$numeroLinea++;
			}
		}
		//Otros cargos forma parte de la version 4.3 nodo necesario en caso de que se requiera
		if (!empty($this->xmlCompleto['OtrosCargos'])) {
			$OtrosCargos = $facturacion->appendChild($xmlDoc->createElement("OtrosCargos"));
			if (!empty($this->xmlCompleto['OtrosCargos'])) {
                for ($oc = 0; $oc < count($this->xmlCompleto['OtrosCargos']) && $oc < 15; $oc++) {
					$OtrosCargos->appendChild($xmlDoc->createElement("TipoDocumentoOC", $this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumentoOC']));

					//Validacion de si el codigo de otros cargos es 99 describir el codigo preciso
					if ($this->xmlCompleto['tipoDocumento'] != '09') {
						if ($this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumentoOC'] === '04') {
							if ($this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumentoOC'] == '99') {
								$tipo_documento_otrocargo  = $OtrosCargos->appendChild($xmlDoc->createElement("TipoDocumentoOTROS", $this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumentoOTROS']));
								$identificacion_tercero_oc = $tipo_documento_otrocargo->appendChild($xmlDoc->createElement("IdentificacionTercero", $this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumentoOTROS']['IdentificacionTercero']));
								$identificacion_tercero_oc->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumentoOTROS']['IdentificacionTercero']['Tipo']));
								$identificacion_tercero_oc->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumentoOTROS']['IdentificacionTercero']['Numero']));
							} else {
                                $identificacion_tercero = $OtrosCargos->appendChild($xmlDoc->createElement("IdentificacionTercero"));
								$identificacion_tercero->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['OtrosCargos'][$oc]['IdentificacionTercero']['Tipo']));
								$identificacion_tercero->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['OtrosCargos'][$oc]['IdentificacionTercero']['Numero']));
                            }
						}
					}
					if ($this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumentoOC'] === '04') {
						$OtrosCargos->appendChild($xmlDoc->createElement("NombreTercero", $this->xmlCompleto['OtrosCargos'][$oc]['NombreTercero']));
					}
					$OtrosCargos->appendChild($xmlDoc->createElement("Detalle", $this->xmlCompleto['OtrosCargos'][$oc]['Detalle']));
					$OtrosCargos->appendChild($xmlDoc->createElement("PorcentajeOC", $this->xmlCompleto['OtrosCargos'][$oc]['PorcentajeOC']));
					$OtrosCargos->appendChild($xmlDoc->createElement("MontoCargo", $this->ModificarEntero($this->xmlCompleto['OtrosCargos'][$oc]['MontoCargo'])));
				}
			}
		}

		//Comienzo de los totales, Toda la informacion del resumen de los diferentes tipos de documentos
		//Nivel Principal resumen_factura
		$resumen_factura = $facturacion->appendChild($xmlDoc->createElement("ResumenFactura"));
			//Nivel 2 para codigo moneda
			$codigo_tipo_moneda = $resumen_factura->appendChild($xmlDoc->createElement("CodigoTipoMoneda"));
			$codigo_tipo_moneda->appendChild($xmlDoc->createElement("CodigoMoneda", $this->xmlCompleto['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']));
			$codigo_tipo_moneda->appendChild($xmlDoc->createElement("TipoCambio", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'])));
			//Fin Nivel 2 para codigo de moneda

		$resumen_factura->appendChild($xmlDoc->createElement("TotalServGravados", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalServGravados'])));
		$resumen_factura->appendChild($xmlDoc->createElement("TotalServExentos", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalServExentos'])));
		//Si el documento tiene exoneracion se agrega el nodo de servicio Exonerado
		if ($this->xmlCompleto['TieneExoneracion'] === '01') {
			$resumen_factura->appendChild($xmlDoc->createElement("TotalServExonerado", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalServExonerado'])));
		}
		//Nuevo nodo 4.4 servicio no sujeto para productos y servicios no sujetos.
		if ($this->xmlCompleto['tipoDocumento'] != '09') {
		$resumen_factura->appendChild($xmlDoc->createElement("TotalServNoSujeto", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalServNoSujeto'])));
		}


		


		//Fin de los totales para servicios.

		//Comienzo de totales para Mercancias y productos gravados, que no son servicios.
		$resumen_factura->appendChild($xmlDoc->createElement("TotalMercanciasGravadas", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalMercanciasGravadas'])));
		$resumen_factura->appendChild($xmlDoc->createElement("TotalMercanciasExentas", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalMercanciasExentas'])));
		if ($this->xmlCompleto['TieneExoneracion'] === '01') {
			$resumen_factura->appendChild($xmlDoc->createElement("TotalMercExonerada", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalMercExonerada'])));
		}
			if ($this->xmlCompleto['tipoDocumento'] != '09') {
		$resumen_factura->appendChild($xmlDoc->createElement("TotalMercNoSujeta", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalMercNoSujeta'])));
			}
		//Nuevo nodo 4.4 mercancias no sujetos para productos y mercancias no sujetos.

		//Fin de los totales de mercancias y productos gravados

		//Comienzo de sumatorias entre servicios y mercancias
		$resumen_factura->appendChild($xmlDoc->createElement("TotalGravado", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalGravado'])));
		$resumen_factura->appendChild($xmlDoc->createElement("TotalExento", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalExento'])));
        if ($this->xmlCompleto['TieneExoneracion'] === '01') {
			$resumen_factura->appendChild($xmlDoc->createElement("TotalExonerado", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalExonerado'])));
		}
			if ($this->xmlCompleto['tipoDocumento'] != '09') {
		$resumen_factura->appendChild($xmlDoc->createElement("TotalNoSujeto", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalNoSujeto'])));
			}
		//Nuevo nodo 4.4 total sumatoria entre servicio no sujeto y mercancia no sujeta


		$resumen_factura->appendChild($xmlDoc->createElement("TotalVenta", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalVenta'])));
		$resumen_factura->appendChild($xmlDoc->createElement("TotalDescuentos", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalDescuentos'])));
		$resumen_factura->appendChild($xmlDoc->createElement("TotalVentaNeta", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalVentaNeta'])));
		//Comienzo del desglose de impuestos, ahora es un ciclo array de 1 a  1000 que es cada impuesto cobrado.
		//Otros cargos forma parte de la version 4.3 nodo necesario en caso de que se requiera
	if (!empty($this->xmlCompleto['ResumenFactura']['TotalDesgloseImpuesto']) && isset($this->xmlCompleto['ResumenFactura']['TotalDesgloseImpuesto'])) {
    // Inicializa un array para acumular los montos por Codigo y CodigoTarifaIVA
    $montosAcumulados = [];

    // Acumula los montos por Codigo y CodigoTarifaIVA
    foreach ($this->xmlCompleto['ResumenFactura']['TotalDesgloseImpuesto'] as $tdi) {
        $codigo = $tdi['Codigo'];
        $codigoTarifa = $tdi['CodigoTarifaIVA'];
        $montoImpuesto = (float)$tdi['TotalMontoImpuesto'];

        // Crea una clave única para cada combinación de Codigo y CodigoTarifaIVA
        $clave = $codigo . '-' . $codigoTarifa;

        // Si la clave ya existe, suma el monto
        if (isset($montosAcumulados[$clave])) {
            $montosAcumulados[$clave]['TotalMontoImpuesto'] += $montoImpuesto;
        } else {
            // Si no existe, inicializa el monto
            $montosAcumulados[$clave] = [
                'Codigo' => $codigo,
                'CodigoTarifaIVA' => $codigoTarifa,
                'TotalMontoImpuesto' => $montoImpuesto,
            ];
        }
    }

    // Genera el XML a partir de los montos acumulados
    foreach ($montosAcumulados as $item) {
        $total_desglose_impuesto = $resumen_factura->appendChild($xmlDoc->createElement("TotalDesgloseImpuesto"));

        // Agrega el Código (obligatorio)
        $total_desglose_impuesto->appendChild($xmlDoc->createElement("Codigo", $item['Codigo']));

        // Agrega el CodigoTarifaIVA (opcional)
        if (!empty($item['CodigoTarifaIVA'])) {
            $total_desglose_impuesto->appendChild($xmlDoc->createElement("CodigoTarifaIVA", $item['CodigoTarifaIVA']));
        }

        // Agrega el TotalMontoImpuesto (obligatorio)
        $total_desglose_impuesto->appendChild($xmlDoc->createElement("TotalMontoImpuesto", number_format($item['TotalMontoImpuesto'], 5, '.', ''))); // Formato decimal
    }
}
		//fin del desglose de impuestos, ahora es un ciclo array de 1 a  1000 que es cada impuesto cobrado.
		//Este nodo pasa a ser la sumatoria de montos del desglose realizado
		$resumen_factura->appendChild($xmlDoc->createElement("TotalImpuesto", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalImpuesto'])));

		//Nuevo nodo 4.4 sumatoria de los impuestos asumidos por fabrica


		if ($this->xmlCompleto['tipoDocumento'] != '09' && $this->xmlCompleto['tipoDocumento'] != '08') {
		    	$resumen_factura->appendChild($xmlDoc->createElement("TotalImpAsumEmisorFabrica", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalImpAsumEmisorFabrica'])));
				$resumen_factura->appendChild($xmlDoc->createElement("TotalIVADevuelto", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalIVADevuelto'])));
			}

		$resumen_factura->appendChild($xmlDoc->createElement("TotalOtrosCargos", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalOtrosCargos'])));

		//hasta un maximo de 4 medios de pago segun version 4.4 ahora va en los totales
		if (is_array($this->xmlCompleto['ResumenFactura']['MedioPago']) && sizeof($this->xmlCompleto['ResumenFactura']['MedioPago']) > 0) {
			for ($medio_pago=0; $medio_pago < count($this->xmlCompleto['ResumenFactura']['MedioPago']); $medio_pago++) {
				$medio_pago_linea = $resumen_factura->appendChild($xmlDoc->createElement("MedioPago"));
				$medio_pago_linea->appendChild($xmlDoc->createElement("TipoMedioPago", $this->xmlCompleto['ResumenFactura']['MedioPago'][$medio_pago]['TipoMedioPago']));
				if ($this->xmlCompleto['ResumenFactura']['MedioPago'][$medio_pago]['TipoMedioPago'] == '99') {
					$medio_pago_linea->appendChild($xmlDoc->createElement("MedioPagoOtros", $this->xmlCompleto['ResumenFactura']['MedioPago'][$medio_pago]['MedioPagoOtros']));
				}
				$medio_pago_linea->appendChild($xmlDoc->createElement("TotalMedioPago", $this->xmlCompleto['ResumenFactura']['MedioPago'][$medio_pago]['TotalMedioPago']));
			}
		}else{
			$medio_pago_linea = $resumen_factura->appendChild($xmlDoc->createElement("MedioPago"));
			$medio_pago_linea->appendChild($xmlDoc->createElement("TipoMedioPago", $this->xmlCompleto['ResumenFactura']['MedioPago']['TipoMedioPago']));
			if ($this->xmlCompleto['ResumenFactura']['MedioPago']['TipoMedioPago'] == '99') {
				$medio_pago_linea->appendChild($xmlDoc->createElement("MedioPagoOtros", $this->xmlCompleto['ResumenFactura']['MedioPago']['MedioPagoOtros']));
			}
			$medio_pago_linea->appendChild($xmlDoc->createElement("TotalMedioPago", $this->xmlCompleto['ResumenFactura']['MedioPago']['TotalMedioPago']));
		}

		$resumen_factura->appendChild($xmlDoc->createElement("TotalComprobante", $this->ModificarEntero($this->xmlCompleto['ResumenFactura']['TotalComprobante'])));
		//Fin de resumen de totales para la 4.4

        if (!empty($this->xmlCompleto['InformacionReferencia'])) {
            // Normativa que va en codigo duro esto porque forma parte de la documentacion de Hacienda
            $InformacionRe = $facturacion->appendChild($xmlDoc->createElement("InformacionReferencia"));
            $InformacionRe->appendChild($xmlDoc->createElement("TipoDocIR", $this->xmlCompleto['InformacionReferencia']['TipoDocIR']));
            $InformacionRe->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['InformacionReferencia']['Numero']));
            $InformacionRe->appendChild($xmlDoc->createElement("FechaEmisionIR", $this->xmlCompleto['InformacionReferencia']['FechaEmisionIR']));
            $InformacionRe->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['InformacionReferencia']['Codigo']));
            $InformacionRe->appendChild($xmlDoc->createElement("Razon", $this->xmlCompleto['InformacionReferencia']['Razon']));
        }
        if($this->xmlCompleto['tipoDocumento'] === '08' or $this->xmlCompleto['tipoDocumento'] === '03'){
            if (!empty($this->xmlCompleto['Otros'])) {
                // Campo Otros Utilzado solo para otros textos aplicado en 06-06-2020
                $Otros = $facturacion->appendChild($xmlDoc->createElement("Otros"));
                $Otros->appendChild($xmlDoc->createElement("OtroTexto", $this->xmlCompleto['Otros']['OtroTexto']));
            }
        }
			$xmlDoc->formatOutput = true;
			$xml_sin_firma = $xmlDoc->saveXML();
			//Aca envio a firmar el documento que se acaba de guardar
			$firma = $this->armar_firmado($xml_sin_firma);
			//Luego de retornar la firma sin el documento hay que agregarlo al documento que se va a enviar contra el webservice de hacienda
			//Aca cargo el documento de la firma a un Nodo
			$dom = new DOMDocument();
			$dom_2 = new DOMDocument();
			//cargo el documento sin firma
			$doc_sin_firma = $dom->LoadXML($xml_sin_firma);
			// cargo la firma del documento canonicalizado probado hasta el 19-01-2018
			$dom_firma = $dom_2->LoadXML($firma);
			//canonicalizo la firm
			$valor_2 = $dom_2->documentElement;
			$dom_2->formatOutput = true;
			$dom->documentElement->appendChild($dom->importNode($valor_2, true));
			$documento_con_firma = $dom->C14N($doc_sin_firma);
			$xml_save = '<?xml version="1.0" encoding="utf-8"?>'.$documento_con_firma;
			switch ($this->xmlCompleto['tipoDocumento']) {
				case '01':
					$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/Facturas/Envio/factura#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '02':
					$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/NotaDebito/Envio/NotaDebito#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '03':
					$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/NotaCredito/Envio/NotaCredito#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '04':
					$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/Tiquete/Envio/Tiquete#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '08':
					$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/FacturaCompra/Envio/FacturaCompra#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '09':
					$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/FacturaExportacion/Envio/FacturaExportacion#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
			}
		return base64_encode($xml_save);
	}

	function armar_xml_receptor($clave, $consecutivo){
		// XML dinamico para el mensaje receptor
		if ($this->xmlCompleto['comando'] == '1') {

			$xml = simplexml_load_file(public_path($this->xmlCompleto['rutaxml']));

		} else {

			$xml = simplexml_load_file($this->xmlCompleto['rutaxml']);
		}
		$cedEmisor= $xml->Emisor->Identificacion->Numero;
		$cedReceptor= $xml->Receptor->Identificacion->Numero;
		$xmlDoc = new DOMDocument('1.0' , 'UTF-8');
		libxml_use_internal_errors(true);

		$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("MensajeReceptor"));
		$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
  		$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.4/mensajeReceptor'));

		//ENCABEZADO DEL XML
		$facturacion->appendChild($xmlDoc->createAttribute("xmlns:xsd"))->appendChild(
   			$xmlDoc->createTextNode('http://www.w3.org/2001/XMLSchema'));
		$facturacion->appendChild($xmlDoc->createAttribute("xmlns:xsi"))->appendChild(
   			$xmlDoc->createTextNode('http://www.w3.org/2001/XMLSchema-instance'));
		//FIN ENCABEZADO DEL XML

		//DATOS DEL MENSAJE
		$facturacion->appendChild($xmlDoc->createElement("Clave", $clave));
		$facturacion->appendChild($xmlDoc->createElement("NumeroCedulaEmisor", $cedEmisor));
		$facturacion->appendChild($xmlDoc->createElement("FechaEmisionDoc", $this->xmlCompleto['fechaEmision']));
		$facturacion->appendChild($xmlDoc->createElement("Mensaje", $this->xmlCompleto['Emisor']['Mensaje']));
		$facturacion->appendChild($xmlDoc->createElement("DetalleMensaje", $this->xmlCompleto['Emisor']['DetalleMensaje']));
		$facturacion->appendChild($xmlDoc->createElement("MontoTotalImpuesto", $this->xmlCompleto['Emisor']['MontoTotalImpuesto']));
		$facturacion->appendChild($xmlDoc->createElement("CodigoActividad", $this->xmlCompleto['Emisor']['CodigoActividad']));
		if ($this->xmlCompleto['CondicionImpuesto'] != '0') {
			$facturacion->appendChild($xmlDoc->createElement("CondicionImpuesto", $this->xmlCompleto['CondicionImpuesto']));
		}
		$facturacion->appendChild($xmlDoc->createElement("MontoTotalImpuestoAcreditar", $this->xmlCompleto['MontoTotalImpuestoAcreditar']));
		$facturacion->appendChild($xmlDoc->createElement("MontoTotalDeGastoAplicable", $this->xmlCompleto['MontoTotalDeGastoAplicable']));
		$facturacion->appendChild($xmlDoc->createElement("TotalFactura", $this->xmlCompleto['Emisor']['TotalFactura']));
		$facturacion->appendChild($xmlDoc->createElement("NumeroCedulaReceptor", $cedReceptor));
		$facturacion->appendChild($xmlDoc->createElement("NumeroConsecutivoReceptor", $consecutivo));
		//FIN DATOS MENSAJE

		$xmlDoc->formatOutput = true;
		$xml_sin_firma = $xmlDoc->saveXML();
		//Aca envio a firmar el documento que se acaba de guardar
		$firma = $this->armar_firmado($xml_sin_firma);
		//Luego de retornar la firma sin el documento hay que agregarlo al documento que se va a enviar contra el webservice de hacienda
		//Aca cargo el documento de la firma a un Nodo
		$dom = new DOMDocument();
		$dom_2 = new DOMDocument();
		//cargo el documento sin firma
		$doc_sin_firma = $dom->LoadXML($xml_sin_firma);
		// cargo la firma del documento canonicalizado probado hasta el 19-01-2018
		$dom_firma = $dom_2->LoadXML($firma);
		//canonicalizo la firm
		$valor_2 = $dom_2->documentElement;
		$dom_2->formatOutput = true;
		$dom->documentElement->appendChild($dom->importNode($valor_2, true));
		$documento_con_firma = $dom->C14N($doc_sin_firma);
		$xml_save = '<?xml version="1.0" encoding="utf-8"?>'.$documento_con_firma;
		//Luego de completar todo el XML con la firma debe ser guardado en una ruta que se debe especificar en el siguiente put_contents ya sea desde Base de datos o en codigo duro para todas las empresas
		switch ($this->xmlCompleto['tipoDocumento']) {
			case '05':
				$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/DocReceptor/Envio/aceptados/MensajeR#".$clave.".xml";
				file_put_contents(public_path($ruta), $xml_save);
			break;
			case '06':
				$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/DocReceptor/Envio/parcialAceptados/MensajeR#".$clave.".xml";
				file_put_contents(public_path($ruta), $xml_save);
			break;
			case '07':
				$ruta = "./XML/".$this->xmlCompleto['idconfigfact']."/DocReceptor/Envio/rechazados/MensajeR#".$clave.".xml";
				file_put_contents(public_path($ruta), $xml_save);
			break;
		}

		return base64_encode($xml_save);
	}

	function armar_firmado($pxml){
		libxml_use_internal_errors(true);
		$GUID = $this->getGUID();
		$GUID = str_replace("{", "", $GUID);
		$GUID = str_replace("}", "", $GUID);
		$GUID = strtolower($GUID);
		$GUID2 = $this->getGUID();
		$GUID2 = str_replace("{", "", $GUID2);
		$GUID2 = str_replace("}", "", $GUID2);
		$GUID2 = strtolower($GUID2);
		$dom = new DOMDocument();
		$docXml = new DOMDocument();
		$xml_tr = $dom->LoadXML($pxml);
       	 $xml_can = $dom->C14N($xml_tr);
        $dvalue = base64_encode(hash("sha256", $xml_can, true));
		$Signature = $docXml->appendChild($docXml->createElement("ds:Signature"));
		//ID de referencia debe ser dinamico ahorita esta en codigo duro
		$Signature->appendChild(
			$docXml->createAttribute("xmlns:ds"))->appendChild(
      		$docXml->createTextNode('http://www.w3.org/2000/09/xmldsig#')
      	);
		$Signature->appendChild(
			$docXml->createAttribute("Id"))->appendChild(
      		$docXml->createTextNode("Signature-".$GUID)
      	);
		// Creacion del Signed Info
    	$SignedInfo = $Signature->appendChild($docXml->createElement("ds:SignedInfo"));
    	//Metodo de canonicalizado con sus atributos
    	$CanonicalizationMethod = $SignedInfo->appendChild($docXml->createElement("ds:CanonicalizationMethod"));
    	$CanonicalizationMethod->appendChild(
    		$docXml->createAttribute("Algorithm"))->appendChild(
      		$docXml->createTextNode("http://www.w3.org/TR/2001/REC-xml-c14n-20010315")
      	);
    	//Signature Method para saber cual es el tipo de encriptado
    	$SignatureMethod = $SignedInfo->appendChild($docXml->createElement("ds:SignatureMethod"));
    	$SignatureMethod->appendChild(
    		$docXml->createAttribute("Algorithm"))->appendChild(
      		$docXml->createTextNode("http://www.w3.org/2001/04/xmldsig-more#rsa-sha256")
      	);
    	// Las referencias de las URI que se van a encriptar el primero es para todo el documento de arriba con la firma incluida vacia
    	$Reference = $SignedInfo->appendChild($docXml->createElement("ds:Reference"));
    	$Reference->appendChild(
    		$docXml->createAttribute("Id"))->appendChild(
      		$docXml->createTextNode("Reference-".$GUID2)
      	);
    	$Reference->appendChild(
    		$docXml->createAttribute("URI"))->appendChild(
      		$docXml->createTextNode("")
      	);
    	//Transformacion del documento para ser firmado el XML
    	$Transforms = $Reference->appendChild($docXml->createElement("ds:Transforms"));
    	$Transform = $Transforms->appendChild($docXml->createElement("ds:Transform"));
    	$Transform->appendChild(
    		$docXml->createAttribute("Algorithm"))->appendChild(
      		$docXml->createTextNode("http://www.w3.org/2000/09/xmldsig#enveloped-signature")
      	);

    	$DigestMethod = $Reference->appendChild($docXml->createElement("ds:DigestMethod"));
    	$DigestMethod->appendChild(
    		$docXml->createAttribute("Algorithm"))->appendChild(
      		$docXml->createTextNode('http://www.w3.org/2001/04/xmlenc#sha256')
      	);
    	$DigestValue = $Reference->appendChild($docXml->createElement("ds:DigestValue", null));
    	//Fin de la primera referencia la de todo el documento
    	//Aca comienza la segunda referencia
    	$Reference3 = $SignedInfo->appendChild($docXml->createElement("ds:Reference"));
    	$Reference3->appendChild($docXml->createAttribute("Id"))->appendChild($docXml->createTextNode("ReferenceKeyInfo"));
    	$Reference3->appendChild($docXml->createAttribute("URI"))->appendChild($docXml->createTextNode("#KeyInfoId-Signature-".$GUID));
    	$DigestMethod = $Reference3->appendChild($docXml->createElement("ds:DigestMethod"));
    	$DigestMethod->appendChild(
    		$docXml->createAttribute("Algorithm"))->appendChild(
      		$docXml->createTextNode("http://www.w3.org/2001/04/xmlenc#sha256")
      	);
    	$DigestValue = $Reference3->appendChild($docXml->createElement("ds:DigestValue", null));
    	$Reference2 = $SignedInfo->appendChild($docXml->createElement("ds:Reference"));
    	$Reference2->appendChild(
    		$docXml->createAttribute("Type"))->appendChild(
      		$docXml->createTextNode("http://uri.etsi.org/01903#SignedProperties")
      	);
    	$Reference2->appendChild(
    		$docXml->createAttribute("URI"))->appendChild(
      		$docXml->createTextNode("#SignedProperties-Signature-".$GUID)
      	);
    	$DigestMethod = $Reference2->appendChild($docXml->createElement("ds:DigestMethod"));
    	$DigestMethod->appendChild(
    		$docXml->createAttribute("Algorithm"))->appendChild(
      		$docXml->createTextNode("http://www.w3.org/2001/04/xmlenc#sha256")
      	);
    	$DigestValue = $Reference2->appendChild($docXml->createElement("ds:DigestValue", null));
    	//Fin de la Segunda referencia
    	//Signature Value del documento
    	$SignatureValue = $Signature->appendChild($docXml->createElement("ds:SignatureValue", null));
    	$SignatureValue->appendChild(
    		$docXml->createAttribute("Id"))->appendChild(
      		$docXml->createTextNode("SignatureValue-".$GUID)
      	);
    	//Dentro del Key info va la informacion del certificado
    	$KeyInfo = $Signature->appendChild($docXml->createElement("ds:KeyInfo"));
    	$X509Data = $KeyInfo->appendChild($docXml->createElement("ds:X509Data"));
    	$X509Data->appendChild($docXml->createElement("ds:X509Certificate" , null));
    	$KeyValue = $KeyInfo->appendChild($docXml->createElement("ds:KeyValue"));
    	$RSA = $KeyValue->appendChild($docXml->createElement("ds:RSAKeyValue"));
    	$Modulus = $RSA->appendChild($docXml->createElement("ds:Modulus"));
    	$Exponent = $RSA->appendChild($docXml->createElement("ds:Exponent"));
    	//Comienzo del Object es donde esta la firma XADES-EPES de tipo enveloped
    	$GUID3 = $this->getGUID();
		$GUID3 = str_replace("{", "", $GUID3);
		$GUID3 = str_replace("}", "", $GUID3);
		$GUID3 = strtolower($GUID3);
		///////////////////////////////////////
		$GUID4 = $this->getGUID();
		$GUID4 = str_replace("{", "", $GUID4);
		$GUID4 = str_replace("}", "", $GUID4);
		$GUID4 = strtolower($GUID4);
    	$Object = $Signature->appendChild($docXml->createElement("ds:Object"));
    	$Object->appendChild($docXml->createAttribute("Id"))->appendChild($docXml->createTextNode("XadesObjectId-".$GUID3));
    	$xades_qualify = $Object->appendChild($docXml->createElement("xades:QualifyingProperties"));
    	$xades_qualify->appendChild(
    		$docXml->createAttribute("xmlns:xades"))->appendChild(
      		$docXml->createTextNode("http://uri.etsi.org/01903/v1.3.2#")
      	);
    	$xades_qualify->appendChild(
    		$docXml->createAttribute("Id"))->appendChild(
      		$docXml->createTextNode("QualifyingProperties-".$GUID4)
      	);
    	$xades_qualify->appendChild(
    		$docXml->createAttribute("Target"))->appendChild(
      		$docXml->createTextNode("#Signature-".$GUID)
      	);
    	$xades_signed = $xades_qualify->appendChild($docXml->createElement("xades:SignedProperties"));
    	$x_signed_signature = $xades_signed->appendChild($docXml->createElement("xades:SignedSignatureProperties"));
    	$x_signed_signature->appendChild(
    		$docXml->createElement("xades:SigningTime", date('Y-m-d')."T".date('H:i:s')."Z")
    	);
    	$x_signin_certificate = $x_signed_signature->appendChild($docXml->createElement("xades:SigningCertificate"));
    	$x_cert = $x_signin_certificate->appendChild($docXml->createElement("xades:Cert"));
    	$x_cert_digest = $x_cert->appendChild($docXml->createElement("xades:CertDigest"));
    	$x_digst = $x_cert_digest->appendChild($docXml->createElement("ds:DigestMethod"));
    	$x_digst->appendChild(
    		$docXml->createAttribute("Algorithm"))->appendChild(
    		$docXml->createTextNode("http://www.w3.org/2001/04/xmlenc#sha256")
    	);
    	$x_cert_digest->appendChild($docXml->createElement("ds:DigestValue"));
    	$x_issuer_serial = $x_cert->appendChild($docXml->createElement("xades:IssuerSerial"));
    	$x_issuer_serial->appendChild($docXml->createElement("ds:X509IssuerName"));
    	$x_issuer_serial->appendChild($docXml->createElement("ds:X509SerialNumber"));
    	$x_signed_policy = $x_signed_signature->appendChild($docXml->createElement("xades:SignaturePolicyIdentifier"));
    	$x_signature_policy = $x_signed_policy->appendChild($docXml->createElement("xades:SignaturePolicyId"));
    	$x_sigpolicy = $x_signature_policy->appendChild($docXml->createElement("xades:SigPolicyId"));
    	$x_sigpolicy->appendChild($docXml->createElement("xades:Identifier", "https://tribunet.hacienda.go.cr/docs/esquemas/2016/v4/Resolucion%20Comprobantes%20Electronicos%20%20DGT-R-48-2016.pdf"));
    	$x_sigpolicy->appendChild($docXml->createElement("xades:Description"));
    	$x_sigpolicy_hash = $x_signature_policy->appendChild($docXml->createElement("xades:SigPolicyHash"));
    	$d_method = $x_sigpolicy_hash->appendChild($docXml->createElement("ds:DigestMethod"));
    	$d_method->appendChild(
    		$docXml->createAttribute("Algorithm"))->appendChild(
    		$docXml->createTextNode("http://www.w3.org/2000/09/xmldsig#sha1")
    	);
    	$x_sigpolicy_hash->appendChild($docXml->createElement("ds:DigestValue", null));
   		$SignedDataObjectProperties = $xades_signed->appendChild($docXml->createElement("xades:SignedDataObjectProperties"));
   		$DataObjectFormat = $SignedDataObjectProperties->appendChild($docXml->createElement("xades:DataObjectFormat"));
   			$DataObjectFormat->appendChild($docXml->createAttribute("ObjectReference"))->appendChild($docXml->createTextNode("#Reference-".$GUID2));
   			$DataObjectFormat->appendChild($docXml->createElement("xades:MimeType", "text/xml"));
   			$DataObjectFormat->appendChild($docXml->createElement("xades:Encoding", "UTF-8"));
   		$docXml->C14N();
    	// Comienzo a armar dinamicamente los digest y todos los valores null
    	$array = openssl_x509_parse(openssl_x509_read($this->certs['cert']));
    	$string = '';
    	$issuer_reverse = array_reverse($array['issuer']);
    		foreach ($issuer_reverse as $key => $value) {
    			$string .= $key.'='.$value.',';
    		}
    	$issuername = substr($string, 0, -1);
    	$certificado = $this->Normalizar_Certificado();
    	$X509Data->getElementsByTagName('ds:X509Certificate')->item(0)->nodeValue = $certificado;

    	$RSA->getElementsByTagName("ds:Modulus")->item(0)->nodeValue = $this->getModulus();
    	$RSA->getElementsByTagName("ds:Exponent")->item(0)->nodeValue = $this->getExponent();
    	$Reference->getElementsByTagName('ds:DigestValue')->item(0)->nodeValue = $dvalue;

    	//Coloco el certificado y todo l oque necesita la firma XADES
    	$x_issuer_serial->getElementsByTagName('ds:X509SerialNumber')->item(0)->nodeValue = $this->serial;
    	$x_issuer_serial->getElementsByTagName('ds:X509IssuerName')->item(0)->nodeValue = $issuername;

		$KeyInfo->appendChild($docXml->createAttribute("xmlns"))->appendChild($docXml->createTextNode("".$this->name_space));
		$KeyInfo->appendChild($docXml->createAttribute("xmlns:ds"))->appendChild($docXml->createTextNode("http://www.w3.org/2000/09/xmldsig#"));
		$KeyInfo->appendChild($docXml->createAttribute("Id"))->appendChild($docXml->createTextNode("KeyInfoId-Signature-".$GUID));
    	$valor_key = $docXml->saveHTML($Signature->getElementsByTagName('ds:KeyInfo')->item(0));

    	//Calculo el Digest value de el key info
    	$digest_key = base64_encode(hash("sha256", $valor_key, true));
    	$Reference3->getElementsByTagName('ds:DigestValue')->item(0)->nodeValue = $digest_key;

    	//El siguiente diguest esta en codigo duro porque es el del PDF de la resulocion en caso de que se modifique el PDF debe ser calculado de la siguiente manera: No existe informacion al 26-01-2018

   		$digest3 = 'V8lVVNGDCPen6VELRD1Ja8HARFk=';
    	$x_sigpolicy_hash->getElementsByTagName('ds:DigestValue')->item(0)->nodeValue = $digest3;

    	// Se calcula el diguest del Raw data que es el certificado limpio en decode de base 64
    	$digest_xades = base64_encode(hash("sha256", base64_decode($certificado), true));
    	$x_cert_digest->getElementsByTagName('ds:DigestValue')->item(0)->nodeValue = $digest_xades;

		//Comienzo del Signed Properties deben estar todos los pasos listos del a firma Xades
		$xades_signed->appendChild($docXml->createAttribute("xmlns"))->appendChild($docXml->createTextNode("".$this->name_space));
		$xades_signed->appendChild($docXml->createAttribute("xmlns:ds"))->appendChild($docXml->createTextNode("http://www.w3.org/2000/09/xmldsig#"));
    	$xades_signed->appendChild($docXml->createAttribute("xmlns:xades"))->appendChild($docXml->createTextNode("http://uri.etsi.org/01903/v1.3.2#"));
    	$xades_signed->appendChild($docXml->createAttribute("Id"))->appendChild($docXml->createTextNode("SignedProperties-Signature-".$GUID));
    	$valor_2 = $docXml->saveHTML($xades_qualify->getElementsByTagName('xades:SignedProperties')->item(0));
    	//Calculo el diguest del Signed Properties del Xades
    	$digest2 = base64_encode(hash("sha256", $valor_2, true));
    	//Sha1 del Digest para la firma XADES QUE SE ENCUENTRA EN EL CERT
    	$Reference2->getElementsByTagName('ds:DigestValue')->item(0)->nodeValue = $digest2;
    	$SignedInfo->appendChild($docXml->createAttribute("xmlns"))->appendChild($docXml->createTextNode("".$this->name_space));
    	$SignedInfo->appendChild($docXml->createAttribute("xmlns:ds"))->appendChild($docXml->createTextNode("http://www.w3.org/2000/09/xmldsig#"));
    	$valor_4 = $docXml->saveHTML($Signature->getElementsByTagName('ds:SignedInfo')->item(0));
    	//guardo el signed info para ver que tiene
  		$digest_value_firma = $this->sign($valor_4);
    	$Signature->getElementsByTagName('ds:SignatureValue')->item(0)->nodeValue = $digest_value_firma;
		$docXml->formatOutput = true;
		$firma = $docXml->saveXML();
		$dom_5 = new DOMDocument();
		$firma_xml = $dom_5->LoadXML($firma);
		$firma_can = $dom_5->C14N($firma_xml);
		$firma_final = $this->LinealizarOutput($firma_can);
		libxml_use_internal_errors(false);
		return $firma_final;
	}
}

 ?>
