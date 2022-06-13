<script>
    import {EventBus} from '../../EventBus.js';
    export default {
        template: '#tmpPedidoAgrupItem',
        props: ['_prod','_tallas'],
        data: function(){
            return{
                currentView: null,
                currentProps: null,
                prod: this._prod,
                tallas: [],
                talla: [],
                Usuario: {
                    q_vera: localStorage.getItem("q_vera"),
                },
            }
        },
        computed:{
            tots(){

                let s_tcant = 0, s_tota = 0
                this.prod.tallas.forEach((val)=>{
                    s_tcant += parseInt(val.s_cant)
                    s_tota += val.s_cant * parseFloat(val.s_vent)
                })

                return {s_tcant: s_tcant, s_tota: s_tota}
            }
        },
        mounted(){
            this.tallas = this._tallas
            EventBus.$on('acttallas',function(data){
                this.tallas = []
                this.tallas = data
            }.bind(this));
        },
        methods:{
            edit(prod){
                if (this.Usuario.q_vera==1) {
                    return false
                }

                this.currentView = 'pedido-agrup-item-edic'
                this.currentProps = {
                    _prod: prod
                }
            },
            hideMdlPedidoAgrupItemEdic(prod){
                if(Object.keys(prod).length > 0){
                    let talla = (prod.c_prod.split("-"))[2]

                    this.prod.tallas[talla].s_cant = prod.s_cant
                    this.prod.tallas[talla].s_vent = prod.s_vent
                    this.prod.tallas[talla].q_grab = 0
                }
                this.currentView = null
                this.currentProps = null
            }
        }
    }
</script>