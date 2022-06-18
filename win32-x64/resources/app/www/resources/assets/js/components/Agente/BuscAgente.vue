<template>
	<!-- Modal Listado de Agentes Encontrados -->
	<div id="mdlListAgentes" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-dark text-white">
					<h6 class="modal-title">Búsqueda...</h6>
				</div>
				<div class="modal-body">
					<div class="alert alert-info" role="alert">
						Solo se mostrarán los primeros 5 resultados.
					</div>
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>Ap. y Nombres/Razón Social</th>
								<th>N° DI/RUC</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="agente in agentes" v-on:dblclick="eligeAgente(agente)">
								<td v-text="agente.l_agen"></td>
								<td v-text="agente.n_docu"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button @click="hideMdlListAgentes({})" type="button" class="btn btn-secondary">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
	<!-- End -->
</template>

<script>
	export default({
		props: ['nl_agen'],
		data() {
			return {
				agentes: []
			}
		},
		mounted() {
			this.busCliente();
		},
		methods: {
			busCliente: function(nl_agen){
				$('body').loadingModal({'animation': 'fadingCircle'});

                axios.get(`/agentes/busCliente/${this.nl_agen}`)
                .then(function(response){
                    $('body').loadingModal('destroy');

                    if(response.data.status == '1'){
                    	// Nº RUC/DNI no existe... desea registrar?
                    	this.hideMdlListAgentes({regdniruc: true});

                    }else if( response.data.cliente.length > 0 ){
                        this.agentes = response.data.cliente;
                        $('#mdlListAgentes').modal('show');
                    }else{
                    	this.eligeAgente(response.data.cliente);
                    }

                }.bind(this))
                .catch(function(error){
                	this.hideMdlListAgentes({});
                    // Do something with response error
                }.bind(this));
			},
			eligeAgente: function(agente){
				$('#mdlListAgentes').modal('hide');
				this.$emit('hide-mdl-agente-elegido', agente);
			},
			hideMdlListAgentes: function(obj){
				$('#mdlListAgentes').modal('hide');
				this.$emit('hide-mdl-agente-busc',obj);
			}
		}
	})
</script>