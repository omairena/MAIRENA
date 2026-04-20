@extends('layouts.app', ['class' => 'login-page', 'page' => _('Inicio de Sesi√≥n'), 'contentClass' => 'login-page' ])
@section('content')

<head>
    {{-- Otros estilos --}}
    <style>
        .login-page {
            background-color: white; /* Fondo blanco para la p®¢gina de inicio de sesi®Æn */
            min-height: 100vh; /* Aseg®≤rate de que cubra toda la altura de la ventana */
        }
    </style>
</head>

    <div class="col-md-10 text-center ml-auto mr-auto">
        
        <h1 class="mb-5">Bienvenido.</h1>
<!--<img src="{{ asset('black') }}/img/logo.JPG" alt="" width="100" height="100">-->
                 
    </div>
    <div class="col-lg-4 col-md-6 ml-auto mr-auto">
        <form class="form" method="post" action="{{ route('login') }}">
            @csrf

            <div class="card card-login card-blue">
                
                <div class="card-body">
                  <h3 class="card-title text-center">{{ _('Inicio de Sesi√≥n') }}</h3> 
                    <div class="input-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="tim-icons icon-email-85"></i>
                            </div>
                           
                        </div>
                        <input type="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ _('Email') }}">
                        @include('alerts.feedback', ['field' => 'email'])
                    </div>
                    <div class="input-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="tim-icons icon-lock-circle"></i>
                            </div>
                        </div>
                        <input type="password" placeholder="{{ _('Contrase√±a') }}" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}">
                        @include('alerts.feedback', ['field' => 'password'])
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" href="" class="btn btn-success btn-lg btn-block mb-3">{{ _('Iniciar') }}</button>
                    <div class="pull-right">
                        <h6>
                           
                        </h6>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
