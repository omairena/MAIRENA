@extends('layouts.app', ['page' => __('Actividades Creadas'), 'pageSlug' => 'Actividades'])
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">{{ __('Actividades') }}</h4>
                        </div>
                         <div class="col-4 text-right">
    <a href="{{ route('actividad.create', ['configuracion' => $idconfig]) }}" class="btn btn-sm btn-primary">{{ __('Crear Actividad') }}</a>
    <form action="{{ route('actividades.actualizar') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-sm btn-warning">{{ __('Actualizar Actividades a Version CII4') }}</button>
    </form>
</div>
                        <div class="col-4 text-right">
                            <a href="{{ route('actividad.create', ['configuracion' => $idconfig]) }}" class="btn btn-sm btn-primary">{{ __('Crear Actividad') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                                <th scope="col">{{ __('Configuración') }}</th>
                                <th scope="col">{{ __('Descripción') }}</th>
                                <th scope="col">{{ __('Código de Actividad') }}</th>
                                 <th scope="col">{{ __('Estado') }}</th>
                                  <th scope="col">{{ __('Principal?') }}</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                @foreach ($actividades as $actividad)
                                    <?php 
                                        $config = App\Configuracion::find($actividad->idconfigfact);
                                        if($actividad->principal==0){
                                            $principal='No';
                                        }else{
                                            $principal='Si';
                                        }
                                         if( $actividad->estado==0){
                                            $estado='Activa';
                                        }else{
                                            $estado='Inactiva';
                                        }
                                    ?>
                                    <tr>
                                        <td>{{ $config->nombre_empresa }}</td>
                                        <td>{{ $actividad->descripcion }}</td>
                                        <td>{{ $actividad->codigo_actividad }}</td>
                                        <td>{{ $estado}}</td>
                                        <td>{{ $principal}}</td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="{{ route('actividad.edit', $actividad->idcodigoactv) }}">{{ __('Editar') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end" aria-label="...">
                        {{ $actividades->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

