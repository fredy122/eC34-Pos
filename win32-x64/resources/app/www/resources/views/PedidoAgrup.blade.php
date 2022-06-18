<script type="text/template" id="tmpPedidoAgrup">
	<div>
		<div id="mdlPedidoAgrup" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false" style="overflow: hidden;">
			<div class="modal-dialog modal-lg mt-3" style="min-width: 98%;">
				<div class="modal-content" :style="[detectmob() ? {'height': '83vh!important'} : {'height': '95vh!important'}]"><!-- style="height: 95vh !important" -->
					<div class="modal-header bg-dark text-white py-1 align-items-center">
						<h6 class="modal-title">Pedido Detallado</h6>
						<div>
							<h6 class="text-success d-inline-block">N°: <span v-text="Pedido.n_comp.substr(6,4)"></span></h6>
							<template v-if="Usuario.q_vera==1 && Pedido.n_comp!=''">
								<button v-if="Pedido.q_aten!=1" @click="atendido()" class="btn btn-sm btn-primary d-inline-block">Marcar Atendido</button>
								<span v-else class="badge badge-success">Atendido</span>
							</template>
						</div>
						<div class="col-auto">
							<div v-if="Usuario.q_vera!=1" class="input-group">
								<input v-model="l_busc"
										v-on:keyup.enter="buscProds(1)"
										type="search" id="l_busc" class="form-control text-uppercase" placeholder="BUSCAR" style="width: 200px">
								<div class="input-group-append">
									<button @click="showMdlTeclado('l_busc',l_busc)" 
											class="btn btn-primary" type="button">
										<span class="icon-keyboard"></span>
									</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-body py-2" style="height: 100px">
						<div class="h-100 d-flex flex-column">
						    <div class="row pb-1 scrollBarBig" style="overflow: auto;">
						        <div class="col-12">
						        	<div class="bg-dark scrollBarBig" style="height: 100%;">
										<table class="table table-dark table-bordered mb-0" style="font-size:12px !important">
											<thead>
												<tr>
													<th scope="col" class="py-1 px-1">CODIGO</th>
													<th scope="col" class="py-1 px-1">SERIE</th>
													<th v-for="talla of Object.keys(tallas)" 
														v-text="talla"
														class="text-center py-1 px-1"></th>
													<th scope="col" class="text-center py-1 px-1">PARES</th>
													<th scope="col" class="text-right py-1 px-1">TOTAL</th>
												</tr>
											</thead>
											<tbody>
												<pedido-agrup-item v-if="renderComponent" v-for="(prod,index) of prods" 
																	v-show="prod.q_edit == 1" 
																	:_prod.sync="prod" 
																	:_tallas="tallas" 
																	:key="index">
												</pedido-agrup-item>
											</tbody>
										</table>
									</div>
								</div>
						    </div>
						    <div class="row h-auto justify-content-between">
						    	<div class="col-auto">
						    		<button @click="imprimir" 
						    				class="btn btn-default" style="line-height: 14px">
						    			<span class="icon-print"></span>
						    			Imprimir</br>Pedido
						    		</button>

						    		<button v-if="Usuario.q_vera!=1" @click="info()" class="btn btn-info" style="line-height: 14px;height: 45px">Info</button>
						    		<button v-if="Usuario.q_vera!=1" @click="grabar" 
						    				class="btn btn-primary" style="line-height: 14px; height: 45px">
						    			Grabar
						    		</button>
						    		<button @click="hideMdlPedidoAgrup" 
						    				class="btn btn-danger" style="line-height: 14px; height: 45px">
						    			Cerrar
						    		</button>
						    	</div>
						    	<div class="col-auto">
						    		<label for="" class="d-inline-block">Pares:</label>
						    		<input :value="NumberFormat0.format(total.n_pares)" type="text" class="form-control text-right d-inline-block" style="width:100px;" disabled>
						    		<label for="" class="d-inline-block">Total:</label>
						    		<input :value="NumberFormat2.format(total.s_tota)" type="text" class="form-control text-right d-inline-block" style="width:100px;" disabled>
						    	</div>
						    </div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal" id="mdlPrev" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="false" data-keyboard="false">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header p-2 bg-primary text-light align-items-center">
						<h6>Impresión de Documentos</h6>
						<div>
							<button @click="descargar" type="button" class="btn btn-light">Descargar</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						</div>
					</div>
					<div class="modal-body h-100 p-2">
						<div class="form-groupp-sm">
							<div class="input-group mb-3">
							  <input @keydown.enter="envEmail()" v-model="l_email" type="text" class="form-control form-control-sm" placeholder="Correo Electrónico" aria-label="Recipient's username" aria-describedby="button-addon2">
							  <div class="input-group-append">
							    <button @click="envEmail()" class="btn btn-sm btn-primary" type="button" id="button-addon2">Enviar</button>
							  </div>
							</div>
						</div>
						<embed :src="'data:application/pdf;base64,'+pdf" type='application/pdf' style="width: 100%; height:70vh">
					</div>
				</div>
			</div>
		</div>
		<!--  -->

		<!-- Dynamic Components -->
		<component :is="currentView" v-bind="currentProps"
					@hide-mdlteclado="hideMdlTeclado"
					@hide-mdl-info-pedido="hideMdlInfoPedido()" @acepta-mdl-info-pedido="aceptaMdlInfoPedido"></component>
	</div>
</script>

<script type="text/template" id="tmpPedidoAgrupItemEdic">
	<!-- Informacion talla producto - -->
	<div id="mdlPedidoAgrupItemEdic" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header bg-dark text-white py-1 align-items-center">
					<h6 class="modal-title text-truncate">Edición de Registro</h6>
					<nav class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
						<a class="nav-link active" data-toggle="pill" href="#pills-s_cant" role="tab" aria-controls="pills-s_cant" aria-selected="true">Cantidad</a>
						<a class="nav-link text-light" data-toggle="pill" href="#pills-s_vent" role="tab" aria-controls="pills-s_vent" aria-selected="false">P.Venta</a>
					</nav>
				</div>
				<div class="modal-body py-1 text-dark">
					<!-- Informacion Producto -->
					<div class="row">
						<div class="col-3">
							<span v-text="prod.c_prod"></span>
						</div>
						<div class="col-9 text-right text-truncate">
							<span v-text="prod.l_prod"></span>
						</div>
						<div class="col-12">
							<span class="text-success font-weight-bold">Stock: <span v-text="stock.toFixed(4)"></span></span>
						</div>
					</div>
					<!--  -->
					
					<hr class="my-2">
					
					<!-- Cantidad / Precio enta -->
					<div class="tab-content" id="pills-tabContent">
						<div class="tab-pane show active" id="pills-s_cant" role="tabpanel" aria-labelledby="pills-s_cant-tab">
							<!-- Cantidad -->
							<div class="form-row mb-1">
								<div class="col-12">
									<div class="form-group row no-gutters mb-0">
										<label class="col-sm-2 col-form-label">Cantidad:</label>
										<div class="col-sm-10">									
											<input :value="prod.s_cant"
													:placeholder="_prod.s_cant"
													type="text" class="form-control text-right" disabled="disabled" autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<!--  -->
						</div>
						<div class="tab-pane" id="pills-s_vent" role="tabpanel" aria-labelledby="pills-s_vent-tab">
							<!-- Precio Venta -->
							<div class="form-row mb-1">
								<div class="col-12">
									<div class="form-group row no-gutters mb-0">
										<label class="col-sm-2 col-form-label">Precio Venta:</label>
										<div class="col-sm-10">									
											<input :value="prod.s_vent" 
													:placeholder="parseFloat(_prod.s_vent).toFixed(2)"
													type="text" class="form-control text-right" disabled="disabled" autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<!--  -->
						</div>
					</div>
					<!--  -->

					<div class="row no-gutters mb-1">
						<div class="col-2 pr-1">
							<button @click="setValue('5')" class="btn btn-block btn-default py-3">5</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('6')" class="btn btn-block btn-default py-3">6</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('7')" class="btn btn-block btn-default py-3">7</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('8')" class="btn btn-block btn-default py-3">8</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('9')" class="btn btn-block btn-default py-3">9</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('')" class="btn btn-block btn-info py-3">Borrar</button>
						</div>
					</div>
					<div class="row no-gutters mb-2">
						<div class="col-2 pr-1">
							<button @click="setValue('0')" class="btn btn-block btn-default py-3">0</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('1')" class="btn btn-block btn-default py-3">1</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('2')" class="btn btn-block btn-default py-3">2</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('3')" class="btn btn-block btn-default py-3">3</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('4')" class="btn btn-block btn-default py-3">4</button>
						</div>
						<div class="col-2 pr-1">
							<button @click="setValue('.')" class="btn btn-block btn-default py-3">.</button>
						</div>
					</div>
					<div class="text-center">
						<button @click="hide(prod)" 
								type="button" class="btn btn-primary px-3 py-2">Aceptar</button>
						<button @click="hide({})" 
								type="button" class="btn btn-secondary px-3 py-2">Cancelar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!---->
</script>
@include('PedidoAgrupItem')