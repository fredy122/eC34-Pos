<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<link rel="shorcut icon" href="/img/favicon.ico"/>
	<title>SistemaC34++ - Pedidos</title>
	<link rel="stylesheet" href="{{ mix('/css/app.css') }}">
	<link href="/iconos/styles.css" rel="stylesheet">
	<style>
		.conainerMain{
			height: 100%;
		}
		.conainerMain .row{
			height: 100%;
		}
		.leftpanel{
			min-height: 100%;
			overflow-y: scroll;
			border-right: 1px solid #343a40;
		}
		.rightpanel{
			min-height: 100%;
			overflow-y: scroll;
		}
		.tblPeditems tr:first-child td{
			border-top: 0;
		}

	</style>
</head>
<body class="bg-light">

	<span id="app">

		<!-- Dynamic Components -->
		<component :is="currentView" v-bind="currentProps" @hide-edithubicacion="hideEdithubicacion()"
					@hide-mdlteclado="hideMdlTeclado">
		</component>
		<!-- End -->

		<nav class="navbar navbar-expand-sm fixed-top navbar-dark bg-dark">
			<a class="navbar-brand" href="#" style="padding: 0">
				<img alt="Brand" src="/img/logo-header-tablero.png" width="60px">
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarTogglerDemo01">
				<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
					<ul class="nav nav-pills" id="pills-tab" role="tablist">
						<li v-show="org.k_empr != '5'" class="nav-item">
							<a class="nav-link active" id="pills-productos-tab" data-toggle="pill" href="#pills-productos" role="tab" aria-controls="pills-productos" aria-selected="true" style="padding-right: 1rem;padding-left: 1rem">Productos</a>
						</li>
						<li v-show="org.k_empr != '2'" class="nav-item">
							<a class="nav-link" id="pills-pedidos-tab" data-toggle="pill" href="#pills-pedidos" role="tab" aria-controls="pills-pedidos" aria-selected="false" style="padding-right: 1rem;padding-left: 1rem">Pedidos</a>
						</li>
						<li v-if="org.k_empr != 5" class="nav-item pl-2">
							<div class="input-group">
								<input v-model="l_buscProd" 
										v-on:keydown.enter="buscProd"
										type="search" class="form-control text-uppercase" placeholder="Busca producto" autocomplete="off" style="width: 170px">
								<div class="input-group-append">
									<button @click="showMdlTeclado('l_buscProd',l_buscProd,'alfanum')" 
											class="btn btn-primary" type="button">Teclado</button>
								</div>
							</div>
						</li>
					</ul>
				</ul>
				<ul class="navbar-nav">
					<li class="nav-item active pr-2">
						<a class="nav-link text-white-50" href="#">Fecha Caja: <span v-text="Apertcaja.f_proc"></span></span></a>
					</li>
					<button disabled="disabled" v-text="Usuario.l_vend" class="btn btn-success mr-2"><!--VENDEDOR 2--></button>
					<!-- <button v-else @click="showMdlCambVend()" v-text="Usuario.c_vend+'-'+Usuario.l_vend" class="btn btn-success mr-2"></button> -->

					<li class="nav-item dropdown">
						<a v-text="Usuario.Usuario" class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<!--Usuario-->
						</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
							@if (session('usuario')->Usuario == 'SUPERVISOR' || session('usuario')->Usuario == 'ADMIN')
								<a v-show="org.k_empr == '2'" @click="editHubicMesas()" class="dropdown-item" href="#">Mapa mesas</a>
								<div v-show="org.k_empr == '2'" class="dropdown-divider"></div>
							@endif
							<a v-show="org.k_empr != '2'" 
								class="dropdown-item" href="/app/logout">Finalizar</a>
						</div>
					</li>
				</ul>
			</div>
		</nav>

		<!-- Cambiar Vendedor - -->
		<div id="mdlCambVend" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
			<div class="modal-dialog modal-dialog-centered" style="max-width: 45%; margin-right: 8px; margin-left: 50px !important">
				<form v-on:submit.prevent class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h5 class="modal-title">Cambiar de Vendedor</span></h5>
					</div>
					<div class="modal-body">
						<!--<form>-->
							<div class="form-group">
								<label for="c_vend">Codigo de vendedor:</label>
								<input @keyup.enter="cambVend()"
									   v-model="vendedor.c_vend" 
									   type="password" id="c_vend" class="form-control form-control-lg" autocomplete="off">
							</div>
						<!--</form>-->
						<div class="row">
							<div class="col">
								<button @click="vendedor.c_vend += '7'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">7</span></button>
							</div>
							<div class="col">
								<button @click="vendedor.c_vend += '8'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">8</span></button>
							</div>
							<div class="col">
								<button @click="vendedor.c_vend += '9'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">9</span></button>
							</div>

							<div class="w-100 mb-1"></div>

							<div class="col">
								<button @click="vendedor.c_vend += '4'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">4</span></button>
							</div>
							<div class="col">
								<button @click="vendedor.c_vend += '5'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">5</span></button>
							</div>
							<div class="col">
								<button @click="vendedor.c_vend += '6'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">6</span></button>
							</div>

							<div class="w-100 mb-1"></div>

							<div class="col">
								<button @click="vendedor.c_vend += '1'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">1</span></button>
							</div>
							<div class="col">
								<button @click="vendedor.c_vend += '2'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">2</span></button>
							</div>
							<div class="col">
								<button @click="vendedor.c_vend += '3'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">3</span></button>
							</div>

							<div class="w-100 mb-1"></div>

							<div class="col">
								<button @click="vendedor.c_vend = ''" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">Clear</span></button>
							</div>
							<div class="col">
								<button @click="vendedor.c_vend += '0'" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">0</span></button>
							</div>
							<div class="col">
								<button @click="cambVend()" type="button" class="btn btn-primary btn-block btn-lg"><span class="h1">Enter</span></button>
							</div>
							<div class="col-12 mt-1">
								<button @click="cambVendCancel()" type="button" class="btn btn-info btn-block btn-lg"><span class="h1">Cancelar</span></button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!---->
		
		<stock-pedidos inline-template>
			<div class="container-fluid conainerMain" style="padding-top: 67px">
				<div class="row">
					<div class="col-7" id="listProd"><!--leftpanel listProd -->
						<div class="tab-content h-100" id="pills-tabContent">
							<!--LISTADO PRODUCTOS-->
							<div class="tab-pane fade show active h-100" id="pills-productos" role="tabpanel" aria-labelledby="pills-productos-tab">
								<!-- Lineas - Sublineas - Productos -->
								<div class="d-flex flex-nowrap flex-column h-100">									
									<!-- Sub Lineas -->
									<div v-if="SubLineProd1.length > 0">
										<div class="row">
											<div class="col-2">
												<button @click="antSubLineProd()"
														 type="button" class="btn btn-dark btn-lg btn-block font-weight-bold mb-1 p-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px"><span v-text="filterProductos.l_line" style="font-size: 9px"></span><br><==<br>RETROCEDER</button>
											</div>
											<!-- Listado de Sub lineas -->
											<div class="col-2">
												<button @click="salirSubLineProd"
														type="button" class="btn btn-lg btn-block btn-info font-weight-bold mb-1 px-0 py-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px">VOLVER LINEAS</br><==</button>
											</div>
											<div v-for="SubLineProd of SubLineProd1"
												class="col-2">
												<button v-if="SubLineProd.c_subl == 'TODO'"
														v-text="SubLineProd.l_subl"
														@click="filterProductosXLineProd(SubLineProd.c_line)"
														type="button" class="btn btn-lg btn-block btn-secondary font-weight-bold mb-1 px-0 py-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px"></button>
												<button v-else
														v-text="SubLineProd.l_subl"
														@click="filterProductosXSubLineProd(SubLineProd.c_subl,SubLineProd.l_subl)"
														type="button" class="btn btn-lg btn-block btn-secondary font-weight-bold mb-1 px-0 py-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px"></button>
											</div>
											<!--  -->
											<div class="col-2">
												<button @click="sigSubLineProd()"
														type="button" class="btn btn-dark btn-lg btn-block font-weight-bold mb-1 p-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px"><span v-text="filterProductos.l_line" style="font-size: 9px"></span><br>==><br>AVANZAR</button>
											</div>
										</div>
									</div>
									<!--  -->
									<!-- Lineas -->
									<div v-else>
										<div class="row">
											<div class="col-2">
												<button @click="antLineProd"
														 type="button" class="btn btn-dark btn-lg btn-block font-weight-bold mb-1 p-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px"><==<br><br>RETROCEDER</button>
											</div>
											<!-- Listado de lineas -->
											<div v-for="LineProd of LineProd1" 
												class="col-2">
												<button v-if="LineProd.c_line=='COMBOS'" 
														@click="showMdlCombos()"
														v-text="LineProd.l_line"
														type="button" class="btn btn-lg btn-block btn-warning font-weight-bold mb-1 px-0 py-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px"></button>

												<button v-else 
														@click="mdlProductosXSubLinea(LineProd.c_line,LineProd.l_line)"
														v-text="LineProd.l_line"														
														type="button" class="btn btn-lg btn-block btn-info font-weight-bold mb-1 px-0 py-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px"></button>
											</div>
											<!--  -->
											<div class="col-2">
												<button @click="sigLineProd"
														type="button" class="btn btn-dark btn-lg btn-block font-weight-bold mb-1 p-0" style="white-space: normal;height: 55px; font-size: 11px;line-height: 10px">==><br><br>AVANZAR</button>
											</div>
										</div>
									</div>
									<!--  -->
									<!-- Lista Productos/Combos y Busqueda de Producto -->
									<div class="listProd" style="flex: 1;display: flex;overflow: auto;">
										<div class="w-100" style="height: 0px">
											<div class="container">
												<div v-if="this.txtBuscProd.length > 0" 
													class="row">
													<!-- Listado de Busqueda de Producto -->
													<div v-for="producto in filteredProds" class="col-2">
														<button @click="elegirProducto(producto,false);agregarModificarItem()"
																type="button" class="btn btn-success btn-lg btn-block font-weight-bold mb-1 px-0 py-0" style="white-space: normal;height: 70px; font-size: 11px;line-height: 10px">
															@{{ producto.l_prod }}
															<p class="card-text font-weight-bold">@{{ producto.l_abre }} - @{{ producto.k_mone == '0' ? 'S/.' : '$/.' }}
															<span class="font-weight-bold" v-if="p_pudefecto == '0' || p_pudefecto == '1'">@{{ parseFloat(producto.s_pre1).toFixed(2) }}</span>
															<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '2'">@{{ parseFloat(producto.s_pre2).toFixed(2) }}</span>
															<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '3'">@{{ parseFloat(producto.s_pre3).toFixed(2) }}</span>
															<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '4'">@{{ parseFloat(producto.s_pre4).toFixed(2) }}</span>
															<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '5'">@{{ parseFloat(producto.s_pre5).toFixed(2) }}</span>
															</p>
														</button>
													</div>
													<!--  -->
												</div>
												<div v-else-if="q_mostcombos == false"
													 class="row">
													 <!-- Listado de productos -->
													<div v-for="producto in filteredProdsXSubLine" class="col-2">
														<button @click="elegirProducto(producto,false);agregarModificarItem()"
																type="button" class="btn btn-success btn-lg btn-block font-weight-bold mb-1 px-0 py-0" style="white-space: normal;height: 70px; font-size: 11px;line-height: 10px">
															@{{ producto.l_prod }}
															<p class="card-text font-weight-bold">@{{ producto.l_abre }} - @{{ producto.k_mone == '0' ? 'S/.' : '$/.' }}
															<span class="font-weight-bold" v-if="p_pudefecto == '0' || p_pudefecto == '1'">@{{ parseFloat(producto.s_pre1).toFixed(2) }}</span>
															<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '2'">@{{ parseFloat(producto.s_pre2).toFixed(2) }}</span>
															<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '3'">@{{ parseFloat(producto.s_pre3).toFixed(2) }}</span>
															<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '4'">@{{ parseFloat(producto.s_pre4).toFixed(2) }}</span>
															<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '5'">@{{ parseFloat(producto.s_pre5).toFixed(2) }}</span>
															</p>
														</button>
													</div>
													<!--  -->
												</div>
												<div v-else
													 class="row">
													 <!-- Listado de combos -->
													<div v-for="combo in Combos" class="col-2">
														<button @click="elegirProducto(combo,false);agregarModificarItem()" type="button" class="btn btn-success btn-lg btn-block font-weight-bold mb-1" style="white-space: normal;height: 70px; font-size: 11px;line-height: 10px">
															@{{ combo.l_comb }}
															<p class="card-text font-weight-bold">S/. @{{ parseFloat(combo.s_impo).toFixed(2) }}</p>
														</button>
													</div>
													<!--  -->
												</div>
											</div>
										</div>
									</div>
									<!--  -->
									<!-- Observaciones -->
									<div v-show="Prodobse1.length > 0" 
										class="container py-1 border-top">
										<div class="row">
											<div class="col-2">
												<button @click="antProdobse" 
														type="button" class="btn btn-block btn-dark btn-sm px-2 py-2" style="height: 50px;font-size: 11px;white-space: normal;line-height: 10px">
													<==<br><br>RETROCEDER
												</button>
											</div>
											<div v-for="Prodobse of Prodobse1" 
												class="col-2">
												<button @click="setLobsePeditem(Prodobse.l_obse)" v-text="Prodobse.l_obse"
														type="button" class="btn btn-block btn-secondary btn-sm px-2 py-2" style="height: 50px;font-size: 11px;white-space: normal;line-height: 10px"></button>
											</div>
											<div class="col-2">
												<button @click="sigProdobse" 
														type="button" class="btn btn-block btn-dark btn-sm px-2 py-2" style="height: 50px;font-size: 11px;white-space: normal;line-height: 10px">
													==><br><br>AVANZAR
												</button>
											</div>
										</div>
									</div>
									<!--  -->
								</div>
								<!--  -->
							</div>
							<!---->

							<!--LISTADO PEDIDOS-->
							<div class="tab-pane fade" id="pills-pedidos" role="tabpanel" aria-labelledby="pills-pedidos-tab">
								<div class="sticky-top" style="margin-bottom: .5rem">
									<div class="card text-white bg-dark">
										<div class="card-body" style="padding: .5rem">
											<div class="row align-items-center">
												<div class="col">Pedidos</div>
												<div class="col text-right">
													<button @click="actPedidos()" type="button" class="btn btn-info">Actualizar</button>
												</div>
											</div>
										</div>
									</div>						
								</div>
								<div class="row justify-content-center">
									<template v-if="Pedidos.length > 0">
										<div v-for="Pedido in Pedidos" @click="devlistpeditemsxpedido(Pedido.n_comp)" class="col-12 col-lg-3" style="margin-bottom: .5rem; cursor: pointer;">

											<button v-if="Pedido.q_pago == '1'" type="button" class="btn btn-dark btn-lg btn-block px-0" style="white-space: normal;">
												<span class="badge badge-success">Pag.</span> <span v-if="Pedido.q_aten==1" class="badge badge-success">Aten.</span> <span class="text-success">N° @{{ parseInt((Pedido.n_comp).substring(6, 10)) }}</span>
											</button>											
											<button v-else-if="Pedido.d_anul == '1'" type="button" class="btn btn-dark btn-lg btn-block px-0" style="white-space: normal;">
												<span class="badge badge-warning">Anul.</span> <span v-if="Pedido.q_aten==1" class="badge badge-success">Aten.</span> <span class="text-success">N° @{{ parseInt((Pedido.n_comp).substring(6, 10)) }}</span>
											</button>											
											<button v-else type="button" class="btn btn-dark btn-lg btn-block px-0" style="white-space: normal;">
												<span v-if="Pedido.q_aten==1" class="badge badge-success">Aten.</span> <span class="text-success">N° @{{ parseInt((Pedido.n_comp).substring(6, 10)) }}</span> 
											</button>
										</div>										
									</template>
									<div v-else class="col-4" style="margin-top: calc(50% - 150px)">
										<button @click="actPedidos()" type="button" class="btn btn-primary btn-lg btn-block">VER PEDIDOS</button>
									</div>
								</div>
							</div>
							<!---->
						</div>
					</div>
					<div class="col-5 border-left"><!-- rightpanel -->

						<div class="row flex-nowrap flex-column h-100">
							<div class="border-bottom">
								<div class="container-fluid mb-1">
									<div v-if="$parent.Usuario.q_vera!=1" class="row justify-content-between">
										<div class="col-auto">
											<!-- <button v-show="Pedido.q_pago == '0' && Pedido.d_anul == '0'" @click="grabarPedido()" type="button" class="btn btn-primary mb-1">Grabar</button>

											<button v-show="Pedido.q_pago == '0' && Pedido.d_anul == '0' && Pedido.n_comp.length != 0" @click="impCocina()" type="button" class="btn btn-dark mb-1">Enviar Comanda</button> -->

											<button @click="grabarPedido()" type="button" class="btn btn-primary px-2 py-2 mb-1">Enviar Pedido</button>

											<button @click="showMdlInfoPed()" type="button" class="btn  btn-info px-3 py-2 mb-1">Info</button><!-- @click="modalComprobante()" -->
											<button @click="toggleExonerado()" type="button" class="btn btn-warning px-3 py-2 mb-1" title="Exonerado(No Afecto IGV)">E</button>
											<button @click="cambMozo()" 
													type="button" class="btn btn-secondary px-1 py-2 mb-1" style="white-space: normal;line-height: 11px;height: 42px;">
												<small>Cambiar</br>Vend. Princ.</small>
											</button>
										</div>
										<div class="col-auto text-right">
											<button @click="nuevoPedido()" type="button" class="btn btn-success px-3 py-2 mb-1">Nuevo</button>
										</div>
									</div>
									<div class="row">
										<template v-if="$parent.Usuario.q_vera!=1">
											<div class="col-2">
												<button @click="restCantPeditem" 
														class="btn btn-lg btn-block btn-round btn-dark">-</button>
											</div>
											<div class="col-2">
												<button @click="aumCantPeditem" 
														class="btn btn-lg btn-block btn-round btn-dark">+</button>
											</div>
											<div class="col-2 border-right border-dark">
												<button @click="showMdlTeclado('peditemCant','','numeric')"
														class="btn btn-lg btn-block btn-round btn-dark"><span class="icon-keyboard"></span></button>
											</div>
										</template>
										<div class="col">
											<input v-model="n_comp_substring" v-bind:class="Pedido.n_comp ? 'is-valid' : 'is-invalid'" type="text" class="form-control form-control-lg px-1 py-0" placeholder="N°" disabled="disabled" style="font-size: 25px; height: 100%;">
										</div>
										<div v-show="org.k_empr == '2'" class="col">
											<button @click="mdlShowSala()" type="button" class="btn btn-lg btn-primary btn-block" v-text="Pedido.l_ubic + ' » ' + Pedido.l_mesa" style="font-size: 14px; height: 100%;"></button>
											<!-- <button @click="mdlShowSala()" type="button" class="btn btn-primary btn-block" v-text="'SALA ' + Pedido.c_ubic + ' »  MESA ' + Pedido.c_mesa"></button> -->
										</div>
									</div>
								</div>
							</div>
							<div id="listPedItem" class="listItems" style="flex: 1;display: flex;overflow: auto;">
								<div class="col-12" style="height: 0px">
									<textarea id="txtimpr" cols="30" rows="10" style="position: fixed;top: -100%;"></textarea>
									<table class="table mb-0 tblPeditems">
										<tbody>
											<tr class="border border-left-0 border-right-0 border-top-0" @click="activeRowPeditem(index,item.c_line)" v-bind:class="[item.q_grab == '0' ? 'text-danger' : '', active_item==index ? 'table-active' : '']" v-for="(item, index) in PedItem" @dblclick="editItem(item,index)" style="font-size: 14px">
												<td class="px-1 py-1" style="vertical-align: middle; font-size: 13px; line-height: 13px;width: 60%"> 
													<!-- Exonerado -->
													<span v-show="item.c_indi=='E' || item.c_indi==='1'" class="badge badge-pill badge-warning" title="Exonerado(No Afecto IGV)">E</span>
													<!---->
													<!-- <span v-show="item.l_obse" class="text-danger" title="Descripción">»</span> --> @{{ item.l_abre }} | @{{ item.l_prod }}
													<!-- Observacion -->
													<div class="text-info">
														<small v-text="item.l_obse" 
																class="font-weight-bold"></small>
													</div>
													<!--  -->
												</td>
												<td class="px-1 py-1" style="vertical-align: middle;white-space: nowrap;">
													<!-- <span class="badge badge-pill badge-dark">@{{ parseFloat(item.s_cant).toFixed(2) }}</span> -->
													<strong>@{{ parseFloat(item.s_cant).toFixed(2) }}</strong> x @{{ parseFloat(item.s_vent).toFixed(2) }}
												</td>
												<!-- <td  style="vertical-align: middle;" class="text-right">@{{ parseFloat(item.s_vent).toFixed(2) }}</td> -->
												<td class="px-1 py-1" style="vertical-align: middle;" class="text-right">@{{ parseFloat(item.s_bimp).toFixed(2) }}</td>

												<td class="text-right px-1 py-1">
													<template v-if="$parent.Usuario.q_vera!=1">
														<span v-if="item.q_preparado === '1'"><span class="badge badge-success">Preparado</span></span>
														<span v-else-if="item.q_envic === '1'"><span class="badge badge-info">Enviado</span></span>
														<button v-else @click="removeItem(index)"  type="button" class="btn btn-dark ">Quitar</button>
													</template>
													<template>
														<span class="p-1"></span>
													</template>
													<!-- Nombre vendedor -->
													<div class="text-info" style="line-height: 7px;">
														<span v-text="item.l_vend" 
																class="font-weight-bold d-inline-block text-truncate" style="font-size: 9px; max-width: 70px"></span>
													</div>
													<!--  -->
													<!-- Hora -->
													<div v-show="item.f_digi" class="text-info" style="line-height: 7px;">
														<span v-text="item.f_digi" 
																class="font-weight-bold" style="font-size: 9px; max-width: 70px"></span>
													</div>
													<!--  -->
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div>
								<div class="container-fluid">
									<div class="row border-top">
										<div class="col-12">
											<table class="table mb-1 mt-1">
												<tr class="bg-success text-white">
													<td class="font-weight-bold py-0" style="vertical-align: middle"><small>ICBPER:</small></td>
													<td colspan="4"  style="vertical-align: middle;" class="text-right font-weight-bold py-0"><small v-text="s_icbper.toFixed(2)"></small></td>
												</tr>
												<tr class="bg-success text-white">
													<td class="font-weight-bold py-0 border-0" style="vertical-align: middle">TOTAL:</td>
													<td colspan="4"  style="vertical-align: middle;" class="text-right font-weight-bold py-0 border-0" v-text="s_tota.toFixed(2)"></td>
												</tr>
											</table>											
										</div>
									</div>
									<div class="row justify-content-between">
										<div class="col-9">
											<div class="row">
												<div v-show="org.k_empr != '5'" class="col-3">
													<button @click="showMdlPedidosReimpItemsEnv()" type="button" class="btn btn-block btn-default px-2 py-2 mb-1" title="Reimprimir Comanda" style="white-space: normal;line-height: 10px">
														<span class="icon-print align-middle"></span>
														<small><br>Reimprimir<br>Comanda</small>
													</button>
												</div>
												
												<div v-show="org.k_empr != '5'" class="col-3">
													<button @click="showMdlPedidoMoverItems" class="btn btn-block btn-default px-2 py-2 mb-1" title="Mover Items" style="white-space: normal;line-height: 10px">
														<span class="icon-switch align-middle"></span>
														<small><br>Mover<br>Producto</small>
													</button>
												</div>

												<div v-show="org.k_empr != '5'" class="col-3">
													<button @click="showMdlElimItem" class="btn btn-block btn-default px-2 py-2 mb-1" title="Quitar Items" style="white-space: normal;line-height: 10px">
														<span class="icon-page-delete align-middle"></span>
														<small><br>Anulación<br>Producto</small>
													</button>
												</div>

												<div v-show="org.k_empr == '5'" 
													class="col-3">
													<button @click="showMdlPedAgrup" class="btn btn-block btn-default px-2 py-2 mb-1" title="Quitar Items" style="white-space: normal;line-height: 10px">
														<span class="icon-print align-middle"></span>
														<small><br>Pedido</br>Agrupado</small>
													</button>
												</div>

												<div v-show="org.k_empr != '5'" 
													class="col-3">
													<button @click="anulPedido" class="btn btn-block btn-default px-2 py-2 mb-1" title="Quitar Items" style="white-space: normal;line-height: 10px">
														<span class="icon-page-delete align-middle"></span>
														<small><br>Anular</br>Pedido</small>
													</button>
												</div>
											</div>
										</div>
										<div v-show="org.k_empr == '2'"
											 class="text-right col-3">
											<button @click="impPreCuenta()" type="button" class="btn btn-block btn-info px-2 py-2 mb-1" style="white-space: normal;line-height: 10px">
												<span class="icon-print align-middle"></span>
												<small><br>Imprimir<br/>Pre-Cuenta</small>
											</button>
										</div>								
									</div>
								</div>
							</div>
						</div>

						<!--Elige Sala-->
						<div id="eligeSala" class="modal modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow: hidden;">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header bg-dark text-white align-items-center py-2">
										<h6 class="modal-title"><span class="icon-map-marker align-middle"></span> Elige Sala <span class="text-white-50 ml-3">Fecha Caja: <span v-text="Apertcaja.f_proc + ' [' + $parent.Usuario.Usuario + ']'"></span></span></h6>
										<!-- <button @click="pedidoBusc" class="btn btn-primary">Buscar pedido</button> -->
										<div>
											<label>Buscar: </label>
											<button @click="pedidoBusc('l_clav')" class="btn btn-primary">Por Clave Vend.</button>
											<button @click="pedidoBusc('l_vend')" class="btn btn-primary">Por Nombre Vend.</button>
											<button @click="pedidoBusc('n_comp')" class="btn btn-primary">Por N° Pedido</button>
										</div>
									</div>
									<div class="modal-body py-2" id="ubicaBody" style="background-image: url(/img/logo-c34.jpg);background-repeat: no-repeat;background-position: 100% 100%; background-size: 350px;">
										<!-- Pedido rapido: solo en usuarios CAJA -->
										<template v-if="$parent.Usuario.Usuario.substr(0,4) == 'CAJA'">
											<div @click="Pedido.c_ubic='99';eligeMesa('',c_mesac,'0')" 
												class="card mr-3 mb-3 d-inline-block bg-info text-white">
												<div class="card-body">
													<h6 class="card-title" style="margin: 0">
														<span class="icon-cutlery align-middle"></span> Pedido Rapido
													</h6>
												</div>
											</div>
										</template>
										<!--  -->

										<!-- Listado Salas -->
										<div v-for="Ubica in Ubicas"
											 @click="Pedido.c_ubic = Ubica.c_ubic;Pedido.l_ubic = Ubica.l_ubic;mdlSalaNext()"
											 :class="Pedido.c_ubic == Ubica.c_ubic ? 'bg-success text-white' : 'bg-light text-dark'"
											 class="card mr-3 mb-3 d-inline-block">
											<div class="card-body">
												<h6 class="card-title" style="margin: 0">
													<span class="icon-map-marker align-middle"></span> @{{ Ubica.l_ubic }}</h6>
											</div>
										</div>
										<!--  -->
									</div>
									<div class="modal-footer justify-content-between py-2">
										<button @click="logout()" type="button" class="btn btn-danger btn-lg">Finalizar</button>
										<button @click="mdlSalaNext()" type="button" class="btn btn-primary btn-lg">Siguiente</button>
									</div>
								</div>
							</div>
						</div>
						<!---->

						<!-- Mesas con draggable -->
						<!-- v-bind:class="Pedido.n_comp ? 'is-valid' : 'is-invalid'" -->
						<div id="eligeMesa" class="modal modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow: hidden;">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header bg-dark text-white py-2" style="position: relative;">
										<!-- <h5 class="modal-title"><span class="icon-cutlery align-middle"></span> SALA @{{ Pedido.c_ubic }} - Elige Mesa</h5> -->
										<h6 class="modal-title"><span class="icon-cutlery align-middle"></span> @{{ Pedido.l_ubic }} - Elige Mesa</h6>
										<h6 class="mb-0 text-white-50" style="position: absolute;top: calc(50% - 10px); left: calc(50% - 66px)">Fecha Caja: <span v-text="Apertcaja.f_proc + ' [' + $parent.Usuario.Usuario + ']'"></span></h6>
									</div>
									<div class="modal-body py-2" id="eligeMesaDraggableBody">
										<div class="containerMesas" style="width: 100%; height: 100%; overflow: auto">
											<div id="eligeMesaDraggableContainer" style="width: 100%;height: 100%; overflow: visible;">

												<div @click="eligeMesa(Mesa.l_mesa, Mesa.c_mesa, Mesa.q_ocup)" :class="Pedido.c_mesa == Mesa.c_mesa && Mesa.q_ocup == '0' ? 'bg-success' : 'bg-light'" v-for="Mesa in filterMesas" :id="Mesa.c_ubic+''+Mesa.c_mesa" class="card messaDraggable" style="width: 100px;white-space:nowrap;">
												  <div class="card-body" 
												  		:class="[Mesa.q_ocup == '1' ? 'bg-danger' : '',Mesa.q_ocup == '2' ? 'bg-info' : '']"
												  		style="position: relative;">
												    <h6 class="card-title" style="margin: 0;">@{{ Mesa.l_mesa }}</h6>
												    <small style="position: absolute;width: 100px;left: 5px;bottom: 2px;text-align: left;">@{{ Mesa.l_dat0 }}</small>
												  </div>
												</div>

											</div>
										</div>
									</div>
									<div class="modal-footer justify-content-between py-2">
										<div class="div">
											<ul class="list-inline mb-0">
												<li class="list-inline-item">
													<small><span class="badge badge-pill badge-danger">&nbsp;</span> Mesa Ocupada</small>
												</li>
												<li class="list-inline-item">
													<small><span class="badge badge-pill badge-info">&nbsp;</span> Mesa con cuenta dividida</small>
												</li>
												<li class="list-inline-item">
													<small><span class="badge badge-pill badge-success">&nbsp;</span> Mesa Seleccionada</small>
												</li>
											</ul>
										</div>
										<div>
											<button @click="aceptarSalaMesa()" type="button" class="btn btn-primary btn-lg">Aceptar</button>
											<!-- <button v-show="editHubicMesas" @click="grabaHubicMesas(filterMesas)" type="button" class="btn btn-primary btn-lg">Grabar</button> -->
											<button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cancelar</button>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- <pedidos-edithubicacion></pedidos-edithubicacion> -->
						<!---->

						<!--Combos-->
						<!-- <div id="mdlCombos" class="modal modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow: hidden;">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header bg-dark text-white">
										<h6 class="modal-title">Lista de Combos</h6>
									</div>
									<div class="modal-body scrollBarBig">
										<div class="container-fluid">
											<div class="row">
												<div v-for="combo in Combos" class="col-12 col-md-4 col-lg-3">
													<button @click="elegirProducto(combo)" type="button" class="btn btn-success btn-lg btn-block mb-3" style="white-space: normal">
														<h4 class="card-title">@{{ combo.l_comb }}</h4>
														<p class="card-text">S/. @{{ combo.s_impo }}</p>
													</button>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
						</div> -->
						<!---->

						<!--Productos-->
						<div id="eligeProducto" class="modal modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow: hidden;">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header bg-dark text-white">
										<h6 class="modal-title">@{{ filterProductos.l_line }} » <span class="text-warning">@{{ filterProductos.l_subl }}</span></h6>
									</div>
									<div class="modal-body scrollBarBig">
										<button type="button" class="btn btn-dark btn-lg mb-2" @click="filterProductosXLineProd(filterProductos.c_line)">TODOS</button>
										<button v-for="SubLine in filteredSubLineProdsxLine" v-text="SubLine.l_subl" @click="filterProductosXSubLineProd(SubLine.c_subl,SubLine.l_subl)" type="button" class="btn btn-dark btn-lg mb-2 mr-1"></button>
										<hr>
										<div class="container-fluid">
											<div class="row">
												<div v-for="producto in filteredProdsXSubLine" class="col-12 col-md-4 col-lg-3">
													<button @click="elegirProducto(producto)" type="button" class="btn btn-success btn-lg btn-block mb-3" style="white-space: normal">
														<h4 class="card-title">@{{ producto.l_prod }}</h4>
														<p class="card-text">@{{ producto.l_abre }} - @{{ producto.k_mone == '0' ? 'S/.' : '$/.' }} <!-- @{{ producto.s_pre1 }} -->
														<span v-if="p_pudefecto == '0' || p_pudefecto == '1'" v-text="producto.s_pre1"></span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '2'" v-text="producto.s_pre2"></span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '3'" v-text="producto.s_pre3"></span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '4'" v-text="producto.s_pre4"></span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '5'" v-text="producto.s_pre5"></span>
														</p>
													</button>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
						</div>
						<!---->

						<!--Buscar Producto-->
						<!-- <div id="mdlBuscProd" class="modal modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow: hidden;">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header bg-dark text-white">
										<h6 class="modal-title">Buscar Producto</h6>
									</div>
									<div class="modal-body scrollBarBig">
										<form v-on:submit.prevent>
											<div class="input-group mb-2">
												<input v-model="txtBuscProd" id="txtBuscProd" type="search" class="form-control form-control-lg text-uppercase" placeholder="Busca producto" autocomplete="off">
												<div class="input-group-append">
													<button @click="showMdlTeclado('txtBuscProd',txtBuscProd)" class="btn btn-primary" type="button">Teclado</button>
												</div>
											</div>
										</form>
										<div class="container-fluid">
											<div class="row">
												<div v-for="producto in filteredProds" class="col-12 col-md-4 col-lg-3">
													<button @click="elegirProducto(producto)" type="button" class="btn btn-success btn-lg btn-block mb-3" style="white-space: normal">
														<h4 class="card-title">@{{ producto.l_prod }}</h4>
														<p class="card-text">@{{ producto.l_abre }} - @{{ producto.k_mone == '0' ? 'S/.' : '$/.' }} 
														<span v-if="p_pudefecto == '0' || p_pudefecto == '1'" v-text="producto.s_pre1"></span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '2'" v-text="producto.s_pre2"></span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '3'" v-text="producto.s_pre3"></span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '4'" v-text="producto.s_pre4"></span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '5'" v-text="producto.s_pre5"></span>
														</p>
													</button>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
						</div> -->
						<!---->

						<!--Modificar Item -> Cantidad - -->
						<div id="modificarItem" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
							<div class="modal-dialog" style="min-width: 90%">
								<div class="modal-content">
									<div class="modal-header bg-dark text-white">
										<h6 class="modal-title text-truncate">Información de Producto - <span class="h6 text-info">@{{ '['+infoItem.c_prod +']'+infoItem.l_prod }}</span></h6>
										<span class="font-weight-bold text-success">@{{ infoItem.l_vend }} <span v-show="infoItem.f_digi">|</span> @{{ infoItem.f_digi }}</span>
									</div>
									<div class="modal-body py-1">
										<div>
											<!-- <div class="form-group mb-0">
												<label for="">
													Vendedor: <span class="badge badge-primary">@{{ infoItem.l_vend }}</span>
												</label>
											</div> -->
											<div class="form-group mb-1">
												<label for="">Cantidad</label>
												<div class="form-row">
													<div class="col-auto">
														<button @click="infoItemRestCant()" :disabled="infoItem.edit == false" type="button" class="btn btn-primary btn-block btn-round-lg" style="width: 80px">-</button>
													</div>
													<div class="col-3">
														<input :disabled="infoItem.edit == false" v-model="infoItem.s_cant" type="tel" class="form-control text-center" id="">
													</div>
													<div class="col-auto">
														<button @click="infoItemSumCant()" :disabled="infoItem.edit == false" type="button" class="btn btn-primary btn-block btn-round-lg" style="width: 80px">+</button>
													</div>
													<button @click="showMdlTeclado('infoItem.s_cant','','numeric')"
														:disabled="infoItem.edit == false" 
														class="btn btn-sm btn-primary mb-1 px-2 py-2">
														TECLADO
													</button>
												</div>
											</div>
											<div class="form-group mb-0">
												<label for="">Precio: <span class="badge badge-success">@{{ parseFloat(infoItem.s_vent).toFixed(4) }}</span></label>
												<button v-show="q_editpv==0 && SispropPDV.pq_punit==0"
														@click="showMdlTeclado('infoItem.s_vent','','numeric',1)"
														:disabled="infoItem.edit == false" 
														class="btn btn-sm btn-primary mb-1 px-2 py-2">
													TECLADO
												</button>
												<br>
												<div v-show="infoItem.c_comb == ''">
													<button @click="infoItem.n_prec = '1'"
															:disabled="p_pudefecto != '0' || infoItem.edit == false"
															:class="infoItem.n_prec == '1' ? 'btn-success' : 'btn-default'"
															type="button" class="btn btn-sm mb-1">Pr.1 - @{{ infoItem.s_pre1 }}
													</button>
													<button @click="infoItem.n_prec = '2'"
															:disabled="p_pudefecto != '0' || infoItem.edit == false"
															:class="infoItem.n_prec == '2' ? 'btn-success' : 'btn-default'"
															type="button" class="btn btn-sm mb-1">Pr.2 - @{{ infoItem.s_pre2 }}
													</button>
													<button @click="infoItem.n_prec = '3'"
															:disabled="p_pudefecto != '0' || infoItem.edit == false"
															:class="infoItem.n_prec == '3' ? 'btn-success' : 'btn-default'"
															type="button" class="btn btn-sm mb-1">Pr.3 - @{{ infoItem.s_pre3 }}
													</button>
													<button @click="infoItem.n_prec = '4'"
															:disabled="p_pudefecto != '0' || infoItem.edit == false"
															:class="infoItem.n_prec == '4' ? 'btn-success' : 'btn-default'"
															type="button" class="btn btn-sm mb-1">Pr.4 - @{{ infoItem.s_pre4 }}
													</button>
													<button @click="infoItem.n_prec = '5'"
															:disabled="p_pudefecto != '0' || infoItem.edit == false"
															:class="infoItem.n_prec == '5' ? 'btn-success' : 'btn-default'"
															type="button" class="btn btn-sm mb-1">Pr.5 - @{{ infoItem.s_pre5 }}
													</button>												
												</div>
											</div>
											<div>
												<label for="">Observación</label>
												<div v-show="org.k_empr == '2'"
													 class="contObs mb-2" 
													 style="max-height: 125px; overflow-y: auto;">
													<button v-for="obse in ProdObseFilter" 
															@click="addL_obse(obse.l_obse)" 
															type="button" class="btn btn-secondary btn-sm mr-1 mb-1 px-2 py-2">
														@{{ obse.l_obse }}
													</button>
												</div>
												<textarea v-model="infoItem.l_obse" 
														  :disabled="infoItem.edit == false" 
														  class="form-control form-control-lg text-uppercase mb-1" cols="30" rows="2">
												</textarea>
												<button @click="showMdlTeclado('infoItem.l_obse',infoItem.l_obse,'alfanum')" 
														:disabled="infoItem.edit == false"
														class="btn btn-sm btn-primary mb-1 px-2 py-2">
													TECLADO
												</button>
												<button @click="addL_obse('LIMPIAR')" 
														:disabled="infoItem.edit == false"
														type="button" class="btn btn-warning btn-sm mb-1 px-2 py-2">
													LIMPIAR
												</button>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button @click="agregarModificarItem()" 
												:disabled="infoItem.edit == false"
												type="button" class="btn btn-primary px-3 py-2">Aceptar</button>
										<button type="button" class="btn btn-secondary px-3 py-2" data-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
						</div>
						<!---->
					</div>
				</div>

				<!-- Dynamic Components -->
				<component :is="currentView" v-bind="currentProps" 
							@hide-mdl-info-pedido="hideMdlInfoPedido()" @acepta-mdl-info-pedido="aceptaMdlInfoPedido"
							@hide-mdl-pedidos-reimp-items-env="hideMdlPedidosReimpItemsEnv()"
							@hide-edithubicacion="hideEdithubicacion()" @acepta-hubicacion="aceptaHubicacion"
							@acepta-mdl-num-pers="acepMdlNumPers"
							@hide-mdlpedidomoveritems="hideMdlPedidoMoverItems"
							@hide-mdlteclado="hideMdlTeclado"
							@hide-mdlpedidoelimitem="hideMdlPedidoElimItem"
							@hide-mdlpedidobusc="hideMdlPedidosbusc"
							@hide-mdlpedidoagrup="hideMdlPedidoAgrup"
							@hide-cambmozoprinc="hideCambMozoPrinc">
				</component>
				<!--  -->
			</div>
		</stock-pedidos>

		

	</span>
	
	@include('PedidoMoverItems')
	@include('PedidoElimItem')
	@include('PedidoBusc')
	@include('Teclado')
	@include('PedidoAgrup')
	@include('CambMozoPrinc')
	<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>