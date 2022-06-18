<template>
	<!-- Cambiar Vendedor - -->
	<div id="mdlNumPers" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<form v-on:submit.prevent class="modal-content">
				<div class="modal-header bg-success text-white">
					<h6 class="modal-title">Ingrese Número de Personas</span></h6>
				</div>
				<div class="modal-body">
					<!--<form>-->
						<div class="form-group">
							<input v-model="pedido.n_pers"  type="number" id="n_pers" class="form-control form-control-lg" autocomplete="off">
						</div>
					<!--</form>-->
					<div class="row">
						<div class="col">
							<button @click="pedido.n_pers += '7'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">7</span></button>
						</div>
						<div class="col">
							<button @click="pedido.n_pers += '8'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">8</span></button>
						</div>
						<div class="col">
							<button @click="pedido.n_pers += '9'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">9</span></button>
						</div>

						<div class="w-100 mb-1"></div>

						<div class="col">
							<button @click="pedido.n_pers += '4'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">4</span></button>
						</div>
						<div class="col">
							<button @click="pedido.n_pers += '5'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">5</span></button>
						</div>
						<div class="col">
							<button @click="pedido.n_pers += '6'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">6</span></button>
						</div>

						<div class="w-100 mb-1"></div>

						<div class="col">
							<button @click="pedido.n_pers += '1'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">1</span></button>
						</div>
						<div class="col">
							<button @click="pedido.n_pers += '2'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">2</span></button>
						</div>
						<div class="col">
							<button @click="pedido.n_pers += '3'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">3</span></button>
						</div>

						<div class="w-100 mb-1"></div>

						<div class="col">
							<button @click="pedido.n_pers = ''" type="button" class="btn btn-secondary btn-block btn-lg"><span class="h1">Clear</span></button>
						</div>
						<div class="col">
							<button @click="pedido.n_pers += '0'" type="button" class="btn btn-success btn-block btn-lg"><span class="h1">0</span></button>
						</div>
						<div class="col">
							<button @click="acepMdlNumPers()" type="button" class="btn btn-primary btn-block btn-lg"><span class="h1">Enter</span></button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!---->
</template>

<script>
	export default {
		props: ['n_pers'],
		data(){
			return {
				pedido: {
					n_pers: this.n_pers || ''
				}
			}
		},
		mounted(){
			$('#mdlNumPers').modal('show');
			$('#n_pers').focus();
		},
		methods: {
			acepMdlNumPers() {
				this.pedido.n_pers = parseInt(this.pedido.n_pers);

				if( ! Number.isInteger(this.pedido.n_pers) ){
	                toastr.info('Número de personas es incorrecto');
	                this.pedido.n_pers = ''
				}else if( this.pedido.n_pers < 1 ){
	                toastr.info('Número de personas debe ser mayor o igual a 1');
				}else if(this.pedido.n_pers > 9999){
	                toastr.info('Número de personas no puede ser mayor a 9999');
				}else{
					$('#mdlNumPers').modal('hide');
					this.$emit('acepta-mdl-num-pers',this.pedido);
				}
			}
		}
	}
</script>