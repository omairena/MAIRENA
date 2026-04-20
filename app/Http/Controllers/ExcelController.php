<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Sales;
use App\Configuracion;
use App\Sales_item;
use App\Facelectron;
use App\Cliente;
use App\Items_exonerados;
use App\Exports\SalesExport;
use App\Exports\fecExport;
use App\Exports\RegimenExport;
use App\Exports\OpExport;
use App\Exports\ReceptorExport;
use App\Exports\ProductosExport;
use App\Exports\ClientesExport;
use App\Exports\InvExport;
use App\Exports\CxcExport;
use App\Exports\CxcAbonoExport;
use App\Exports\CxpExport;
use App\Exports\DventasExport;
use App\Exports\DcomprasExport;
use App\Exports\DcomprasExportR;
use App\Exports\CajaExport;
use App\Exports\BancosExport;
use App\Exports\reaExport;
use App\Exports\SalesExportcolon;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ExcelController extends Controller
{
    public function exportSales($fecha_desde, $fecha_hasta){
        return Excel::download(new SalesExport($fecha_desde, $fecha_hasta), 'Ventas.xlsx');
    }
     public function exportfec($fecha_desde, $fecha_hasta){
        return Excel::download(new fecExport($fecha_desde, $fecha_hasta), 'FEC.xlsx');
    }
    public function exportrea($fecha_desde, $fecha_hasta){
        return Excel::download(new reaExport($fecha_desde, $fecha_hasta), 'REA.xlsx');
    }
    public function exportSalescolon($fecha_desde, $fecha_hasta){
        return Excel::download(new SalesExportcolon($fecha_desde, $fecha_hasta), 'VentasColonizadas.xlsx');
    }

    public function exportRegimen($fecha_desde, $fecha_hasta){
        return Excel::download(new RegimenExport($fecha_desde, $fecha_hasta), 'Regimen.xlsx');
    }
     public function exportop($fecha_desde, $fecha_hasta){
        return Excel::download(new OpExport($fecha_desde, $fecha_hasta), 'op.xlsx');
    }

    public function exportarReceptor($fecha_desde, $fecha_hasta){
    	return Excel::download(new ReceptorExport($fecha_desde, $fecha_hasta), 'receptor.xlsx');
    }

    public function exportarProducto($idproducto, $fecha_desde, $fecha_hasta){
        return Excel::download(new ProductosExport($idproducto, $fecha_desde, $fecha_hasta), 'productos.xlsx');
    }
public function exportarBancos($idproducto, $fecha_desde, $fecha_hasta){
        return Excel::download(new BancosExport($idproducto, $fecha_desde, $fecha_hasta), 'Bancos.xlsx');
    }
    public function exportClientes(){
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        return Excel::download(new ClientesExport(), 'clientes.xlsx');
    }
    public function exportInv(){
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        return Excel::download(new InvExport(), 'inv.xlsx');
    }

    public function exportCaja($idcaja){
        return Excel::download(new CajaExport($idcaja), 'cierreCaja.xlsx');
    }

    public function exportCxc($fecha_desde, $fecha_hasta, $idcxcobrar){
        return Excel::download(new CxcExport($fecha_desde, $fecha_hasta, $idcxcobrar), 'cxc.xlsx');
    }

    public function exportCxcab($fecha_desde, $fecha_hasta, $idcxcobrar){
        return Excel::download(new CxcAbonoExport($fecha_desde, $fecha_hasta, $idcxcobrar), 'cxcabono.xlsx');
    }

    public function exportCxp($fecha_desde, $fecha_hasta, $idcxpagar){
        return Excel::download(new CxpExport($fecha_desde, $fecha_hasta, $idcxpagar), 'cxp.xlsx');
    }

    public function exportDventas($fecha_desde, $fecha_hasta){
        return Excel::download(new DventasExport($fecha_desde, $fecha_hasta), 'd151Ventas.xlsx');
    }

    public function exportDcompras($fecha_desde, $fecha_hasta){
        Excel::download(new DcomprasExportR($fecha_desde, $fecha_hasta), 'd151ComprasR.xlsx');
        return Excel::download(new DcomprasExport($fecha_desde, $fecha_hasta), 'd151Compras.xlsx');
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
