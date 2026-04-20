@extends('layouts.app', ['class' => 'login-page', 'page' => _('Restablecer Contraseña'), 'contentClass' => 'login-page'])

@section('content')
    <div class="col-lg-5 col-md-7 ml-auto mr-auto">
        <form class="form" method="post" action="{{ route('password.email') }}">
            @csrf

            <div class="card card-login card-black">
                <div >
                        <H1>Restablecimiento de Contraseñas</H1>
                </div>
                <div >
                  <label><CENTER> <b>Para el restablecimiento de Contraseñas, por favor enviar un mensaje al Whatsaap 8309-3816 para el envio de una contraseña temporal.</b></CENTER>
                      
                      
                      
                  </label>
                </div>
               
            </div>
        </form>
    </div>
@endsection
