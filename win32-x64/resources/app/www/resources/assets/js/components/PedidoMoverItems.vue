<script>
    export default {
        template: '#tmpPedidoMoverItems',
        props: ['_Pedido','_PedItem','_s_totcant'],
        data: function(){
            return{
                // Dynamic Components
                currentView: null,
                currentProps: null,

                PedItem1: {
                    // s_cant: 1,
                    c_ubic: '',
                    c_mesa: '',
                },
                Ubicas: JSON.parse(localStorage.getItem('Ubicas')),
            }
        },
        computed: {
            Mesas(){
                this.PedItem1.c_mesa = ''

                let Mesas = JSON.parse(localStorage.getItem('Mesas'))
                return Mesas.filter(function(item){
                    return item.c_ubic == this.PedItem1.c_ubic;
                }.bind(this));
            }
        },
        mounted() {
            $('#mdlPedidoMoverItems2').modal()
        },
        methods: {
            hideMdlPedidoMoverItems(PedItem){
                $('#mdlPedidoMoverItems2').modal('hide')
                this.$emit('hide-mdlpedidomoveritems',PedItem);
            },
            // Cantidad
            /*sumCant(){
                this.PedItem1.s_cant = parseInt(this.PedItem1.s_cant) + 1
            },
            restCant(){
                if (this.PedItem1.s_cant > 1) {
                    this.PedItem1.s_cant = parseInt(this.PedItem1.s_cant) - 1
                }
            },*/
            resCant(i){
                if (i.s_cant1 > 0){
                    i.s_cant1 = i.s_cant1 - 1
                }
            },
            sumCant(i){
                if (i.s_cant1 < parseInt(i.s_cant)){
                    i.s_cant1 = i.s_cant1 + 1
                }
            },
            // 
            grabar(){
                let peditem = [],
                    s_totcant = 0
                /*let peditem = this._PedItem.filter((val)=>{
                    return val.s_cant1 > 0
                })*/
                this._PedItem.forEach((val)=>{
                    s_totcant += val.s_cant1

                    if (val.s_cant1 > 0)
                        peditem.push(val)
                })

                if (s_totcant == 0){
                    swal('','Cantidad de items a mover debe ser mayor a cero !!!','info')
                    return false
                }else if(this._s_totcant <= s_totcant){
                    swal('','No se puede mover, debe quedar almenos un item en pedido !!!','info')
                    return false
                }

                $('body').loadingModal({'animation': 'fadingCircle'});

                let params = Object.assign({}, {'_Pedido':this._Pedido},{'_PedItem': peditem}, {'PedItem1':this.PedItem1})
                axios.post('/pedidos/moveritem', params)
                .then(function(response){
                    $('body').loadingModal('destroy');
                    this.hideMdlPedidoMoverItems({PedItem: peditem})
                }.bind(this))

                // console.log(peditem)

                /*if (this._n_items == 1 && (parseFloat(this._PedItem.s_cant) - this.PedItem1.s_cant) <= 0 ) {
                    swal('','No se puede mover, debe quedar almenos un item en pedido !!!','info')
                    return false
                }

                $('body').loadingModal({'animation': 'fadingCircle'});

                let params = Object.assign({}, {'_Pedido':this._Pedido},{'_PedItem':this._PedItem}, {'PedItem1':this.PedItem1})
                axios.post('/pedidos/moveritem', params)
                .then(function(response){
                    $('body').loadingModal('destroy');

                    this.hideMdlPedidoMoverItems({PedItem: response.data.PedItem})
                }.bind(this))*/
            },
            // Teclado Virtual
            /*showMdlTeclado(model,_tecl){
                this.currentView = 'teclado'
                this.currentProps = {
                    _model: model,
                    _tecl: _tecl
                }
            },
            hideMdlTeclado(resul){
                this.currentView = null;
                this.currentProps = null;

                if (resul.model == 'PedItem1.s_cant') {
                    resul.val = isNaN(resul.val) || (resul.val).trim() == "" ? '0.00' : parseFloat(resul.val).toFixed(2)
                }

                if(resul.hasOwnProperty('val')){
                    let val = resul.model.split(".");
                    this.$data[val[0]][val[1]] = resul.val
                }
            }*/
        }
    }
</script>