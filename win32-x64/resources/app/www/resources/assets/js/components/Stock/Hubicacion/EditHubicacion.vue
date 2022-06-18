<template>
	<div>
		<!-- Hubicacion de salas -->
		<div id="edithubicacion" class="modal modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-info text-white">
						<h6 class="modal-title">{{ titulo }} - Elige Sala</h6>
					</div>
					<div class="modal-body">
						<!-- <label v-for="Ubica in Ubicas" class="custom-control custom-radio mb-3 h5">
							<input v-model="c_ubic" v-bind:value="Ubica.c_ubic" id="radioStacked3" name="radio-stacked-1" type="radio" class="custom-control-input">
							<span class="custom-control-indicator"></span>
							<span class="custom-control-description">{{ Ubica.l_ubic }}</span>
						</label> -->
						<div v-for="Ubica in Ubicas"
							 @click="c_ubic = Ubica.c_ubic; l_ubic = Ubica.l_ubic"
							 :class="c_ubic == Ubica.c_ubic ? 'bg-success text-white' : 'bg-light text-dark'"
							 class="card mr-3 mb-3 d-inline-block">
							<div class="card-body">
								<h6 class="card-title" style="margin: 0">{{ Ubica.l_ubic }}</h6>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button @click="mdlSalaNext()" type="button" class="btn btn-primary">Siguiente »</button>
						<button @click="hideEdithubicacion()" type="button" class="btn btn-secondary">Cancelar</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Edicion de hubicacion de mesas -->
		<div id="edithubicacionmesa" class="modal modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-info text-white">
						<h6 class="modal-title">{{ titulo }} - SALA {{ c_ubic }} » Elige Mesa</h6>
					</div>
					<div class="modal-body">
						<div id="editMesaDraggableContainer" style="width: 100%;height: 100%;overflow: scroll;">

							<div @click="eligeMesa(Mesa.c_mesa,Mesa.l_mesa,Mesa.q_ocup)" v-for="Mesa in filterMesas" :id="'edit'+Mesa.c_ubic+''+Mesa.c_mesa" class="card editmessaDraggable" style="width: 100px;white-space:nowrap;" :class="c_mesa == Mesa.c_mesa && Mesa.q_ocup == '0' ? 'bg-success' : 'bg-light'">
							  <div class="card-body" :class="[accion != undefined && Mesa.q_ocup == '1' ? 'bg-danger' : '', accion != undefined && Mesa.q_ocup == '2' ? 'bg-info' : '']">
							    <h6 class="card-title" style="margin: 0">{{ Mesa.l_mesa }}</h6>
							  </div>
							</div>

						</div>
					</div>
					<div class="modal-footer">
						<button @click="grabaHubicMesas(filterMesas)" type="button" class="btn btn-primary">Grabar</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">« Cancelar</button>
					</div>
				</div>
			</div>
		</div>		
	</div>
</template>

<script>
	// import {EventBus} from '../../../EventBus.js';
	export default{
		props: ['accion','n_comp','pedc_ubic','pedc_mesa'],
		data: function(){
			return {
				c_ubic: '',
				l_ubic: '',
				c_mesa: null,
				l_mesa: '',
				Ubicas : [],
                Mesas : []
			}
		},
		computed: {
			filterMesas: function(){
                return this.Mesas.filter(function(mesa){
                    return mesa.c_ubic.includes(this.c_ubic);
                }.bind(this));
            },
            titulo: function(){
            	if (this.accion == undefined) {
            		return 'Edición de Ubicación';
            	}else if (this.accion == 'cambmesa'){
            		return `Cambio de mesa[Sala ${this.pedc_ubic} » Mesa ${this.pedc_mesa}]`;
            	}
            }
		},
		mounted(){
			// modal-fullscreen
			$('#edithubicacion').fullscreen();
			// modal-fullscreen
			$('#edithubicacionmesa').fullscreen();
			//editHubicMesas
            // EventBus.$on('editHubicMesas',function(){
            	this.Ubicas = JSON.parse(localStorage.getItem('Ubicas'));
                this.Mesas = JSON.parse(localStorage.getItem('Mesas'));
                $('#edithubicacion').modal('show');
            // }.bind(this));
		},
		methods: {
			mdlSalaNext: function(){
				if ( this.filterMesas.length == 0 ) {
                    swal({
                        title: 'Mensaje',
                        text: 'No existen mesas para sala seleccionada!!!',
                        type: 'error',
                        // allowOutsideClick: false,
                        // allowEscapeKey: false,
                        // animation: false
                    })
                }else if(this.c_ubic.length > 0 && this.accion == undefined){
					// Volvemos items draggable
                    $( ".editmessaDraggable" ).draggable({
                        containment: '#editMesaDraggableContainer',
                        disabled: false
                    });

                    // Posicionamos las mesas en sus ejes x y y
                    this.filterMesas.map((mesa) => {
                        var element = `#edit${mesa.c_ubic}${mesa.c_mesa}`,
                            n_ejex = parseFloat(mesa.n_ejex),
                            n_ejey = parseFloat(mesa.n_ejey);
                        
                        $(element).css({'left' : n_ejex,'top': n_ejey})
                    })

					$('#edithubicacionmesa').modal('show');
				}else if(this.c_ubic.length > 0 && this.accion == 'cambmesa'){

					$('body').loadingModal({'animation': 'fadingCircle'});

                    axios.get(`/mesas/listmesasxubic/${this.c_ubic}`)
                    .then(function(response){

                        this.Mesas = response.data;

                        // Volvemos items draggable
	                    $( ".editmessaDraggable" ).draggable({
	                        containment: '#editMesaDraggableContainer',
	                        disabled: true
	                    });

	                    // Posicionamos las mesas en sus ejes x y y
	                    this.filterMesas.map((mesa) => {
	                        var element = `#edit${mesa.c_ubic}${mesa.c_mesa}`,
	                            n_ejex = parseFloat(mesa.n_ejex),
	                            n_ejey = parseFloat(mesa.n_ejey);
	                        
	                        $(element).css({'left' : n_ejex,'top': n_ejey})
	                    })

                        this.c_mesa = null;

                        // mostramos modal
                        // $('#eligeMesa').modal('show');

						$('#edithubicacionmesa').modal('show');

                        $('body').loadingModal('destroy');

                    }.bind(this))
                    .catch(function(error){
                        // Do something with response error
                    });
				}
			},
			hideEdithubicacion: function(){
				$('#edithubicacion').modal('hide');
				this.$emit('hide-edithubicacion');
			},
			grabaHubicMesas(filterMesas) {
				if (this.accion == undefined) {
	                swal({
	                    title: '',
	                    text: "¿ Grabar ubicacion de mesas ?",
	                    type: 'question',
	                    showCancelButton: true,
	                    // confirmButtonColor: '#3085d6',
	                    // cancelButtonColor: '#d33',
	                    // confirmButtonText: 'Aceptar',
	                    // cancelButtonText: 'Cancelar',
	                    // allowOutsideClick: false,
	                    // allowEscapeKey: false,
	                    // animation: false
	                }).then(function () {

	                    $( ".editmessaDraggable" ).each(function(index) {
	                        var n_ejex = parseFloat($(this).css('left')),
	                            n_ejey = parseFloat($(this).css('top'));

	                        filterMesas[index].n_ejex = n_ejex;
	                        filterMesas[index].n_ejey = n_ejey;
	                    });

	                    $('body').loadingModal({'animation': 'fadingCircle'});

	                    axios.post(`/mesas/grabahubicmesas`,{mesas:this.filterMesas})
	                    .then(function(response){

	                    	localStorage.setItem('Mesas',JSON.stringify(this.Mesas));


	                        $('#edithubicacion').modal('hide');
	                        $('#edithubicacionmesa').modal('hide');

	                        $('body').loadingModal('destroy');
	                        toastr.success('Hubicacion de mesas se actualizó correctamente.')

	                        this.$emit('hide-edithubicacion');

	                    }.bind(this))
	                    .catch(function(error){
	                        // Do something with response error
	                    });
	                    
	                }.bind(this), function (dismiss) {
	                    // dismiss can be 'cancel', 'overlay',
	                    // 'close', and 'timer'
	                    if (dismiss === 'cancel') {
	                    }
	                })
				}else if (this.accion == 'cambmesa') {

					swal({
                        title: '',
                        text: "¿ Seguro(a) de grabar cambio de Mesa ?",
                        type: 'question',
                        showCancelButton: true,
                    }).then(function () {

						$('body').loadingModal({'animation': 'fadingCircle'});

	                    axios.post(`/pedidos/cambmesa`,{n_comp: this.n_comp, c_ubic:this.c_ubic, c_mesa: this.c_mesa, pedc_ubic: this.pedc_ubic, pedc_mesa: this.pedc_mesa})
	                    .then(function(response){

	                        $('#edithubicacion').modal('hide');
	                        $('#edithubicacionmesa').modal('hide');

	                        $('body').loadingModal('destroy');
	                        toastr.success('Mesa se cambio correctamente.')

	                        this.$emit('acepta-hubicacion',{c_ubic:this.c_ubic, l_ubic:this.l_ubic, c_mesa: this.c_mesa, l_mesa: this.l_mesa});

	                    }.bind(this))
	                    .catch(function(error){
	                        // Do something with response error
	                    });

                    }.bind(this), function (dismiss) {
                        if (dismiss === 'cancel') {
                        	
                        }
                    }.bind(this))
				}
            },
            eligeMesa: function(c_mesa,l_mesa,q_ocup){
            	if (this.accion == 'cambmesa') {
	            	if (this.c_ubic == this.pedc_ubic && c_mesa == this.pedc_mesa) {
	            		// alert("Pedido actual ya se encuentra con mesa seleccionada");
	            		swal({
	                        title: 'Mensaje',
	                        text: 'Pedido actual ya se encuentra con mesa seleccionada',
	                        type: 'info',
	                    })
	            	}else if(q_ocup == "1"){
	            		// alert("Mesa se encuentra ocupada !!!");
	            		swal({
	                        title: 'Mensaje',
	                        text: 'Mesa se encuentra ocupada !!!',
	                        type: 'info',
	                    })
	            	}else if(q_ocup == "2"){
	            		// alert("Mesa se encuentra ocupada !!!");
	            		swal({
	                        title: 'Mensaje',
	                        text: 'Mesa se encuentra con cuenta dividida !!!',
	                        type: 'info',
	                    })
	            	}else{
	            		this.c_mesa = c_mesa
	            		this.l_mesa = l_mesa
	            	}            		
            	}
            }
		}
	}
</script>