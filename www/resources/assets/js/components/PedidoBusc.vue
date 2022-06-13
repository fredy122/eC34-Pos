<script>
    export default {
        template: '#tmpPedidoBusc',
        props: ['_tab'],
        data: function(){
            return{
                currentView: null,
                currentProps: null,
                l_busc: '',
                n_comp: '',
                pedidos: [],
                pedidos2: [],
                // vendedor: [],
                listPedidos3: [],
                tots: []
            }
        },
        mounted() {
            $('#mdlPedidoBusc').fullscreen();
            $('#mdlPedidoBusc').modal()
            $('#bodyMdlPedidoBusc').css('overflow','hidden')

            if (this._tab == 'l_clav') {
                $('#pills-tab a[href="#pills-c_clav"]').tab('show')
                this.tabPorClave()
            }else if (this._tab == 'l_vend') {
                $('#pills-tab a[href="#pills-l_vend"]').tab('show')
                this.listVend()
            }else if (this._tab == 'n_comp') {
                $('#pills-tab a[href="#pills-n_comp"]').tab('show')
                this.tabPorNcomp()
            }
        },
        methods: {
            hideMdlPedidosbusc(pedido){
                $('#mdlPedidoBusc').modal('hide')
                pedido.l_clav = this.l_busc
                this.$emit('hide-mdlpedidobusc',pedido);
            },
            listpedido(){
                $('body').loadingModal({'animation': 'fadingCircle'});

                this.pedidos = []

                axios.post(`/pedidos/buscpedidos`,{c_vend: this.l_busc})
                .then(function(response){

                    this.pedidos = response.data.pedidos

                    $('body').loadingModal('destroy');

                }.bind(this));
            },
            listpedido2(c_vend){
                
                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.post(`/pedidos/buscpedidos2`,{c_vend: c_vend})
                .then(function(response){

                    this.pedidos2 = response.data.pedidos
                    $('body').loadingModal('destroy');

                }.bind(this));
                
            },
            listTotPedVends(){
                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.post(`/pedidos/listtotpedvends`)
                .then(function(response){

                    this.tots = response.data.resul
                    $('body').loadingModal('destroy');

                }.bind(this));
            },
            /*devTot(c_vend){
                let $n_tota = 0

                this.tots.some((element)=>{
                    if (element.c_vendd == c_vend) {
                        $n_tota = element.n_tota
                        return true
                    }
                })

                return $n_tota
            },*/
            devPedido(l_ubic,c_ubic,l_mesa,c_mesa){
                this.hideMdlPedidosbusc({pedido:{l_ubic:l_ubic, c_ubic: c_ubic, l_mesa: l_mesa, c_mesa: c_mesa, q_ocup: 1}})
            },
            tabPorClave(){
                this.l_busc = ''
                // $('#txtPedidoBusc').focus()
                this.showMdlTeclado('l_busc','numeric')
            },
            listVend(){
                this.l_busc = ''
                this.pedidos = []
                this.pedidos2 = []
                /*if(localStorage.getItem('vendedor') == null){
                    $('body').loadingModal({'animation': 'fadingCircle'});
                    axios.post(`/vendedor/listvend2`)
                    .then(function(response){

                        this.vendedor = response.data.vendedor
                        localStorage.setItem('vendedor',JSON.stringify(response.data.vendedor))

                        $('body').loadingModal('destroy');

                    }.bind(this));
                }else{
                    this.vendedor = JSON.parse(localStorage.getItem('vendedor'))
                }*/
                this.listTotPedVends()
            },
            // Busqueda por numero de pedido
            tabPorNcomp(){
                this.l_busc = ''
                this.pedidos = []
                this.pedidos2 = []
                this.listPedidos3 = []

                this.n_comp = ''
                // $('#n_comp').focus()
                this.showMdlTeclado2('n_comp','numeric')
            },
            devPedido2(){
                $('body').loadingModal({'animation': 'fadingCircle'});
                axios.post(`/pedidos/buscpedidosxncomp`,{n_comp:this.n_comp})
                .then(function(response){

                    this.listPedidos3 = response.data.pedidos

                    $('body').loadingModal('destroy');

                }.bind(this));
            },
            showMdlTeclado2(model,tecl){
                this.currentView = 'teclado'
                this.currentProps = {
                    _model: model,
                    _type: 'text',
                    _tecl: tecl
                }
            },
            // Teclado Virtual
            showMdlTeclado(model,tecl){
                this.currentView = 'teclado'
                this.currentProps = {
                    _model: model,
                    _type: 'password',
                    _tecl: tecl
                }
            },
            hideMdlTeclado(resul){
                this.currentView = null;
                this.currentProps = null;
                
                $('#txtPedidoBusc').focus()

                if(resul.hasOwnProperty('val')){
                    if (resul.val != '' && resul.model == 'n_comp') {
                        this.n_comp = resul.val
                        this.devPedido2()
                    }else if (resul.val != '' && resul.model == 'l_busc') {
                        this.l_busc = resul.val
                        this.listpedido()
                    }else{
                        this.pedidos = []
                    }
                }
            },
            // Pre-cuenta
            preCuenta(l_ubic,c_ubic,l_mesa,c_mesa){
                let pedido = {pedido:{l_ubic:l_ubic, c_ubic: c_ubic, l_mesa: l_mesa, c_mesa: c_mesa, q_ocup: 1, q_prcue: 1}}
                pedido.l_clav = this.l_busc
                this.$emit('hide-mdlpedidobusc',pedido);
            }
        }
    }
</script>