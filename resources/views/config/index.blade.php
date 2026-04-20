@extends('layouts.app', ['page' => __('Configuraciones Creadas'), 'pageSlug' => 'config'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Configuraciones') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                           
                            <a href="{{ url('limpiar') }}" class="btn btn-sm btn-primary">{{ __('Limpiar Facturas') }}</a>

                            @if (Auth::user()->super_admin == 1 ) 
                                <a href="{{ route('config.recepcion') }}" class="btn btn-sm btn-primary">{{ __('Ejecutar Comando') }}</a>
                                 <a href="{{ route('config.create') }}" class="btn btn-sm btn-primary">{{ __('Crear Configuraci籀n') }}</a>
                                 @if (session('success'))  
    <div class="alert alert-success">  
        {{ session('success') }}  
    </div>  
@endif  

@if (session('error'))  
    <div class="alert alert-danger">  
        {{ session('error') }}  
    </div>  
@endif  

<form action="{{ route('ejecutar.receptor') }}" method="GET" style="display: inline;">  
    @csrf  
    <button type="submit" class="btn btn-sm btn-primary">  
        API ABY
    </button>  
</form>

                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')
 <?php 
 $user = Auth::user();  

$isSuperAdmin = $user->super_admin == 1;  
if($user->super_admin == 1 ){
  //  dd($user->super_admin );
if($user->id != 6 ){
  if(empty($user->gnl) ){
   
// Obtener la configuraci車n basado en el rol del usuario  
$configuracion = DB::table('configuracion as c')  
                ->select('c.*')  
                ->join('users as u', 'c.idconfigfact', '=', 'u.idconfigfact')  
                ->where([  
                    ['c.gnl', '=', Auth::user()->id],  
                    ['u.id', '!=', Auth::user()->id],  
                ])  
                ->distinct()
                ->whereNotIn('c.status', [0]) // Filtrar que status no sea 0 ni 1  
                ->get();  
                
    }else{
        $configuracion = DB::table('configuracion as c')  
                ->select('c.*')  
                ->join('users as u', 'c.idconfigfact', '=', 'u.idconfigfact')  
                ->where([  
                    ['c.gnl', '=', Auth::user()->gnl],  
                    ['u.id', '!=', Auth::user()->id],  
                ])  
                ->distinct()
                ->whereNotIn('c.status', [0]) // Filtrar que status no sea 0 ni 1  
                ->get();  
        
        
    } 
}else{
   $configuracion = App\Configuracion::all();  // Obtener todos los registros de la configuraci車n 
}
}else{
    
  $configuracion = DB::table('configuracion as c')  
                ->select('c.*')  
                ->join('users as u', 'c.idconfigfact', '=', 'u.idconfigfact')  
                ->where([  
                    ['c.idconfigfact', '=', Auth::user()->idconfigfact],  
                   // ['u.id', '!=', Auth::user()->id],  
                ])  
                ->distinct()
                //->whereNotIn('c.status', [0]) // Filtrar que status no sea 0 ni 1  
                ->get();   
                // dd($configuracion );
}
//dd($configuracion);
if ($configuracion->isnotEmpty()) {  
    // Si no se encontr車 configuraci車n, traemos todos los datos  
   // $configuracion = App\Configuracion::all();  // Obtener todos los registros de la configuraci車n  
   
//}  

//dd($configuracion);
// Obtener el contador o la fecha del plan dependiendo del rol  
$e = $isSuperAdmin ? $user->f_contador : $configuracion->isNotEmpty() ? $configuracion[0]->fecha_plan : null;  
 
// Contar las ventas  
$totalVentas = $configuracion->sum(function($val) {  
    return App\Sales::where('idconfigfact', $val->idconfigfact)->count();  
});  

// Calcular documentos emitidos  
$b = $user->factura;  
$C = $totalVentas - $b;  

// Mostrar resultado  
echo 'Su cuenta a Emitido:<b> ' . $C . '</b> Documentos Electronicos (FACT/TIQ/NC/ND). Su plan Vence el: <b>' . $e . '</b><br>';  

// Contar receptores  
$totalReceptores = $configuracion->sum(function($valr) {  
    return App\Receptor::where('idconfigfact', $valr->idconfigfact)->count();  
});  

// Calcular documentos recibidos  
$br = $user->receptor;  
$Cr = $totalReceptores - $br;  

// Mostrar resultado de receptores  
echo 'Su cuenta a Recepcionado:<b> ' . $Cr . '</b> Documentos Electronicos (FACT/NC/ND).';  
}

 ?>  
 
 @if(!empty($configuracion) && $configuracion->isNotEmpty())  
   
                    <div class="">
                        <table class="table tablesorter " id="configuracion_datatable">
                            <thead class=" text-primary">
                             <th scope="col">{{ __('Identificaci籀n') }}</th>
                                 <th scope="col">{{ __('Nombre') }}</th>
                                <th scope="col">{{ __('Doc Disponibles') }}</th>
                               
                               
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Tel矇fono') }}</th>
                                  <th scope="col">{{ __('Estatus Cliente') }}</th>
                                   @if (Auth::user()->super_admin == 1)
                                <th scope="col">Loggin</th>
                                @endif
                                 <th scope="col">Acciones</th>
                            </thead>
                            <tbody>
                                @foreach ($configuracion as $config)
                                    <?php 
                                     $configuracion = App\Configuracion::where('idconfigfact', $config->idconfigfact)->first();  

// Comprobar si existe la configuraci車n  
if ($configuracion) {  
    $fecha_plan = $configuracion->fecha_plan;  
    $docs = $configuracion->docs;  

    // Obtener todas las ventas relacionadas en una sola consulta  
    $ventas = App\Facelectron::where([
        ['idconfigfact', $config->idconfigfact],
        ['estatushacienda', 'aceptado']
        ])->get();  
    $i = count($ventas);  

    $facturaUsuario = Auth::user()->factura;  
    $C = $i - $facturaUsuario;  
    $d = $docs - $i;  

    // Aqu赤 puedes utilizar $fecha_plan, $C, $d seg迆n necesites  
} else {  
    // Manejar el caso donde no se encuentra la configuraci車n  
    // Por ejemplo:  
    $fecha_plan = null;  
    $C = null;  
    $d = null;  
}  
                          
                                    
                                    switch ($config->tipo_id_emisor) {
                                        case '01':
                                           $tipo_ident = 'CN-';
                                        break;
                                        case '02':
                                            $tipo_ident = 'CJ-';
                                        break;
                                        case '03':
                                            $tipo_ident = 'DIME-';
                                        break;
                                        case '04':
                                            $tipo_ident = 'NITE-';
                                        break;
                                    }
                                    $consulta = DB::table('users')  
                                    ->select('users.*')  
                                    ->where('idconfigfact', $config->idconfigfact)  
                                    ->first();  

if ($consulta) {  
    $esta = ($consulta->status === 0) ? 'Inactivo' : 'Activo';  
} else {  
    // Manejar el caso en el que no se encuentran resultados  
    $esta = 'No encontrado'; // O cualquier otro manejo de errores que consideres apropiado  
}  
  // dd(Auth::user()->super_admin);      
                                     ?>
                                    <tr>
                                     <td>{{ $tipo_ident }}{{ $config->numero_id_emisor }}</td>
                                      <td>{{ $config->nombre_emisor }}</td>
                                        <td>{{ $d }}</td>
                                       
                                       
                                        <td>
                                            <a href="mailto:{{ $config->email_emisor }}">{{ $config->email_emisor }}</a>
                                        </td>
                                        <td>{{ $config->telefono_emisor }}</td>
                                        <?php
                                        
                                        ?>
                                       
                                      @if (Auth::user()->super_admin == 1)
    @if($config->status == 1)
        <td>
            <a href="{{ route('config.onoff', $config->idconfigfact) }}" class="btn btn-sm btn-success" onclick="return confirm('Estas seguro de que desea desactivar esta configuracion?');">{{ 'Activo' }}</a>
        </td>
    @else
        <td>
            <a href="{{ route('config.onoff', $config->idconfigfact) }}" class="btn btn-sm btn-danger" onclick="return confirm('Estas seguro de que desea activar esta configuracion?');">{{ 'Inactivo' }}</a>
        </td>
    @endif
@else
    @if($esta === 'Activo') {{-- Cambi谷 = por === para comparaci車n correcta --}}
        <td>
            <a href="" class="btn btn-sm btn-success">{{ 'Activo' }}</a>
        </td>
    @else
        <td>
            <a href="" class="btn btn-sm btn-danger">{{ 'Inactivo' }}</a>
        </td>
    @endif
@endif
                                         
                                         
                                          @if (Auth::user()->super_admin == 1)
                                        <td> <a href="{{ route('config.ingresar', $config->idconfigfact) }}" class="btn btn-sm btn-primary">{{ __('Ingresar') }}</a></td>
                                         @endif
                                         
                                       
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="{{ route('config.edit', $config->idconfigfact) }}">{{ __('Editar') }}</a>
                                                    
                                                    <a class="dropdown-item" href="{{ route('actividad.show', $config->idconfigfact) }}">{{ __('Asignar Codigo de Actividad') }}</a>
                                                    <a class="dropdown-item" href="{{ route('userconfig.show', $config->idconfigfact) }}">{{ __('Asignar Usuarios') }}</a>
                                                    <a class="dropdown-item" href="{{ route('config.limpiar', $config->idconfigfact) }}">{{ __('Limpiar Database') }}</a>
                                                      @if (Auth::user()->super_admin = 1)
                                                      <a class="dropdown-item" href="{{ route('config.ingresar', $config->idconfigfact) }}">{{ __('Ingresar') }}</a>
                                                      <a class="dropdown-item" href="{{ route('config.onoff', $config->idconfigfact) }}">{{ __('On/Off') }}</a>
                                                      @endif
                                                  
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                 
@else  
    <p><h1>No hay configuraciones disponibles.</h1></p>  
@endif  
                </div>
            </div>
        </div>
    </div>
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#configuracion_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
    });
</script>
@endsection