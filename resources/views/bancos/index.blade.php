@extends('layouts.app', ['page' => __('Cuentas Bancarias'), 'pageSlug' => 'allfacturas'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">
.loader {
  border: 16px solid #f3f3f3; /* Light grey */
  border-top: 16px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
    </style>
</head>
@section('content')
 <div class="loader" style="display: none;"></div>
    <div class="row">
        <div class="col-md-12">
           
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Cuentas Bancarias') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                           
                                <div class="col-md-12">
                                        <div class="form-group text-right">
                                          
                                               <input type="button" class="btn btn-sm btn-success" value="Crear Cuenta" data-target="#newBanco" data-toggle="modal" id="New_cliente"/>
                                        </div>
                                    </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="table-responsive">
                        <table class="table" id="ver_masivo_datatable">
                            <thead class=" text-primary">
                            	<th scope="col">{{ __('Id_Cuenta') }}</th>
                                <th scope="col">{{ __('Nombre Cuenta') }}</th>
                                 <th scope="col">{{ __('Saldo') }}</th>
                                 <th scope="col">{{ __('Acciones') }}</th>
                               
                            </thead>
                            <tbody>
                                <?php
                                 $total_neto = 0;
                                           
                                 ?>
                                @foreach($bancos as $lm)
                                    
                                    <tr>
                                        <td>{{ $lm->id_bancos}}</td>
                                        <td>{{ $lm->cuenta }}</td>
                                         <td>{{number_format( $lm->saldo, 2, '.', ',') }}</td>
                                         
                                        <td><a href="{{ route('bancos.deleted', $lm->id_bancos) }}" class="btn btn-sm btn-primary">{{ __('Eliminar') }}</a></td>
                                        </tr>
                                        <?php
                                 	  $total_neto = $total_neto +  $lm->saldo;
                                      
                                    ?>
                                      
                                @endforeach
                                  <tr>  
                                    <td>Total Efectivo en Cuentas</td>
                                       <td>--></td>
                                     <td colspan="2">{{number_format( $total_neto, 2, '.', ',') }}</td>
                                    
                                     </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection

@include('modals.newBanco')
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#ver_masivo_datatable').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 1, "asc" ]]
            }
        );
       
        
  
</script>
@endsection
