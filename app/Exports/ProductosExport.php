<?php

namespace App\Exports;

use App\Productos;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;

class ProductosExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
{
    protected $fecha_desde;
    protected $fecha_hasta;
    protected $idproducto;
        public function __construct($idproducto, $fecha_desde, $fecha_hasta)
    {
            $this->idproducto = $idproducto;
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
            'Código',  
            'Producto/Servicio',
            'Nombre/Descripcion', 
            'Cantidad',  
            'Moneda Documento', 
            'Costo (CRC)',
            '% Descuento (CRC)',
            '% Impuesto (CRC)',    
            'Total Impuesto Línea(CRC)', 
            'Precio Venta (CRC)',    
            'Total Línea (CRC)', 
            'Numero Documento',
            'Fecha Documento',  
            'Tipo Documento',   
            'Nombre Cliente',   
            'Estado Documento',
            'Tipo Pago',
            'Condicion Venta',
        ];
    }
    public function array(): array
    {
        if ($this->idproducto != 0) {
            $sales = DB::table('sales_item')->select('sales_item.*', 'sales.idcliente','clientes.nombre', 'facelectron.consecutivo','facelectron.tipodoc','facelectron.estatushacienda','facelectron.fechahora', 'productos.*', 'sales.condicion_venta', 'sales.medio_pago', 'sales.tipo_moneda')
            ->join('sales', 'sales_item.idsales', '=', 'sales.idsale')
            ->join('facelectron', 'sales_item.idsales', '=', 'facelectron.idsales')
            ->join('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
            ->join('productos', 'sales_item.idproducto', '=', 'productos.idproducto')
            ->where([
               
                ['sales_item.idproducto', '=', $this->idproducto],
                ['facelectron.fechahora', '>=', $this->fecha_desde],
                ['facelectron.fechahora', '<=', $this->fecha_hasta],
                ['facelectron.estatushacienda', '=', 'aceptado'],
                ['sales.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }else{
            $sales = DB::table('sales_item')->select('sales_item.*', 'sales.idcliente','clientes.nombre', 'facelectron.consecutivo','facelectron.tipodoc','facelectron.estatushacienda','facelectron.fechahora', 'productos.*', 'sales.condicion_venta', 'sales.medio_pago', 'sales.tipo_moneda')
            ->join('sales', 'sales_item.idsales', '=', 'sales.idsale')
            ->join('facelectron', 'sales_item.idsales', '=', 'facelectron.idsales')
            ->join('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
            ->join('productos', 'sales_item.idproducto', '=', 'productos.idproducto')
            ->where([
                ['facelectron.estatushacienda', '=', 'aceptado'],
                ['facelectron.fechahora', '>=', $this->fecha_desde],
                ['facelectron.fechahora', '<=', $this->fecha_hasta],
                ['sales.idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }
        $estructura = [];
        $valor_linea = 0;
        foreach ($sales as $sale) {
            switch ($sale->tipodoc) {
                case '01':
                    $nombre_doc = 'Fáctura Electrónica';
                break;
                case '02':
                    $nombre_doc = 'Nota de Débito Electrónica';
                break;
                case '03':
                    $nombre_doc = 'Nota de Crédito Electrónica';
                break;
                case '04':
                    $nombre_doc = 'Tiquete Electrónico';
                break;
                case '08':
                    $nombre_doc = 'Fáctura Electrónica de Compra';
                break;
            }
            switch ($sale->condicion_venta) {
                case '01':
                    $condicion_venta = 'Contado';
                break;
                case '02':
                    $condicion_venta = 'Crédito';
                break;
            }
            switch ($sale->medio_pago) {
                case '01':
                    $medio_pago ="Efectivo";                                        
                break;
                case '02':
                    $medio_pago = "Tarjeta";
                break;
                case '03':
                    $medio_pago = "Cheque";
                break;
                case '04':
                    $medio_pago = "Transferencia – depósito bancario";
                break;
                case '05':
                    $medio_pago = "Recaudado por terceros";
                break;
            }
            if ($sale->tipo_producto == 1) {
                $tipo_producto = 'Producto';
            }else{
                $tipo_producto = 'Servicio';
            }
            if($sale->granel==1){
              $cantidad= ($sale->cantidad/46); 
            }else{
                 $cantidad= $sale->cantidad;
            }
            
            if($sale->tipodoc!='03'){
            $valor_linea = $sale->valor_neto + $sale->valor_impuesto;
            $datos = array(
                '#' => $sale->idsalesitem,
                'codigo' => $sale->codigo_producto,
                'tipo_producto' => $tipo_producto,
                'nombre_producto' => $sale->nombre_producto,
                'cantidad' => $cantidad,
                'tipo_moneda' => $sale->tipo_moneda,
                'costo_utilidad' => $sale->costo_utilidad,
                'descuento_prc' => $sale->descuento_prc,
                'impuesto_prc' => $sale->impuesto_prc,
                'valor_impuesto' => $sale->valor_impuesto,
                'valor_neto' => $sale->valor_neto,
                'valor_linea' => $valor_linea,
                'numero_documento' => $sale->consecutivo,
                'fecha_documento' => $sale->fechahora,
                'tipo_doc' => $nombre_doc,
                'nombre_cliente' => $sale->nombre,
                'estado_doc' => $sale->estatushacienda,
                'tipo_pago' => $medio_pago,
                'condicion_venta' => $condicion_venta,
            );
            }else{
                $valor_linea = $sale->valor_neto + $sale->valor_impuesto;
                $datos = array(
                '#' => $sale->idsalesitem,
                'codigo' => $sale->codigo_producto,
                'tipo_producto' => $tipo_producto,
                'nombre_producto' => $sale->nombre_producto,
                'cantidad' => -$cantidad,
                'tipo_moneda' => $sale->tipo_moneda,
                'costo_utilidad' => -$sale->costo_utilidad,
                'descuento_prc' => -$sale->descuento_prc,
                'impuesto_prc' => -$sale->impuesto_prc,
                'valor_impuesto' => -$sale->valor_impuesto,
                'valor_neto' => -$sale->valor_neto,
                'valor_linea' => -$valor_linea,
                'numero_documento' => $sale->consecutivo,
                'fecha_documento' => $sale->fechahora,
                'tipo_doc' => $nombre_doc,
                'nombre_cliente' => $sale->nombre,
                'estado_doc' => $sale->estatushacienda,
                'tipo_pago' => $medio_pago,
                'condicion_venta' => $condicion_venta,
            );
            }
            array_push($estructura, $datos);
        }
        return $estructura;
        
    }
}