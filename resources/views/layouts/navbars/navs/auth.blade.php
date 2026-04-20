<nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
    <div class="container-fluid">

        <div class="navbar-wrapper">
            <div class="navbar-toggle d-inline">
                <button type="button" class="navbar-toggler">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                    
                </button>

            </div>
            <a class="navbar-brand" href="#">{{ $page ?? __('Dashboard') }}</a>

                
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>

        <div class="collapse navbar-collapse" id="navigation">
            <ul class="navbar-nav ml-auto">
               <!-- <li class="search-bar input-group">
                    <button class="btn btn-link" id="search-button" data-toggle="modal" data-target="#searchModal"><i class="tim-icons icon-zoom-split"></i>
                        <span class="d-lg-none d-md-block">{{ __('Search') }}</span>
                    </button>
                </li>-->
                <?php  
$buscar_usapos = App\User_config::where('idconfigfact', Auth::user()->config_u[0]->idconfigfact)->get();  

if (isset($buscar_usapos) && $buscar_usapos->isNotEmpty()) {  
    if ($buscar_usapos[0]->usa_pos === 0) {  
        ?>  
        <li class="dropdown nav-item">  
            <a href="{{ route('punto.create') }}" class="dropdown-toggle nav-link">  
                <i class="fas fa-cash-register"></i>  
            </a>  
        </li>  
        <?php  
    } else {  
        ?>  
        <li class="dropdown nav-item">  
            <a href="{{ route('pos.create') }}" class="dropdown-toggle nav-link">  
                <i class="fas fa-cash-register"></i>  
            </a>  
        </li>  
        <?php  
    }  
}  
?>  
                <li class="dropdown nav-item">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <div class="photo">
                          <?php  
$buscar_usapos = App\Configuracion::where('idconfigfact', Auth::user()->config_u[0]->idconfigfact)->get();  

 
        ?>  
                            <img src="./black/img/logo.JPG" alt="{{ __('Profile Photo') }}">
                            <?php  
    

?>  
                            
                        </div>
                        <b class="caret d-none d-lg-block d-xl-block"></b>
                        <p class="d-lg-none">{{ __('Salir') }}</p>
                    </a>
                    <ul class="dropdown-menu dropdown-navbar">
                        <li class="nav-link">
                            <a href="{{ route('profile.edit') }}" class="nav-item dropdown-item">{{ __('Perfil') }}</a>
                        </li>
                       <!-- <li class="nav-link">
                            <a href="#" class="nav-item dropdown-item">{{ __('Configuración') }}</a>
                        </li>-->
                        <li class="dropdown-divider"></li>
                        <li class="nav-link">
                            <a href="{{ route('logout') }}" class="nav-item dropdown-item"   
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">  
    {{ __('Logout') }}  
</a>  
                        </li>
                    </ul>
                </li>
                <li class="separator d-lg-none"></li>
            </ul>
        </div>
    </div>
</nav>
<div class="modal modal-search fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="{{ __('SEARCH') }}">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <i class="tim-icons icon-simple-remove"></i>
              </button>
            </div>
        </div>
    </div>
</div>
