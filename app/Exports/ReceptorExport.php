<?php

namespace App\Exports;

use App\Receptor;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class ReceptorExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
{
    protected $fecha_desde;
    protected $fecha_hasta;

        public function __construct($fecha_desde, $fecha_hasta)
    {
            $this->fecha_desde = $fecha_desde;
            $this->fecha_hasta = $fecha_hasta;
    }

        public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:AJ1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            '#',   
            'Tipo Documento',  
            'Número Documento Recepción',  
            'Estado del documento',  
            'Detalle Mensaje', 
            'Número Documento Proveedor',
            'Identificación Proveedor',
            'Nombre Proveedor',    
            'Fecha Recepcion Documento',
            'Fecha Emisión Documento', 
            'Clave Documento', 
            'Código Actividad',  
            'No Sujeto',
            'Total Excento 0% (CRC)',
            'Total Reducida 0.5% (CRC)',
            'Total Reducida 1% (CRC)',
            'Total Reducida 2% (CRC)',
            'Total Reducida 4% (CRC)',
            'Total Transitorio 0% (CRC)',
            'Total Transitorio 4% (CRC)',
            'Total Transitorio 8% (CRC)',
            'Total Gravado 13% (CRC)',
            'Total Impuesto Gravado',
            'Total Impuesto Exonerado',
            'Total Otros Impuestos',
            'Otros Cargos',
            'Total Recepción',
            'Total del Impuesto a Acreditar',  
            'Total del Gasto Aplicable',  
            'Total IVA Devuelto',
            'Condición de Impuesto',   
            'Clasificación d-151', 
            'Id_Receptor',
            'Nombre Receptor',
            'Moneda',
            'TC',
            'Doc_Referencia',
             'Detalle',
             'Version'
        ];
    }
    public function array(): array
    {

        $sales = DB::table('receptor')->select('receptor.*', 'configuracion.*')
        ->join('configuracion', 'receptor.idconfigfact', '=', 'configuracion.idconfigfact')
        ->where([
                ['receptor.fecha_xml_envio', '>=', $this->fecha_desde],
                ['receptor.fecha_xml_envio', '<=', $this->fecha_hasta],
                ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
                ['receptor.estatus_hacienda', '=', 'aceptado'],
            ])->get();
        $estructura = [];
      
        foreach ($sales as $sale) {
            $imp_0 = 0;
            $imp_05 = 0;
            $imp_2 = 0;
            $imp_3 = 0;
            $imp_4 = 0;
            $imp_5 = 0;
            $imp_6 = 0;
            $imp_7 = 0;
            $imp_13 = 0;
            $imp_otros = 0;
            $moneda = 0;
            $no_sujeto=0;
            $no_sujetos=0;
            $tc=0;
            $iva_devuelto =0;
            $otros_cargos=0;
            $mto_imp_exonerado=0;
             $detalle='';
             $version=$sale->version;
             if($sale->version === '4.4'){
                 $codigo_tarifa='CodigoTarifaIVA';
             }else{
                 $codigo_tarifa='CodigoTarifa'; 
             }
             
            $strContents = file_get_contents($sale->ruta_carga);
            $strDatas = $this->Xml2Array($strContents);
            
         
            
            switch ($sale->tipo_documento_recibido) {
                case '01':
                    $nombre_doc = 'Fáctura Electrónica';
                    $documento = 'FacturaElectronica';
                break;
                case '02':
                    $nombre_doc = 'Nota de Débito Electrónica';
                    $documento = 'NotaDebitoElectronica';
                break;
                case '03':
                    $nombre_doc = 'Nota de Crédito Electrónica';
                    $documento = 'NotaCreditoElectronica';
                break;
                case '04':
                    $nombre_doc = 'Tiquete Electrónico';
                break;
            }
       
   
                 $n_recep=$strDatas[$documento]['Receptor']['Nombre'];
                 if (isset($strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'])) {
                  $moneda=$strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'];
                    $tc = floatval($strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio']);  
                  
                  //$tc=$strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                 }
                
                 if(isset($strDatas[$documento]['Receptor']['Identificacion']['Numero'])){
                        $id_recep=$strDatas[$documento]['Receptor']['Identificacion']['Numero'];
                    }else{
                        $id_recep=$strDatas[$documento]['Receptor']['IdentificacionExtranjero'];
                    }
    
            if (isset($strDatas[$documento]['ResumenFactura']['TotalComprobante'])) {   ///cambie total impuesto x total de comprobante, pues hay docs que no traen el nodo de total impuesto
           if (isset($strDatas[$documento]['ResumenFactura']['TotalOtrosCargos'])) {      
          $otros_cargos =$strDatas[$documento]['ResumenFactura']['TotalOtrosCargos'];
           }else{
               $otros_cargos=0;
           }
                if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'])) {// puede existir otros cargos entonces se valida.
                
                if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'])) { //si no existe nodo de imp, entra aqui (22-02-2024 se cambio la validacion de Impuesto a SubTotal)
                       
                    for ($i=0; $i < count($strDatas[$documento]['DetalleServicio']['LineaDetalle']); $i++) {
                         $detalle .= $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Detalle'] . ' <> '; // Concatenar con un espacio  

                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'])) {
                            if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'])) {

                                for ($im=0; $im < count($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']); $im++) { 

                                    if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im][$codigo_tarifa])) {
                                        switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im][$codigo_tarifa]) {
                                            case '10':  
                                             if ($version == '4.4') {  
                                             $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            }   
                                            break;  
                                            case '11':  
                                             if ($version == '4.4') {  
                                             $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            }   
                                            break; 
                                            case '01':  
                                            if ($version != '4.4') {  
                                            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            }else{
                                             $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];   
                                            }  
                                            break;  
                                            case '02':
                                                $imp_2 +=  $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '03':
                                                $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '04':
                                                $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '05':
                                                $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '06':
                                                $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '07':
                                                $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '08':
                                                 
                                                $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '09':
                                                $imp_05 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                        }
                                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion'])) {
                                        $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion']['MontoExoneracion'];
                                        }
                                                    
                                    } else {
                                        $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];
                                    }
                                }
                            } else {

                                if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$codigo_tarifa])) {
                                        switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$codigo_tarifa]) {
                                            case '10':  
                                             if ($version == '4.4') {  
                                             $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            }   
                                            break;  
                                             case '11':  
                                             if ($version == '4.4') {  
                                             $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            }  
                                            break; 
                                            case '01':  
                                            if ($version != '4.4') {  
                                            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            }else{
                                             $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];   
                                            }   
                                            break;  
                                            case '02':
                                                $imp_2 +=  $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '03':
                                                $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '04':
                                                $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '05':
                                                $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '06':
                                                $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '07':
                                                $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '08':
                                             // dd(  $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal']);
                                                $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                            case '09':
                                                $imp_05 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                            break;
                                        }
                                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion'])) {
                                        $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion']['MontoExoneracion'];
                                        }
                                } else {
                                        $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];//se agrego [$im] 21-02-2024
                                }
                                
                            }
                           
                        }else{
                          
                   $no_sujeto +=  $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];     
                }
                    }
                $detalle = trim($detalle); 
                } else {
                   
                     if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'])) {
                        $no_sujeto +=  $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];   
                       } else{
                            $no_sujeto=0;
                        }
                         $detalle=$strDatas[$documento]['DetalleServicio']['LineaDetalle']['Detalle'];
                    if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'])) {
 
                        if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'])) {

                            for ($im=0; $im < count($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']); $im++) { 
                                

                                if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im][$codigo_tarifa])) {

                                    switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im][$codigo_tarifa]) {
                                         case '10':  
        if ($version == '4.4') {  
            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        }  
        break;  
case '11':  
                                             if ($version == '4.4') {  
                                             $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal']; 
                                            }  
                                            break; 
    case '01':  
        if ($version != '4.4') {  
            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        }else{
                                             $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];    
                                            }  
        break;  

    case '02':  
        $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '03':  
        $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '04':  
        $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '05':  
        $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '06':  
        $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '07':  
        $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '08':  
        $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '09':  
        $imp_05 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  
                                    }
                                    if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion'])) {
                                        $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion']['MontoExoneracion'];
                                        }
                                } else {

                                    $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Monto'];
                                }
                            }
                        } else {

                            if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$codigo_tarifa])) {

                                    switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$codigo_tarifa]) {  
    case '10':  
        if ($version == '4.4') {  
            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        }  
        break;  
case '11':  
                                             if ($version == '4.4') {  
                                             $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
                                            }  
                                            break; 
    case '01':  
        if ($version != '4.4') {  
            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        }else{
                                             $no_sujetos += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];    
                                            } 
        break;  

    case '02':  
        $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '03':  
        $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '04':  
        $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '05':  
        $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '06':  
        $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '07':  
        $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '08':  
        $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

    case '09':  
        $imp_05 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];  
        break;  

   
}  
if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion'])) {
                                        $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion']['MontoExoneracion'];
                                        }
                                } else {

                                    $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];
                                }
                        }
                        
                    }else{
                   
                }
                }
               
            }
          
            switch ($sale->condicion_impuesto) {
                case '0':
                    $condicion_impuesto = 'Sin Condicion';
                    if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {
                        $total_impuesto =  $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                    }else{
                        $total_impuesto = '0.00000';
                    }
                break;
                case '01':
                    $condicion_impuesto = 'Genera crédito IVA';
                break;
                case '02':
                    $condicion_impuesto = 'Genera Crédito parcial del IVA';
                break;
                case '03':
                    $condicion_impuesto = 'Bienes de Capital';
                break;
                case '04':
                    $condicion_impuesto = 'Gasto corriente no genera crédito';
                break;
                case '05':
                    $condicion_impuesto = 'Proporcionalidad';
                break;
            }
            switch ($sale->clasifica_d151) {
                case '1':
                    $clasifica_d151 = 'Compras';
                break;
                case '2':
                    $clasifica_d151 = 'Gastos';
                break;
                case '3':
                    $clasifica_d151 = 'Alquileres';
                break;
                case '4':
                    $clasifica_d151 = 'Servicios Profesionales';
                break;
                case '5':
                    $clasifica_d151 = 'Comisiones';
                break;
                case '6':
                    $clasifica_d151 = 'Intereses';
                break;
                case '7':
                    $clasifica_d151 = 'Otros';
                break;
                case '8':
                    $clasifica_d151 = 'Otros Gastos';
                break;
            }
            
            if (isset($strDatas[$documento]['InformacionReferencia']['Numero'])) {
              $documento_referencia = $strDatas[$documento]['InformacionReferencia']['Numero'];  
                
            }else{
                 $documento_referencia ='0';
            }
            $documento_proveedor = substr($strDatas[$documento]['NumeroConsecutivo'], 10, 20);
            
            if(is_null($no_sujeto)){
                 if (isset($strDatas[$documento]['ResumenFactura']['TotalNoSujeto'])) {
                     $no_sujeto = $strDatas[$documento]['ResumenFactura']['TotalNoSujeto'];
                 }
            }
             if (isset($strDatas[$documento]['ResumenFactura']['TotalIVADevuelto'])) {
                        $iva_devuelto = $strDatas[$documento]['ResumenFactura']['TotalIVADevuelto'];
                    }
                    
      if ($moneda == 'CRC') {  
    if ($sale->tipo_documento_recibido == '03') {  
        $datos = array(  
            '#' => strval($sale->idreceptor),  
            'tipo_documento' => $nombre_doc,  
            'numero_doc_receptor' => strval($sale->numero_documento_receptor),  
            'estatus_hacienda' => $sale->estatus_hacienda,  
            'detalle_mensaje' => $sale->detalle_mensaje,  
            'documento_proveedor' => $documento_proveedor,  
            'cedula_emisor' => strval($strDatas[$documento]['Emisor']['Identificacion']['Numero']),  
            'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],  
            'fecha_recepcion_doc' => $sale->fecha,  
            'fecha_emite_doc' => $sale->fecha_xml_envio,  
            'clave_doc' => $sale->clave,  
            'codigo_act' => $sale->codigo_act_xml, 
            'no_sujeto' => strval(-$no_sujetos),  
            'total_exento' => strval(-$imp_0),  
            'total_red_05' => strval(-$imp_05),  
            'total_red_1' => strval(-$imp_2),  
            'total_red_2' => strval(-$imp_3),  
            'total_red_4' => strval(-$imp_4),  
            'total_trans_0' => strval(-$imp_5),  
            'total_trans_4' => strval(-$imp_6),  
            'total_trans_8' => strval(-$imp_7),  
            'total_gravado' => strval(-$imp_13),  
            'total_imp' => strval(-$sale->total_impuesto), 
            'total_imp_exonerados' => strval($mto_imp_exonerado), 
            'total_otros' => strval(-$imp_otros),  
            'otros_cargos' => strval(-$otros_cargos),  
            'total_recepcion' => strval(-$sale->total_comprobante),  
            'total_imp_creditar' => strval(-$sale->imp_creditar),  
            'total_gasto_ap' => strval(-$sale->gasto_aplica), 
            'total_iva_devuelto'=> strval(-$iva_devuelto),  
            'condicion_imp' => $condicion_impuesto,  
            'clasificacion' => $clasifica_d151,  
            'Id_Receptor' => strval($id_recep),  
            'Nombre_Receptor' => $n_recep,  
            'Moneda' => $moneda,  
            'TC' => strval($tc),  
            'Doc_Referencia' => $documento_referencia,
            'Detalle'=>$detalle,
            'Version' => $sale->version, 
        );  
    } else {  
        $datos = array(  
            '#' => strval($sale->idreceptor),  
            'tipo_documento' => $nombre_doc,  
            'numero_doc_receptor' => strval($sale->numero_documento_receptor),  
            'estatus_hacienda' => $sale->estatus_hacienda,  
            'detalle_mensaje' => $sale->detalle_mensaje,  
            'documento_proveedor' => $documento_proveedor,  
            'cedula_emisor' => strval($strDatas[$documento]['Emisor']['Identificacion']['Numero']),  
            'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],  
            'fecha_recepcion_doc' => $sale->fecha,  
            'fecha_emite_doc' => $sale->fecha_xml_envio,  
            'clave_doc' => $sale->clave,  
            'codigo_act' => $sale->codigo_act_xml,   
            'no_sujeto' => strval($no_sujetos),  
            'total_exento' => strval($imp_0),  
            'total_red_05' => strval($imp_05),  
            'total_red_1' => strval($imp_2),  
            'total_red_2' => strval($imp_3),  
            'total_red_4' => strval($imp_4),  
            'total_trans_0' => strval($imp_5),  
            'total_trans_4' => strval($imp_6),  
            'total_trans_8' => strval($imp_7),  
            'total_gravado' => strval($imp_13),  
            'total_imp' => strval($sale->total_impuesto),  
            'total_imp_exonerados' => strval($mto_imp_exonerado), 
            'total_otros' => strval($imp_otros),  
            'otros_cargos' => strval($otros_cargos),  
            'total_recepcion' => strval($sale->total_comprobante),  
            'total_imp_creditar' => strval($sale->imp_creditar),  
            'total_gasto_ap' => strval($sale->gasto_aplica),  
            'total_iva_devuelto'=> strval($iva_devuelto),
            'condicion_imp' => $condicion_impuesto,  
            'clasificacion' => $clasifica_d151,  
            'Id_Receptor' => strval($id_recep),  
            'Nombre_Receptor' => $n_recep,  
            'Moneda' => $moneda,  
            'TC' => strval($tc),  
            'Doc_Referencia' => $documento_referencia,
            'Detalle'=>$detalle,
            'Version' => $sale->version, 
        );  
    }  
} else {  
    if ($sale->tipo_documento_recibido == '03') {  
        $datos = array(  
            '#' => strval($sale->idreceptor),  
            'tipo_documento' => $nombre_doc,  
            'numero_doc_receptor' => strval($sale->numero_documento_receptor),  
            'estatus_hacienda' => $sale->estatus_hacienda,  
            'detalle_mensaje' => $sale->detalle_mensaje,  
            'documento_proveedor' => $documento_proveedor,  
            'cedula_emisor' => strval($strDatas[$documento]['Emisor']['Identificacion']['Numero']),  
            'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],  
            'fecha_recepcion_doc' => $sale->fecha,  
            'fecha_emite_doc' => $sale->fecha_xml_envio,  
            'clave_doc' => $sale->clave,  
            'codigo_act' => $sale->codigo_act_xml,   
            'no_sujeto' => strval(-$no_sujetos * $tc),  
            'total_exento' => strval(-$imp_0 * $tc),  
            'total_red_05' => strval(-$imp_05 * $tc),  
            'total_red_1' => strval(-$imp_2 * $tc),  
            'total_red_2' => strval(-$imp_3 * $tc),  
            'total_red_4' => strval(-$imp_4 * $tc),  
            'total_trans_0' => strval(-$imp_5 * $tc),  
            'total_trans_4' => strval(-$imp_6 * $tc),  
            'total_trans_8' => strval(-$imp_7 * $tc),  
            'total_gravado' => strval(-$imp_13 * $tc),  
            'total_imp' => strval(-$sale->total_impuesto * $tc), 
            'total_imp_exonerados' => strval(-$mto_imp_exonerado * $tc), 
            'total_otros' => strval(-$imp_otros * $tc),  
            'otros_cargos' => strval(-$otros_cargos * $tc),  
            'total_recepcion' => strval(-$sale->total_comprobante * $tc),  
            'total_imp_creditar' => strval(-$sale->imp_creditar * $tc),  
            'total_gasto_ap' => strval(-$sale->gasto_aplica * $tc),
            'total_iva_devuelto'=> strval(-$iva_devuelto * $tc),
            'condicion_imp' => $condicion_impuesto,  
            'clasificacion' => $clasifica_d151,  
            'Id_Receptor' => strval($id_recep),  
            'Nombre_Receptor' => $n_recep,  
            'Moneda' => $moneda,  
            'TC' => strval($tc),  
            'Doc_Referencia' => $documento_referencia,
             'Detalle'=>$detalle,
            'Version' => $sale->version, 
        );  
    } else {  
        $datos = array(  
            '#' => strval($sale->idreceptor),  
            'tipo_documento' => $nombre_doc,  
            'numero_doc_receptor' => strval($sale->numero_documento_receptor),  
            'estatus_hacienda' => $sale->estatus_hacienda,  
            'detalle_mensaje' => $sale->detalle_mensaje,  
            'documento_proveedor' => $documento_proveedor,  
            'cedula_emisor' => strval($strDatas[$documento]['Emisor']['Identificacion']['Numero']),  
            'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],  
            'fecha_recepcion_doc' => $sale->fecha,  
            'fecha_emite_doc' => $sale->fecha_xml_envio,  
            'clave_doc' => $sale->clave,  
            'codigo_act' => $sale->codigo_act_xml,   
            'no_sujeto' => strval($no_sujetos * $tc),  
            'total_exento' => strval($imp_0 * $tc),  
            'total_red_05' => strval($imp_05 * $tc),  
            'total_red_1' => strval($imp_2 * $tc),  
            'total_red_2' => strval($imp_3 * $tc),  
            'total_red_4' => strval($imp_4 * $tc),  
            'total_trans_0' => strval($imp_5 * $tc),  
            'total_trans_4' => strval($imp_6 * $tc),  
            'total_trans_8' => strval($imp_7 * $tc),  
            'total_gravado' => strval($imp_13 * $tc),  
            'total_imp' => strval($sale->total_impuesto * $tc),  
            'total_imp_exonerados' => strval($mto_imp_exonerado * $tc),
            'total_otros' => strval($imp_otros * $tc),  
            'otros_cargos' => strval($otros_cargos * $tc),  
            'total_recepcion' => strval($sale->total_comprobante * $tc),  
            'total_imp_creditar' => strval($sale->imp_creditar * $tc),  
            'total_gasto_ap' => strval($sale->gasto_aplica * $tc),  
            'total_iva_devuelto'=> strval($iva_devuelto * $tc),
            'condicion_imp' => $condicion_impuesto,  
            'clasificacion' => $clasifica_d151,  
            'Id_Receptor' => strval($id_recep),  
            'Nombre_Receptor' => $n_recep,  
            'Moneda' => $moneda,  
            'TC' => strval($tc),  
            'Doc_Referencia' => $documento_referencia,
             'Detalle'=>$detalle,
            'Version' => $sale->version, 
        );  
    }  
}
              array_push($estructura, $datos);
            }
        }
        return $estructura;
        
    }

    public function Xml2Array($contents, $get_attributes=1, $priority = 'tag') {
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
}
