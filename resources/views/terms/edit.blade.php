@extends('layouts.app', ['page' => __('Edición de Términos y Condiciones'), 'pageSlug' => 'administrar_terminos'])  

@section('content')  
    <div class="container-fluid mt--7">  
        <div class="row">  
            <div class="col-xl-12 order-xl-1">  
                <div class="card">  
                    <div class="card-header">  
                        <div class="row align-items-center">  
                            <div class="col-9">  
                                <h3 class="mb-0">{{ __('Edición de los Términos y Condiciones') }}</h3>  
                            </div>  
                            <div class="col-3 text-right">  
                                <a href="{{ route('config.index') }}" class="btn btn-sm btn-success">{{ __('Atrás') }}</a>  
                            </div>  
                        </div>  
                    </div>  
                    <div class="card-body">  
                        <form method="post" action="{{ route('terms.update', $terminos->idsettings) }}" autocomplete="off" enctype="multipart/form-data">  
                            @csrf  
                            @method('PUT')  
                            <h6 class="heading-small text-muted mb-4">{{ __('Descripción de los Términos y Condiciones') }}</h6>  
                            
                            <div class="form-group{{ $errors->has('value') ? ' has-danger' : '' }}">  
                                <label class="form-control-label" for="value">{{ __('Términos y Condiciones') }}</label>  
                                <textarea name="value" id="value"  style="width: 100%; height: 400px;">{{ old('value', $terminos->value) }}</textarea>  

                                @include('alerts.feedback', ['field' => 'value'])  
                            </div>  
                            
                            <div class="text-center">  
                                <button type="submit" class="btn btn-success mt-4">{{ __('Guardar') }}</button>  
                            </div>  
                            
                            <input type="hidden" name="idsettings" id="idsettings" value="{{ old('idsettings', $terminos->idsettings) }}">  
                        </form>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  
@endsection
