<template>
	<div id="mdlPedidosReimpItemsEnv" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-dark text-white">
					<h6 class="modal-title">Reimpresión de Items Enviados</h6>
				</div>
				<div class="modal-body">
					<table v-if="pedido.items.length > 0" class="table mb-0 tblPeditems">
						<tbody>
							<tr v-for="(item,index) in pedido.items" @click="addItem(index)" :class="selectItems.indexOf(index) != -1 ? 'table-success' : ''" class="border">
								<td><input :value="index" v-model="selectItems" @click="addItem(index)" type="checkbox"></td>
								<td  style="vertical-align: middle;">
									<!-- Exonerado -->
									<span v-show="item.c_indi=='E' || item.c_indi==='1'" class="badge badge-pill badge-warning" title="Exonerado(No Afecto IGV)">E</span>
									<!---->
									<span v-show="item.l_obse" class="text-danger" title="Descripción">»</span>
									{{ item.l_abre }} | {{ item.l_prod }}
								</td>
								<td  style="vertical-align: middle;">
									<span class="badge badge-pill badge-dark">{{ parseFloat(item.s_cant) }}</span>
								</td>
								<td  style="vertical-align: middle;" class="text-right">{{ parseFloat(item.s_vent) }}</td>
								<td  style="vertical-align: middle;" class="text-right">{{ parseFloat(item.s_bimp) }}</td>

								<td style="vertical-align: middle;" class="text-right">
									<h5><span class="badge badge-info">Enviado</span></h5>
								</td>
							</tr>
						</tbody>
					</table>
					<div v-else class="alert alert-warning" role="alert">
					  No hay items para imprimir.
					</div>
				</div>
				<div class="modal-footer">
					<button @click="imprimirItems()" type="button" class="btn btn-primary">Imprimir</button>
					<button @click="hideMdlPedidosReimpItemsEnv()" type="button" class="btn btn-secondary">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

import {saveAs} from 'file-saver';
	
export default{
	props: ['peditems','n_comp','c_ubic','l_ubic','c_mesa','l_mesa','n_pers','l_vend'],
	data(){
		return {
			selectItems: [],
			pedido: {
				n_comp: this.n_comp,
				c_ubic: this.c_ubic,
				c_mesa: this.c_mesa,
				items: this.peditems,
                n_pers: this.n_pers,
                l_vend: this.l_vend
			}
		}
	},
	mounted(){
		// console.log(this.peditems);
		$('#mdlPedidosReimpItemsEnv').modal('show');

	},
	methods: {
		addItem: function(index){
			if (this.selectItems.indexOf(index) == -1) {
				this.selectItems.push(index);
			}else{
				this.selectItems.splice(this.selectItems.indexOf(index), 1);
			}
		},
		imprimirItems: function(){
			if(this.selectItems.length == 0){
				swal({
                    title: 'Mensaje',
                    text: "Debe seleccionar items que desea imprimir",
                    type: 'info',
                });
			}else{
				swal({
                    title: '',
                    text: "¿ Desea reimprimir items enviados ?",
                    type: 'question',
                    showCancelButton: true,
                }).then(function () {
                	
                	var peditems =  this.pedido.items.filter(function(item,index){
                		return this.selectItems.indexOf(index) != -1;
                	}.bind(this))

                	this.impresion(peditems);

                }.bind(this), function (dismiss) {
                    if (dismiss === 'cancel') {
                    }
                }.bind(this))
			}
		},
		impresion: function(peditems){
			var CombosDet = JSON.parse(localStorage.getItem('CombosDet')),
				ProductosXMediprods = JSON.parse(localStorage.getItem('ProductosXMediprods')),
				LineProds = JSON.parse(localStorage.getItem('LineProds'));

			// Buscamos productos que pertenecen a combos y buscamos el codigo de linea
            var combodet = [],
                items = [];
            for (let item of peditems) {
                if (item.c_comb.length > 0) {
                    for (let producto of CombosDet) {
                        if (producto.c_comb == item.c_comb) {
                            var item_comb = JSON.parse(JSON.stringify(item));
                            item_comb.c_prod = producto.c_prod;
                            item_comb.k_medi = producto.k_medi;
                            item_comb.s_cant = parseFloat(producto.s_cant) * parseFloat(item_comb.s_cant);
                            item_comb.s_vent = producto.s_vent;
                            combodet.push(item_comb);
                        }
                    }
                }else{
                    items.push(item);
                }
            }

            combodet.map(function(item,index){
                for (let prod of ProductosXMediprods) {
                    if (prod.c_prod == item.c_prod && prod.k_medi == item.k_medi) {
                        item.c_line = prod.c_line;
                        item.l_prod = prod.l_prod;
                        item.l_impr1 = prod.l_impr1
                        item.l_impr2 = prod.l_impr2
                        break;
                    }
                }                            
            }.bind(this))

            var combodet_items = combodet.concat(items);

            // Agrupamos los items del pedido por nombre de impresora
            var imprimir = {};
            let prods_impr = [] // Productos con impresora independiente
            combodet_items.map(function(item,index){
                if(item.l_impr1 == undefined || item.l_impr1.trim().length == 0){
                    for(let LineProd of LineProds){
                        if( item.q_coci=='1'){
                            if (LineProd.c_line == item.c_line) {

                                if (LineProd.l_impr in imprimir == false) {
                                    imprimir[`${LineProd.l_impr}`] = [];
                                }

                                imprimir[LineProd.l_impr].push(item);
                                break;
                            }
                        }
                    }
                }else if(item.q_coci=='1'){
                    // Productos con impresora independiente
                    if(item.l_impr1!=undefined && item.l_impr1.trim().length>0){
                        if (item.l_impr1.trim() in imprimir == false) {
                            imprimir[`${item.l_impr1.trim()}`] = [];
                        }

                        imprimir[item.l_impr1.trim()].push(item);
                    }
                    if(item.l_impr2!=undefined && item.l_impr2.trim().length>0){
                        if (item.l_impr2.trim() in imprimir == false) {
                            imprimir[`${item.l_impr2.trim()}`] = [];
                        }

                        imprimir[item.l_impr2.trim()].push(item);
                    }
                    // prods_impr.push(item)
                }
            }.bind(this));

            // Generamos el formato para el archivo de texto
            var n_comp = 'Pedido N: ' + parseInt(String(this.pedido.n_comp).substring(6,10)),
                c_ubic = this.pedido.c_ubic,
                c_mesa = this.pedido.c_mesa,
                n_pers = this.pedido.n_pers;

            var l_vend = localStorage.getItem('l_vend'),
                fecha = new Date().toLocaleString();

            var strPeditems = '';
            var contador_imprimir = 0;
            for (var key in imprimir) {
                if (contador_imprimir == 0) {
                    strPeditems += `${key}\r\n===================================\r\n||         [Reimpresion]         ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.l_ubic}) ${c_ubic} Mesa:(${this.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                }else{
                    strPeditems += `\r\nCORTAR\r\n${key}\r\n===================================\r\n||         [Reimpresion]         ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.l_ubic}) ${c_ubic} Mesa:(${this.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                }

                for (var item of imprimir[key]) {
                    var s_cant = (parseFloat(item.s_cant)).toString().padStart(4,' ');

                        let l_prod1 = item.l_prod.match(/.{1,30}/g)
                        l_prod1.forEach((element, index)=>{
                            strPeditems += index == 0 ? `${s_cant} ${element.padEnd(30,' ')}\r\n` : `     ${element.padEnd(30,' ')}\r\n`
                        })

                        // Si hay observacion se agregara el texto
                        if(item.l_obse.length > 0){
                            var l_obse = item.l_obse.toLowerCase().match(/.{1,28}/g).join('\r\n     ');
                            strPeditems += `     ->${l_obse}\r\n`;
                        }
                }

                strPeditems += `===================================\r\n||         [Reimpresion]         ||\r\n===================================\r\n\r\n\r\n\r\n\r\n\r\n`;
                contador_imprimir++;
            }

            // Generamos formato de impresion para productos con impresora independiente
            console.log(prods_impr)
            prods_impr.forEach((item)=>{
                let str_head = '',
                    str_head2 = '',
                    str_footer = ''

                // Head
                if (contador_imprimir == 0) {
                    str_head += `${item.l_impr1}\r\n===================================\r\n||         [Reimpresion]         ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.l_ubic}) ${c_ubic} Mesa:(${this.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;

                }else{
                    str_head += `\r\nCORTAR\r\n${item.l_impr1}\r\n===================================\r\n||         [Reimpresion]         ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.l_ubic}) ${c_ubic} Mesa:(${this.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                }
                
                str_head2 += `\r\nCORTAR\r\n${item.l_impr2}\r\n===================================\r\n||         [Reimpresion]         ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.l_ubic}) ${c_ubic} Mesa:(${this.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                // 
                
                var s_cant = (parseFloat(item.s_cant)).toString().padStart(4,' ');

                    let l_prod1 = item.l_prod.match(/.{1,30}/g)
                    l_prod1.forEach((element, index)=>{
                        str_footer += index == 0 ? `${s_cant} ${element.padEnd(30,' ')}\r\n` : `     ${element.padEnd(30,' ')}\r\n`
                    })

                    // Si hay observacion se agregara el texto
                    if(item.l_obse.length > 0){
                        var l_obse = item.l_obse.toLowerCase().match(/.{1,28}/g).join('\r\n     ');
                        str_footer += `     ->${l_obse}\r\n`;
                    }

                str_footer += `===================================\r\n||         [Reimpresion]         ||\r\n===================================\r\n\r\n\r\n\r\n\r\n\r\n`;

                strPeditems += str_head + str_footer
                strPeditems += item.l_impr2 == undefined || item.l_impr2.length == 0 ? '' : str_head2 + str_footer
                contador_imprimir++;
            })

            // Archivo de texto
            if(strPeditems.length > 0){
                // Generamos archivo de texto
                var blob = new Blob([strPeditems], {type: "text/plain"});
                saveAs(blob, "ec34imprped.txt");
                window.location = 'ec34printicket:';
            }

            this.hideMdlPedidosReimpItemsEnv();
		},
		hideMdlPedidosReimpItemsEnv: function(){
			this.$emit('hide-mdl-pedidos-reimp-items-env');
			$('#mdlPedidosReimpItemsEnv').modal('hide');
		}
	}
}


</script>