<script type="text/template" id="tmpPedidoAgrupItem">
	<tr>
		<th v-text="prod.c_prod" class="py-1 px-1"></th>
		<td v-text="prod.l_seri" class="py-1 px-1"></td>
		<th v-for="talla of Object.keys(this.tallas)" class="py-1 px-1">
			<span v-if="prod.tallas[talla] != undefined">
				<input v-model="prod.tallas[talla].s_cant"
						@change="prod.tallas[talla].q_grab = 0" 
						@focus="edit(prod.tallas[talla])"
						type="text" class="form-control form-control-sm text-center" readonly>
				<div class="d-flex justify-content-between mt-1">
					<small style="font-size: 9px" class="badge badge-dark" v-text="'P.V: '+parseFloat(prod.tallas[talla].s_vent).toFixed(2)"></small>
					<small style="font-size: 9px" class="badge badge-dark" v-text="'S: '+(prod.tallas[talla].n_stok==undefined?'0.0000':parseFloat(prod.tallas[talla].n_stok).toFixed(4))"></small>
				</div>
			</span>
		</th>
		<td v-text="tots.s_tcant"
			class="text-center py-1 px-1"></td>
		<td v-text="tots.s_tota.toFixed(2)" 
			class="text-right py-1 px-1"></td>

		<!-- Dynamic Components -->
		<component :is="currentView" v-bind="currentProps"
					@hide-mdlpedidoagrupitemedic="hideMdlPedidoAgrupItemEdic"></component>
	</tr>
</script>