<template>
	<div>
		<!-- Modal Informacion Pedido -->
		<div id="mdlInfoPedido" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h6 class="modal-title">Información de Pedido</h6>
					</div>
					<div class="modal-body">
						<nav>
							<div class="nav nav-tabs mb-1" id="navInfoPedido" role="tablist">
								<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Comprobante</a>
								<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Adicional</a>
							</div>
						</nav>
						<div class="tab-content" id="navInfoPedidoContent">
							<!-- Tab informacion de comprobante -->
							<div class="tab-pane show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
								<div>
									<div class="form-group">
										<label for="c_comp">Tipo</label>
										<select :disabled="compr1.q_eligecomp"  class="form-control" id="c_comp" v-model="compr1.c_comp"><!-- :disabled="Agente.diabled_c_comp" -->
											<option value="01">01 - FACTURA</option>
											<option value="03">03 - BOLETA</option>
											<option value="12">12 - TICKET</option>
											<option value="55">55 - VENTA INTERNA</option>
										</select>
									</div>
									<div class="form-row">
										<div class="col-6">
											<div class="form-group">
												<label for="n_docu">N° RUC</label>
												<div class="form-row">
													<div class="col-9">
														<input @keydown.enter="busCliente()" v-model="compr1.n_docu" type="tel" class="form-control text-uppercase" id="n_docu" maxlength="11" autocomplete="off">
													</div>
													<div class="col-3">
														<button @click="busCliente()" type="button" class="btn btn-primary btn-block">»</button>
													</div>
												</div>
											</div>													
										</div>
										<div class="col-6">
											<div class="form-group">
												<label for="">Cliente</label>
												<input v-model="compr1.l_agen" type="text" class="form-control text-uppercase" disabled="disabled">
											</div>
										</div>
									</div>
									<div class="form-group">
										<!-- <label for="">Dirección</label> -->
										<input v-model="compr1.l_dire" type="text" class="form-control text-uppercase" disabled="disabled">
									</div>
									<div>
										<label for="k_page">Tipo de Pago</label>
										<select v-model="compr1.k_page" class="form-control" id="k_page">
											<option value="1">1 - Efec/Cont</option>
											<option value="2">2 - Banco</option>
											<option value="3">3 - Crédito</option>
										</select>
									</div>
								</div>
							</div>
							<!-- Tab informacion adicional -->
							<div class="tab-pane" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
								<div>
									<div class="form-group mb-2">
										<label class="mb-0" for="n_pers">Número de Personas</label>
										<input v-model="compr1.n_pers" type="number" class="form-control" id="n_pers">
									</div>
									<div class="form-group mb-2">
										<label class="mb-0">Placa</label>
										<div class="input-group mb-3">
											<input v-model="compr1.ad_plac" 
													type="text" class="form-control text-uppercase">
											<div class="input-group-append">
												<button @click="showMdlTeclado('compr1.ad_plac',compr1.ad_plac)" 
														class="btn btn-primary" type="button" id="button-addon2">Teclado</button>
											</div>
										</div>
									</div>
									<div>
										<label class="mb-0" for="l_obse">Observación</label>
										<textarea v-model="compr1.l_obse" id="l_obse" class="form-control text-uppercase mb-1" cols="30" rows="5"></textarea>
										<button @click="showMdlTeclado('compr1.l_obse',compr1.l_obse)" class="btn btn-sm btn-primary">TECLADO</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button @click="aceptaMdlInfoPedido()" type="button" class="btn btn-primary">Aceptar</button>
						<button @click="hideMdlInfoPedido()" type="button" class="btn btn-secondary">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- End -->
		<!-- Dynamic Components -->
		<component :is="currentView" v-bind="currentProps" @hide-mdl-agente-elegido="eligeAgente" @hide-mdl-agente-busc="hideMdlListAgentes"
					@hide-mdl-agente-registrado="eligeAgente" @hide-mdl-agente-reg-rapid="cancelarGrabarCliente"
					@hide-mdlteclado="hideMdlTeclado"></component>
		<!-- End -->
	</div>
</template>

<script>
	export default({
		props: ['compr','adicional'],
		data() {
			return {
				// Dynamic watcher n_docu
				unwatch_n_docu: null,
				// Dynamic Components
				currentView: null,
                currentProps: null,
                // Data
				compr1: {
					q_valid: true,
					q_eligecomp: false,
					c_comp: this.compr.c_comp,
					c_docu: this.compr.c_docu,
					n_docu: this.compr.n_docu,
					l_agen: this.compr.l_agen,
					l_dire: this.compr.l_dire,
					k_page: this.compr.k_page,
					l_obse: this.adicional.l_obse,
					n_pers: this.adicional.n_pers,
					ad_plac: this.adicional.ad_plac,
				}
			}
		},
		mounted() {
			// Solo las empresas que no son restaurantes pueden elegit tipo de comprobante
			var k_empr = (JSON.parse(localStorage.getItem('org'))).k_empr;
			if (k_empr == '2') {
                this.compr1.q_eligecomp = true;
            }else{
                this.compr1.q_eligecomp = false;
            }
            // Habilitamos watcher de n_docu
			this.unwatch_n_docu = this.watch_n_docu();			
			// Mostramos modal
			$('#mdlInfoPedido').modal('show');
			$('#n_docu').focus();
		},
		methods:{
			aceptaMdlInfoPedido: function(){
				this.compr1.n_pers = parseInt(this.compr1.n_pers);

				if (!this.compr1.q_valid) {
					swal({
                        title: 'Mensaje',
                        text: "Debe ingresar Nº de documento de identidad...",
                        type: 'info',
                    }).then(function () {
                    	$('#navInfoPedido a:first').tab('show');
                        $('#n_docu').focus();
                    });
				}else if( ! Number.isInteger(this.compr1.n_pers) ){
					swal({
	                    title: 'Mensaje',
	                    text: "Número de personas es incorrecto",
	                    type: 'info',
	                }).then(function () {
                    	$('#navInfoPedido a:nth-child(2)').tab('show');
                        $('#n_pers').focus();
                    });
				}else if( this.compr1.n_pers < 1 ){
					swal({
	                    title: 'Mensaje',
	                    text: "Número de personas debe ser mayor o igual a 1",
	                    type: 'info',
	                }).then(function () {
                    	$('#navInfoPedido a:nth-child(2)').tab('show');
                        $('#n_pers').focus();
                    });
				}else if(this.compr1.n_pers > 9999){
					swal({
	                    title: 'Mensaje',
	                    text: "Número de personas no puede ser mayor a 9999",
	                    type: 'info',
	                }).then(function () {
                    	$('#navInfoPedido a:nth-child(2)').tab('show');
                        $('#n_pers').focus();
                    });
				}else{
					$('#mdlInfoPedido').modal('hide');
					this.$emit('acepta-mdl-info-pedido',this.compr1);
				}
			},
			hideMdlInfoPedido: function(){
				$('#mdlInfoPedido').modal('hide');
				this.$emit('hide-mdl-info-pedido');
			},
			// Buscar Agente
			busCliente: function(){
				if (this.compr1.q_valid == true) {
                    return true;
                }else{
                	this.compr1.n_docu = this.compr1.n_docu.trim();

                	this.currentView = 'agente-busc';
                	this.currentProps = {
                		nl_agen: this.compr1.n_docu
                	};
                }
			},
			eligeAgente: function(agente){
				this.unwatch_n_docu();

				this.compr1.q_valid = true;
				this.compr1.n_docu = agente.n_docu;
				this.compr1.c_docu = agente.c_docu;
				this.compr1.l_agen = agente.l_agen;
				this.compr1.l_dire = agente.l_dire;
				this.currentView = null;
				this.currentProps = null;
				
				$('#n_docu').focus();

				this.unwatch_n_docu = this.watch_n_docu();

				// Para scroll en modal
				$('body').addClass('modal-open');
			},
			hideMdlListAgentes: function(data){
				if( data.hasOwnProperty('regdniruc') ){
					swal({
                        title: '',
                        text: "N° RUC/DNI no existe... desea registrar?",
                        type: 'question',
                        showCancelButton: true,
                    }).then(function () {
                    	this.currentView = 'agente-reg-rapid';
						this.currentProps = {n_docu: this.compr1.n_docu};

                    }.bind(this), function (dismiss) {
                        if (dismiss === 'cancel') {
                        	$('#n_docu').focus();
							this.currentView = null;
							this.currentProps = null;
                        }
                    }.bind(this))
					
				}else{
					$('#n_docu').focus();
					this.currentView = null;
					this.currentProps = null;
				}
				// Para scroll en modal
				$('body').addClass('modal-open');
			},
			// Grabar nuevo cliente
			cancelarGrabarCliente: function(){
				$('#n_docu').focus();
				// Para scroll en modal
				$('body').addClass('modal-open');
			},
			// Watcher n_docu
			watch_n_docu: function(){
				return this.$watch('compr1.n_docu', function(newVal, oldVal) {
					this.compr1.q_valid = false;
					this.compr1.c_docu = null;
					this.compr1.l_agen = null;
					this.compr1.l_dire = null;
				});
			},
			// Teclado Virtual
            showMdlTeclado(model,value){
                this.currentView = 'teclado'
                this.currentProps = {
                    _model: model,
                    _value: value
                }
            },
            hideMdlTeclado(resul){
                this.currentView = null;
                this.currentProps = null;

                if(resul.hasOwnProperty('val')){
                    let val = resul.model.split(".");
                    this.$data[val[0]][val[1]] = resul.val
                }
            }
		}
	})
</script>