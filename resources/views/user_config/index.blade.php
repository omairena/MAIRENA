@extends('layouts.app', ['page' => __('Configuraciones Creadas'), 'pageSlug' => 'config_user'])
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Configuración por Usuario') }}</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('userconfig.create', ['configuracion' => $idconfig]) }}" class="btn btn-sm btn-primary">{{ __('Configurar Usuario') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter ">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Usuario') }}</th>
                                <th scope="col">{{ __('Empresa') }}</th>
                                <th scope="col">{{ __('Usa Punto de Venta') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($user_config as $u_c)
                                    <?php 
                                        switch ($u_c->usa_pos) {
                                            case '1':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-success'>Si</button>";
                                                
                                            break;
                                            case '0':
                                                $estatus ="<button  type='button' class='btn btn-sm btn-danger'>No</button>";
                                            break;
                                        }
                                     ?>
                                    <tr>
                                        <td>{{ $u_c->idusuario }}</td>
                                       
                                        <td>{{ $u_c->config_u[0]->nombre_empresa }}</td>
<td>  
    <a href="{{ route('userconfig.onoff', $u_c->idconfigfact) }}"   
       class="btn btn-sm {{ $u_c->usa_pos == '1' ? 'btn-success' : 'btn-danger' }}"   
       data-estatus="{{ $estatus }}">  
        {{ $u_c->usa_pos == '1' ? 'Si' : 'No' }}  
    </a>  
</td>   
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end" aria-label="...">
                        {{ $user_config->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

