@extends('layouts.app', ['page' => __('Crear Lista a un cliente'), 'pageSlug' => 'createclientelist'])
@section('content')
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Listas de Precio para el cliente') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('cliente.listacli', $cliente->idcliente) }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('listcliente.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group{{ $errors->has('idlist') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="idlist">{{ __('Listas de Precios') }}</label>
                                <select class="form-control form-control-alternative" id="idlist" name="idlist" value="{{ old('idlist') }}" required>
                                    @foreach ( $lista as $list )
                                        <option value="{{ $list->idlist }}">{{ $list->descripcion }} - {{ $list->porcentaje }} %</option>
                                    @endforeach
                                </select>
                                @include('alerts.feedback', ['field' => 'idlist'])
                            </div>
                            <div class="form-group{{ $errors->has('nombre_cliente') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="nombre_cliente">{{ __('Nombre de Cliente') }}</label>
                                <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control form-control-alternative{{ $errors->has('nombre_cliente') ? ' is-invalid' : '' }}" placeholder="{{ __('Cliente') }}" value="{{ $cliente->nombre }}" required readonly>
                                @include('alerts.feedback', ['field' => 'nombre_cliente'])
                            </div>
                            <div class="form-group{{ $errors->has('por_defecto') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="por_defecto">{{ __('Por Defecto') }}</label>
                                <select class="form-control form-control-alternative" id="por_defecto" name="por_defecto" value="{{ old('por_defecto') }}" required>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                                @include('alerts.feedback', ['field' => 'por_defecto'])
                            </div>
                            <input type="text" name="idcliente" id="idcliente" value="{{ $cliente->idcliente }}" required hidden>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Guardar') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
