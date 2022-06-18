<script>
    export default {
        template: '#tmpPedidoAgrupItemEdic',
        props: ['_prod'],
        data: function(){
            return{
                activeTab: 'pills-s_cant',
                prod:  Vue.util.extend({}, this._prod),
                stock: 0
            }
        },
        mounted(){
            // this.prod.s_vent = parseFloat(this.prod.s_vent).toFixed(2)
            this.prod.s_cant = ''
            this.prod.s_vent = ''

            // Buscamos stock
            axios.post('/prodstock/devprodstock', {c_prod: this.prod.c_prod})
            .then(function(response){
                if (response.data.stock.n_stok != null) {
                    this.stock = parseFloat(response.data.stock.n_stok)
                }
            }.bind(this))
            // 

            $('#mdlPedidoAgrupItemEdic').modal()

            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                this.activeTab = $(e.target).attr("aria-controls")
            }.bind(this))
        },
        methods:{
            hide(resp){
                if(!$.isEmptyObject(resp)){
                    let s_cant = this.prod.s_cant == '' ? parseInt(this._prod.s_cant) : parseInt(this.prod.s_cant),
                        s_vent = this.prod.s_vent == '' ? parseFloat(this._prod.s_vent) : parseFloat(this.prod.s_vent)

                    if (!$.isNumeric(s_cant)) {
                        swal('','Cantidad invalido, revise por favor...','info')
                        return false
                    }else if (!$.isNumeric(s_vent)) {
                        swal('','Precio Venta invalido, revise por favor...','info')
                        return false
                    }

                    this.prod.s_cant = s_cant
                    this.prod.s_vent = s_vent
                }

                $('#mdlPedidoAgrupItemEdic').modal('hide')
                this.$emit('hide-mdlpedidoagrupitemedic', resp);
            },
            setValue(val){
                if(this.activeTab == 'pills-s_cant'){
                    if (val == '.') {
                        return false
                    }
                    this.prod.s_cant = val == '' ? '' : this.prod.s_cant + val
                }else if(this.activeTab == 'pills-s_vent'){
                    this.prod.s_vent = val == '' ? '' : this.prod.s_vent + val
                }

            }
        }
    }
</script>