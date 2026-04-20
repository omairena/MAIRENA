<?php 
/**
 * 
 */

namespace App\Libraries;

class funcionFacturacion506
{
	public  $xmlCompleto, $array_seguridad, $config, $data, $certs, $name_space, $urlApi;
	function __construct($array_xml, $array_seguridad, array $config = [])
	{
		$this->xmlCompleto = $array_xml;
		$this->array_seguridad = $array_seguridad;
		if ($this->array_seguridad['client_id'] === 'api-stag') {
			$this->urlApi = 'https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1/';
		}else{
			$this->urlApi = 'https://api.comprobanteselectronicos.go.cr/recepcion/v1/';
		}
		
		if (!$config) {
		   	$config = [];
   		}
   		switch ($this->xmlCompleto['tipoDocumento']) {
      		case '01':
           		$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronica';
      		break;
      		case '02':
           		$this->name_space =  'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaDebitoElectronica';
      		break;
      		case '03':
           		$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaCreditoElectronica';
      		break;
      		case '04':
           		$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/tiqueteElectronico';
      		break;
      		case '05':
      			$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/mensajeReceptor';
        	break;
        	case '08':
      			$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronicaCompra';
        	break;
        	case '09':
      			$this->name_space = 'https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronicaExportacion';
        	break;
    	}
		//Esta parte se debe traer de base de datos la ruta de donde esta guardado el certificado .p12 para poder adquirir lo que tiene dentro si se crea una carpeta en el servidor.	
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
   				} else {
   					return $this->error('Archivo de la firma electrónica '.basename($this->config['file']).' no puede ser leído');
   				}
   			}
   			// leer datos de la firma electrónica
   			if ($this->config['data'] and openssl_pkcs12_read($this->config['data'], $this->certs, $this->config['pass'])===false) {
   				return $this->error('No fue posible leer los datos de la firma electrónica (verificar la contraseña)');
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

    function ModificarEntero($pvalor){
		$valor = number_format($pvalor,5,',','.');
		$valor = str_replace('.','',$valor);
		$valor = str_replace(',','.', $valor);
		return $valor;
	}

	function ConviertePhone($output){
		$search = array('+', '(', ')', ' ');
		$replace = array('', '','','');
		$phone = str_replace($search, $replace, $output);
		return $phone;
	}

	//Funcion para crear la clave dinamica recibe varios parametros
	function Crea_clave(){

		$pais = '506'; //Corresponde al pais donde se esta ejecutando la clave
		$documento = $this->xmlCompleto['numeroFactura'];
		$cod_tipodoc = $this->xmlCompleto['tipoDocumento'];
		$emision = $this->xmlCompleto['fechaEmision'];
		if ($this->xmlCompleto['tipoDocumento'] != '08') {
			$emisor = $this->xmlCompleto['Emisor']['Identificacion']['Numero'];
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
			$respuesta = 'bearer '.$response->{'access_token'};
  			return $respuesta;
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
	// Fin del proceso del logout
    // Proceso a enviar el documento
	function envia_documento(){
		$fecha = $this->xmlCompleto['fechaEmision'];
		$numeroFactura = $this->xmlCompleto['numeroFactura'];
		$tipoDocumento = $this->xmlCompleto['tipoDocumento'];
		//datos del emisor
		$codIdEmisor = $this->xmlCompleto['Emisor']['Identificacion']['Tipo'];
		$numIdEmisor = $this->xmlCompleto['Emisor']['Identificacion']['Numero'];
		//datos del receptor
		if ($this->xmlCompleto['tipoDocumento'] != '04') {
			$codIdReceptor = $this->xmlCompleto['Receptor']['Identificacion']['Tipo'];
			$numIdReceptor = $this->xmlCompleto['Receptor']['Identificacion']['Numero'];
		}
		
		//Armo la clave del documento a enviar
		$clave = $this->Crea_clave();
		$xml_ws = $this->armar_xml($clave);
		// Luego de recibir el XML armo una clase para crear un array del JSON para ser enviado en el CURL 
		// Se debe verificar la informacion de Hacienda para cumplir con lo solicitado aca en el JSON
		$array = new \stdClass();
		$array->clave = "".$clave;
		$array->fecha = "".$fecha;
		$array->emisor = array( 
			"tipoIdentificacion" => ''.$codIdEmisor, 
			"numeroIdentificacion" => ''.str_pad($numIdEmisor, 12, "0", STR_PAD_LEFT)
		);
		if ($this->xmlCompleto['tipoDocumento'] != '04') {
			$codIdReceptor = $this->xmlCompleto['Receptor']['Identificacion']['Tipo'];
			$numIdReceptor = $this->xmlCompleto['Receptor']['Identificacion']['Numero'];
			$array->receptor = array(
			"tipoIdentificacion" => ''.$codIdReceptor, 
			"numeroIdentificacion" => ''.str_pad($numIdReceptor, 12, "0", STR_PAD_LEFT)
			);
		}
		
		$array->comprobanteXml = $xml_ws;
		$json = json_encode($array, true);
		$token = $this->Generar_Token();
		$curl_envio = $this->Envia_Doc($json,$token,$clave);
		$logout = $this->logout($token);
		//$guardado = $this->Guardar($clave);
		return $curl_envio;
	}

	function Guardar($pclave)
	{
		$tipodocumento = $this->xmlCompleto['tipoDocumento'];
		$numero_documento = $this->xmlCompleto['numeroFactura'];
		$sales_id = $this->xmlCompleto['sales_id'];
		switch ($this->xmlCompleto['tipoDocumento']) {
			case '01':
				$rutaxml = "./assets/XML/Facturas/Envio/factura#".$pclave.".xml";
			break;
			case '02':
				$rutaxml = "./assets/XML/NotaDebito/Envio/NotaDebito#".$pclave.".xml";
			break;
			case '03':
				$rutaxml = "./assets/XML/NotaCredito/Envio/NotaCredito#".$pclave.".xml";
			break;
			case '04':
				$rutaxml = "./assets/XML/Tiquete/Envio/Tiquete#".$pclave.".xml";
			break;
			case '05':
				$rutaxml = "./assets/XML/DocReceptor/Envio/aceptados/MensajeR#".$pclave.".xml";
			break;
			case '06':
				$rutaxml = "./assets/XML/DocReceptor/Envio/parcialAceptados/MensajeR#".$pclave.".xml";
			break;
			case '07':
				$rutaxml = "./assets/XML/DocReceptor/Envio/rechazados/MensajeR#".$pclave.".xml";
			break;
			case '08':
			break;
			case '09':
				$rutaxml = "./assets/XML/FacturaExportacion/Envio/FacturaExportacion#".$pclave.".xml";
			break;
		}
		$fechahora = date('Y-m-d');
		$consecutivo = $this->armar_consecutivo();
		$cons = LogFacelectron::find_by_sql("SELECT * FROM `zarest_facelectron` WHERE sales_id = '$sales_id'");
		if (empty($cons)) {
			$attributes = array(
            	'sales_id' => $sales_id,
            	'tipodoc' => $tipodocumento,
            	'consecutivo' => $consecutivo,
            	'numdoc' => $numero_documento,
            	'clave' => $pclave,
            	'codigoHTTP' => '200',
            	'rutaxml' => $rutaxml,
            	'fechahora' => $fechahora
        	);
        	LogFacelectron::create($attributes);
        	return TRUE;
		}else{
			return false;
		}
		
	}
	// Proceso de envio de documento para mensaje receptor
	function envia_documento_receptor(){
		$xml = simplexml_load_file($this->xmlCompleto['rutaxml']);
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
		$logout = $this->logout($token);
		return $curl_envio;
	}
	// Fin del proceso de envio del documento

	function armar_xml($clave){
		// Comienzo del XML DINAMICO con todos los datos referenciales empresa, cliente, productos.
		// El documento creado a travez del Domdocument de PHP content UTF-8 version 1.0 solicitado por la documentacion de Hacienda
		// Realizado por Luis D. Carreño - para el pais de Costa Rica
		$xmlDoc = new DOMDocument('1.0' , 'UTF-8');
		libxml_use_internal_errors(true);
		switch ($this->xmlCompleto['tipoDocumento']) {
			case '01':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("FacturaElectronica"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronica'));
			break;
			case '02':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("NotaDebitoElectronica"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaDebitoElectronica'));
			break;
			case '03':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("NotaCreditoElectronica"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaCreditoElectronica'));
			break;
			case '04':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("TiqueteElectronico"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/tiqueteElectronico'));
			break;
			case '08':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("FacturaElectronicaCompra"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronicaCompra'));
			break;
			case '09':
				$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("FacturaElectronicaExportacion"));
				$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
      			$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronicaExportacion'));
			break;
	
		}
		$facturacion->appendChild($xmlDoc->createAttribute("xmlns:xsd"))->appendChild(
			$xmlDoc->createTextNode('http://www.w3.org/2001/XMLSchema'));
		$facturacion->appendChild($xmlDoc->createAttribute("xmlns:xsi"))->appendChild(
   			$xmlDoc->createTextNode('http://www.w3.org/2001/XMLSchema-instance'));
		//La clave que se recibe como parametro en la funcion se almacena en este nodo
		$clave = $facturacion->appendChild($xmlDoc->createElement("Clave", $clave));
		$CodigoActividad = $facturacion->appendChild($xmlDoc->createElement("CodigoActividad", $this->xmlCompleto['CodigoActividad']));
		//Numero Consecutivo que tambien debe ser armado
		$nconsecutivo = $facturacion->appendChild($xmlDoc->createElement("NumeroConsecutivo", $this->armar_consecutivo()));
		//Fecha de Emision que va armada dinamicamente igual tiene su formato
		$fecha_emision = $facturacion->appendChild($xmlDoc->createElement("FechaEmision", $this->xmlCompleto['fechaEmision']));
			//Comienzo del Nodo del Emisor
			$emisor = $facturacion->appendChild($xmlDoc->createElement("Emisor"));
			//Datos Globales de la Seccion del Emisor aca comienza todo lo refente al emisor
			$emisor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Emisor']['Nombre']));
			//Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
			$ident_emisor = $emisor->appendChild($xmlDoc->createElement("Identificacion"));
				$ident_emisor->appendChild($xmlDoc->createElement("Tipo",  $this->xmlCompleto['Emisor']['Identificacion']['Tipo']));
				$ident_emisor->appendChild($xmlDoc->createElement("Numero",  $this->xmlCompleto['Emisor']['Identificacion']['Numero']));
			$emisor->appendChild($xmlDoc->createElement("NombreComercial", $this->xmlCompleto['Emisor']['NombreComercial']));
			//Comienzo de la Ubicacion para el emisor debe tener Provincia, Canton, Distrito, Barrio
			$ubicacion_emisor = $emisor->appendChild($xmlDoc->createElement("Ubicacion"));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Provincia", $this->xmlCompleto['Emisor']['Ubicacion']['Provincia']));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Canton", $this->xmlCompleto['Emisor']['Ubicacion']['Canton']));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("Distrito", $this->xmlCompleto['Emisor']['Ubicacion']['Distrito']));
				$ubicacion_emisor->appendChild($xmlDoc->createElement("OtrasSenas", $this->xmlCompleto['Emisor']['Ubicacion']['OtrasSenas']));
			//Fin de la Ubicacion
			//Comienzo del Numero del Emisor
			$telf_emisor = $emisor->appendChild($xmlDoc->createElement("Telefono"));
				$telf_emisor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Emisor']['Telefono']['CodigoPais']));
				$telf_emisor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Emisor']['Telefono']['NumTelefono']));
			//Fin del Numero del Emisor
			//Comienzo del Numero Fax del Emisor
			$fax_emisor = $emisor->appendChild($xmlDoc->createElement("Fax"));
				$fax_emisor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Emisor']['Fax']['CodigoPais']));
				$fax_emisor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Emisor']['Fax']['NumTelefono']));
			//Fin del Numero Fax del Emisor
			$emisor->appendChild($xmlDoc->createElement("CorreoElectronico", $this->xmlCompleto['Emisor']['CorreoElectronico']));
			//Fin de toda la seccion del Emisor
			if ($this->xmlCompleto['tipoDocumento'] != '04') {	
				//Comienzo de los datos del Receptor
				$receptor = $facturacion->appendChild($xmlDoc->createElement("Receptor"));
				//Datos Globales de la Seccion del Receptor aca comienza todo lo refente al receptor
				$receptor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Receptor']['Nombre']));
				//Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
				$ident_receptor = $receptor->appendChild($xmlDoc->createElement("Identificacion"));
				$ident_receptor->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['Receptor']['Identificacion']['Tipo']));
				$ident_receptor->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['Receptor']['Identificacion']['Numero']));
				//Fin de los tipo de identificacion
				if ($this->xmlCompleto['EsExtranjero'] === '01') {
					$receptor->appendChild($xmlDoc->createElement("IdentificacionExtranjero", $this->xmlCompleto['Receptor']['IdentificacionExtranjero']));
				}
				$receptor->appendChild($xmlDoc->createElement("NombreComercial", $this->xmlCompleto['Receptor']['NombreComercial']));
				if ($this->xmlCompleto['tipoDocumento'] != '09') {
					//Comienzo de la Ubicacion para el receptor debe tener Provincia, Canton, Distrito, Barrio
					$ubicacion_receptor = $receptor->appendChild($xmlDoc->createElement("Ubicacion"));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Provincia", $this->xmlCompleto['Receptor']['Ubicacion']['Provincia']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Canton", $this->xmlCompleto['Receptor']['Ubicacion']['Canton']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Distrito", $this->xmlCompleto['Receptor']['Ubicacion']['Distrito']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("OtrasSenas", $this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas']));
					//Fin de la ubicacion
				}
				//Comienzo del Numero del receptor
				$telf_receptor = $receptor->appendChild($xmlDoc->createElement("Telefono"));
				$telf_receptor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Receptor']['Telefono']['CodigoPais']));
				$telf_receptor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Receptor']['Telefono']['NumTelefono']));
				//Fin del numero del Receptor
				//Comienzo del Numero Fax del Receptor
				$fax_receptor = $receptor->appendChild($xmlDoc->createElement("Fax"));
				$fax_receptor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Receptor']['Fax']['CodigoPais']));
				$fax_receptor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Receptor']['Fax']['NumTelefono']));
				//Fin del Numero Receptor
				$receptor->appendChild($xmlDoc->createElement("CorreoElectronico", $this->xmlCompleto['Receptor']['CorreoElectronico']));
				//Final de los datos del Receptor para el XML
			}else{
				if (!empty($this->xmlCompleto['Receptor'])) { 
					//Comienzo de los datos del Receptor
				$receptor = $facturacion->appendChild($xmlDoc->createElement("Receptor"));
				//Datos Globales de la Seccion del Receptor aca comienza todo lo refente al receptor
				$receptor->appendChild($xmlDoc->createElement("Nombre", $this->xmlCompleto['Receptor']['Nombre']));
				//Van los mencionados datos del Tipo de Identificacion y Numero de Identificacion
				$ident_receptor = $receptor->appendChild($xmlDoc->createElement("Identificacion"));
					$ident_receptor->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['Receptor']['Identificacion']['Tipo']));
					$ident_receptor->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['Receptor']['Identificacion']['Numero']));
				//Fin de los tipo de identificacion
				if ($this->xmlCompleto['EsExtranjero'] === '01') {
					$receptor->appendChild($xmlDoc->createElement("IdentificacionExtranjero", $this->xmlCompleto['Receptor']['IdentificacionExtranjero']));
				}
				$receptor->appendChild($xmlDoc->createElement("NombreComercial", $this->xmlCompleto['Receptor']['NombreComercial']));
				//Comienzo de la Ubicacion para el receptor debe tener Provincia, Canton, Distrito, Barrio
				$ubicacion_receptor = $receptor->appendChild($xmlDoc->createElement("Ubicacion"));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Provincia", $this->xmlCompleto['Receptor']['Ubicacion']['Provincia']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Canton", $this->xmlCompleto['Receptor']['Ubicacion']['Canton']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("Distrito", $this->xmlCompleto['Receptor']['Ubicacion']['Distrito']));
					$ubicacion_receptor->appendChild($xmlDoc->createElement("OtrasSenas", $this->xmlCompleto['Receptor']['Ubicacion']['OtrasSenas']));
				//Fin de la ubicacion
				//Comienzo del Numero del receptor
				$telf_receptor = $receptor->appendChild($xmlDoc->createElement("Telefono"));
					$telf_receptor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Receptor']['Telefono']['CodigoPais']));
					$telf_receptor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Receptor']['Telefono']['NumTelefono']));
				//Fin del numero del Receptor
				//Comienzo del Numero Fax del Receptor
				$fax_receptor = $receptor->appendChild($xmlDoc->createElement("Fax"));
					$fax_receptor->appendChild($xmlDoc->createElement("CodigoPais", $this->xmlCompleto['Receptor']['Fax']['CodigoPais']));
					$fax_receptor->appendChild($xmlDoc->createElement("NumTelefono", $this->xmlCompleto['Receptor']['Fax']['NumTelefono']));
				//Fin del Numero Receptor
				$receptor->appendChild($xmlDoc->createElement("CorreoElectronico", $this->xmlCompleto['Receptor']['CorreoElectronico']));
				//Final de los datos del Receptor para el XML
				}
			}
		$facturacion->appendChild($xmlDoc->createElement("CondicionVenta", $this->xmlCompleto['CondicionVenta']));
		if ($this->xmlCompleto['CondicionVenta'] === '02') {
			$facturacion->appendChild($xmlDoc->createElement("PlazoCredito", $this->xmlCompleto['PlazoCredito']));
		}
		//hasta un maximo de 4 medios de pago segun version 4.3 para los medios de pagos
		if (is_array($this->xmlCompleto['MedioPago'])) {
			for ($mp=0; $mp < count($this->xmlCompleto['MedioPago']); $mp++) {
				$facturacion->appendChild($xmlDoc->createElement("MedioPago", $this->xmlCompleto['MedioPago'][$mp]));
			}
		}else{
			$facturacion->appendChild($xmlDoc->createElement("MedioPago", $this->xmlCompleto['MedioPago']));
		}
		
		//Comienzo de el detalle de Servicio Todos los arrays que existan en linea detalle
		$detalle_servicio = $facturacion->appendChild($xmlDoc->createElement("DetalleServicio"));
		$x = 1;
		for ($i=0; $i < count($this->xmlCompleto['DetalleServicio']); $i++) { 		
			$linea_detalle = $detalle_servicio->appendChild($xmlDoc->createElement("LineaDetalle"));
			$linea_detalle->appendChild($xmlDoc->createElement("NumeroLinea", $x));
			if ($this->xmlCompleto['tipoDocumento'] === '09') {
				$linea_detalle->appendChild($xmlDoc->createElement("PartidaArancelaria", $this->xmlCompleto['DetalleServicio'][$i]['PartidaArancelaria']));
			}
			$linea_detalle->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['DetalleServicio'][$i]['Codigo']));
			$CodigoComercial =	$linea_detalle->appendChild($xmlDoc->createElement("CodigoComercial"));
			$CodigoComercial->appendChild($xmlDoc->createElement("Tipo", $this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']['Tipo']));
			$CodigoComercial->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['DetalleServicio'][$i]['CodigoComercial']['Codigo']));
			$linea_detalle->appendChild($xmlDoc->createElement("Cantidad", $this->xmlCompleto['DetalleServicio'][$i]['Cantidad']));
			$linea_detalle->appendChild($xmlDoc->createElement("UnidadMedida", $this->xmlCompleto['DetalleServicio'][$i]['UnidadMedida']));
			//$linea_detalle->appendChild($xmlDoc->createElement("UnidadMedidaComercial", $this->xmlCompleto['DetalleServicio'][$i]['UnidadMedidaComercial']));
			$linea_detalle->appendChild($xmlDoc->createElement("Detalle", $this->xmlCompleto['DetalleServicio'][$i]['Detalle']));
			$linea_detalle->appendChild($xmlDoc->createElement("PrecioUnitario", $this->xmlCompleto['DetalleServicio'][$i]['PrecioUnitario']));
			$linea_detalle->appendChild($xmlDoc->createElement("MontoTotal", $this->xmlCompleto['DetalleServicio'][$i]['MontoTotal']));

			//Validacion necesaria en caso exista algun descuento
			if (!empty($this->xmlCompleto['DetalleServicio'][$i]['Descuento'])) {
				for ($imp=0; $imp < count($this->xmlCompleto['DetalleServicio'][$i]['Descuento']); $imp++) {
					$Descuento = $linea_detalle->appendChild($xmlDoc->createElement("Descuento"));
					$Descuento->appendChild($xmlDoc->createElement("MontoDescuento", $this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$imp]['MontoDescuento']));
					$Descuento->appendChild($xmlDoc->createElement("NaturalezaDescuento", $this->xmlCompleto['DetalleServicio'][$i]['Descuento'][$imp]['NaturalezaDescuento']));
				}
			}
			$linea_detalle->appendChild($xmlDoc->createElement("SubTotal", $this->xmlCompleto['DetalleServicio'][$i]['SubTotal']));

			if (!empty($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'])) {
				for ($impto=0; $impto < count($this->xmlCompleto['DetalleServicio'][$i]['Impuesto']); $impto++) {
					// Cuando el codigo del impuesto sea 07 debe ir la base imponible en el XML ver 4.3
					if ($this->xmlCompleto['tipoDocumento'] != '09') {
						if ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo'] === '07') {
							$linea_detalle->appendChild($xmlDoc->createElement("BaseImponible", $this->xmlCompleto['DetalleServicio'][$i]['BaseImponible']));
						}
					}
					//Comienzo a armar la linea de impuestos normalmente es 1 solo por linea detalle en caso de que en algun momento exista multiples impuestos se debe recorrer este array y realizar el for correspondiente para agregar tantas lineas se necesiten tantos impuestos tenga
					$impuesto = $linea_detalle->appendChild($xmlDoc->createElement("Impuesto"));
						$impuesto->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo']));
						switch ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo']) {
							case '01':
								$impuesto->appendChild($xmlDoc->createElement("CodigoTarifa", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoTarifa']));
							break;
							case '07':
								$impuesto->appendChild($xmlDoc->createElement("CodigoTarifa", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoTarifa']));
							break;
							case '08':
								$impuesto->appendChild($xmlDoc->createElement("CodigoTarifa", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['CodigoTarifa']));
							break;
						}
						$impuesto->appendChild($xmlDoc->createElement("Tarifa", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Tarifa']));

						if ($this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Codigo'] === '08') {
							$impuesto->appendChild($xmlDoc->createElement("FactorIVA", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['FactorIVA']));
						}

						$impuesto->appendChild($xmlDoc->createElement("Monto", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['Monto']));

						if ($this->xmlCompleto['tipoDocumento'] === '09') {
							$impuesto->appendChild($xmlDoc->createElement("MontoExportacion", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['MontoExportacion']));
						}
						//Estas dos validaciones son aplicadas para verificar si es nota de credito o debito provenientes de una factura de exportacion
						if ($this->xmlCompleto['tipoDocumento'] === '02') {
							if ($this->xmlCompleto['InformacionReferencia']['TipoDoc'] === '09') {
								$impuesto->appendChild($xmlDoc->createElement("MontoExportacion", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['MontoExportacion']));
							}
						}

						if ($this->xmlCompleto['tipoDocumento'] === '03') {
							if ($this->xmlCompleto['InformacionReferencia']['TipoDoc'] === '09') {
								$impuesto->appendChild($xmlDoc->createElement("MontoExportacion", $this->xmlCompleto['DetalleServicio'][$i]['Impuesto'][$impto]['MontoExportacion']));
							}
						}
					//Dentro de los impuestos existe la exoneracion que dependera de la empresa a la cual se le esta facturando, algunas de ellas cuentan con este adicional que es exoneracion
				if ($this->xmlCompleto['tipoDocumento'] != '09') {
					if ($this->xmlCompleto['DetalleServicio'][$i]['EsExoneracion'] === '01') {
						$exoneracion = $impuesto->appendChild($xmlDoc->createElement("Exoneracion"));
							$exoneracion->appendChild($xmlDoc->createElement("TipoDocumento", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['TipoDocumento']));
							$exoneracion->appendChild($xmlDoc->createElement("NumeroDocumento", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['NumeroDocumento']));
							$exoneracion->appendChild($xmlDoc->createElement("NombreInstitucion", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['NombreInstitucion']));
							$exoneracion->appendChild($xmlDoc->createElement("FechaEmision", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['FechaEmision']));
							$exoneracion->appendChild($xmlDoc->createElement("PorcentajeExoneracion", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['PorcentajeExoneracion']));
							$exoneracion->appendChild($xmlDoc->createElement("MontoExoneracion", $this->xmlCompleto['DetalleServicio'][$i]['Exoneracion']['MontoExoneracion']));
					}
				}
				}
			}
			if ($this->xmlCompleto['DetalleServicio'][$i]['EsExoneracion'] === '01') {
				$linea_detalle->appendChild($xmlDoc->createElement("ImpuestoNeto", $this->xmlCompleto['DetalleServicio'][$i]['ImpuestoNeto']));
			}
			$linea_detalle->appendChild($xmlDoc->createElement("MontoTotalLinea", $this->xmlCompleto['DetalleServicio'][$i]['MontoTotalLinea']));
			$x++;
		}
		//Otros cargos forma parte de la version 4.3 nodo necesario en caso de que se requiera
		if (!empty($this->xmlCompleto['OtrosCargos'])) {
			$OtrosCargos = $facturacion->appendChild($xmlDoc->createElement("OtrosCargos"));
			if (!empty($this->xmlCompleto['OtrosCargos'])) {
				for ($oc=0; $oc < count($this->xmlCompleto['OtrosCargos']); $oc++) {
					$OtrosCargos->appendChild($xmlDoc->createElement("TipoDocumento", $this->xmlCompleto['OtrosCargos'][$oc]['TipoDocumento']));
					if ($this->xmlCompleto['tipoDocumento'] != '09') {
						if ($this->xmlCompleto['tipoDocumento'] === '04') {
							$OtrosCargos->appendChild($xmlDoc->createElement("NumeroIdentidadTercero", $this->xmlCompleto['OtrosCargos'][$oc]['NumeroIdentidadTercero']));
							$OtrosCargos->appendChild($xmlDoc->createElement("NombreTercero", $this->xmlCompleto['OtrosCargos'][$oc]['NombreTercero']));
						}
					}
					$OtrosCargos->appendChild($xmlDoc->createElement("Detalle", $this->xmlCompleto['OtrosCargos'][$oc]['Detalle']));
					$OtrosCargos->appendChild($xmlDoc->createElement("Porcentaje", $this->xmlCompleto['OtrosCargos'][$oc]['Porcentaje']));
					$OtrosCargos->appendChild($xmlDoc->createElement("MontoCargo", $this->xmlCompleto['OtrosCargos'][$oc]['MontoCargo']));
				}
			}
		}

		//Comienzo de los totales, Toda la informacion del resumen de los diferentes tipos de documentos
		$resumen_factura = $facturacion->appendChild($xmlDoc->createElement("ResumenFactura"));
			$codtm = $resumen_factura->appendChild($xmlDoc->createElement("CodigoTipoMoneda"));
				$codtm->appendChild($xmlDoc->createElement("CodigoMoneda", $this->xmlCompleto['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']));
				$codtm->appendChild($xmlDoc->createElement("TipoCambio", $this->xmlCompleto['ResumenFactura']['CodigoTipoMoneda']['TipoCambio']));
			$resumen_factura->appendChild($xmlDoc->createElement("TotalServGravados", $this->xmlCompleto['ResumenFactura']['TotalServGravados']));
			$resumen_factura->appendChild($xmlDoc->createElement("TotalServExentos", $this->xmlCompleto['ResumenFactura']['TotalServExentos']));
			if ($this->xmlCompleto['TieneExoneracion'] === '01') {
				$resumen_factura->appendChild($xmlDoc->createElement("TotalServExonerado", $this->xmlCompleto['ResumenFactura']['TotalServExonerado']));
			}
			$resumen_factura->appendChild($xmlDoc->createElement("TotalMercanciasGravadas", $this->xmlCompleto['ResumenFactura']['TotalMercanciasGravadas']));
			$resumen_factura->appendChild($xmlDoc->createElement("TotalMercanciasExentas", $this->xmlCompleto['ResumenFactura']['TotalMercanciasExentas']));
			if ($this->xmlCompleto['TieneExoneracion'] === '01') {
				$resumen_factura->appendChild($xmlDoc->createElement("TotalMercExonerada", $this->xmlCompleto['ResumenFactura']['TotalMercExonerada']));
			}
			$resumen_factura->appendChild($xmlDoc->createElement("TotalGravado", $this->xmlCompleto['ResumenFactura']['TotalGravado']));
			$resumen_factura->appendChild($xmlDoc->createElement("TotalExento", $this->xmlCompleto['ResumenFactura']['TotalExento']));
			if ($this->xmlCompleto['TieneExoneracion'] === '01') {
				$resumen_factura->appendChild($xmlDoc->createElement("TotalExonerado", $this->xmlCompleto['ResumenFactura']['TotalExonerado']));
			}
			$resumen_factura->appendChild($xmlDoc->createElement("TotalVenta", $this->xmlCompleto['ResumenFactura']['TotalVenta']));
			$resumen_factura->appendChild($xmlDoc->createElement("TotalDescuentos", $this->xmlCompleto['ResumenFactura']['TotalDescuentos']));
			$resumen_factura->appendChild($xmlDoc->createElement("TotalVentaNeta", $this->xmlCompleto['ResumenFactura']['TotalVentaNeta']));
			$resumen_factura->appendChild($xmlDoc->createElement("TotalImpuesto", $this->xmlCompleto['ResumenFactura']['TotalImpuesto']));
			if ($this->xmlCompleto['tipoDocumento'] != '09') {
				if ($this->xmlCompleto['tipoDocumento'] != '08') {
					$resumen_factura->appendChild($xmlDoc->createElement("TotalIVADevuelto", $this->xmlCompleto['ResumenFactura']['TotalIVADevuelto']));
					$resumen_factura->appendChild($xmlDoc->createElement("TotalOtrosCargos", $this->xmlCompleto['ResumenFactura']['TotalOtrosCargos']));
				}
			}
			
			$resumen_factura->appendChild($xmlDoc->createElement("TotalComprobante", $this->xmlCompleto['ResumenFactura']['TotalComprobante']));

			if (!empty($this->xmlCompleto['InformacionReferencia'])) {
				// Normativa que va en codigo duro esto porque forma parte de la documentacion de Hacienda
				$InformacionRe = $facturacion->appendChild($xmlDoc->createElement("InformacionReferencia"));
				$InformacionRe->appendChild($xmlDoc->createElement("TipoDoc", $this->xmlCompleto['InformacionReferencia']['TipoDoc']));
				$InformacionRe->appendChild($xmlDoc->createElement("Numero", $this->xmlCompleto['InformacionReferencia']['Numero']));
				$InformacionRe->appendChild($xmlDoc->createElement("FechaEmision", $this->xmlCompleto['InformacionReferencia']['FechaEmision']));
				$InformacionRe->appendChild($xmlDoc->createElement("Codigo", $this->xmlCompleto['InformacionReferencia']['Codigo']));
				$InformacionRe->appendChild($xmlDoc->createElement("Razon", $this->xmlCompleto['InformacionReferencia']['Razon']));
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
					$ruta = "./XML/Facturas/Envio/factura#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '02':
					$ruta = "./XML/NotaDebito/Envio/NotaDebito#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '03':
					$ruta = "./XML/NotaCredito/Envio/NotaCredito#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '04':
					$ruta = "./XML/Tiquete/Envio/Tiquete#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '08':
					$ruta = "./XML/FacturaCompra/Envio/FacturaCompra#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
				case '09':
					$ruta = "./XML/FacturaExportacion/Envio/FacturaExportacion#".$this->Crea_clave().".xml";
					file_put_contents($ruta, $xml_save);
				break;
			}
		return base64_encode($xml_save);
	}

	function armar_xml_receptor($clave, $consecutivo){
		// XML dinamico para el mensaje receptor 
		$xml = simplexml_load_file($this->xmlCompleto['rutaxml']);
		$cedEmisor= $xml->Emisor->Identificacion->Numero;
		$cedReceptor= $xml->Receptor->Identificacion->Numero;
		$xmlDoc = new DOMDocument('1.0' , 'UTF-8');
		libxml_use_internal_errors(true);

		$facturacion = $xmlDoc->appendChild($xmlDoc->createElement("MensajeReceptor"));
		$facturacion->appendChild($xmlDoc->createAttribute("xmlns"))->appendChild(
  		$xmlDoc->createTextNode('https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/mensajeReceptor'));

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
				$ruta = "XML/DocReceptor/Envio/aceptados/MensajeR#".$clave.".xml";
				file_put_contents($ruta, $xml_save);
			break;
			case '06':
				$ruta = "XML/DocReceptor/Envio/parcialAceptados/MensajeR#".$clave.".xml";
				file_put_contents($ruta, $xml_save);
			break;
			case '07':
				$ruta = "XML/DocReceptor/Envio/rechazados/MensajeR#".$clave.".xml";
				file_put_contents($ruta, $xml_save);
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