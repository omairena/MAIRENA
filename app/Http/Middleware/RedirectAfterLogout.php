<?php

namespace App\Http\Middleware;  

use Closure;  
use Illuminate\Support\Facades\Auth;  

class RedirectAfterLogout  
{  
    public function handle($request, Closure $next)  
    {  
        // Aquí podrías manejar lógica adicional si es necesario  
        return $next($request);  
    }  
}  