
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import {EventBus} from './EventBus.js';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


// Agentes
Vue.component('agente-busc', require('./components/Agente/BuscAgente.vue'));
Vue.component('agente-reg-rapid', require('./components/Agente/RegRapidClien.vue'));
//
Vue.component('stock-pedidos', require('./components/Stock/Pedidos.vue'));
Vue.component('stock-pedidos-reimp-items-env', require('./components/Stock/PedidosReimpItemsEnv.vue'));
Vue.component('pedidos-num-pers', require('./components/Stock/PedidosNumPers.vue'));

Vue.component('pedidos-edithubicacion', require('./components/Stock/Hubicacion/EditHubicacion.vue'));
Vue.component('info-pedido', require('./components/Stock/InfoPedido.vue'));
Vue.component('acceso', require('./components/Acceso/Acceso.vue'));

// Pedido Mover Items
Vue.component('pedido-moveritems', require('./components/PedidoMoverItems.vue'));

// Teclado Virtual
Vue.component('teclado', require('./components/Teclado.vue'));

// Eliminar Item
Vue.component('pedido-elimitem', require('./components/PedidoElimItem.vue'));

// Buscar Pedido
Vue.component('pedido-busc', require('./components/PedidoBusc.vue'));

// Modal pedido agrupado
Vue.component('pedido-agrup', require('./components/Stock/PedidoAgrup.vue'));
Vue.component('pedido-agrup-item', require('./components/Stock/PedidoAgrupItem.vue'));
Vue.component('pedido-agrup-item-edic', require('./components/Stock/PedidoAgrupItemEdic.vue'));

Vue.component('CAMBMOZOPRINC', require('./components/CambMozoPrinc.vue'));

const app = new Vue({
    el: '#app',
    data: {
        currentView: null,
        currentProps: null,
    	Usuario: {
    		Usuario : '',
            l_vend: '',
            c_vend: '',
            k_tipp: '',
            p_pudefecto: 0,
            q_vera: 0,
    	},
        sisprop: {
            ped_gencomp: ''
        },
        vendedor: {
            c_vend: ''
        },
        org: {
            k_empr: ''
        },
        pedido: {
            c_ubic: '',
            c_mesa: ''
        },
        Apertcaja: JSON.parse(localStorage.getItem('Apertcaja')) || {},
        // Buscar Producto
        l_buscProd: ''
    },
    mounted() {
        // Para multiples modals bootstrap
        $('.modal').on('hidden.bs.modal',function(){
            if($('.modal').is('.show')){
                $('body').addClass('modal-open');
                $(window).resize(); // for long modal and backdrop
            }
        });
        //
        if (JSON.parse(localStorage.getItem('q_tactil')) == true) {
            $('input[type="text"],input[type="tel"],input[type="password"],textarea').focus(function(){
                window.location = 'ec34tabtip:';
            })
        }
        
        
    	this.Usuario.Usuario = localStorage.getItem('Usuario');
        this.Usuario.l_vend = localStorage.getItem('l_vend');
        this.Usuario.c_vend = localStorage.getItem('c_vend');
        this.Usuario.k_tipp = localStorage.getItem('k_tipp');
        this.Usuario.p_pudefecto = localStorage.getItem('p_pudefecto');
        this.Usuario.q_vera = localStorage.getItem('q_vera');
        // Sisprop
        this.sisprop.ped_gencomp = localStorage.getItem('ped_gencomp');
        
        //organizacion
        var org = (JSON.parse(localStorage.getItem('org')));
        if( org != null ){
            this.org.k_empr = org.k_empr;
        }

        EventBus.$on('appcambinfovend',function(data){
            this.Usuario.c_vend = data.c_vend;
            this.Usuario.l_vend = data.l_vend;
            localStorage.setItem('c_vend',data.c_vend);
            localStorage.setItem('l_vend',data.l_vend);
        }.bind(this));
    },
    methods: {
        // Cambiar vendedor
        showMdlCambVend: function(params){
            $('#mdlCambVend').modal('show');
            $('#pills-tab a:first').tab('show');
            this.vendedor.c_vend = params.l_clav == undefined ? '' : params.l_clav;
            this.pedido.c_ubic = params.c_ubic
            this.pedido.c_mesa = params.c_mesa

            EventBus.$emit('showMdlCambVend',params);

            $('#c_vend').focus();

            if (params.l_clav != '' && params.l_clav != undefined) {
                $('#mdlCambVend').modal('hide')
                this.cambVend()
            }
        },
        cambVend: function(){
            $('body').loadingModal({'animation': 'fadingCircle'});

            const instance = axios.create()
            instance.post('/vendedor/cambvend', {c_vend:this.vendedor.c_vend, c_ubic: this.pedido.c_ubic, c_mesa: this.pedido.c_mesa})
            .then(function(response){

                this.Usuario.c_vend = response.data.vendedor.c_vend;
                this.Usuario.l_vend = response.data.vendedor.l_vend;
                localStorage.setItem('c_vend',response.data.vendedor.c_vend);
                localStorage.setItem('l_vend',response.data.vendedor.l_vend);

                EventBus.$emit('cambVend',response.data.pedidos);

                $('#mdlCambVend').modal('hide');

                $('body').loadingModal('destroy');

            }.bind(this))
            .catch(function(error){
                $('body').loadingModal('destroy');

                this.vendedor.c_vend = ''

                if(error.message == "Network Error"){
                    toastr.error('Error de red');
                }else if(error.response.status == 401){
                    swal({
                        title: 'No iniciaste sesión',
                        text: "Inicia sesión para continuar.",
                        type: 'error',
                        confirmButtonText: 'Iniciar sesión',
                        preConfirm: function(){
                            window.location = '/';
                        }
                    });
                }else if(error.response.status == 422){
                    let mensaje = '', 
                        name_field = '';
                    for( var key in  error.response.data.errors ){
                        mensaje = error.response.data.errors[key][0];
                        name_field = key;
                        break;
                    }

                    toastr.error(mensaje);
                    $('#'+name_field).focus();

                }else{
                    toastr.error(error.response.data.message);
                }

            }.bind(this));

        },
        cambVendCancel(){
            EventBus.$emit('cambVendCancel');
            $('#mdlCambVend').modal('hide');
        },
        // Editar mapa de Mesas
        editHubicMesas(){
            this.currentView = 'pedidos-edithubicacion';
            this.currentProps = {};
            // EventBus.$emit('editHubicMesas');
        },
        hideEdithubicacion(){
            this.currentView = null;
            this.currentProps = null;
        },
        // Buscar Producto
        buscProd(){
            if (this.l_buscProd.trim().length > 0)
                EventBus.$emit('buscProd',this.l_buscProd.toUpperCase());
        },
        // Teclado Virtual
        showMdlTeclado(model,value,_tecl,_q_edit=0){
            this.currentView = 'teclado'
            this.currentProps = {
                _model: model,
                _value: value,
                _tecl: _tecl,
                _q_edit: _q_edit
            }
        },
        hideMdlTeclado(resul){
            this.currentView = null;
            this.currentProps = null;

            if(resul.hasOwnProperty('val')){
                let val = resul.model.split(".");

                if (val.length == 1) {
                    this.$data[val[0]] = resul.val
                }
            }

            this.buscProd()
        },
    }
});