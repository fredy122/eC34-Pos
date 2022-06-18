<?php
// Prueba Fpdf
Route::get('/fpdf','PruebasController@fpdf');

Route::group(['middleware' => 'invitado'], function () {
	// Acceso
	Route::get('/', 'AccesoController@getLogin');
	Route::post('/acceso', 'AccesoController@postLogin');
});

Route::group(['prefix' => 'app','middleware'=>['autenticado','connectionsqlserver']], function(){
	// Logout
	Route::get('/logout','AccesoController@getLogout');

	// Index
	Route::get('/', 'PedidosController@getPedidos');

	// 	Productos
	Route::get('/productos/listprodlineprodsublineprod', 'ProductoController@listProdLineProdSubLineProd');
	Route::post('/productos/devprod', 'ProductoController@devProd');

	// ProdStock
	Route::post('/prodstock/devprodstock', 'ProdStockController@devProdStock');

	// Pedidos
	Route::get('/pedidos/index', 'PedidosController@devDatosIndex');
	Route::get('/pedidos/listpedidos', 'PedidosController@listPedidos');
	Route::get('/pedidos/devpedidopeditemsxn_comp/{n_comp}', 'PedidosController@devPedidoPedItemsXn_comp');
	Route::post('/pedidos/grabarpedido', 'PedidosController@grabarPedido');
	Route::post('/pedidos/actpedido', 'PedidosController@actPedido');
	Route::post('/pedidos/anulpedido', 'PedidosController@anulPedido');
	Route::post('/pedidos/cerrarpedido', 'PedidosController@cerrarPedido');
	Route::get('/pedidos/devpedidoxc_ubicc_mesa/{c_ubic}/{c_mesa}', 'PedidosController@devPedidoXC_ubicC_mesa');
	Route::post('/pedidos/buscpedidos', 'PedidosController@buscPedidos');
	Route::post('/pedidos/buscpedidos2', 'PedidosController@buscPedidos2');
	Route::post('/pedidos/listtotpedvends', 'PedidosController@listTotPedVends');
	Route::post('/pedidos/buscpedidosxncomp', 'PedidosController@buscPedidosxNcomp');
	Route::post('/pedidos/imprpedagrup', 'PedidosController@imprPedAgrup');
	Route::post('/pedidos/devprodstock1', 'PedidosController@devProdStock1');
	Route::post('/pedidos/modpedaten', 'PedidosController@modPedAten');
	Route::post('/pedidos/envemail', 'PedidosController@envEmail');
	// --Cambio de mesas
	Route::post('/pedidos/cambmesa', 'PedidosController@cambMesa');
	// Mover item
	Route::post('/pedidos/moveritem', 'PedidosController@moverItem');
	// Eliminar Item
	Route::post('/peditem/elimitem', 'PedItemController@elimItem');

	// Agentes
	Route::get('/agentes/busCliente/{n_docu}', 'AgenteController@busCliente');
	Route::post('/agentes/grabarcliente', 'AgenteController@grabarCliente');
	Route::post('/agente/buscsunat', 'AgenteController@postBuscSunat');

	// SUNAT
	Route::get('/sunat/consultaruc/captcha', 'SunatController@getCaptchaBase64');
	Route::get('/sunat/consultaruc/buscaruc/{captcha}/{n_ruc}', 'SunatController@buscRuc');

	// Vendedor
	Route::post('/vendedor/cambvend','VendedorController@cambVend');
	Route::post('/vendedor/listvend2','VendedorController@listVend2');

	// Mesas
	Route::post('/mesas/grabahubicmesas','MesasController@grabaHubicMesas');
	Route::get('/mesas/listmesasxubic/{c_ubic}','MesasController@listMesasXUbic');
	Route::post('/mesas/libedicmesa','MesasController@libEdicMesa');
	Route::post('/mesas/postsetocupedicmesa','MesasController@postSetOcupEdicMesa');
	Route::post('/mesas/devmesa','MesasController@devMesa');

	// Cambiar mozo principal
	Route::post('/cambmozoprinc/index','CambMozoPrinc@index');
	Route::post('/cambmozoprinc/grabar','CambMozoPrinc@grabar');	
});