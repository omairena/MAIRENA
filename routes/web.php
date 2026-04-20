<?php
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 use App\Http\Controllers\MasivoController; 
 use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Api\ReportesController;  
use App\Http\Controllers\BoletasController;
use App\Http\Controllers\ActividadController; 
Route::post('/actividades/actualizar', [ActividadController::class, 'actualizarActividades'])->name('actividades.actualizar');


Route::get('/ejecutar-receptor', [ReportesController::class, 'ejecutarReceptor'])->name('ejecutar.receptor');  

Route::get('/', 'HomeController@inicio')->name('inicio');
use App\Http\Controllers\UserconfigController; // Asegúrate de que la ruta sea correcta
Route::get('/userconfig/onoff/{idconfigfact}', [UserconfigController::class, 'toggleStatus'])->name('userconfig.onoff');
//Route::get('/', 'HomeController@dash')->name('dash');

//Route::get('/home', 'HomeController@index')->name('home');
Auth::routes();

Route::get('/home', 'HomeController@dash')->name('dash')->middleware('auth');
Route::get('/home', 'HomeController@inicio')->name('inicio')->middleware('auth');

use App\Http\Controllers\Auth\LoginController;
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => 'auth'], function () {
Route::get('editar-cc-correo-pos', 'PosController@editarCcCorreo');
	Route::resource('home','HomeController');

Route::post('/config/full', 'ConfigController@storeFull')
    ->name('config.store.full');

 Route::get('/dashboard1', ['as' => 'home.dashboard1', 'uses' => 'HomeController@resumen']);
 
 
 Route::get('/boletas', [BoletasController::class, 'index'])->name('boletas.index');
Route::get('/boletas/create', [BoletasController::class, 'create'])->name('boletas.create');
Route::post('/boletas', [BoletasController::class, 'store'])->name('boletas.store');
Route::get('/boletas/{id}/edit', [BoletasController::class, 'edit'])->name('boletas.edit');
Route::put('/boletas/{id}', [BoletasController::class, 'update'])->name('boletas.update');
Route::get('/boletas/limpiar', [BoletasController::class, 'limpiarBoletas'])->name('boletas.limpiar');
Route::post('/boletas/correo/{id}', [DownloadController::class, 'correoBoleta'])->name('boletas.correo');

Route::post('filtroBoleta', ['as' => 'filtro.boleta', 'uses' => 'BoletasController@filtrarFacturas']);
Route::post('jsonstorefb', ['as' => 'clientefb.jsonstore', 'uses' => 'FacturacionController@jsonclienteb']);
Route::get('pdf-boleta/{id}','ReportesController@pdfBoleta');
Route::post('/ajaxeliminarConfig', 'MasivoController@ajaxeliminarConfig')->name('ajaxeliminarConfig');
    /*
	|--------------------------------------------------------------------------
	| Setting App Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains app_settings info
	|
	*/
        Route::get('settings/terms/', ['as' => 'terminos.edit', 'uses' => 'SettingappController@edit_terms']);
        Route::put('settings/{id}/terms/', ['as' => 'terms.update', 'uses' => 'SettingappController@update_terms']);
		Route::get('aceptar-terminos-condiciones','SettingappController@aceptarTerminos');

    /*
	|--------------------------------------------------------------------------
	| List Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains list info
	|
	*/
		Route::resource('list','ListController');


	/*
	|--------------------------------------------------------------------------
	| Config Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains config info
	|
	*/
		Route::resource('config','ConfigController');
		//ingresar a otra configuracion
		Route::get('config/ingresar/{idconfigfact}', ['as' => 'config.ingresar', 'uses' => 'ConfigController@ingresar']);
		//Activar o Desactivar usuario
		Route::get('config/onoff/{idconfigfact}', ['as' => 'config.onoff', 'uses' => 'ConfigController@onoff']);

		//Limpiar la base de datos
		Route::get('config/limpiar/{idconfigfact}', ['as' => 'config.limpiar', 'uses' => 'ConfigController@limpiar']);
		//Comando para recepcion automatica para super admin
		Route::get('startComamnd', ['as' => 'config.recepcion', 'uses' => 'ConfigController@recepcion']);
        // Administrar terminos y condiciones

        // Config Automatica para Recepcion
        Route::resource('config_automatica','Config_automaticaController');

  	/*
	|--------------------------------------------------------------------------
	| Facturacion Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains facturar info
	|
	*/
		Route::resource('facturar','FacturacionController');
		Route::post('facturar/guardar', ['as' => 'facturar.guardar', 'uses' => 'FacturacionController@guardar']);

		//Imprimir tiket
		Route::get('facturar/{id}/imprimir', ['as' => 'facturar.imprimir', 'uses' => 'FacturacionController@ImprimirTicket']);

		// Accion para filtrar documentos desde la vista INDEX
		Route::post('filtroFactura', ['as' => 'filtro.factura', 'uses' => 'FacturacionController@filtrarFacturas']);
		Route::post('numero_factura', ['as' => 'filtro.numero_factura', 'uses' => 'FacturacionController@numero_factura']);

		// Ruta para transporte
		Route::post('facturar/guardartrans/', ['as' => 'facturar.guardartrans', 'uses' => 'FacturacionController@guardarTransporte']);
		Route::get('agregar-producto-transporte','FacturacionController@agregarLineaTransporte');

		// ruta para limpiar base de datos
		Route::get('limpiar', ['as' => 'facturar.limpiar', 'uses' => 'FacturacionController@limpiarFacturas']);

		// Reenviar documentos en proceso o sin enviar
		Route::get('reenviar-doc/{id}', ['as' => 'reenviar.documento', 'uses' => 'FacturacionController@reenviarDoc']);
		Route::get('reenviar-docrecp/{id}', ['as' => 'reenviar.documento', 'uses' => 'FacturacionController@reenviarDocrecp']);
		// Rutas para FEC del controlador Facturacion
		Route::get('fec', ['as' => 'facturar.fec', 'uses' => 'FacturacionController@fec']);
       Route::get('deletefac/{id}', ['as' => 'facturar.deletefac', 'uses' => 'FacturacionController@deletefac']);
		// Rutas para Regimen Simplificado Facturas
		Route::resource('simplificado','SimplificadoController');
		Route::post('filtroRegimen', ['as' => 'filtro.regimen', 'uses' => 'SimplificadoController@filtrarRegimen']);


		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Facturar Routes
  		|--------------------------------------------------------------------------
  		*/
  			Route::get('hacienda', ['as' => 'facturar.hacienda', 'uses' => 'FacturacionController@hacienda']);
  			//Facturacion con el POS y Crear Factura
			Route::get('actualizar-cant-factura','FacturacionController@actualiarCantFactura');
			Route::get('actualizar-descripcion-factura','FacturacionController@actualiarDescripFactura');
			Route::get('actualizar-desc-factura','FacturacionController@actualiarDescFactura');
			Route::get('agregar-linea-factura','FacturacionController@agregarLineaFactura');
			Route::get('eliminar-fila-factura','FacturacionController@eliminarLineaFactura');
			Route::get('agregar-exoneracion','FacturacionController@agregarExoneracion');
			Route::get('traer-cliente','FacturacionController@traerCliente');
			Route::get('actualizar-costo-factura','FacturacionController@actualiarCostoFactura');
			Route::get('actualizar-costo-con-iva','FacturacionController@actualiariva'); //omairena 26-05-2021
			Route::get('actualizar-costo-con-iva_u','FacturacionController@actualiariva_u'); //omairena 01-02-2023
			Route::get('actualizar-costo-sin-iva_u','FacturacionController@actualiarsiva_u'); //omairena 02-02-2023
			Route::get('modificar-flotante','FacturacionController@modificarFlotante');
			Route::get('modificar-actividad','FacturacionController@actualiarActividad');
            Route::get('modificar-actividad-cliente','FacturacionController@actualiarActividadcliente');


  	/*
	|--------------------------------------------------------------------------
	| Cliente Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains cliente info
	|
	*/
		Route::resource('cliente','ClienteController');

  		// Ruta para clasificacion 14-06-21
  		Route::get('clasificacion/{id}', ['as' => 'cliente.clasifica', 'uses' => 'ClienteController@mostrarClasificacion']);
  		Route::get('updateClasificacion', ['as' => 'cliente.clasiupdate', 'uses' => 'ClienteController@updateClasificacion']);
  		Route::get('deleteClasificacion', ['as' => 'cliente.clasidelete', 'uses' => 'ClienteController@deleteClasificacion']);
  		Route::get('addClasificacion', ['as' => 'cliente.clasiadd', 'uses' => 'ClienteController@addClasificacion']);

		// Ruta para asignar lista a cliente 11-12-21
		Route::get('listacli/{id}', ['as' => 'cliente.listacli', 'uses' => 'ClienteController@mostrarListascli']);
        Route::get('addListacli/{id}', ['as' => 'listcliente.create', 'uses' => 'ClienteController@crearListascli']);
        Route::post('storeListacli', ['as' => 'listcliente.store', 'uses' => 'ClienteController@storeListascli']);

        // Ruta para edicion de cliente a nivel de correo
        Route::get('saveAdicionalEmail','ClienteController@actualiarEmailAdicional');
        Route::get('deleteAdicionalEmail','ClienteController@deleteEmailAdicional');

	/*
	|--------------------------------------------------------------------------
	| Tiquetes Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains tiquetes info
	|
	*/
		Route::resource('tiquetes','TiquetesController');
		//Filtro de Tiquete para busqueda de documentos
		Route::post('filtroTiquete', ['as' => 'filtro.tiquete', 'uses' => 'TiquetesController@filtrarTiquetes']);

	/*
	|--------------------------------------------------------------------------
	| Donwload Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains donwload info
	|
	*/
		Route::resource('donwload-file','DonwloadController');

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Facturar Routes
  		|--------------------------------------------------------------------------
  		*/

  		Route::get('donwload-xml/{idsale}','DonwloadController@xmlfactura');
		Route::get('donwload-xml-respuesta/{idsale}','DonwloadController@xmlfactura_respuesta');

		Route::get('donwload-xml-receptor/{idsale}','DonwloadController@xmlreceptor');
		Route::get('donwload-xml-receptor-respuesta/{idsale}','DonwloadController@xmlreceptor_respuesta');
		Route::get('donwload-xml-receptor-original/{idsale}','DonwloadController@xmlreceptor_original');

		Route::get('envia-xml/{id}','DonwloadController@correoXml');
		Route::get('envia-correo-xml/{idsale}','DonwloadController@correoCXml');
		Route::get('envia-xml-pos/{idsale}','DonwloadController@correoPos');
		Route::get('envia-abono/{id}','DonwloadController@correoCxc');
		Route::get('envia-abono-pagar/{id}','DonwloadController@correoCxp');
		Route::get('envia-abono-masivo','DonwloadController@correoCxcmasivo');

		Route::get('envia-pedido/{id}','DonwloadController@correoCotizacion');

		Route::get('reenviar-correo', ['as' => 'reenviar.correo', 'uses' => 'DonwloadController@envio_masivo']);
	/*
	|--------------------------------------------------------------------------
	| Consecutivo Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains consecutivo info
	|
	*/
		Route::resource('consecutivo','ConsecutivoController');

	/*
	|--------------------------------------------------------------------------
	| Receptor Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains receptor info
	|
	*/
		Route::resource('receptor','ReceptorController');

		Route::get('allReceptor', ['as' => 'receptor.index', 'uses' => 'ReceptorController@index']);
		Route::post('saveReceptor', ['as' => 'receptor.store', 'uses' => 'ReceptorController@store']);
		Route::post('filtroReceptor', ['as' => 'filtro.receptor', 'uses' => 'ReceptorController@filtrarReceptor']);

		//Recepcion automatica Marzo 2021
		Route::get('recepcionAutomatica', ['as' => 'receptor.automatica', 'uses' => 'ReceptorController@recepcionAutomatica']);
		Route::post('sendReceptor', ['as' => 'receptor.send', 'uses' => 'ReceptorController@sendRecepcion']);
		Route::get('comandoIndividual', ['as' => 'receptor.recepcion', 'uses' => 'CronController@ejecutarComandoIndividual']);

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Receptor Routes
  		|--------------------------------------------------------------------------
  		*/
			Route::get('haciendaReceptor', ['as' => 'receptor.hacienda', 'uses' => 'ReceptorController@haciendaReceptor']);

			// Recepcion Automatica Marzo 2021
			Route::get('infoReceptor', ['as' => 'receptor.inforeceptor', 'uses' => 'ReceptorController@infReceptor']);

			// Recepcion Automatica ver documento recibido Junio 2021
			Route::get('documentReceptor', ['as' => 'receptor.document', 'uses' => 'ReceptorController@docRecibido']);
			Route::get('showClasificacion', ['as' => 'receptor.showclasifica', 'uses' => 'ReceptorController@showClasificacion']);
			Route::get('editModalClasificacion', ['as' => 'receptor.editmclasifica', 'uses' => 'ReceptorController@editModalClasificacion']);



	/*
	|--------------------------------------------------------------------------
	| Actividad Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains actividad info
	|
	*/

		Route::resource('actividad','ActividadController');



	/*
	|--------------------------------------------------------------------------
	| Factura Electronica de Compra Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains fec info
	|
	*/
		Route::resource('fec','FecController');
		Route::post('fec/guardar', ['as' => 'fec.guardar', 'uses' => 'FecController@guardar']);
		Route::post('filtroFEC', ['as' => 'filtro.feci', 'uses' => 'FecController@filtrarFEC']);

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Factura Electronica de Compra Routes
  		|--------------------------------------------------------------------------
  		*/
			Route::get('agregar-linea-fec','FecController@agregarLineaFec');
			Route::get('eliminar-fila-fec','FecController@eliminarLineaFec');
			Route::get('actualizar-cant-fec','FecController@actualiarCantFec');
			Route::get('actualizar-cost-fec','FecController@actualiarCostFec');
			Route::get('actualizar-desc-fec','FecController@actualiarDescFec');
			Route::get('modificar-fec-flotante','FecController@modificarProducto');
	/*
	|--------------------------------------------------------------------------
	| Factura Electronica de Exportacion Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains fee info
	|
	*/
		Route::resource('fee','FeeController');
		Route::post('fee/guardar', ['as' => 'fee.guardar', 'uses' => 'FeeController@guardar']);
		Route::post('filtroFee', ['as' => 'filtro.fee', 'uses' => 'FeeController@filtrarFee']);

	/*
	|--------------------------------------------------------------------------
	| Nota de Credito Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains notacredito info
	|
	*/
		Route::resource('notacredito','NotacController');
		Route::get('notaCredito/create/{idsale}','NotacController@create');

		//Regimen Simplificado 29-01
		Route::get('regimen/notaCredito/create/{idsale}','NotacController@create_regimen');
		Route::get('regimen/notaCredito', ['as' => 'notacredito.regimen', 'uses' => 'NotacController@index_regimen']);
		Route::post('filtroRegimennc', ['as' => 'filtro.notaregimen', 'uses' => 'NotacController@filtroRegimennc']);
		Route::put('regimen/notaCredito/update/{idsale}', ['as' => 'notacredito.upregimen', 'uses' => 'NotacController@update_regimen']);
		Route::get('regimen/notaCredito/edit/{idsale}', ['as' => 'notacredito.edit_regimen', 'uses' => 'NotacController@edit_regimen']);

		// Filtro de Vista index
		Route::post('filtroNC', ['as' => 'filtro.notacredito', 'uses' => 'NotacController@filtrarNC']);

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Nota de Credito Routes
  		|--------------------------------------------------------------------------
  		*/
  			Route::get('actualizar-cant-notac','NotacController@actualizarCant');
			Route::get('actualizar-costo-notac','NotacController@actualizarcosto');

	/*
	|--------------------------------------------------------------------------
	| Nota de Debito Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains notadebito info
	|
	*/

		Route::resource('notadebito','NotadController');
		Route::get('notaDebito/create/{idsale}','NotadController@create');
		Route::post('filtroND', ['as' => 'filtro.notadebito', 'uses' => 'NotadController@filtrarND']);

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Nota de Debito Routes
  		|--------------------------------------------------------------------------
  		*/
  			Route::get('agregar-linea-ndebito','NotadController@agregarLinea');

	/*
	|--------------------------------------------------------------------------
	| Productos Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains productos info
	|
	*/
		Route::resource('productos','ProductosController');

		// Enrutado para opciones CABYS
		Route::get('productos/cabys/{id}', ['as' => 'productos.cabys', 'uses' => 'ProductosController@cabys']);
		Route::post('productos/filtrarcabys/', ['as' => 'productos.buscarcabys', 'uses' => 'ProductosController@buscarcabys']);
		Route::post('productos/{id}/savecabys/', ['as' => 'productos.savecabys', 'uses' => 'ProductosController@savecabys']);
       	Route::get('productos/deleted/{id}', ['as' => 'productos.deleted', 'uses' => 'ProductosController@deleted']);
       	Route::get('productos/duplicar/{id}', ['as' => 'productos.duplicar', 'uses' => 'ProductosController@duplicar']);

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Productos Routes
  		|--------------------------------------------------------------------------
  		*/
  			Route::get('actualizar-producto','ProductosController@actualiarProducto');

  	/*
	|--------------------------------------------------------------------------
	| Inventario Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains inventario info
	|
	*/
		Route::resource('inventario','InventarioController');
		Route::get('inventario/{idinventario}/borrar', ['as' => 'inventario.delete', 'uses' => 'InventarioController@delete']);
		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Inventario Routes
  		|--------------------------------------------------------------------------
  		*/
			Route::get('agregar-linea','InventarioController@agregarLinea');


	/*
	|--------------------------------------------------------------------------
	| Userconfig Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains userconfig info
	|
	*/
		Route::resource('userconfig','UserconfigController');

	/*
	|--------------------------------------------------------------------------
	| Cxcobrar Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains cxcobrar info
	|
	*/
		Route::resource('cxcobrar','CxcobrarController');

	/*
	|--------------------------------------------------------------------------
	| Cxpagar Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains cxpagar info
	|
	*/
		Route::resource('cxpagar','CxpagarController');

	/*
	|--------------------------------------------------------------------------
	| Logcxcobrar Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains logcxcobrar info
	|
	*/
		Route::resource('logcxcobrar','LogcxcobrarController');
		Route::get('logcxcobrar/crear/{id}', ['as' => 'logcxcobrar.crear', 'uses' => 'LogcxcobrarController@crear']);
		Route::get('cxcobrar/{id}/imprimir', ['as' => 'cxcobrar.imprimir', 'uses' => 'LogcxcobrarController@ImprimirAbono']);

		//cierre de cxcobrar varias cuentas carniceria
		Route::post('cxcobrar/cierre/', ['as' => 'cxcobrar.cierre', 'uses' => 'LogcxcobrarController@Storecierre']);
		Route::get('/ajaxCuentacierre', 'LogcxcobrarController@ajaxCuentacierre')->name('ajaxCuentacierre');

	/*
	|--------------------------------------------------------------------------
	| Logcxpagar Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains logcxpagar info
	|
	*/
		Route::resource('logcxpagar','LogcxpagarController');
		Route::get('logcxpagar/crear/{id}', ['as' => 'logcxpagar.crear', 'uses' => 'LogcxpagarController@crear']);

	/*
	|--------------------------------------------------------------------------
	| Pos PUNTO DE VENTA Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains pos info
	|
	*/
        Route::resource('punto','PuntoController')->middleware('prevent-back-history');
		Route::resource('pos','PosController')->middleware('prevent-back-history');
		Route::post('pos/guardar', ['as' => 'pos.guardar', 'uses' => 'PosController@guardar']);
		Route::post('punto/guardar', ['as' => 'punto.guardar', 'uses' => 'PuntoController@guardar']);
		Route::get('punto_edit_data/{id}','PuntoController@edit_data');
		Route::get('punto_edit_det/{id}','PuntoController@edit');

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Pos Routes
  		|--------------------------------------------------------------------------
  		*/
			Route::get('autocomplete', 'PosController@autocomplete')->name('autocomplete');
			Route::get('autocomplete/nombre', 'PosController@autocomplete_nombre')->name('autocomplete_nombre');
			Route::get('autocomplete/cliente', 'PosController@autocomplete_cliente')->name('autocomplete_cliente');
			Route::get('autocomplete/forma_farmaceutica', 'ProductosController@autocomplete_forma_farmaceutica')->name('autocomplete_forma_farmaceutica');

			Route::get('autocompletecla/cliente', 'PosController@autocomplete_clientecla')->name('autocomplete_clientecla');
			Route::get('autocomplete/clientefec', 'FecController@autocomplete_clientefec')->name('autocomplete_clientefec');

            // JSON para la parte de serch de Telefono
            Route::get('autocomplete/telefono', 'PosController@autocomplete_telefono')->name('autocomplete_telefono');
			// Agregar nuevo cliente 06-02-21
			Route::post('jsonstore', ['as' => 'cliente.jsonstore', 'uses' => 'PosController@jsoncliente']);
            Route::post('jsonstoref', ['as' => 'clientef.jsonstore', 'uses' => 'FacturacionController@jsoncliente']);
			 Route::post('jsonclienfc', ['as' => 'clientefc.jsonstore', 'uses' => 'FecController@jsonclienfc']);
			// Actualizaciones del POS

			Route::get('buscar-producto-pos','PosController@buscarProducto');
			Route::get('buscar-nombre-pos','PosController@buscarProductoNombre');
            // JSON para la parte de serch de telefono
            Route::get('buscar-telefono','PosController@buscarTelefono');
            Route::get('editar-direccion-pos','PosController@editarDireccion');

            // JSON para actualizar observacion
            Route::get('editar-observacion-pos','PosController@editarObservaciones');
            Route::get('editar-observacion-fec','FecController@editarObservacionesfec');
            Route::get('ref_fact_fec','FecController@editarrefcompra');


			Route::get('buscar-cliente-pos','PosController@buscarClienteNombre')->name('buscar-cliente-pos');

			Route::get('buscar-forma_farmaceutica','ProductosController@buscar_forma_farmaceutica')->name('buscar-forma_farmaceutica');

			Route::get('buscar-cliente-posfe','FecController@buscarClienteNombrefe')->name('buscar-cliente-posfe');

			Route::get('editar-cliente-pos','PosController@editarClienteNombre');
			Route::get('agregar-art-pos','PosController@agregarArtPos');
			Route::get('modificar-dias-cxc','PosController@modficiarCxc');
			Route::get('modificar-condicion','PosController@modficiarCondicion');
			Route::get('editar-mediopago-pos','PosController@modficiarMediopago');
            Route::get('editar-mediopago-pos-new','PosController@modficiarMediopagoNuevo');
            Route::get('/buscar-medios-pagos', 'PosController@getMediosPago')->name('buscar-medios-pagos');

			Route::get('claveref','PosController@clave_ref');
			Route::get('modificar-tipocambio','PosController@modTipocambio');
			Route::get('editar-tipodoc-pos','PosController@modficiarTipodoc');
			Route::get('editar-config-pos','PosController@modficiarConfig');
			Route::get('editar-caja-pos','PosController@modficiarCaja');
			Route::get('editar-actividad-pos','PosController@modficiarAct');
			Route::get('editar-referencia-pos','PosController@modficiarRef');

			//Seccion de ruta de otros cargos 21-07-2021
			Route::get('eliminar-fila-otrocargo','PosController@eliminarLineaOtrocargo');
			Route::get('agregar-otrocargo','PosController@agregarLineaOtrocargo');

            //
            Route::get('editar-listaprecio-pos','PosController@recalcularFactura');

	/*
	|--------------------------------------------------------------------------
	| Cajas Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains cajas info
	|
	*/
		Route::resource('cajas','CajasController');
		Route::get('cajas/abrir/{id}', ['as' => 'cajas.abrir', 'uses' => 'CajasController@abrir']);
		Route::get('cajas/cerrar/{id}', ['as' => 'cajas.cerrar', 'uses' => 'CajasController@cerrar']);
		Route::get('cajas/vertodas/', ['as' => 'cajas.vertodas', 'uses' => 'CajasController@vertodas']);
		Route::get('cajas/cierrediario/{id}', ['as' => 'cajas.cierrediario', 'uses' => 'CajasController@cierrediario']);
		Route::post('cajas/cierredia/{id}', ['as' => 'cajas.cierredia', 'uses' => 'CajasController@Storecierredia']);

		Route::post('cajas/cierredia/{id}', ['as' => 'cajas.cierredia', 'uses' => 'CajasController@Storecierredia']);
		// Resumen del Dia
		Route::get('resumen/', ['as' => 'cajas.resumen', 'uses' => 'CajasController@resumendia']);
		Route::post('resumen/dailycaja/', ['as' => 'cajas.filtrodaily', 'uses' => 'CajasController@filtrodaily']);

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Cajas Routes
  		|--------------------------------------------------------------------------
  		*/
			Route::get('/ajaxCajas', 'CajasController@ajaxCajas')->name('ajaxCajas');
			Route::get('/ajaxAbonos', 'CajasController@ajaxAbonos')->name('ajaxAbonos');
			Route::get('/consultaCajeros', 'CajasController@consultaCajeros')->name('consultaCajeros');
			Route::get('/guardarCajeros', 'CajasController@guardarCajeros')->name('guardarCajeros');
	/*
	|--------------------------------------------------------------------------
	| Pedidos Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains pedidos info
	|
	*/

		Route::resource('pedidos','PedidosController');

		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Pedidos Routes
  		|--------------------------------------------------------------------------
  		*/
			Route::get('convertir-pedido/{id}','PedidosController@convertirPedido');
			Route::get('convertir-fr/{id}','FacturacionController@convertirfr');
			Route::get('convertir-ver/{id}','FacturacionController@ver_fact');
            Route::get('convertir-automatica-fr/{id}','FacturacionController@convertirAutomaticafr');
			Route::get('agregar-linea-pedido','PedidosController@agregarLineaPedido');
			Route::get('eliminar-fila-pedido','PedidosController@eliminarLineaPedido');
			Route::get('actualizar-cant-pedido','PedidosController@actualiarCantPedido');
			Route::get('actualizar-desc-pedido','PedidosController@actualiarDescPedido');
			Route::get('modificar-flotante-ped','PedidosController@modificarFlotantePed');
			Route::get('actualizar-pedido-con-iva','PedidosController@actualiarivap'); //omairena 12-01-2021

            // Nuevo update 02-09 Nuevos campos adicionales
            Route::get('actualizar-adic-1','PedidosController@actualiarLabel1Pedido');

	/*
	|--------------------------------------------------------------------------
	| Bancos Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains masivo info
	|
	*/
		Route::resource('bancos','BancosController');
		Route::post('jsonstoreb', ['as' => 'bancos.jsonstoreb', 'uses' => 'BancosController@jsonbancos']);
		Route::get('bancos/deleted/{id}', ['as' => 'bancos.deleted', 'uses' => 'BancosController@deleted']);
	    Route::post('jsonstoret', ['as' => 'clientet.jsonstore', 'uses' => 'E_manual@jsoncliente']);


	    Route::resource('ingresos','IngresosController');
	    Route::get('ingresos/{id}/procesar', ['as' => 'ingresos.procesar', 'uses' => 'IngresosController@show']);
	     Route::get('ingresos/{id}/rechazar', ['as' => 'ingresos.rechazar', 'uses' => 'IngresosController@rechaza']);

	    Route::resource('trans','TrController');
	 	Route::post('filtroTr', ['as' => 'filtro.tr', 'uses' => 'TrController@filtrarTiquetes']);
	 	Route::get('tr/deleted/{id}', ['as' => 'tr.deleted', 'uses' => 'TrController@deleted']);
	 	Route::get('autocomplete/clientetr', 'TrController@autocomplete_clientetr')->name('autocomplete_clientetr');
	 	Route::get('buscar-cliente-postr','TrController@buscarClienteNombretr')->name('buscar-cliente-postr');
	 	Route::get('tr/{id}/procesar', ['as' => 'tr.procesar', 'uses' => 'IngresosController@procesar']);

	    Route::resource('ing_manual','Ing_manual');

	    Route::resource('egresos','EgresosController');
	 	Route::get('egresos/{id}/procesar', ['as' => 'egresos.procesar', 'uses' => 'EgresospController@show']);
	 	Route::get('egresos/{id}/rechazar', ['as' => 'egresos.rechazar', 'uses' => 'EgresospController@rechaza']);

	 	Route::get('autocomplete/clasificacion', 'EgresospController@autocomplete_clasificacion')->name('autocomplete_clasificacion');
	 	Route::resource('t_egresos','Egre_manual');
	 	Route::get('t_egresos/deleted/{id}', ['as' => 't_egresos.deleted', 'uses' => 'Egre_manual@deleted']);
	    Route::resource('Egreso_manual','E_manual');
	 	Route::get('autocomplete/clasificaciontr', 'EgresospController@autocomplete_clasificacion')->name('autocomplete_clasificacion');

        Route::resource('transferencias','TransferenciasController');
        Route::get('tranfer/deleted/{id}', ['as' => 'tranfer.deleted', 'uses' => 'TransferenciasController@deleted']);
	/*
	|--------------------------------------------------------------------------
	| Masivo Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains masivo info
	|
	*/

Route::get('/masivo/mostrar-facturas', [App\Http\Controllers\MasivoController::class, 'mostrarFacturasAjax'])->name('masivo.mostrarFacturasAjax');
Route::resource('masivo', App\Http\Controllers\MasivoController::class);

		Route::resource('masivo','MasivoController');
		Route::get('/edit/{id}/cliente/{cliente}','MasivoController@edit')->name('masivo.edit');
		Route::get('/facturas', [MasivoController::class, 'mostrarFacturas'])->name('facturas.index');  
		Route::post('/update_lista', [MasivoController::class, 'update_lista'])->name('masivo.update_lista');
		Route::post('/ajaxEnviarConfigMasivo', [MasivoController::class, 'ajaxEnviarConfigMasivo'])->name('ajaxEnviarConfigMasivo'); 



		/*
  		|--------------------------------------------------------------------------
  		| Ajax section for Pedidos Routes
  		|--------------------------------------------------------------------------
  		*/
  			Route::get('/ajaxGuardarCliente', 'MasivoController@ajaxGuardarCliente')->name('ajaxGuardarCliente');
			Route::get('/informacion-cliente', 'MasivoController@ajaxInfocliente')->name('informacion-cliente');

			Route::get('editar-mediopago-masivo','MasivoController@modficiarMediopago');
			Route::get('editar-tipodoc-masivo','MasivoController@modficiarTipodoc');
			Route::get('editar-caja-masivo','MasivoController@modficiarCaja');
			Route::get('modificar-condicion-masivo','MasivoController@modficiarCondicion');
			Route::get('modificar-moneda-masivo','MasivoController@modficiarmoneda');
			Route::get('modificar-dias-masivo','MasivoController@modficiarCxc');
			Route::get('agregar-linea-masivo','MasivoController@agregarLineaFactura');
			Route::get('eliminar-fila-masivo','MasivoController@eliminarLineaFactura');
			Route::get('actualizar-cant-masivo','MasivoController@actualiarCantFactura');
			Route::get('actualizar-desc-masivo','MasivoController@actualiarDescFactura');
			Route::get('actualizar-costo-masivo','MasivoController@actualiarCostoFactura');
			Route::get('/ajaxEditarConfig', 'MasivoController@ajaxEditarConfig')->name('ajaxEditarConfig');
			Route::get('/ajaxEnviarConfig', 'MasivoController@ajaxEnviarConfig')->name('ajaxEnviarConfig');
			Route::get('modificar-flotante-mas','MasivoController@modificarFlotanteMas');
			Route::get('/infoFlotanteMas', 'MasivoController@infoFlotanteMas')->name('infoFlotanteMas');
			Route::get('editar-observacion-masivo','MasivoController@modficiarObservacion');
			Route::get('borrar-config-masivo','MasivoController@borrarConfig');
			Route::get('/ajaxBorrarMasivo', 'MasivoController@ajaxBorrarMasivo')->name('ajaxBorrarMasivo');


	/*
	|--------------------------------------------------------------------------
	| Peticiones Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains peticiones info
	|
	*/
		Route::get('/validar-porcentaje-mas', 'PeticionesController@ajaxPorcentajesMasivo')->name('ajaxPorcentajesMasivo');
		Route::get('consultarReceptor', ['as' => 'receptor.consultar', 'uses' => 'PeticionesController@ajaxConsultarReceptor']);
	    //Route::get('consultarReceptor', ['as' => 'receptor.consultar', 'uses' => 'PeticionesController@ajaxEjecutarConsultarReceptor']);

        Route::get('/ajaxCantones', 'PeticionesController@ajaxCantones');
		Route::get('/ajaxDistritos', 'PeticionesController@ajaxDistritos')->name('ajaxDistritos');
		Route::get('/ajaxNumFactura', 'PeticionesController@ajaxNumFactura')->name('ajaxNumFactura');
		Route::get('/infoFlotante', 'PeticionesController@infoFlotante')->name('infoFlotante');
		Route::get('/consultaEmpresa', 'PeticionesController@consultaEmpresa')->name('consultaEmpresa');
		Route::get('/consultaEmpresad', 'PeticionesController@consultaEmpresad')->name('consultaEmpresad');
		Route::get('/ajaxNumPedido', 'PeticionesController@ajaxNumPedido')->name('ajaxNumPedido');
		Route::get('/ajaxPcredenciales', 'PeticionesController@ajaxPcredenciales')->name('ajaxPcredenciales');
		Route::get('/ajaxNumAbono', 'PeticionesController@ajaxNumAbono')->name('ajaxNumAbono');
		Route::get('/ajaxNumCuentaxp', 'PeticionesController@ajaxNumAbono')->name('ajaxNumCuentaxp');
		Route::get('/ajaxNumMasivo', 'PeticionesController@ajaxNumMasivo')->name('ajaxNumMasivo');
		Route::get('/validar-porcentaje', 'PeticionesController@ajaxPorcentajes')->name('ajaxPorcentajes');
		Route::get('/ajaxSerchFacelectron', 'PeticionesController@ajaxSerchFacelectron')->name('ajaxSerchFacelectron');
		Route::get('/ajaxProducto', 'PeticionesController@ajaxProducto')->name('ajaxProducto');

		Route::get('consultar', ['as' => 'consultardoc.index', 'uses' => 'PeticionesController@ajaxConsultar']);
		Route::get('consultarTiquete', ['as' => 'consultartiq.index', 'uses' => 'PeticionesController@ajaxConsultarTiquete']);
		Route::get('consultarFec', ['as' => 'consultardoc.fec', 'uses' => 'PeticionesController@ajaxConsultarFec']);
		Route::get('consultarnc', ['as' => 'consultarnc.index', 'uses' => 'PeticionesController@ajaxConsultarNC']);
		Route::get('consultarnd', ['as' => 'consultarnd.index', 'uses' => 'PeticionesController@ajaxConsultarND']);
		Route::get('consultarFee', ['as' => 'consultarfee.index', 'uses' => 'PeticionesController@ajaxConsultarFee']);
		Route::get('/ajaxSerchCliente', 'PeticionesController@ajaxSerchCliente')->name('ajaxSerchCliente');
		Route::get('buscar-identificacion', 'PeticionesController@buscar_id')->name('buscar-identificacion');
		Route::get('buscar-exoneracion/{idsale}','PeticionesController@buscarExoneracion');
		Route::get('buscar-inventario','PeticionesController@buscarInventario');

        Route::get('/infoFlotanteCot', 'PeticionesController@infoFlotanteCot')->name('infoFlotanteCot');


	/*
	|--------------------------------------------------------------------------
	| Reportes Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains reportes info
	|
	*/
		//nuevo reporte
		Route::get('reporteDailysales', ['as' => 'reportes.daily', 'uses' => 'ReportesController@reporteDailysales']);
		Route::post('filtroDailysales', ['as' => 'filtro.daily', 'uses' => 'ReportesController@filtrarDaily']);
		Route::post('filtrodDailysales', ['as' => 'filtro.ddaily', 'uses' => 'ReportesController@filtrardDaily']);
        Route::get('reportedDailysales', ['as' => 'reportes.ddaily', 'uses' => 'ReportesController@reportedDailysales']);
        Route::post('filtroDailysalesconso', ['as' => 'filtro.dailyconso', 'uses' => 'ReportesController@filtrarDailyconsolidado']); //23-11-2023
		Route::get('reporteDailysalesconso', ['as' => 'reportes.dailyconso', 'uses' => 'ReportesController@reporteDailysalesconso']);
		//Compras por proveedor 25-06 Nuevo
		Route::get('compras/proveedor', ['as' => 'reportes.comprasproveedor', 'uses' => 'ReportesController@reportesComprasProveedor']);
		Route::post('filtro/compras/proveedor', ['as' => 'filtro.comprasproveedor', 'uses' => 'ReportesController@filtrarComprasProveedor']);

			//reporte de ventas diarias con OP
		Route::get('con_op', ['as' => 'reportes.con_op', 'uses' => 'ReportesController@diariocon_op']);
		Route::get('sin_op', ['as' => 'reportes.sin_op', 'uses' => 'ReportesController@diariosin_op']);

		//Pedido
		Route::get('pdf-pedido/{id}','ReportesController@pdfPedido');
		//Factura
		Route::get('imprimir-factura/{idsale}/{idcaja}','ReportesController@imprimirFactura');
		//CXC
		Route::get('imprimir-cxc/{idcxc}','ReportesController@imprimirCxc');
		// IVA
		Route::get('reporteIva', ['as' => 'reportes.iva', 'uses' => 'ReportesController@reporteIva']);
		Route::get('reportebanco', ['as' => 'reportes.banco', 'uses' => 'ReportesController@reportebanco']);
		Route::post('filtroIva', ['as' => 'filtro.iva', 'uses' => 'ReportesController@pdfIva']);
		Route::post('filtrobancos', ['as' => 'filtro.bancos', 'uses' => 'ReportesController@filtrarbancos']);
		Route::post('filtrofact', ['as' => 'filtro.fac', 'uses' => 'ReportesController@pdffac']);
		Route::get('reportefac', ['as' => 'reportes.fac', 'uses' => 'ReportesController@reportefac']);
		// VENTA
		Route::get('pdf-factura/{idsale}','ReportesController@pdf_factura');
		Route::get('pdf-regimen/{idsale}','ReportesController@pdf_regimen');
        Route::post('filtroSalescolon', ['as' => 'filtro.reportescolon', 'uses' => 'ReportesController@filtrarSalescolon']);
		Route::get('reporteSales', ['as' => 'reportes.sales', 'uses' => 'ReportesController@reporteSales']);
		Route::get('fecSales', ['as' => 'fec.sales', 'uses' => 'ReportesController@fecSales']);
		Route::get('reaSales', ['as' => 'rea.sales', 'uses' => 'ReportesController@reaSales']);
		Route::post('filtroSales', ['as' => 'filtro.reportes', 'uses' => 'ReportesController@filtrarSales']);
		Route::post('filtroFec', ['as' => 'filtro.fec', 'uses' => 'ReportesController@filtrarfec']);
		Route::post('filtrorea', ['as' => 'filtro.rea', 'uses' => 'ReportesController@filtrarrea']);
		Route::post('filtroPDF', ['as' => 'filtroPDF.reportes', 'uses' => 'ReportesController@filtroPDF']);
		Route::post('filtroPDFXML', ['as' => 'filtroPDFXML.reportes', 'uses' => 'ReportesController@filtroPDFXML']);


		//Regimen
		//Route::get('reporteRegimen', ['as' => 'reportes.regimen', 'uses' => 'ReportesController@reporteRegimen']);
		//Route::post('filtroRegimen', ['as' => 'filtro.regimen', 'uses' => 'ReportesController@filtrarRegimen']);
		Route::get('reporteRegimen', ['as' => 'reportes.regimen', 'uses' => 'ReportesController@reporteRegimen']);
		//Route::post('filtroRegimen', ['as' => 'filtro.regimen', 'uses' => 'ReportesController@filtrarRegimen']);
		Route::post('filtroRegimenexcel', ['as' => 'filtro.regimenexcel', 'uses' => 'ReportesController@filtrarRegimen']);
		Route::post('filtroop', ['as' => 'filtro.op', 'uses' => 'ReportesController@filtrarop']);
            Route::get('reporteop', ['as' => 'reportes.op', 'uses' => 'ReportesController@reporteop']);
		//Recepcion
		Route::get('reporteRecepcion', ['as' => 'reportes.recepcion', 'uses' => 'ReportesController@reporteRecepcion']);
		Route::post('filtroRecepcion', ['as' => 'filtro.recepcion', 'uses' => 'ReportesController@filtrarRecepcion']);
		Route::post('receptorxml', ['as' => 'receptorxml.reportes', 'uses' => 'ReportesController@receptorxml']);
		//Ventas
		Route::get('reporteDventas', ['as' => 'reportes.dventas', 'uses' => 'ReportesController@reporteDventas']);
		Route::post('filtroDventas', ['as' => 'filtro.dventas', 'uses' => 'ReportesController@filtrarDventas']);
		//Compras
		Route::get('reporteDcompras', ['as' => 'reportes.dcompras', 'uses' => 'ReportesController@reporteDcompras']);
		Route::post('filtroDcompras', ['as' => 'filtro.dcompras', 'uses' => 'ReportesController@filtrarDcompras']);
		//Productos
		Route::get('reporteProductos', ['as' => 'reportes.productos', 'uses' => 'ReportesController@reporteProductos']);
		Route::post('filtroProductos', ['as' => 'filtro.productos', 'uses' => 'ReportesController@filtrarProductos']);
		//CXC
		Route::get('reporteCxc', ['as' => 'reportes.cxc', 'uses' => 'ReportesController@reporteCxc']);
		Route::post('filtroCxc', ['as' => 'filtro.cxc', 'uses' => 'ReportesController@filtrarCxc']);
		//CXCABONO
		Route::get('reporteCxcabono', ['as' => 'reportes.cxcabono', 'uses' => 'ReportesController@reporteCxcabono']);
		Route::post('filtroCxcabono', ['as' => 'filtro.cxcabono', 'uses' => 'ReportesController@filtrarCxcabono']);
		//cxp
		Route::get('reporteCxp', ['as' => 'reportes.cxp', 'uses' => 'ReportesController@reporteCxp']);
		Route::post('filtroCxp', ['as' => 'filtro.cxp', 'uses' => 'ReportesController@filtrarCxp']);
		//caja
		Route::get('pdfCaja/{id}', ['as' => 'pdf.caja', 'uses' => 'ReportesController@pdfCaja']);
		//Utilidad
		Route::get('reporteUtilidad', ['as' => 'reportes.utilidad', 'uses' => 'ReportesController@reporteUtilidad']);
		Route::post('filtroUtilidad', ['as' => 'filtro.utilidad', 'uses' => 'ReportesController@filtrarUtilidad']);

	/*
	|--------------------------------------------------------------------------
	| Reportes Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains reportes info
	|
	*/

		Route::get('exportarClientes', ['as' => 'excel.clientes', 'uses' => 'ExcelController@exportClientes']);
		Route::get('exportarInv', ['as' => 'excel.inv', 'uses' => 'ExcelController@exportInv']);
		Route::get('reporteCaja/{id}', ['as' => 'reportes.caja', 'uses' => 'ExcelController@exportCaja']);

	/*
	|--------------------------------------------------------------------------
	| User Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains user info
	|
	*/
		Route::resource('user', 'UserController', ['except' => ['show']]);

	/*
	|--------------------------------------------------------------------------
	| Profile Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains profile info
	|
	*/
		Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
		Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
		Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
