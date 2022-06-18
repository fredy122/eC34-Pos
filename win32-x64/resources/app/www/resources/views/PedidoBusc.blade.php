<script type="text/template" id="tmpPedidoBusc">
	<div>
		<div id="mdlPedidoBusc" class="modal modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow: hidden;">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white py-2 align-items-center">
						<h6 class="modal-title">Buscar Pedido</h6>
						<!-- Tabs -->
						<ul class="nav nav-pills" id="pills-tab" role="tablist">
							<li class="nav-item">
								<a @click="tabPorClave()"
									class="nav-link text-light active" id="pills-c_clav-tab" data-toggle="pill" href="#pills-c_clav" role="tab" aria-controls="pills-c_clav" aria-selected="true">POR CLAVE VENDEDOR</a>
							</li>
							<li class="nav-item">
								<a @click="listVend()"
									class="nav-link text-light" id="pills-l_vend-tab" data-toggle="pill" href="#pills-l_vend" role="tab" aria-controls="pills-l_vend" aria-selected="false">POR NOMBRE VENDEDOR</a>
							</li>
							<li class="nav-item">
								<a @click="tabPorNcomp()" 
									class="nav-link text-light" id="pills-n_comp-tab" data-toggle="pill" href="#pills-n_comp" role="tab" aria-controls="pills-n_comp" aria-selected="false">POR NÚMERO PEDIDO</a>
							</li>
						</ul>
						<!--  -->
					</div>
					<div id="bodyMdlPedidoBusc" class="modal-body">
						<div class="tab-content h-100" id="pills-tabContent">
							<!-- Tab Item -->
							<div class="tab-pane show active h-100" id="pills-c_clav" role="tabpanel" aria-labelledby="pills-c_clav-tab">
								<div class="input-group mb-2">
									<input v-model="l_busc" 
											@keyup.enter="listpedido"
											id="txtPedidoBusc" type="password" class="form-control form-control-lg text-uppercase" placeholder="Ingrese clave de vendedor..." autocomplete="off" readonly>
									<div class="input-group-append">
										<button @click="showMdlTeclado('l_busc','numeric')" 
												class="btn btn-primary" type="button">Teclado</button>
									</div>
								</div>
								<div class="container-fluid scrollBarBig" style="overflow-y: auto; max-height: calc(100% - 50px) ">
									<div class="row justify-content-start">
										<div v-for="pedido of pedidos"
											class="col-12 col-md-3 col-lg-2">
											<div class="btn-group-vertical mb-2 d-block">
												<button @click="devPedido(pedido.l_ubic, pedido.c_ubic, pedido.l_mesa, pedido.c_mesa)" 
														:class="pedido.q_vend == '1' ? 'btn-success' : 'btn-default'"
														type="button" class="btn btn-block " style="white-space: normal">
													#@{{ pedido.n_comp.substring(6,10) }}<br/>@{{ pedido.l_ubic }} - @{{ pedido.l_mesa }}
												</button>
												<button @click="preCuenta(pedido.l_ubic, pedido.c_ubic, pedido.l_mesa, pedido.c_mesa)"
														class="btn btn-block btn-info py-3">
													<span class="icon-print align-middle"></span> Pre-Cuenta
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--  -->
							<!-- Tab Item -->
							<div class="tab-pane h-100" id="pills-l_vend" role="tabpanel" aria-labelledby="pills-l_vend-tab">
								<div class="row justify-content-start">
									<div class="col-md-3 scrollBarBig h-100" style="overflow-y: auto">

										<div class="list-group" id="list-tab" role="tablist">
											<!-- <a v-for="vend of vendedor"
												@click="listpedido2(vend.c_vend)"
												class="list-group-item d-flex justify-content-between align-items-center" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">
												@{{ vend.l_vend }}
												<div>
													<span class="badge badge-success badge-pill" style="font-size: 15px">
														@{{ devTot(vend.c_vend) }}
													</span>
												</div>
											</a> -->
											<a v-for="Vendedor of tots"
												@click="listpedido2(Vendedor.c_vend)"
												class="list-group-item px-1" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">
												<div class="container">
													<div class="row no-gutters">
														<div class="col-8">
															@{{ Vendedor.l_vend }}
														</div>
														<div class="col-2">
															<span v-text="Vendedor.n_totac"
															  class="badge badge-success badge-pill" style="font-size: 17px"></span>
														</div>
														<div class="col-2">
															<span v-text="Vendedor.n_totae"
															  class="badge badge-secondary badge-pill" style="font-size: 17px"></span>
														</div>
													</div>
												</div>
											</a>
										</div>

									</div>
									<div class="col-md-9 h-100">
										<div class="container scrollBarBig" style="overflow-y: auto;max-height: 100%">
											<div class="row">
												<div v-for="pedido of pedidos2"
													class="col-12 col-md-4 col-lg-3">
													<button @click="devPedido(pedido.l_ubic, pedido.c_ubic, pedido.l_mesa, pedido.c_mesa)" 
															:class="pedido.q_vend == '1' ? 'btn-success' : 'btn-default'"
															type="button" class="btn btn-block mb-2" style="white-space: normal">
														#@{{ pedido.n_comp.substring(6,10) }}<br/>@{{ pedido.l_ubic }} [ @{{ pedido.l_mesa }} ]
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--  -->
							<!-- Tab Item -->
							<div class="tab-pane h-100" id="pills-n_comp" role="tabpanel" aria-labelledby="pills-n_comp-tab">
								<div class="input-group mb-2">
									<input v-model="n_comp" 
											@keyup.enter="devPedido2"
											id="n_comp" type="text" class="form-control form-control-lg text-uppercase" placeholder="INGRESE NÚMERO DE PEDIDO..." autocomplete="off">
									<div class="input-group-append">
										<button @click="showMdlTeclado2('n_comp','numeric')" 
												class="btn btn-primary" type="button">Teclado</button>
									</div>
								</div>
								<div class="container-fluid scrollBarBig" style="overflow-y: auto; max-height: calc(100% - 50px) ">
									<div class="row justify-content-start">
										<div v-for="pedido of listPedidos3"
											class="col-12 col-md-3 col-lg-2">
											<button @click="devPedido(pedido.l_ubic, pedido.c_ubic, pedido.l_mesa, pedido.c_mesa)"
													type="button" class="btn btn-block btn-success mb-2" style="white-space: normal">
												#@{{ pedido.n_comp.substring(6,10) }}<br/>@{{ pedido.l_ubic }} - @{{ pedido.l_mesa }}
											</button>
										</div>
									</div>
								</div>
							</div>
							<!--  -->
						</div>
					</div>
					<div class="modal-footer">
						<button @click="hideMdlPedidosbusc({})"
								type="button" class="btn btn-lg btn-secondary">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<component :is="currentView" v-bind="currentProps" @hide-mdlteclado="hideMdlTeclado"></component>
	</div>
</script>