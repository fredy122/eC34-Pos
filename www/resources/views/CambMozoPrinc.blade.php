<script type="text/template" id="tmpCambMozoPrinc">
	<div id="CambMozoPrinc" class="modal scrollBarBig" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<form v-on:submit.prevent class="modal-content">
				<div class="modal-header bg-dark">
					<h6 class="modal-title text-light">
						Cambiar Vendedor Principal
					</h6>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="col-12">
							<div class="form-group">
								<label class="form-label">Vendedor</label>
								<select v-model="c_vend"
										class="form-control">
									<option v-for="val of Vendedor" :value="val.c_vend" v-text="val.c_vend+' - '+val.l_vend"></option>
								</select>
							</div>
						</div>
						<div class="col-12 text-center">
							<button @click="grabar" 
									class="btn btn-primary">Grabar</button>
							<button @click="hideCambMozoPrinc" 
									class="btn btn-default">Cerrar</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</script>