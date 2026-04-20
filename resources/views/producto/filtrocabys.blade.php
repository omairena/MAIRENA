@extends('layouts.app', ['page' => __('Asignacion Cabys'), 'pageSlug' => 'newCabys'])
<head>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
@section('content')
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12">
            <form method="post" action="{{ route('productos.savecabys', $producto->idproducto) }}" autocomplete="off">
                @csrf
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Asignación Cabys') }} {{ $producto->nombre_producto }}</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('productos.cabys', $producto->idproducto) }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                                </div>
                                <div class="col-6">
                                    <div class="form-group{{ $errors->has('categoria') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="categoria">{{ __('Categoria') }} <a  target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="left" title="<b>Si necesitas volver a buscar datos, puedes hacer una nueva consulta, dando clic en el botón atrás que aparece a la derecha de la pantalla. </b>">¿Necesitas Ayuda?
                                        </a></label>
                                        <select class="form-control form-control-alternative" id="categoria" name="categoria" disabled="true">
                                            <option value="0" {{ ($datos_array['categoria'] == 0 ? 'selected="selected"' : '') }}>0 - Productos de la agricultura, silvicultura y pesca</option>
                                            <option value="1" {{ ($datos_array['categoria'] == 1 ? 'selected="selected"' : '') }}>1 - Minerales, electricidad, gas y agua</option>
                                            <option value="2" {{ ($datos_array['categoria'] == 2 ? 'selected="selected"' : '') }}>2 - Productos alimenticios, bebidas y tabaco; textiles, prendas de vestir y productos de cuero</option>
                                            <option value="3" {{ ($datos_array['categoria'] == 3 ? 'selected="selected"' : '') }}>3 - Bienes transportables, excepto productos metálicos, maquinaria y equipo, n.c.p.</option>
                                            <option value="4" {{ ($datos_array['categoria'] == 4 ? 'selected="selected"' : '') }}>4 - Productos metálicos, maquinaria y equipo</option>
                                            <option value="5" {{ ($datos_array['categoria'] == 5 ? 'selected="selected"' : '') }}>5 - Construcciones y servicios de construcción</option>
                                            <option value="6" {{ ($datos_array['categoria'] == 6 ? 'selected="selected"' : '') }}>6 - Servicios de venta y distribución; alojamiento; servicios de suministro de comidas y bebidas; servicios de transporte; servicios de distribución de electricidad, gas y agua</option>
                                            <option value="7" {{ ($datos_array['categoria'] == 7 ? 'selected="selected"' : '') }}>7 - Servicios financieros y servicios conexos; servicios inmobiliarios; servicios de arrendamiento financiero (leasing)</option>
                                            <option value="8" {{ ($datos_array['categoria'] == 8 ? 'selected="selected"' : '') }}>8 - Servicios prestados a las empresas y servicios de producción</option>
                                            <option value="9" {{ ($datos_array['categoria'] == 9 ? 'selected="selected"' : '') }}>9 - Servicios para la comunidad, sociales y personales</option>
                                        </select>
                                    </div>
                                    <div class="form-group{{ $errors->has('tarifa') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="tarifa">{{ __('Tarifas') }}</label>
                                        <select class="form-control form-control-alternative" id="tarifa" name="tarifa" value="{{ old('tarifa') }}" required readonly="true">
                                            <option value="0" {{ ($datos_array['tarifa'] == 0 ? 'selected="selected"' : '') }}>-- Sin Tarifa --</option> 
                                            <option value="01" {{ ($datos_array['tarifa'] == '01' ? 'selected="selected"' : '') }}>Tarifa 0% (Exento)</option>
                                            <option value="02" {{ ($datos_array['tarifa'] == '02' ? 'selected="selected"' : '') }}>Tarifa reducida 1%</option>
                                            <option value="03" {{ ($datos_array['tarifa'] == '03' ? 'selected="selected"' : '') }}>Tarifa reducida 2%</option>
                                            <option value="04" {{ ($datos_array['tarifa'] == '04' ? 'selected="selected"' : '') }}>Tarifa reducida 4%</option>
                                            <option value="08" {{ ($datos_array['tarifa'] == '08' ? 'selected="selected"' : '') }}>Tarifa general 13%</option>
                                        </select>
                                        @include('alerts.feedback', ['field' => 'tarifa'])
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group{{ $errors->has('descripcion') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="descripcion">{{ __('Descripción') }}</label>
                                        <input type="text" name="descripcion" id="descripcion" class="form-control form-control-alternative{{ $errors->has('descripcion') ? ' is-invalid' : '' }}" placeholder="{{ __('Descripción') }}" value="{{ old('descripcion', $datos_array['descripcion']) }}" readonly="true"> 
                                    </div>
                                    <div class="form-group{{ $errors->has('codigo') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="codigo">{{ __('Código Cabys') }}</label>
                                        <input type="number" name="codigo" id="codigo" class="form-control form-control-alternative{{ $errors->has('codigo') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Cabys') }}" value="{{ old('codigo', $datos_array['codigo']) }}" readonly="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-items-center" id="codigos_cabys">
                                    <thead class="thead-light">
                                        <tr>
                                            <th></th>
                                            <th scope="col">Categoria</th>
                                            <th scope="col">Código</th>
                                            <th scope="col">Descripción</th>
                                            <th scope="col">Impuesto(%)</th>
                                            <th scope="col">Código Tarifa</th>
                                        </tr>
                                    </thead> 
                                    <tbody>
                                        @foreach($cabys as $cab)
                                            <tr>
                                                <td>
                                                    <input type='checkbox' class='select-checkbox' name='seleccion[]'  value="{{ $cab['id'] }}">
                                                </td>
                                                <td>Caegoria # {{ $cab['categoria_0'] }}</td>
                                                <td>{{ $cab['codigo_cabys'] }}</td>
                                                <td>{{ $cab['descripcion_cabys'] }}</td>
                                                <td>{{ $cab['impuesto_cabys'] }}</td>
                                                <td>{{ $cab['tarifa_cabys'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="text" name="producto_cabys" id="producto_cabys" value="{{ old('producto_cabys', $producto->idproducto) }}" hidden="true">
                            <div class="col-12">
                                <div class="text-center">
<a  target="_blank" class="btn-info" data-toggle="tooltip" data-html="true" data-placement="left" title="<b>Recuerda, puedes re-filtrar la búsqueda de tu código CABYS con el campo SEARCH que esta a la derecha del encabezado de la tabla, una ves ubicado el código que coincida con tu productos ó servicio, debes seleccionarlo (solo uno), dando clic en el cuadro que esta a la izquierda de la tabla (a la par de la columna donde se muestra la categoría) y luego dar clic en el botón GUARDAR. </b>">¿Necesitas Ayuda?
                                        </a>
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Guardar') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('myjs')
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {       
        var table = $('#codigos_cabys').DataTable(
            {
                "autoWidth": true,
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                order: [[ 0, "desc" ]]
            }
        );
        var maxChecks = 1;
        $(".select-checkbox").change(function () {
            if($( "input:checked" ).length >= maxChecks)
                $(".select-checkbox:not(:checked)").prop( "disabled", true );
            else
                $(".select-checkbox:not(:checked)").prop( "disabled", false );
        });
    });
</script>
@endsection
