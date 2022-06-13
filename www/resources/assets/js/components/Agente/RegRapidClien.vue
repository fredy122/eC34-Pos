<template>
	<div>
		<!-- Modal Registro Rapido de Clientes -->
		<div id="regRapiClien" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h6 class="modal-title">Registro rápido de Clientes</h6>
					</div>
					<div class="modal-body">
						<form>
							<div class="form-row">
								<div class="col-6">
									<div class="form-group">
										<label for="">Tipo DI</label>
										<select v-model="agente.c_docu" class="form-control" id="c_docu" disabled="disabled">
											<option value="01">01 - LIBRETA ELECTORAL O DNI</option>
											<option value="06">06 - RUC</option>
										</select>
									</div>													
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="">N° Doc. Ident.</label>
										<input v-model="agente.n_docu" type="tel" class="form-control" disabled="disabled" maxlength="11">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="l_agen">Razón Social</label>
								<input v-model="agente.l_agen" type="text" class="form-control text-uppercase" id="l_agen" maxlength="100" autocomplete="off">
							</div>
							<div class="form-group">
								<label for="l_dire">Dirección</label>
								<input  v-model="agente.l_dire" type="text" class="form-control text-uppercase" id="l_dire" maxlength="100" autocomplete="off">
							</div>
							<div class="form-row">
								<div class="col-6">
									<div class="form-group">
										<label for="c_rutaa">Ruta</label>
										<select v-model="agente.c_rutaa" class="form-control" id="c_rutaa" style="font-family: monospace; font-size: 16px">
											<option v-for="SubSect in SubSect" v-bind:value="SubSect.c_subs">{{ SubSect.l_subs.padEnd(30,'&nbsp;')+SubSect.l_sect.padEnd(30,'&nbsp;')+SubSect.c_subs }}</option>
										</select>													
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="n_celu">N° Cel</label>
										<input v-model="agente.n_celu" type="text" class="form-control text-uppercase" id="n_celu">
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button @click="mdlCaptcha()" type="button" class="btn btn-info">JALAR SUNAT</button>
						<button @click="grabarCliente()" type="button" class="btn btn-primary">Grabar</button>
						<button @click="cancelarGrabarCliente()" type="button" class="btn btn-secondary">Cancelar</button>		
					</div>
				</div>
			</div>
		</div>
		<!-- End -->
		<!-- Modal codigo captcha -->
		<div id="mdlCaptcha" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white">
						<h6 class="modal-title">N° DI: {{ agente.n_docu }}</h6>
					</div>
					<div class="modal-body">
						<form>
							<div class="form-group text-center">
								<img width="130px" v-bind:src="captcha.base64captcha" alt="CATPCHA" style="background: gray">
							</div>
							<div class="form-group text-center">
								<input v-model="captcha.l_captcha" type="text" class="form-control text-uppercase" id="l_captcha" maxlength="4">
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button @click="mdlBuscRUCxCaptcha()" type="button" class="btn btn-primary">Buscar</button>
						<button @click="mdlCerrarCaptcha()" type="button" class="btn btn-secondary">Cerrar</button>		
					</div>
				</div>
			</div>
		</div>
		<!-- End -->
	</div>
</template>

<script>
	export default({
		props: ['n_docu'],
		data() {
			return {
				SubSect: [],
				agente: {
					c_docu: null,
	                n_docu: this.n_docu,
	                l_agen: null,
	                l_dire: null,
	                c_rutaa: null,
	                n_celu: null,
				},
				captcha: {
					base64captcha: null,
					l_captcha: null,
				}
			}
		},
		mounted() {
			this.SubSect = JSON.parse(localStorage.getItem('SubSect'));
			$('#regRapiClien').modal('show');
			$('#l_agen').focus();

			if (this.agente.n_docu.length == 11) {
				this.mdlCaptcha()
				this.agente.c_docu = '06'
			}else if (this.agente.n_docu.length == 8) {
				this.agente.c_docu = '01'
			}

		},
		methods: {
			grabarCliente: function() {
				// $('#regRapiClien').modal('hide');
				$('body').loadingModal({'animation': 'fadingCircle'});

                axios.post(`/agentes/grabarcliente`,this.agente)
                .then(function(response){
                    $('body').loadingModal('destroy');

                    $('#regRapiClien').modal('hide');
                    this.$emit('hide-mdl-agente-registrado',this.agente);

                }.bind(this))
                .catch(function(error){
                    // Do something with response error
                });
			},
			cancelarGrabarCliente: function() {
				$('#regRapiClien').modal('hide');
				this.$emit('hide-mdl-agente-reg-rapid');
			},
			// Captcha
			mdlCaptcha: function() {
				axios.post('/agente/buscsunat',{n_docu: this.agente.n_docu})
                .then((response)=>{

                    let agente = response.data.agente

                    this.agente.l_agen = agente.l_agen
                    this.agente.l_dire = agente.l_dire

                })

				// this.captcha.l_captcha = '';
				// this.captcha.base64captcha = null;

				/*$('body').loadingModal({'animation': 'fadingCircle'});

                axios.get(`/sunat/consultaruc/captcha`)
                .then(function(response){

                    this.captcha.base64captcha = 'data:image/jpg;base64,'+response.data.base64captcha;
                    $('#mdlCaptcha').modal('show');

                    $('body').loadingModal('destroy');

                }.bind(this))
                .catch(function(error){
                });*/
			},
			mdlBuscRUCxCaptcha: function(){
				if (this.captcha.l_captcha.trim().length == 0) {
                    swal({
                        title: 'Mensaje',
                        text: 'Debe ingresar el código de la imagen...',
                        type: 'error',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        animation: false
                    }).then(function () {
                        $('#l_captcha').focus();
                    }.bind(this));

                    return false;
                }

                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.get(`/sunat/consultaruc/buscaruc/${this.captcha.l_captcha}/${this.agente.n_docu}`)
                .then(function(response){

                	this.agente.l_agen = response.data.info['RAZON SOCIAL'];
                    this.agente.l_dire = response.data.info['DOMICILIO FISCAL'];
                    $('#mdlCaptcha').modal('hide');
                    // Para scroll en modal
					$('body').addClass('modal-open');
                	
                    $('body').loadingModal('destroy');

                }.bind(this))
                .catch(function(error){
                    // Do something with response error
                    $('#mdlCaptcha').modal('hide');
                    // Para scroll en modal
					$('body').addClass('modal-open');
                });
			},
			mdlCerrarCaptcha: function(){
				$('#mdlCaptcha').modal('hide');
				// Para scroll en modal
				$('body').addClass('modal-open');
			}
		}
	})
</script>