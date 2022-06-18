<script>
    import {EventBus} from '../../EventBus.js';
    export default {
        template: '#tmpPedidoAgrup',
        props: ['_Pedido','_PedItem'],
        data: function(){
            return{
                currentView: null,
                currentProps: null,
                NumberFormat2:new Intl.NumberFormat('en-US', {minimumFractionDigits: 2,maximumFractionDigits:2}),
                NumberFormat0:new Intl.NumberFormat('en-US', {minimumFractionDigits: 0,maximumFractionDigits:0}),
                l_busc: '',
                ProductosXMediprods: JSON.parse(localStorage.getItem('ProductosXMediprods')),
                prods: [],
                tallas: [],
                Pedido: this._Pedido,
                PedItem: this._PedItem,
                pdf: '',
                l_email: "",
                Usuario: {
                    q_vera: localStorage.getItem("q_vera"),
                },
                renderComponent: true,
                /*item: {
                    c_comb:"",
                    c_indi:"",
                    c_line:"",
                    c_prod:"",
                    c_vend:"",
                    k_medi:"",
                    l_abre:"",
                    l_impr1:"",
                    l_impr2:"",
                    l_obse:"",
                    l_prod:"",
                    l_vend:"",
                    n_item: "",
                    n_prec: "0",
                    q_coci: "0",
                    q_envic: "0",
                    q_grab: "0",
                    q_preparado: "0",
                    q_store: "0",
                    s_bimp: 0,
                    s_cant: 0,
                    s_pre1: 0,
                    s_pre2: 0,
                    s_pre3: 0,
                    s_pre4: 0,
                    s_pre5: 0,
                    s_vent: 0,
                }*/
            }
        },
        computed:{
            total(){
                let s_tota = 0,
                    n_pares = 0

                this.prods.forEach((val)=>{
                    val.tallas.forEach((val)=>{
                        s_tota += val.s_cant * parseFloat(val.s_vent)
                        n_pares += parseFloat(val.s_cant)
                    })
                })

                return {s_tota:s_tota,n_pares:n_pares}
            }
        },
        mounted() {
            // Cuando editamos un pedido creamos tabla
            // -- Agrupamos codigo de productos
            let prods = []
            this.PedItem.forEach((val)=>{
                let c_prod = (val.c_prod.split("-"))[0] // val.c_prod.substr(0,4)
                if (prods.indexOf(c_prod) == -1){
                    prods.push(c_prod)
                }
            })
            
            // -- Agregamos productos a tabla
            prods.forEach((val)=>{
                this.l_busc = val
                let q_edit = 0
                this.buscProds(q_edit)
            })

            // -- Agregamos cantidad a tallas
            this.PedItem.forEach((peditem)=>{
                let prod = peditem.c_prod.split("-"),
                    c_prod = prod[0],
                    serie = prod[1],
                    talla = prod[2]

                
                this.prods.forEach((prod,i)=>{
                    if (prod.c_prod==c_prod && prod.l_seri==serie) {
                        this.prods[i].q_edit = 1;
                        this.prods[i].tallas[talla].s_cant = parseFloat(peditem.s_cant)
                        this.prods[i].tallas[talla].s_vent = parseFloat(peditem.s_vent)
                        this.prods[i].tallas[talla].q_grab = 1
                        this.prods[i].tallas[talla].q_store = 1
                        this.prods[i].tallas[talla].n_stok = parseFloat(peditem.n_stok)
                    }
                })
            })
            //

            $('#mdlPedidoAgrup').modal()
            $('#l_busc').focus()
        },
        methods: {
            detectmob() { 
                return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
            },
            hideMdlPedidoAgrup(){
                this.PedItem = []
                let tot_q_grab = 0

                this.prods.forEach((prod)=>{
                    prod.tallas.forEach((talla)=>{
                        if (talla.s_cant > 0) {
                            talla.s_bimp = talla.s_cant * talla.s_vent
                            this.PedItem.push(talla)
                        }
                        if (talla.q_grab == 0) {
                            tot_q_grab += 1
                        }
                    })
                })

                if(this.Pedido.n_comp.length == 0){
                    this.PedItem = []
                }else if (tot_q_grab > 0) {
                    swal(  '', 'Debe grabar cambios para cerrar...!!!', 'info')
                    return false;
                }

                // Cerramos modal
                $('#mdlPedidoAgrup').modal('hide')
                this.$emit('hide-mdlpedidoagrup',this.PedItem);
            },
            async buscProds(q_edit){
                // Detenemos el renderizado del componente pedido-agrup-item
                this.renderComponent = false

                // Buscamos producto
                let prod_exist = false
                this.l_busc = this.l_busc.toUpperCase()

                let c_prods = [],
                    listStok = []
                
                this.prods.forEach((prod)=>{
                    if (prod.c_prod == this.l_busc) {
                        prod_exist = true

                        prod.q_edit = 1;
                        
                        prod.tallas.forEach((val)=>{
                            c_prods.push(val.c_prod)
                        })
                    }
                })

                if (prod_exist == true) {
                    if (c_prods.length>0) {
                        // Actualizamos stok
                        await axios.post('/pedidos/devprodstock1',{c_prods})
                        .then((resp)=>{
                            listStok = resp.data.resul

                            this.prods.forEach((prod)=>{
                                if (prod.c_prod == this.l_busc) {                                    
                                    prod.tallas.forEach((val,item)=>{
                                        listStok.forEach((stok)=>{
                                            if (val.c_prod.trim()==stok.c_prod.trim()) {
                                                val.n_stok = stok.n_stok
                                                prod.tallas.splice(item,1,val)
                                            }
                                        })
                                    })
                                }
                            })
                        })
                    }

                    this.$nextTick().then(() => {
                        // Vuelve a renderizar componente pedido-agrup-item
                        this.renderComponent = true
                    });

                    this.l_busc = ''
                    toastr.info('Producto ya se encuentra en listado, se mostraran series ocultas.')
                    return false
                }

                let prods = this.ProductosXMediprods.filter((prod)=>{
                    let c_prod = prod.c_prod.split("-")
                    if (c_prod.length == 3) {
                        return c_prod[0] == this.l_busc
                    }
                })
                // return

                if(prods.length == 0){
                    this.$nextTick().then(() => {
                        // Vuelve a renderizar componente pedido-agrup-item
                        this.renderComponent = true
                    });
                    
                    this.l_busc = ''
                    toastr.error('No se encontro producto(s)')
                    return false
                }

                let series = []
                prods.forEach((prod)=>{
                    let c_prod = prod.c_prod.split("-")
                    let serie = c_prod[1],
                        talla = ""+c_prod[2]+""

                    c_prods.push(prod.c_prod)

                    let peditem = {
                        c_prod: prod.c_prod,
                        l_prod: prod.l_prod,
                        k_medi: prod.k_medi,
                        l_abre: prod.l_abre,
                        s_cant: 0,
                        s_vent: prod.s_pre1,
                        n_prec: 1,
                        c_indi: '',
                        q_coci: 0,
                        q_envic: 0,
                        q_grab: 1,
                        q_preparado: 0,
                        q_store: 1,
                        l_obse: '',
                        c_comb: '',
                        n_item: 0,
                    }

                    if(series[serie] == undefined){
                        series[serie] = []
                        series[serie][talla] = peditem
                    }else{
                        series[serie][talla] = peditem
                    }

                    // Tallas Para todos los productos
                    this.tallas[talla] = 0
                })

                if(q_edit==1){
                    // Traemos stok
                    await axios.post('/pedidos/devprodstock1',{c_prods})
                    .then((resp)=>{
                        listStok = resp.data.resul
                    })
                }                    

                // Agregamos item a tabla
                let prods2 = []
                Object.keys(series).forEach((val)=>{
                    // Actualizamos stok
                    if(q_edit==1){
                        series[val].forEach((val1)=>{
                            listStok.forEach((stok)=>{
                                if (val1.c_prod.trim()==stok.c_prod.trim()) {
                                    val1.n_stok = stok.n_stok
                                }
                            })
                        })
                    }

                    if(q_edit==1){
                        // Al buscar nuevo producto agregamos a otro array para despues insertarlo al inicio del grid
                        prods2.push({
                            c_prod: this.l_busc,
                            l_seri: val,
                            q_edit: q_edit,
                            tallas: series[val]
                        })
                    }else{
                        this.prods.push({
                            c_prod: this.l_busc,
                            l_seri: val,
                            q_edit: q_edit,
                            tallas: series[val]
                        })
                    }
                })
                if(q_edit==1){
                    // Agregamos producto buscado al inicio del grid, solo cuando agregamos un nuevo producto
                    this.prods = prods2.concat(this.prods)
                }

                // Limpiamos texto busqueda
                this.l_busc = ''

                this.$nextTick().then(() => {
                    // Vuelve a renderizar componente pedido-agrup-item
                    this.renderComponent = true

                    // Para que se actualice tallas en los items
                    EventBus.$emit('acttallas',this.tallas);
                });

            },
            grabar(){
                this.Pedido.s_icbper = 0
                if( this.prods.length == 0 ){
                    toastr.info('No se puede grabar con 0 items',)
                }else if(this.Pedido.n_comp.length == 0){
                    // Grabamos nuevo pedido
                    this.Pedido.s_tota = this.total.s_tota
                    axios.post('/pedidos/grabarpedido',{pedido: this.Pedido, peditem: this.prods})
                    .then(function(response){
                        this.Pedido.n_comp = response.data.n_comp

                        this.$parent.Pedidos.unshift({
                            c_mesa: "",
                            c_ubic: "01",
                            d_anul: "0",
                            n_comp: response.data.n_comp,
                            q_pago: "0",
                        })

                        localStorage.setItem('Pedidos',JSON.stringify(this.$parent.Pedidos));

                        this.prods.forEach((prod,i)=>{
                            prod.tallas.forEach((talla)=>{
                                talla.q_grab = 1
                                talla.q_store = 1
                            })
                        })

                        toastr.success('Pedido se grabo correctamente','',{"positionClass": "toast-top-left"})
                    }.bind(this))
                }else if(this.Pedido.n_comp.length != 0){
                    // Actualizamos pedido
                    this.Pedido.s_tota = this.total.s_tota
                    axios.post('/pedidos/actpedido',{pedido: this.Pedido, peditem: this.prods})
                    .then(function(response){

                        if(response.data.info == 'q_pago'){
                            swal('',"No se puede grabar, Pedido ha sido pagado !!!",'info')
                        }
                        else if(response.data.info == 'd_anul'){
                            swal('',"No se puede grabar, Pedido fue anulado !!!",'info')
                        }
                        else{                        
                            this.prods.forEach((prod,i)=>{
                                prod.tallas.forEach((talla)=>{
                                    talla.q_grab = 1
                                    talla.q_store = 1
                                })
                            })
                            toastr.success('Pedido se actualizo correctamente','',{"positionClass": "toast-top-left"})
                        }
                    }.bind(this))
                }
            },
            // Opciones de Impresion
            imprimir(){
                let tot_q_grab = 0
                this.PedItem = []


                this.prods.forEach((prod)=>{
                    prod.tallas.forEach((talla)=>{
                        if (talla.s_cant > 0) {
                            talla.s_bimp = talla.s_cant * talla.s_vent
                            this.PedItem.push(talla)
                        }
                        if (talla.q_grab == 0) {
                            tot_q_grab += 1
                        }
                    })
                })

                if (this.PedItem.length == 0) {
                    swal(  '', 'No se puede imprimir con 0 items...!!!', 'info')
                    return false;
                }
                else if(tot_q_grab > 0 || this.Pedido.n_comp.substr(6,4) == "") {
                    swal(  '', 'Debe grabar cambios para imprimir...!!!', 'info')
                    return false;
                }

                axios.post('/pedidos/imprpedagrup', {pedido: this.Pedido, peditem: this.prods,tallas: this.tallas})
                .then(function(response){
                    this.pdf = response.data.pdf
                    $('#mdlPrev').modal()
                }.bind(this))
            },
            descargar(){
                let href = 'data:application/pdf;base64,'+this.pdf
                let link = document.createElement('a')
                link.href = href
                link.download = 'Pedido-'+this.Pedido.n_comp.substring(6,10)+'.pdf'
                link.click()
            },
            envEmail(){
                axios.post('/pedidos/envemail', {l_email:this.l_email,l_pdf:this.pdf,pedido: this.Pedido})
                .then((response)=>{
                    swal(  '', 'Correo electrónico enviado correctamente.', 'success')
                })
            },
            // 
            showMdlTeclado(model,value){
                this.currentView = 'teclado'
                this.currentProps = {
                    _model: model,
                    _value: value
                }
            },
            hideMdlTeclado(resul){
                this.currentView = null
                this.currentProps = null
                if(resul.hasOwnProperty('val')){
                    let val = resul.model.split(".");

                    if (val.length == 1) {
                        this.$data[val[0]] = resul.val
                        this.buscProds()
                    }
                }
            },
            // Info
            info(){
                this.currentView = 'info-pedido';
                this.currentProps = {
                    adicional: {
                        l_obse: this.Pedido.l_obse,
                        n_pers: this.Pedido.n_pers,
                        ad_plac: this.Pedido.ad_plac
                    },
                    compr: {
                        c_comp: this.Pedido.c_compgen, 
                        c_docu: this.Pedido.c_docu,
                        n_docu: this.Pedido.n_docu, 
                        l_agen: this.Pedido.l_agen, 
                        l_dire: this.Pedido.l_dire,
                        k_page: this.Pedido.k_page
                    } 
                };
            },
            aceptaMdlInfoPedido: function(data){
                this.currentView = null;
                this.currentProps = null;

                this.Pedido.c_compgen = data.c_comp;
                this.Pedido.n_docu = data.n_docu;
                this.Pedido.l_agen = data.l_agen;
                this.Pedido.c_docu = data.c_docu;
                this.Pedido.l_dire = data.l_dire;
                this.Pedido.k_page = data.k_page;
                this.Pedido.l_obse = data.l_obse;
                this.Pedido.n_pers = data.n_pers;
                this.Pedido.ad_plac = data.ad_plac;
            },
            hideMdlInfoPedido: function(){
                this.currentView = null;
                this.currentProps = null;
            },
            // Marcar pedido como atendido
            atendido(){
                swal({
                    title: '',
                    text: '¿Seguro(a) de Marcar Pedido Como Atendido?',
                    type: 'question',
                    showCancelButton: true,
                }).then(()=>{
                    axios.post('/pedidos/modpedaten',{n_comp:this.Pedido.n_comp})
                    .then((resp)=>{
                        this.Pedido.q_aten = 1                        
                    })
                });
            }
        },
    }
</script>