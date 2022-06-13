<script type="text/template" id="tmpPedidoElimItem">
	<div>
		<div id="mdlPedidoElimItem" class="modal scrollBarBig" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-lg modal-dialog-centered">
				<form v-on:submit.prevent class="modal-content">
					<div class="modal-header bg-dark">
						<h6 class="modal-title text-light">
							<span class="icon-page-delete align-middle"></span>
							Anulación Productos</span>
						</h6>
					</div>
					<div class="modal-body">
						<table class="table mb-1 tblPeditems">
							<tbody>
								<!-- <tr class="border table-primary">
									<td>
										<span v-show="_PedItem.c_indi=='E' || _PedItem.c_indi==='1'" class="badge badge-pill badge-warning" title="Exonerado(No Afecto IGV)">E</span>
										<span v-show="_PedItem.l_obse" class="text-danger" title="Descripción">»</span>
										@{{ _PedItem.l_abre }} | @{{ _PedItem.l_prod }}
									</td>
									<td  style="vertical-align: middle;">
										<span class="badge badge-pill badge-dark">@{{ parseFloat(_PedItem.s_cant) }}</span>
									</td>
									<td  style="vertical-align: middle;" class="text-right">@{{ parseFloat(_PedItem.s_vent).toFixed(2) }}</td>
									<td  style="vertical-align: middle;" class="text-right">@{{ parseFloat(_PedItem.s_bimp).toFixed(2) }}</td>
								</tr> -->

								<tr v-for="(item,index) in _PedItem" class="border" :class="item.s_cant1 > 0 ? 'table-success' : ''">
									<td class="py-1" style="vertical-align: middle;">
										<!-- Exonerado -->
										<span v-show="item.c_indi=='E' || item.c_indi==='1'" class="badge badge-pill badge-warning" title="Exonerado(No Afecto IGV)">E</span>
										<!---->
										<span v-show="item.l_obse" class="text-danger" title="Descripción">»</span>
										@{{ item.l_abre }} | @{{ item.l_prod }}
									</td>
									<td class="py-1" style="vertical-align: middle;">
										<span class="badge badge-pill badge-dark">@{{ parseFloat(item.s_cant) }}</span>
									</td>
									<td class="py-1" style="vertical-align: middle;">
										<button @click="resCant(item)"
												type="button" class="btn btn-dark px-4 py-2">-</button>
										<span class="badge badge-pill badge-danger">@{{ parseFloat(item.s_cant1) }}</span>
										<button @click="sumCant(item)"
												type="button" class="btn btn-dark px-4 py-2">+</button>
									</td>
									<td style="vertical-align: middle;" class="text-right py-1">@{{ parseFloat(item.s_vent) }}</td>
									<td style="vertical-align: middle;" class="text-right py-1">@{{ parseFloat(item.s_bimp).toFixed(2) }}</td>

									<td style="vertical-align: middle;" class="text-right">
										<h5><span class="badge badge-info">Enviado</span></h5>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="form-row">
							<!-- <div class="col-12">
								<div class="form-group">
									<label for="">Cantidad a eliminar</label>
									<div class="form-row">
										<div class="col-1">
											<button @click="restCant" 
													type="button" class="btn btn-primary btn-block btn-round-lg">-</button>
										</div>
										<div class="col-2">
											<input v-model="PedItem1.s_cant" 
												   type="tel" class="form-control text-center">
										</div>
										<div class="col-1">
											<button @click="sumCant" 
													type="button" class="btn btn-primary btn-block btn-round-lg">+</button>
										</div>
										<button @click="showMdlTeclado('PedItem1.s_cant','numeric')"
											class="btn btn-sm btn-primary mb-1 px-2 py-2">
											TECLADO
										</button>
									</div>
								</div>
							</div> -->
							<div class="col-12">
								<div class="form-group">
									<label for="l_obse">Observación</label>
									<textarea v-model="PedItem1.l_obse" id="l_obse" class="form-control text-uppercase mb-1" cols="30" rows="3"></textarea>
									<button @click="showMdlTeclado('PedItem1.l_obse')" class="btn btn-sm btn-primary">TECLADO</button>
								</div>
							</div>
							<div class="col-12">
								<button @click="grabar" 
										class="btn btn-primary">Grabar</button>
								<button @click="hideMdlPedidoElimItem" 
										class="btn btn-default">Cerrar</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- Dynamic Components -->
		<component :is="currentView" v-bind="currentProps" @hide-mdlteclado="hideMdlTeclado"></component>
		<!-- End -->
	</div>
</script>