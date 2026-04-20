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
            <form method="post" action="{{ route('productos.buscarcabys') }}" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Asignación Cabys') }} {{ $producto->nombre_producto }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-success">{{ __('Atras') }}</a>
                            </div>
                            <div class="col-6">
                                <div class="form-group{{ $errors->has('categoria') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="categoria">{{ __('Categoria') }}&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a  target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Para buscar el código CABYS de tu producto ó servicio, puedes indicar en los campos de Categoría, tarifa, descripción o código CABYS, palabras relacionadas a tu producto y dar click en BUSCAR, el sistema te traerá las coincidencias encontradas, y podrás filtrar aun más en el campo SEARCH a la derecha del encabezado de la tabla. </b>">¿Necesitas Ayuda?
                                        </a></label>
                                    <select class="form-control form-control-alternative" id="categoria" name="categoria">
                                        <option value="99">-- Sin Categoria --</option>
                                        <option value="00">0 - Productos de la agricultura, silvicultura y pesca</option>
                                        <option value="1">1 - Minerales, electricidad, gas y agua</option>
                                        <option value="2">2 - Productos alimenticios, bebidas y tabaco; textiles, prendas de vestir y productos de cuero</option>
                                        <option value="3">3 - Bienes transportables, excepto productos metálicos, maquinaria y equipo, n.c.p.</option>
                                        <option value="4">4 - Productos metálicos, maquinaria y equipo</option>
                                        <option value="5">5 - Construcciones y servicios de construcción</option>
                                        <option value="6">6 - Servicios de venta y distribución; alojamiento; servicios de suministro de comidas y bebidas; servicios de transporte; servicios de distribución de electricidad, gas y agua</option>
                                        <option value="7">7 - Servicios financieros y servicios conexos; servicios inmobiliarios; servicios de arrendamiento financiero (leasing)</option>
                                        <option value="8">8 - Servicios prestados a las empresas y servicios de producción</option>
                                        <option value="9">9 - Servicios para la comunidad, sociales y personales</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('tarifa') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="tarifa">{{ __('Tarifas') }}</label>
                                    <select class="form-control form-control-alternative" id="tarifa" name="tarifa" value="{{ old('tarifa') }}" required>
                                        <option value="0" selected="true">-- Sin Tarifa --</option> 
                                        <option value="01">Tarifa 0% (Exento)</option>
                                        <option value="02">Tarifa reducida 1%</option>
                                        <option value="03">Tarifa reducida 2%</option>
                                        <option value="04">Tarifa reducida 4%</option>
                                        <option value="08">Tarifa general 13%</option>
                                    </select>
                                    @include('alerts.feedback', ['field' => 'tarifa'])
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group{{ $errors->has('descripcion') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="descripcion">{{ __('Descripción') }}</label>
                                    <input type="text" name="descripcion" id="descripcion" class="form-control form-control-alternative{{ $errors->has('descripcion') ? ' is-invalid' : '' }}" placeholder="{{ __('Descripción') }}" value="{{ old('descripcion') }}">
                                </div>
                                <div class="form-group{{ $errors->has('codigo') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="codigo">{{ __('Código Cabys') }}</label>
                                    <input type="number" name="codigo" id="codigo" class="form-control form-control-alternative{{ $errors->has('codigo') ? ' is-invalid' : '' }}" placeholder="{{ __('Código Cabys') }}" value="{{ old('codigo') }}">
                                </div>

                            </div>
                            
                            <div class="col-12">

                                <div class="text-center">

                                    <button type="submit" class="btn btn-success mt-4">{{ __('Buscar') }}</button>
                                </div>
<a  target="_blank" class="btn-success" data-toggle="tooltip" data-html="true" data-placement="right" title="<b>Una ves hayas dado clic en buscar, te aparecerá la coincidencia de artículos relacionados, debes elegir SOLO UNO, y luego dar clic en el botón guardar. Recuerda, puedes hacer filtros nuevos en la tabla de datos, con el campo SEARCH que aparece a la derecha de esta sección. </b>">¿Necesitas Ayuda?
                                        </a>

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
                                </tbody>
                            </table>
                        </div>
                        <input type="text" name="producto_cabys" id="producto_cabys" value="{{ old('producto_cabys', $producto->idproducto) }}" hidden="true">
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
    });
</script>
@endsection
