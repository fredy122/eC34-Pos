<script>
    import {EventBus} from '../../EventBus.js';
    import {saveAs} from 'file-saver';

    export default {
        data: function(){
            return{
                currentView: null,
                currentProps: null,
                // Texto COMBOS
                l_comb: '',
                // Tablas
                LineProds: [],
                LineProd1: [],
                SubLineProds : [],
                ProductosXMediprods : [],
                Ubicas : [],
                Mesas : [],
                Pedidos : [],
                // SubSect: [],
                p_pudefecto: 0,
                // Pedido
                Pedido: {
                    c_compgen: '03',
                    c_docu: '00',
                    l_mesa: '',
                    c_mesa: '',
                    l_ubic: '',
                    c_ubic: '',
                    d_anul: '0',
                    l_agen: 'VENTAS AL CONTADO',
                    l_dire: '',
                    n_comp: '',
                    n_docu: '100',
                    q_pago: '0',
                    s_tota: 0,
                    k_page: '1',
                    l_obse: '',
                    n_pers: 1,
                    l_vend: '',
                    ad_plac: ''
                },
                PedItem: [],
                /*Agente: {
                    validado: true,
                    c_comp: '03',
                    n_docu: '100',
                    l_agen: 'VENTAS AL CONTADO',
                    c_docu: '00',
                    l_dire: '',
                    k_page: '1',
                    diabled_c_comp: false
                },*/
                /*registroCliente:{
                    c_docu: '',
                    n_docu: '',
                    l_agen: '',
                    l_dire: '',
                    c_rutaa: '',
                    n_celu: ''
                },*/
                /*SUNAT:{
                    base64captcha: '',
                    n_docu: '',
                    l_captcha: ''
                },*/
                // Filtros
                filterProductos: {
                    c_line: '',
                    l_line: '',
                    l_subl: ''
                },
                filteredSubLineProdsxLine: [], SubLineProd1: [],
                filteredProdsXSubLine: [],
                // Informacion de item
                infoItem: {
                    edit: true,
                    action: 'new', //new|edit
                    edit_index: 0,
                    c_prod: "",
                    k_medi: "",
                    l_abre: "",
                    l_obse: "",
                    l_prod: "",
                    q_coci: "0",
                    k_igv: "0",
                    q_envic: "0",
                    q_preparado: "0",
                    s_bimp: 0,
                    s_cant: "",
                    n_prec: 0,
                    s_vent: 0,
                    s_pre1: "",
                    s_pre2: "",
                    s_pre3: "",
                    s_pre4: "",
                    s_pre5: "",
                    c_line: "",
                    c_comb: "",
                },
                anular: false,
                //Busqueda de Producto
                txtBuscProd: '',
                agentes:[],
                Combos: [],
                CombosDet: [],
                impresorakt : '',
                org: {
                    k_empr: null
                },
                active_item: null,
                ProdObse: [],
                Apertcaja: JSON.parse(localStorage.getItem('Apertcaja')) || {},
                Prodobse1: [], // Listado para pantalla todo en uno
                q_mostcombos: false,
                SispropPDV: {
                    pq_punit: 1 // (Sisprop)Bloquear precio de venta
                },
                q_editpv: 1, // (Usuario)Bloquear Precio Venta 
                c_mesac: localStorage.getItem('c_mesac'), //Codigo mesa pedido rapido
                s_icbper: 0,
                icbper_v: parseFloat(localStorage.getItem('icbper_v')),
            }
        },
        computed:{
            s_tota: function(){
                let s_icbper = 0
                let s_tota = this.PedItem.reduce(function(s_tota,item){
                    if(item.q_icbper == 1){
                        s_icbper += parseFloat(item.s_cant) * this.icbper_v
                    }
                    return s_tota + parseFloat(item.s_bimp);
                }.bind(this),0);

                this.s_icbper = s_icbper

                return s_tota + s_icbper
            },
            n_comp_substring: function(){
                if (typeof(this.Pedido.n_comp) == 'undefined' || this.Pedido.n_comp == '') {
                    return '';
                }
                return parseInt(String(this.Pedido.n_comp).substring(6,10));
            },
            filteredProds: function(){
                if (this.txtBuscProd.length == 0) {
                    return [];
                }
                let txtBuscProd = this.txtBuscProd.trim()
                txtBuscProd = txtBuscProd.split(" ")

                return this.ProductosXMediprods.filter(function(prod){
                    const _buscs = []
                    txtBuscProd.forEach((e)=>{
                        _buscs.push(prod.l_prod.includes(e))
                    })
                    return _buscs.every((e)=>{return e===true}) || prod.c_prod.includes(this.txtBuscProd)
                    // return prod.l_prod.includes(this.txtBuscProd.toUpperCase()) || prod.c_prod.includes(this.txtBuscProd);
                }.bind(this));
            },
            filterMesas: function(){
                return this.Mesas.filter(function(mesa){
                    return mesa.c_ubic.includes(this.Pedido.c_ubic);
                }.bind(this));
            },
            ProdObseFilter(){
                return this.ProdObse.filter((obse)=>{
                    return obse.c_line == this.infoItem.c_line || obse.c_line == ""
                });
            },
        },
        mounted() {
            //modales
            //$('#comprobante').modal('show');
            //$('#regRapiClien').modal('show');
            //$('#eligeSala').modal('show');
            //$('#eligeMesa').modal('show');
            //$('#eligeProducto').modal('show');
            //$('#modificarItem').modal('show');
            //$('#mdlBuscProd').modal('show');
            //$('#mdlCambVend').modal('show');
            //$('#mdlListAgentes').modal('show');
            //$('#mdlCombos').modal('show');
            //==================================

            if (localStorage.getItem('LineProds') == null || 
                localStorage.getItem('SubLineProds') == null ||
                localStorage.getItem('ProductosXMediprods') == null ||
                localStorage.getItem('Ubicas') == null ||
                localStorage.getItem('Mesas') == null ||
                localStorage.getItem('Pedidos') == null ||
                localStorage.getItem('SubSect') == null ||
                localStorage.getItem('Combos') == null ||
                localStorage.getItem('CombosDet') == null ||
                localStorage.getItem('impresorakt') == null
                ) {
                this.datosIndex();
            }else{
                this.LineProds = JSON.parse(localStorage.getItem('LineProds'));
                this.SubLineProds = JSON.parse(localStorage.getItem('SubLineProds'));
                this.ProductosXMediprods = JSON.parse(localStorage.getItem('ProductosXMediprods'));
                this.Ubicas = JSON.parse(localStorage.getItem('Ubicas'));
                this.Mesas = JSON.parse(localStorage.getItem('Mesas'));
                this.Pedidos = JSON.parse(localStorage.getItem('Pedidos'));
                this.SubSect = JSON.parse(localStorage.getItem('SubSect'));
                this.Combos = JSON.parse(localStorage.getItem('Combos'));
                this.CombosDet = JSON.parse(localStorage.getItem('CombosDet'));
                this.impresorakt = localStorage.getItem('impresorakt');
                this.ProdObse = JSON.parse(localStorage.getItem('ProdObse')) || []

                this.selecFirstSalaFirstMesa();

                // LineProd
                this.cargLineProd1()
            }

            this.Pedido.k_page = localStorage.getItem('k_tipp') == '0' ? '3' : localStorage.getItem('k_tipp');
            this.p_pudefecto = localStorage.getItem('p_pudefecto') == null ? '0' : localStorage.getItem('p_pudefecto');

            // Comprobante por defecto
            if (localStorage.getItem('ped_gencomp').trim().length > 0) {
                this.Pedido.c_compgen = localStorage.getItem('ped_gencomp');
            }

            // Tipo de Empresa
            this.org.k_empr = (JSON.parse(localStorage.getItem('org'))).k_empr;

            // Limpiar listado de pedidos
            EventBus.$on('showMdlCambVend',function(params){
                if(params.n_comp.length == 0){
                    this.Pedidos = [];
                    this.resetPedido();
                }
            }.bind(this));
            // Settear pedidos de vendedor elegido
            this.Pedido.l_vend = localStorage.getItem('l_vend')
            EventBus.$on('cambVend',function(data){
                this.Pedidos = data;
                localStorage.setItem('Pedidos',JSON.stringify(data));
                if ( this.org.k_empr == 2 && this.Pedido.n_comp.length == 0 && localStorage.getItem('q_npers') != 0) {
                    this.currentView = 'pedidos-num-pers';
                    this.currentProps = null;

                    this.Pedido.l_vend = localStorage.getItem('l_vend')
                }else if(this.org.k_empr == 2 && this.Pedido.n_comp.length == 0){
                    this.Pedido.l_vend = localStorage.getItem('l_vend')
                }
            }.bind(this));

            // Cancelar el formulario Cambiar Venededor
            EventBus.$on('cambVendCancel',function(data){
                this.resetPedido();
                if (this.org.k_empr == '2') {
                    this.libEdicMesa();
                    $('#eligeSala').modal('show');
                    // $('#eligeMesa').modal('show');
                }
            }.bind(this));

            // Buscar Producto
            EventBus.$on('buscProd',(data)=>{
                this.txtBuscProd = data
                this.salirSubLineProd()
                $('#pills-tab a:first').tab('show');
            });

            // Mostrar Seleccion de Hubicacion al cargar aplicacion
            if (this.org.k_empr == '2') {
                $( document ).ready(function(){
                    $('#eligeSala').fullscreen();
                    $('#eligeSala').modal();
                    $('#ubicaBody').css('overflow','hidden')
                })
            }else if (this.org.k_empr == '5'){
                $('#pills-tab a[href="#pills-pedidos"]').tab('show')
            }
        },
        methods: {
            antLineProd(){
                const c_line = this.LineProd1[0].c_line
                let q_cline = false
                let LineProds = JSON.parse(JSON.stringify(this.LineProds))
                let nuevoLineProd = []
                LineProds.reverse().some((val,i)=>{
                    if (q_cline == true) {
                        nuevoLineProd.push(val)
                        if (nuevoLineProd.length == 10) {
                            return true
                        }
                    }

                    if (c_line == val.c_line) {
                        q_cline = true
                    }
                })


                if (nuevoLineProd.length == 10) {
                    this.LineProd1.splice(0,10)
                    this.LineProd1 = nuevoLineProd.reverse()
                }
            },
            sigLineProd(){
                let length = 0
                this.LineProd1.forEach((val)=>{
                    if (val.l_line != "") {
                        length++
                    }
                })

                if (length == 10) {
                    const c_line = this.LineProd1[9].c_line
                    let q_cline = false
                    
                    let nuevoLineProd = []
                    this.LineProds.some((val,i)=>{
                        if (q_cline == true) {
                            nuevoLineProd.push(val)
                            if (nuevoLineProd.length == 10) {
                                return true
                            }
                        }

                        if (c_line == val.c_line) {
                            q_cline = true
                        }
                    })

                    let n_items = nuevoLineProd.length
                    if(n_items >0 && n_items < 10){
                        const length = 10 - n_items
                        for (var i = 0; i < length; i++) {
                            nuevoLineProd.push({c_line:"--",l_impr:"",l_line:""})
                        }
                    }

                    if (nuevoLineProd.length == 10) {
                        this.LineProd1.splice(0,10)
                        this.LineProd1 = nuevoLineProd
                    }
                }
            },
            antProdobse(){
                const l_obse = this.Prodobse1[0].l_obse
                let q_lobse = false
                let ProdObseFilter = JSON.parse(JSON.stringify(this.ProdObseFilter))

                let nuevProdobse1 = []
                ProdObseFilter.reverse().some((val,i)=>{
                    if (q_lobse == true) {
                        nuevProdobse1.push(val)
                        if (nuevProdobse1.length == 4) {
                            return true
                        }
                    }

                    if (l_obse == val.l_obse) {
                        q_lobse = true
                    }
                })

                if (nuevProdobse1.length == 4) {
                    this.Prodobse1.splice(0,4)
                    this.Prodobse1 = nuevProdobse1.reverse()
                }
            },
            sigProdobse(){
                let length = 0
                this.Prodobse1.forEach((val)=>{
                    if (val.l_obse != "") {
                        length++
                    }
                })

                if (length == 4) {
                    const l_obse = this.Prodobse1[3].l_obse
                    let q_lobse = false

                    let nuevProdobse1 = []
                    this.ProdObseFilter.some((val,i)=>{
                        if (q_lobse == true) {
                            nuevProdobse1.push(val)
                            if (nuevProdobse1.length == 4) {
                                return true
                            }
                        }

                        if (l_obse == val.l_obse) {
                            q_lobse = true
                        }
                    })

                    let n_items = nuevProdobse1.length
                    if(n_items > 0 && n_items < 4){
                        const length = 4 - n_items
                        for (var i = 0; i < length; i++) {
                            nuevProdobse1.push({c_line:"",l_obse:""})
                        }
                    }

                    if (nuevProdobse1.length == 4) {
                        this.Prodobse1.splice(0,4)
                        this.Prodobse1 = nuevProdobse1
                    }
                }
            },
            antSubLineProd(){
                const l_subl = this.SubLineProd1[0].l_subl
                let q_lsubl = false
                let filteredSubLineProdsxLine = JSON.parse(JSON.stringify(this.filteredSubLineProdsxLine))

                let nuevSubLineProd1 = []
                filteredSubLineProdsxLine.reverse().some((val,i)=>{
                    if (q_lsubl == true) {
                        nuevSubLineProd1.push(val)
                        if (nuevSubLineProd1.length == 9) {
                            return true
                        }
                    }

                    if (l_subl == val.l_subl) {
                        q_lsubl = true
                    }
                })

                if (nuevSubLineProd1.length == 9) {
                    this.SubLineProd1.splice(0,9)
                    this.SubLineProd1 = nuevSubLineProd1.reverse()
                }
            },
            sigSubLineProd(){
                let length = 0
                this.SubLineProd1.forEach((val)=>{
                    if (val.l_subl != "") {
                        length++
                    }
                })

                if (length == 9) {
                    const l_subl = this.SubLineProd1[8].l_subl
                    let q_lsubl = false

                    let nuevSubLineProd1 = []
                    this.filteredSubLineProdsxLine.some((val,i)=>{
                        if (q_lsubl == true) {
                            nuevSubLineProd1.push(val)
                            if (nuevSubLineProd1.length == 9) {
                                return true
                            }
                        }

                        if (l_subl == val.l_subl) {
                            q_lsubl = true
                        }
                    })

                    let n_items = nuevSubLineProd1.length
                    if(n_items > 0 && n_items < 9){
                        const length = 9 - n_items
                        for (var i = 0; i < length; i++) {
                            nuevSubLineProd1.push({c_line: "",c_subl: "--",l_subl: ""})
                        }
                    }

                    if (nuevSubLineProd1.length == 9) {
                        this.SubLineProd1.splice(0,9)
                        this.SubLineProd1 = nuevSubLineProd1
                    }
                }
            },
            salirSubLineProd(){
                this.SubLineProd1 = []
            },
            logout(){
                swal({
                    title: '',
                    text: "¿ Seguro de finalizar ?",
                    type: 'question',
                    showCancelButton: true,
                }).then(function () {
                    location.href="/app/logout"
                }.bind(this))
            },
            datosIndex: function(){

                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.get('/pedidos/index')
                .then(function(response){


                    this.LineProds = response.data.LineProd;
                    this.SubLineProds = response.data.SubLineProd;
                    this.ProductosXMediprods = response.data.ProductosXMediprod;
                    this.Ubicas = response.data.Ubica;
                    this.Mesas = response.data.Mesas;
                    this.Pedidos = response.data.Pedidos;
                    this.SubSect = response.data.SubSect;
                    this.Combos = response.data.Combos;
                    this.CombosDet = response.data.CombosDet;
                    this.impresorakt = response.data.impresorakt.l_prg1;
                    this.ProdObse = response.data.ProdObse;

                    if(this.Combos.length > 0){
                        response.data.LineProd.unshift({c_line:"COMBOS",l_impr:"",l_line:"COMBOS"})
                    }

                    localStorage.setItem('LineProds',JSON.stringify(response.data.LineProd));
                    localStorage.setItem('SubLineProds',JSON.stringify(response.data.SubLineProd));
                    localStorage.setItem('ProductosXMediprods',JSON.stringify(response.data.ProductosXMediprod));
                    localStorage.setItem('Ubicas',JSON.stringify(response.data.Ubica));
                    localStorage.setItem('Mesas',JSON.stringify(response.data.Mesas));
                    localStorage.setItem('Pedidos',JSON.stringify(response.data.Pedidos));
                    localStorage.setItem('SubSect',JSON.stringify(response.data.SubSect));
                    localStorage.setItem('Combos',JSON.stringify(response.data.Combos));
                    localStorage.setItem('CombosDet',JSON.stringify(response.data.CombosDet));
                    localStorage.setItem('impresorakt',response.data.impresorakt.l_prg1);
                    localStorage.setItem('ProdObse',JSON.stringify(response.data.ProdObse));

                    this.selecFirstSalaFirstMesa();

                    // LineProd
                    this.cargLineProd1()

                    $('body').loadingModal('destroy');

                }.bind(this))
                .catch(function(error){
                    // Do something with response error
                });
            },
            cargLineProd1(){
                this.LineProds.forEach((val)=>{
                    if(this.LineProd1.length < 10){
                        this.LineProd1.push(val)
                    }
                })
                let n_itemsLineProd1 = this.LineProd1.length
                if(n_itemsLineProd1 < 10){
                    const length = 10 - n_itemsLineProd1
                    for (var i = 0; i < length; i++) {
                        this.LineProd1.push({c_line:"",l_impr:"",l_line:"",})
                    }
                }
            },
            // Modal Sublineas
            mdlProductosXSubLinea: function(c_line,l_line){
                this.$parent.l_buscProd = '' // Limpiamos cuadro de busqueda de productos
                this.txtBuscProd = '' // Limpiamos variable de componente para busqueda de productos

                this.q_mostcombos = false // Ocultamos listado de combos
                // Verificamos si este pedido esta anulado o cancelado
                if(this.Pedido.d_anul == '1'){
                    swal({
                        title: 'Mensaje',
                        text: "Pedido a sido anulado, no se puede agregar mas items !!!",
                        type: 'info',
                    });
                }else if(this.Pedido.q_pago == '1'){
                    swal({
                        title: 'Mensaje',
                        text: "Pedido a sido pagado, no se puede agregar mas items !!!",
                        type: 'info',
                    });
                }else{
                    this.filterProductos.c_line = c_line;
                    this.filterProductos.l_line = l_line;
                    this.l_obse = ''

                    // Filtramos sublineas del c_line
                    var sublineas = this.SubLineProds.filter(function(obj){
                        return obj.c_line == c_line;
                    });

                    if (sublineas.length > 0) {
                        sublineas.unshift({c_line: c_line,c_subl: "TODO",l_subl: "VER TODO"})
                    }

                    this.filteredSubLineProdsxLine = sublineas;

                    // Filtramos todos los productos del c_line
                    this.filterProductosXLineProd(c_line);

                    // $('#eligeProducto').modal('show');
                    $('#pills-tabLineProd a[href="#pills-prods"]').tab('show')
                }
            },
            filterProductosXLineProd: function(c_line){
                this.$parent.l_buscProd = '' // Limpiamos cuadro de busqueda de productos
                this.txtBuscProd = '' // Limpiamos variable de componente para busqueda de productos

                this.filterProductos.l_subl = 'TODOS';

                var productos = this.ProductosXMediprods.filter(function(obj){
                    return obj.c_line == c_line;
                });

                this.filteredProdsXSubLine = productos;
            },
            filterProductosXSubLineProd: function(c_subl,l_subl){
                this.$parent.l_buscProd = '' // Limpiamos cuadro de busqueda de productos
                this.txtBuscProd = '' // Limpiamos variable de componente para busqueda de productos

                this.filterProductos.l_subl = l_subl;

                var productos = this.ProductosXMediprods.filter(function(obj){
                    return obj.c_subl == c_subl;
                });

                this.filteredProdsXSubLine = productos;
            },
            // Elegir producto
            elegirProducto: function(producto, q_mostmodal = true){
                // Para zapatilla
                if (this.org.k_empr == '5') {
                    swal('','Utilice modulo Pedido Agrupado para elegir Producto(s)','info')
                    return false;
                }
                // 

                if(producto.c_comb != undefined){

                    // Verificamos si item se encuenta en lista
                    /*var exist_product = false;
                    for(let peditem of this.PedItem){
                        if( peditem.c_comb == producto.c_comb && peditem.q_envic=='0'  ){
                            swal({
                                title: 'Mensaje',
                                text: "Combo ya se encuentra agregado a items !!!",
                                type: 'info',
                            });
                            exist_product = true;
                            break;
                        }
                    }*/

                    // if (exist_product == false) {
                        this.infoItem.edit = true;
                        this.infoItem.action = 'new',
                        this.infoItem.c_prod = "";
                        this.infoItem.k_medi = "";
                        this.infoItem.l_abre = "";
                        this.infoItem.l_obse = '';
                        this.infoItem.l_prod = producto.l_comb;
                        this.infoItem.q_coci = 1;
                        this.infoItem.k_igv = '0';
                        this.infoItem.q_envic = '0';
                        this.infoItem.q_preparado = '0';
                        this.infoItem.s_bimp = 0;
                        this.infoItem.s_cant = 1;
                        this.infoItem.n_prec = 1;
                        this.infoItem.s_pre1 = producto.s_impo;
                        this.infoItem.s_pre2 = 0;
                        this.infoItem.s_pre3 = 0;
                        this.infoItem.s_pre4 = 0;
                        this.infoItem.s_pre5 = 0;
                        this.infoItem.c_line = "";
                        this.infoItem.s_vent = producto.s_impo;
                        this.infoItem.c_comb = producto.c_comb;
                        this.infoItem.l_vend = localStorage.getItem('l_vend');
                        this.infoItem.q_icbper = 0;

                        if (q_mostmodal) {
                            $('#modificarItem').modal('show')
                        }
                    // }

                }else{                
                    // Verificamos si item se encuenta en lista
                    /*var exist_product = false;
                    for(let peditem of this.PedItem){
                        if( peditem.c_prod == producto.c_prod && peditem.k_medi == producto.k_medi && peditem.q_envic=='0'  ){
                            swal({
                                title: 'Mensaje',
                                text: "Producto ya se encuentra agregado a items !!!",
                                type: 'info',
                            });
                            exist_product = true;
                            break;
                        }
                    }*/

                    // if (exist_product == false) {
                        this.infoItem.edit = true;
                        this.infoItem.action = 'new',
                        this.infoItem.c_prod = producto.c_prod;
                        this.infoItem.k_medi = producto.k_medi;
                        this.infoItem.l_abre = producto.l_abre;
                        this.infoItem.l_obse = '';
                        this.infoItem.l_prod = producto.l_prod;
                        this.infoItem.q_coci = producto.q_coci;
                        this.infoItem.k_igv = producto.k_igv;
                        this.infoItem.q_envic = '0';
                        this.infoItem.q_preparado = '0';
                        this.infoItem.s_bimp = parseFloat(producto.s_pre1) * parseFloat(1);
                        this.infoItem.s_cant = 1;
                        this.infoItem.n_prec = this.p_pudefecto != '0' ? this.p_pudefecto : 1;
                        this.infoItem.s_pre1 = producto.s_pre1;
                        this.infoItem.s_pre2 = producto.s_pre2;
                        this.infoItem.s_pre3 = producto.s_pre3;
                        this.infoItem.s_pre4 = producto.s_pre4;
                        this.infoItem.s_pre5 = producto.s_pre5;
                        this.infoItem.c_line = producto.c_line;
                        this.infoItem.c_comb = "";
                        this.infoItem.l_impr1 = producto.l_impr1;
                        this.infoItem.l_impr2 = producto.l_impr2;
                        this.infoItem.l_vend = localStorage.getItem('l_vend');
                        this.infoItem.q_icbper = producto.q_icbper;

                        if (this.infoItem.n_prec=='1') {
                            this.infoItem.s_vent=this.infoItem.s_pre1;
                        }else if(this.infoItem.n_prec=='2'){
                            this.infoItem.s_vent=this.infoItem.s_pre2;
                        }else if(this.infoItem.n_prec=='3'){
                            this.infoItem.s_vent=this.infoItem.s_pre3;
                        }else if(this.infoItem.n_prec=='4'){
                            this.infoItem.s_vent=this.infoItem.s_pre4;
                        }else if(this.infoItem.n_prec=='5'){
                            this.infoItem.s_vent=this.infoItem.s_pre5;
                        }

                        if (q_mostmodal) {
                            $('#modificarItem').modal('show');
                        }
                    // }
                }
            },
            infoItemRestCant: function(){
                if (this.infoItem.s_cant > 1) {
                    this.infoItem.s_cant = parseFloat(this.infoItem.s_cant) - 1;
                }
            },
            infoItemSumCant: function(){
                this.infoItem.s_cant = parseFloat(this.infoItem.s_cant) + 1;
            },
            editItem: function(item,index){
                this.q_editpv = localStorage.getItem('q_editpv')
                this.SispropPDV.pq_punit = JSON.parse(localStorage.getItem('SispropPDV')).pq_punit

                if(item.c_comb != ""){
                    this.infoItem.c_comb = item.c_comb;
                }else{
                    this.infoItem.c_comb = "";
                }

                this.infoItem.action = 'edit',
                this.infoItem.edit_index = index,
                this.infoItem.c_prod = item.c_prod;
                this.infoItem.k_medi = item.k_medi;
                this.infoItem.l_abre = item.l_abre;
                this.infoItem.l_obse = item.l_obse;
                this.infoItem.l_prod = item.l_prod;
                this.infoItem.q_coci = item.q_coci;
                this.infoItem.q_envic = item.q_envic;
                this.infoItem.q_preparado = item.q_preparado;
                this.infoItem.s_bimp = item.s_bimp;
                this.infoItem.s_cant = parseFloat(item.s_cant);
                this.infoItem.n_prec= item.n_prec != '0' ? item.n_prec : 1;
                this.infoItem.s_vent= item.s_vent;
                this.infoItem.s_pre1= item.s_pre1;
                this.infoItem.s_pre2= item.s_pre2;
                this.infoItem.s_pre3= item.s_pre3;
                this.infoItem.s_pre4= item.s_pre4;
                this.infoItem.s_pre5= item.s_pre5;
                this.infoItem.l_vend= item.l_vend;
                this.infoItem.f_digi= item.f_digi;
                this.infoItem.c_line= item.c_line;

                if(item.q_envic == '1' || this.Pedido.d_anul == '1' || this.Pedido.q_pago == '1'){
                    this.infoItem.edit = false;
                }else{
                    this.infoItem.edit = true;
                }

                $('#modificarItem').modal('show');
            },
            agregarModificarItem: function(){
                 if ( isNaN(this.infoItem.s_cant) == true /*|| Number.isInteger(Number(this.infoItem.s_cant)) == false*/   ) {
                    swal({
                        title: 'Mensaje',
                        text: "Ingrese un numero valido !!!",
                        type: 'info',
                    });
                    return false;
                }else if( Number(this.infoItem.s_cant) <= 0 ){
                    swal({
                        title: 'Mensaje',
                        text: "Valor minimo debe ser 1 !!!",
                        type: 'info',
                    });
                    return false;
                }else{
                    this.infoItem.s_cant = parseFloat(this.infoItem.s_cant).toFixed(2)
                    // Si es producto
                    if(this.infoItem.c_comb.length == 0){
                        // Verificamos si producto se encuentra disponible
                        $('body').loadingModal({'animation': 'fadingCircle'});
                        axios.post(`/productos/devprod`, {c_prod:this.infoItem.c_prod})
                        .then(function(response){

                            $('body').loadingModal('destroy');

                            if(response.data.prod.q_acti == "0"){
                                swal('', "Producto no se encuentra disponible !!!", 'info',);
                            }
                            else{
                                this.agregarModificarItem2()
                            }

                        }.bind(this))
                    }
                    // Si es combo no validamos si esta disponible (aun no se impementa en base de datos disponibilida de combos)
                    else{
                        this.agregarModificarItem2()
                    }
                }

            },
            agregarModificarItem2(){
                if (this.infoItem.action == 'new') {

                    this.PedItem.push({
                        c_prod: this.infoItem.c_prod,
                        k_medi: this.infoItem.k_medi,
                        l_abre: this.infoItem.l_abre,
                        l_obse: this.infoItem.l_obse,
                        l_prod: this.infoItem.l_prod,
                        q_coci: this.infoItem.q_coci,
                        c_indi: this.infoItem.k_igv,
                        q_envic: this.infoItem.q_envic,
                        q_preparado: this.infoItem.q_preparado,
                        s_bimp: parseFloat(this.infoItem.s_vent) * parseFloat(this.infoItem.s_cant),
                        s_cant: this.infoItem.s_cant,
                        s_vent: this.infoItem.s_vent,
                        n_prec: this.infoItem.n_prec,
                        s_pre1: this.infoItem.s_pre1,
                        s_pre2: this.infoItem.s_pre2,
                        s_pre3: this.infoItem.s_pre3,
                        s_pre4: this.infoItem.s_pre4,
                        s_pre5: this.infoItem.s_pre5,
                        c_line: this.infoItem.c_line,
                        c_comb: this.infoItem.c_comb,
                        l_impr1: this.infoItem.l_impr1,
                        l_impr2: this.infoItem.l_impr2,
                        l_vend: this.infoItem.l_vend,
                        q_grab: '0',
                        q_store: '0',
                        n_item: this.PedItem.length == 0 ? 1 : parseInt(this.PedItem[this.PedItem.length - 1].n_item) + 1,
                        q_icbper: this.infoItem.q_icbper,
                    });

                    // Ponemos como item activo el ultimo item que se agrega a PedItem
                    this.active_item = this.PedItem.length - 1
                    let listPedItem = $("#listPedItem");
                    this.$nextTick(function () {
                        listPedItem.scrollTop(listPedItem[0].scrollHeight);
                    }.bind(this))
                    // 

                }else if(this.infoItem.action == 'edit'){
                    var index = this.infoItem.edit_index;
                    this.PedItem[index].s_cant = this.infoItem.s_cant;
                    this.PedItem[index].l_obse = this.infoItem.l_obse;
                    this.PedItem[index].s_vent = this.infoItem.s_vent;
                    this.PedItem[index].n_prec = this.infoItem.n_prec;
                    this.PedItem[index].s_bimp = parseFloat(this.infoItem.s_vent) * parseFloat(this.infoItem.s_cant);

                    if(this.infoItem.q_envic != '1'){
                        this.PedItem[index].q_grab = '0';
                    }
                }

                $('#eligeProducto').modal('hide');
                $('#mdlBuscProd').modal('hide');
                $('#modificarItem').modal('hide');
                $('#mdlCombos').modal('hide');
            },
            removeItem: function(index){
                /*swal({
                    title: '',
                    text: "¿ Quitar Item ?",
                    type: 'question',
                    showCancelButton: true,
                }).then(function () {*/

                    this.PedItem.splice(index,1);

                /*}.bind(this), function (dismiss) {
                    if (dismiss === 'cancel') {
                    }
                }) */ 
            },
            restCantPeditem(){
                let PedItem = this.PedItem[this.active_item]
                let s_cant = parseFloat(PedItem.s_cant)

                if(PedItem != undefined && PedItem.q_envic != 1 && s_cant > 1){
                    PedItem.s_cant = (s_cant - 1).toFixed(2)
                    PedItem.s_bimp = PedItem.s_cant * parseFloat(PedItem.s_vent)
                }
            },
            aumCantPeditem(){
                let PedItem = this.PedItem[this.active_item]

                if(PedItem != undefined && PedItem.q_envic != 1){
                    let s_cant = parseFloat(PedItem.s_cant)
                    PedItem.s_cant = (s_cant + 1).toFixed(2)
                    PedItem.s_bimp = PedItem.s_cant * parseFloat(PedItem.s_vent)
                }
            },
            setLobsePeditem(l_obse){
                let PedItem = this.PedItem[this.active_item]

                if(PedItem != undefined && PedItem.q_envic != 1){
                    PedItem.l_obse += " "+l_obse
                }
            },
            // Pedidos
            actPedidos: function(){
                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.get(`/pedidos/listpedidos`)
                .then(function(response){

                    this.Pedidos = response.data;
                    localStorage.setItem('Pedidos',JSON.stringify(response.data));

                    $('body').loadingModal('destroy');
                    toastr.success('Se actualizó lista de pedidos.');

                }.bind(this))
                .catch(function(error){
                    // Do something with response error
                });
            },
            devlistpeditemsxpedido: function(n_comp){
                var count_q_grab = 0;
                this.PedItem.forEach(function(item){
                    if(item.q_grab == '0'){
                        count_q_grab += 1;
                    }
                });

                if (count_q_grab>0) {
                    swal({
                        title: '',
                        text: "",
                        html: 'No has grabado los cambios del pedido actual !!! </br> ¿ Salir sin grabar ?',
                        type: 'info',
                        showCancelButton: true,
                    }).then(function () {

                        this.devlistpeditemsxpedido2(n_comp);

                    }.bind(this), function (dismiss) {
                        // dismiss can be 'cancel', 'overlay',
                        // 'close', and 'timer'
                        if (dismiss === 'cancel') {
                        }
                    })
                }else{
                    this.devlistpeditemsxpedido2(n_comp);
                }
            },
            devlistpeditemsxpedido2: function(n_comp){
                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.get(`/pedidos/devpedidopeditemsxn_comp/${n_comp}`)
                .then(function(response){

                    var count_q_envic_true = 0;

                    var peditems = response.data.peditem;
                    var peditems = peditems.map(function(item, index){

                        count_q_envic_true += item.q_envic == '1' ? 1 : 0;

                        if (item.c_comb.length > 0) {

                            for(let Combo of this.Combos){
                                if ( Combo.c_comb == item.c_comb ) {
                                    item['l_prod'] = Combo.l_comb;
                                    item['s_pre1'] = Combo.s_impo;
                                    break;
                                }
                            }

                            for(let ComboDet of this.CombosDet){
                                if ( ComboDet.c_comb == item.c_comb && ComboDet.c_prod == item.c_prod && ComboDet.k_medi == item.k_medi ) {
                                    item['s_cant'] = item['s_cant'] / ComboDet.s_cant;
                                    break;
                                }
                            }

                            item['n_prec'] = 1;
                            item['s_vent'] = item['s_pre1'];
                            item['s_bimp'] = parseFloat(item.s_vent) * parseFloat(item.s_cant);
                            item['q_grab'] = '1';
                            item['q_store'] = '1';

                        }else{                            
                            for(let producto of this.ProductosXMediprods){
                                if(item.c_prod == producto.c_prod && item.k_medi == producto.k_medi){
                                    item['l_prod'] = producto.l_prod;
                                    item['l_abre'] = producto.l_abre;
                                    item['s_pre1'] = producto.s_pre1;
                                    item['s_pre2'] = producto.s_pre2;
                                    item['s_pre3'] = producto.s_pre3;
                                    item['s_pre4'] = producto.s_pre4;
                                    item['s_pre5'] = producto.s_pre5;
                                    item['c_line'] = producto.c_line;
                                    item['l_impr1'] = producto.l_impr1;
                                    item['l_impr2'] = producto.l_impr2;
                                    break;
                                }
                            }
                            item['n_prec'] = item.n_prec;
                            item['s_vent'] = item.s_vent;
                            item['s_bimp'] = parseFloat(item.s_vent) * parseFloat(item.s_cant);
                            item['q_grab'] = '1';
                            item['q_store'] = '1';
                        }


                        return item;
                    }.bind(this));
                    
                    if ( count_q_envic_true > 0 || response.data.pedido.q_pago=='1' || response.data.pedido.d_anul=='1') {
                        this.anular = false;
                    }else{
                        this.anular = true;
                    }

                    this.PedItem = peditems;
                    this.Pedido = response.data.pedido;


                    $('body').loadingModal('destroy');

                }.bind(this))
                .catch(function(error){
                    // Do something with response error
                });
            },
            nuevoPedido: function(){
                this.mostLineas()

                var count_q_grab = 0;
                this.PedItem.forEach(function(item){
                    if(item.q_grab == '0'){
                        count_q_grab += 1;
                    }
                });

                if (count_q_grab>0) {
                    swal({
                        title: '',
                        text: "",
                        html: 'No has grabado los cambios del pedido actual !!! </br> ¿ Generar nuevo pedido de todas formas ?',
                        type: 'info',
                        showCancelButton: true,
                    }).then(function () {
                        if (this.org.k_empr == '2') {
                            this.libEdicMesa()
                        }
                        this.resetPedido();
                        if (this.org.k_empr == '2') {
                            $('#eligeSala').modal('show');
                        }else{
                            $('#pills-tab a:first').tab('show');
                        }

                    }.bind(this), function (dismiss) {
                        // dismiss can be 'cancel', 'overlay',
                        // 'close', and 'timer'
                        if (dismiss === 'cancel') {
                        }
                    })
                }else{
                    if (this.org.k_empr == '2') {
                        this.libEdicMesa()
                    }
                    this.resetPedido();
                    if (this.org.k_empr == '2') {
                        $('#eligeSala').modal('show');
                    }else if (this.org.k_empr == '5') {
                        $('#pills-tab a[href="#pills-pedidos"]').tab('show')
                    }else{
                        $('#pills-tab a:first').tab('show');
                    }
                }
            },
            resetPedido: function(){
                this.PedItem = [];
                // Seteamos campos de Pedido
                this.Pedido.c_compgen = '03';
                this.Pedido.c_docu = '00';                
                this.Pedido.d_anul = '0';
                this.Pedido.l_agen = 'VENTAS AL CONTADO';
                this.Pedido.l_dire = '';
                this.Pedido.n_comp = '';
                this.Pedido.n_docu = '100';
                this.Pedido.q_pago = '0';
                this.Pedido.s_tota = 0;
                this.Pedido.l_obse = '';
                this.Pedido.n_pers = 1;
                this.Pedido.ad_plac = '';

                this.anular = false;

                if (localStorage.getItem('ped_gencomp').trim().length > 0) {
                    this.Pedido.c_compgen = localStorage.getItem('ped_gencomp');
                }
            },
            libEdicMesa(){
                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.post(`/mesas/libedicmesa`,{c_ubic: this.Pedido.c_ubic, c_mesa: this.Pedido.c_mesa})
                .then(function(response){

                    $('body').loadingModal('destroy');

                }.bind(this))
            },
            grabarPedido: function(){
                // Para zapatilla
                if (this.org.k_empr == '5') {
                    swal('','Utilice modulo Pedido Agrupado para Grabar/Modificar Pedido','info')
                    return false;
                }
                // 

                if(this.PedItem.length == 0){
                    swal({
                        title: 'Mensaje',
                        text: "No se puede grabar con 0 items !!!",
                        type: 'info',
                    });
                }else{

                    if(this.Pedido.n_comp.length == 0){
                        if(localStorage.getItem('q_pregenvped') != 0){
                            swal({
                                title: '',
                                text: "¿ Grabar pedido ?",
                                type: 'question',
                                showCancelButton: true,
                            }).then(function () {
                                this.grabarNewPedido();
                            }.bind(this))
                        }else{
                            this.grabarNewPedido();
                        }
                    }else{
                        if(localStorage.getItem('q_pregenvped') != 0){
                            swal({
                                title: '',
                                text: "¿ Grabar pedido ?",
                                type: 'question',
                                showCancelButton: true,
                            }).then(function () {
                                this.updatePedido();
                            }.bind(this))
                        }else{
                            this.updatePedido();
                        }
                    }

                }
            },
            grabarNewPedido: function(){
                /*swal({
                    title: '',
                    text: "¿ Grabar pedido ?",
                    type: 'question',
                    showCancelButton: true,
                }).then(function () {*/

                    // Buscamos productos que pertenecen a combos
                    var combodet = [],
                        items = [];
                    for (let item of this.PedItem) {
                        if (item.c_comb != "") {
                            for (let producto of this.CombosDet) {
                                if (producto.c_comb == item.c_comb) {
                                    var item_comb = JSON.parse(JSON.stringify(item));
                                    item_comb.c_prod = producto.c_prod;
                                    item_comb.k_medi = producto.k_medi;
                                    item_comb.s_cant = parseFloat(producto.s_cant) * parseFloat(item_comb.s_cant);
                                    item_comb.s_vent = producto.s_vent;
                                    item_comb.c_indi = producto.k_igv;
                                    combodet.push(item_comb);
                                }
                            }
                        }else{
                            items.push(item);
                        }
                    }
                    var combodet_items = combodet.concat(items);
                    // =======

                    $('body').loadingModal({'animation': 'fadingCircle'});

                    this.Pedido.s_tota = this.s_tota;
                    this.Pedido.s_icbper = this.s_icbper;

                    axios.post(`/pedidos/grabarpedido`,{pedido:this.Pedido,peditem:combodet_items})
                    .then(function(response){

                        this.Pedido.n_comp = response.data.n_comp;

                        this.Pedidos.unshift({
                            c_mesa: this.Pedido.c_mesa,
                            c_ubic: this.Pedido.c_ubic,
                            d_anul: '0',
                            n_comp: this.Pedido.n_comp,
                            q_pago: '0',
                        });

                        this.PedItem.forEach(function(item){
                            item.q_grab = '1';
                            item.q_store = '1';
                        });

                        localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));

                        this.anular = true;

                        $('body').loadingModal('destroy');
                        toastr.success('Pedido grabado correctamente');

                        // Para enviar a cocina
                        this.impCocina()

                    }.bind(this))
                    .catch(function(error){
                        // Do something with response error
                    });

                /*}.bind(this), function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                    if (dismiss === 'cancel') {
                    }
                })*/  
            },
            updatePedido: function(){
                /*swal({
                    title: '',
                    text: "¿ Grabar pedido ?",
                    type: 'question',
                    showCancelButton: true,
                }).then(function () {*/
                    // Buscamos productos que pertenecen a combos
                    var combodet = [],
                        items = [];
                    for (let item of this.PedItem) {
                        if (item.c_comb != "") {
                            for (let producto of this.CombosDet) {
                                if (producto.c_comb == item.c_comb) {
                                    var item_comb = JSON.parse(JSON.stringify(item));
                                    item_comb.c_prod = producto.c_prod;
                                    item_comb.k_medi = producto.k_medi;
                                    item_comb.s_cant = parseFloat(producto.s_cant) * parseFloat(item_comb.s_cant);
                                    item_comb.s_vent = producto.s_vent;
                                    item_comb.c_indi = producto.k_igv;
                                    combodet.push(item_comb);
                                }
                            }
                        }else{
                            items.push(item);
                        }
                    }
                    var combodet_items = combodet.concat(items);
                    // =======

                    $('body').loadingModal({'animation': 'fadingCircle'});

                    this.Pedido.s_tota = this.s_tota;
                    this.Pedido.s_icbper = this.s_icbper;

                    axios.post(`/pedidos/actpedido`,{pedido:this.Pedido,peditem:combodet_items})
                    .then(function(response){

                        if(response.data.info == 'q_pago'){
                            swal({
                                title: 'Mensaje',
                                text: "No se puede grabar, Pedido ha sido pagado !!!",
                                type: 'info',
                            }).then(function () {
                                this.resetPedido();
                            }.bind(this));

                            for(let pedido of this.Pedidos){
                                if(pedido.n_comp == this.Pedido.n_comp){
                                    pedido.q_pago = '1';
                                    localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                    break;
                                }
                            }
                        }
                        else if(response.data.info == 'd_anul'){
                            swal({
                                title: 'Mensaje',
                                text: "No se puede grabar, Pedido fue anulado !!!",
                                type: 'info',
                            }).then(function () {
                                this.resetPedido();
                            }.bind(this));

                            for(let pedido of this.Pedidos){
                                if(pedido.n_comp == this.Pedido.n_comp){
                                    pedido.d_anul = '1';
                                    localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                    break;
                                }
                            }
                        }
                        else{
                            this.PedItem.forEach(function(item){
                                item.q_grab = '1';
                                item.q_store = '1';
                            });
                            toastr.success('Pedido actualizado correctamente.');

                            // Para enviar a cocina
                            this.impCocina()
                        }                        
                        
                        $('body').loadingModal('destroy');

                    }.bind(this))
                    .catch(function(error){
                        if(error.response.status == 422){
                            var mensaje = '';
                            var name_field = '';
                            for( var key in  error.response.data.errors ){
                                mensaje = error.response.data.errors[key][0];
                                name_field = key;
                                break;
                            }

                            if (name_field == 'c_vendedit') {
                                this.resetPedido();
                                if (this.org.k_empr == '2') {
                                    $('#eligeSala').modal('show');
                                }else{
                                    $('#pills-tab a:first').tab('show');
                                }
                            }
                        }
                    }.bind(this));
                /*}.bind(this), function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                    if (dismiss === 'cancel') {
                    }
                }) */
            },
            anulPedido: function(){
                if(localStorage.getItem('q_anulped') != 1){
                    swal('','No tiene privilegio para realizar esta acción','info')
                    return false
                }

                swal({
                    title: '',
                    text: "¿ Seguro de Anular Pedido ?",
                    type: 'question',
                    showCancelButton: true,
                }).then(function () {

                    $('body').loadingModal({'animation': 'fadingCircle'});

                    axios.post(`/pedidos/anulpedido`,{n_comp:this.Pedido.n_comp,c_ubic: this.Pedido.c_ubic,c_mesa: this.Pedido.c_mesa})
                    .then(function(response){

                        if(response.data.info == 'q_pago'){
                            swal({
                                title: 'Mensaje',
                                text: "No se puede anular, Pedido ha sido pagado !!!",
                                type: 'info',
                            }).then(function () {
                                this.resetPedido();
                            }.bind(this));

                            for(let pedido of this.Pedidos){
                                if(pedido.n_comp == this.Pedido.n_comp){
                                    pedido.q_pago = '1';
                                    localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                    break;
                                }
                            }
                        }
                        else if(response.data.info == 'd_anul'){
                            swal({
                                title: 'Mensaje',
                                text: "Pedido ya fue anulado !!!",
                                type: 'info',
                            }).then(function () {
                                this.resetPedido();
                            }.bind(this));

                            for(let pedido of this.Pedidos){
                                if(pedido.n_comp == this.Pedido.n_comp){
                                    pedido.d_anul = '1';
                                    localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                    break;
                                }
                            }
                        }
                        else{
                            for(let pedido of this.Pedidos){
                                if(pedido.n_comp == this.Pedido.n_comp){
                                    pedido.d_anul = '1';
                                    localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                    break;
                                }
                            }
                            this.anulPedidoImp()
                            // this.resetPedido();
                        }

                        if (this.org.k_empr == '2') {
                            $('#eligeSala').modal('show');
                        }/*else{
                            this.$root.showMdlCambVend();
                        }*/

                        $('body').loadingModal('destroy');

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
            },
            anulPedidoImp: function(){
                var combodet = [],
                    items = [];
                for (let item of this.PedItem) {
                    if (item.c_comb.length > 0) {
                        for (let producto of this.CombosDet) {
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
                    for (let prod of this.ProductosXMediprods) {
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
                
                // =======
                
                // Agrupamos los items del pedido por nombre de impresora
                var imprimir = {};
                let prods_impr = [] // Productos con impresora independiente
                combodet_items.map(function(item,index){
                    if(item.l_impr1 == undefined || item.l_impr1.trim().length == 0){
                        for(let LineProd of this.LineProds){
                            if( item.q_coci=='1' && item.q_envic == '1'){
                                if (LineProd.c_line == item.c_line) {

                                    if (LineProd.l_impr in imprimir == false) {
                                        imprimir[`${LineProd.l_impr}`] = [];
                                    }

                                    imprimir[LineProd.l_impr].push(item);
                                    break;
                                }
                            }
                        }
                    }else if(item.q_coci=='1' && item.q_envic == '1'){
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
                        // prods_impr.push(item)
                    }
                }.bind(this));

                // Generamos el formato para el archivo de texto
                var n_comp = 'ANULAR Pedido N: ' + parseInt(String(this.Pedido.n_comp).substring(6,10)),
                    c_ubic = this.Pedido.c_ubic,
                    c_mesa = this.Pedido.c_mesa,
                    n_pers = this.Pedido.n_pers;

                var l_vend = localStorage.getItem('l_vend'),
                    fecha = new Date().toLocaleString();

                var strPeditems = '';
                var contador_imprimir = 0;
                for (var key in imprimir) {
                    if (contador_imprimir == 0) {
                        strPeditems += `${key}\r\nxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                    }else{
                        strPeditems += `\r\nCORTAR\r\n${key}\r\nxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                    }

                    for (var item of imprimir[key]) {
                        var /*l_prod = (item.l_prod.padEnd(30,' ')).substring(0,30),*/
                            s_cant = (parseInt(item.s_cant)).toString().padStart(4,' ');

                            // strPeditems += `${l_prod} ${s_cant}\r\n`;
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

                    strPeditems += `xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\r\n\r\n\r\n\r\n\r\n\r\n`;
                    contador_imprimir++;
                }

                // Generamos formato de impresion para productos con impresora independiente
                prods_impr.forEach((item)=>{
                    let str_head = '',
                        str_head2 = '',
                        str_footer = ''

                    // Head
                    if (contador_imprimir == 0) {
                        str_head += `${item.l_impr1}\r\nxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;

                    }else{
                        str_head += `\r\nCORTAR\r\n${item.l_impr1}\r\nxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                    }
                    
                    str_head2 += `\r\nCORTAR\r\n${item.l_impr2}\r\nxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                    // 
                    
                    var /*l_prod = (item.l_prod.padEnd(30,' ')).substring(0,30),*/
                        s_cant = (parseInt(item.s_cant)).toString().padStart(4,' ');

                        // str_footer += `${l_prod} ${s_cant}\r\n`;
                        let l_prod1 = item.l_prod.match(/.{1,30}/g)
                        l_prod1.forEach((element, index)=>{
                            str_footer += index == 0 ? `${s_cant} ${element.padEnd(30,' ')}\r\n` : `     ${element.padEnd(30,' ')}\r\n`
                        })

                        // Si hay observacion se agregara el texto
                        if(item.l_obse.length > 0){
                            var l_obse = item.l_obse.toLowerCase().match(/.{1,28}/g).join('\r\n     ');
                            str_footer += `     ->${l_obse}\r\n`;
                        }

                    str_footer += `xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\r\n\r\n\r\n\r\n\r\n\r\n`;

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

                    // Clipboard
                    /*document.getElementById('txtimpr').value = "";
                    document.getElementById('txtimpr').value = strPeditems;
                    var copyText = document.getElementById('txtimpr');
                    copyText.focus();
                    copyText.select();
                    var res = document.execCommand("copy");
                    document.getElementById('txtimpr').value = "";
                    if(res){
                        window.location = 'ec34printicket:';
                    }else{
                        toastr.error('No se pudo copiar formato de impresión.')
                    }*/
                    //
                }
                // Reseteamos pedido
                this.resetPedido();
            },
            cerrarPedido: function(){
                var count_q_grab = 0;
                this.PedItem.forEach(function(item){
                    if(item.q_grab == '0'){
                        count_q_grab += 1;
                    }
                });

                if (count_q_grab>0) {
                    swal({
                        title: 'Mensaje',
                        text: "Primero debe grabar items modificados !!!",
                        type: 'info',
                    });
                }else{
                    swal({
                        title: '',
                        text: "¿ Enviar pedido ?",
                        type: 'question',
                        showCancelButton: true,
                    }).then(function () {
                        if(this.Pedido.n_comp.trim.length > 0){
                            swal({
                                title: 'Mensaje',
                                text: "Primero debe grabar Pedido !!!",
                                type: 'info',
                            });
                        }else{
                            $('body').loadingModal({'animation': 'fadingCircle'});

                            axios.post(`/pedidos/cerrarpedido`,{n_comp:this.Pedido.n_comp})
                            .then(function(response){

                                if(response.data.info == 'q_pago'){
                                    swal({
                                        title: 'Mensaje',
                                        text: "No se puede cerrar, Pedido ha sido pagado !!!",
                                        type: 'info',
                                    }).then(function () {
                                        this.resetPedido();
                                    }.bind(this));

                                    for(let pedido of this.Pedidos){
                                        if(pedido.n_comp == this.Pedido.n_comp){
                                            pedido.q_pago = '1';
                                            localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                            break;
                                        }
                                    }
                                }
                                else if(response.data.info == 'd_anul'){
                                    swal({
                                        title: 'Mensaje',
                                        text: "No se puede cerrar, Pedido fue anulado !!!",
                                        type: 'info',
                                    }).then(function () {
                                        this.resetPedido();
                                    }.bind(this));

                                    for(let pedido of this.Pedidos){
                                        if(pedido.n_comp == this.Pedido.n_comp){
                                            pedido.d_anul = '1';
                                            localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                            break;
                                        }
                                    }
                                }
                                else{
                                    this.resetPedido();
                                }

                                $('body').loadingModal('destroy');
                                toastr.success('Pedido enviado correctamente.')

                            }.bind(this))
                            .catch(function(error){
                                // Do something with response error
                            });
                        }
                    }.bind(this), function (dismiss) {
                        // dismiss can be 'cancel', 'overlay',
                        // 'close', and 'timer'
                        if (dismiss === 'cancel') {
                        }
                    })
                }
            },
            // Productos
            listProdLineProdSubLineProd: function(){
                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.get(`/productos/listprodlineprodsublineprod`)
                .then(function(response){

                    this.LineProds = response.data.LineProd;
                    this.SubLineProds = response.data.SubLineProd;
                    this.ProductosXMediprods = response.data.ProductosXMediprod;

                    localStorage.setItem('LineProds',JSON.stringify(response.data.LineProd));
                    localStorage.setItem('SubLineProds',JSON.stringify(response.data.SubLineProd));
                    localStorage.setItem('ProductosXMediprods',JSON.stringify(response.data.ProductosXMediprod));

                    $('body').loadingModal('destroy');

                }.bind(this))
                .catch(function(error){
                    // Do something with response error
                });
            },
            // Modal Sala
            selecFirstSalaFirstMesa: function(){
                this.Pedido.c_ubic = this.Ubicas[0].c_ubic;
            },
            mdlShowSala: function(){
                if(this.Pedido.n_comp.length > 0){
                    
                    this.currentView = 'pedidos-edithubicacion';
                    this.currentProps = {accion: 'cambmesa',n_comp:this.Pedido.n_comp ,pedc_ubic:this.Pedido.c_ubic ,pedc_mesa: this.Pedido.c_mesa};

                }else{

                }
            },
            mdlSalaNext: function(){
                if ( this.filterMesas.length == 0 ) {
                    swal({
                        title: 'Mensaje',
                        text: 'No existen mesas para sala seleccionada!!!',
                        type: 'error',
                    })
                }
                else if ( this.Pedido.c_ubic.trim().length > 0) {

                    axios.get(`/mesas/listmesasxubic/${this.Pedido.c_ubic}`)
                    .then(function(response){

                        this.Mesas = response.data;

                        // Volvemos items draggable
                        $( ".messaDraggable" ).draggable({
                            containment: '#eligeMesaDraggableContainer',
                            disabled: true
                        });

                        // Posicionamos las mesas en sus ejes x y y
                        this.filterMesas.map((mesa) => {
                            var element = `#${mesa.c_ubic}${mesa.c_mesa}`,
                                n_ejex = parseFloat(mesa.n_ejex),
                                n_ejey = parseFloat(mesa.n_ejey);
                            
                            $(element).css({'left' : n_ejex,'top': n_ejey})
                        })

                        this.Pedido.c_mesa = '';

                        // mostramos modal
                        $('#eligeMesa').modal('show');

                        $('#eligeMesaDraggableBody').css("overflow","hidden")

                    }.bind(this))
                    .catch(function(error){
                        // Do something with response error
                    });
                }
            },
            eligeMesa(l_mesa, c_mesa, q_ocup){
                this.Pedido.l_mesa = l_mesa

                if(q_ocup == '1' || q_ocup == '2'){
                    // Mesas ocupadas o con division de cuentas
                    this.devPedido(c_mesa)
                }
                else{
                    this.Pedido.c_mesa = c_mesa;

                    // if(this.$parent.Usuario.Usuario.substr(0,4) == 'CAJA' && this.Pedido.c_mesa == '99'){
                    if(this.$parent.Usuario.Usuario.substr(0,4) == 'CAJA' && this.Pedido.c_ubic == '99' && this.Pedido.c_mesa == this.c_mesac){
                        // Para pedidos rapidos
                        this.Pedido.c_ubic = '99' // this.Pedido.c_ubic = '01'
                        this.Pedido.l_ubic = 'CAJA' // this.Pedido.l_ubic = 'Sala 01'

                        let c_vend = localStorage.getItem('c_vend1'),
                            l_vend = localStorage.getItem('l_vend1')

                        this.$root.Usuario.c_vend = c_vend
                        this.$root.Usuario.l_vend = l_vend
                        this.Pedido.c_vend = c_vend
                        this.Pedido.l_vend = l_vend

                        axios.post('/mesas/devmesa',{c_ubic: this.Pedido.c_ubic, c_mesa: c_mesa,c_vend: c_vend, l_vend: l_vend})
                        .then((resp)=>{
                            this.Pedido.l_mesa = resp.data.mesa.l_mesa

                            if(resp.data.mesa.q_ocup == '0'){
                                $('#eligeSala').modal('hide');
                                $('#eligeMesa').modal('hide');
                            }else{
                                this.devPedido(c_mesa)
                            }
                        })

                    }else{
                        // Para nuevos pedidos
                        axios.post(`/mesas/postsetocupedicmesa`,{c_ubic: this.Pedido.c_ubic, c_mesa: c_mesa})
                        .then(function(response){
                            // Elegir mesa automaticamente
                            $('#eligeSala').modal('hide');
                            $('#eligeMesa').modal('hide');
                            
                            this.$root.showMdlCambVend({n_comp: '', c_ubic:this.Pedido.c_ubic, c_mesa:this.Pedido.c_mesa});

                        }.bind(this))
                    }                    
                }
            },
            devPedido(c_mesa, l_clav = '', q_prcue=0){
                let l_ubic = this.Pedido.l_ubic,
                    l_mesa = this.Pedido.l_mesa

                $('body').loadingModal({'animation': 'fadingCircle'});

                axios.get(`/pedidos/devpedidoxc_ubicc_mesa/${this.Pedido.c_ubic}/${c_mesa}`,{params: {q_prcue: q_prcue, l_clav: l_clav}})
                .then(function(response){

                    this.Pedidos = [];
                    localStorage.setItem('Pedidos',JSON.stringify([]));

                    EventBus.$emit('appcambinfovend',response.data.vendedor);

                    // De funcion devlistpeditemsxpedido2
                    var count_q_envic_true = 0;

                    var peditems = response.data.peditem;
                    var peditems = peditems.map(function(item, index){

                        count_q_envic_true += item.q_envic == '1' ? 1 : 0;

                        if (item.c_comb.length > 0) {

                            for(let Combo of this.Combos){
                                if ( Combo.c_comb == item.c_comb ) {
                                    item['l_prod'] = Combo.l_comb;
                                    item['s_pre1'] = Combo.s_impo;
                                    break;
                                }
                            }

                            for(let ComboDet of this.CombosDet){
                                if ( ComboDet.c_comb == item.c_comb && ComboDet.c_prod == item.c_prod && ComboDet.k_medi == item.k_medi ) {
                                    item['s_cant'] = item['s_cant'] / ComboDet.s_cant;
                                    break;
                                }
                            }

                            item['n_prec'] = 1;
                            item['s_vent'] = item['s_pre1'];
                            item['s_bimp'] = parseFloat(item.s_vent) * parseFloat(item.s_cant);
                            item['q_grab'] = '1';
                            item['q_store'] = '1';

                        }else{                            
                            for(let producto of this.ProductosXMediprods){
                                if(item.c_prod == producto.c_prod && item.k_medi == producto.k_medi){
                                    item['l_prod'] = producto.l_prod;
                                    item['l_abre'] = producto.l_abre;
                                    item['s_pre1'] = producto.s_pre1;
                                    item['s_pre2'] = producto.s_pre2;
                                    item['s_pre3'] = producto.s_pre3;
                                    item['s_pre4'] = producto.s_pre4;
                                    item['s_pre5'] = producto.s_pre5;
                                    item['c_line'] = producto.c_line;
                                    item['l_impr1'] = producto.l_impr1;
                                    item['l_impr2'] = producto.l_impr2;
                                    item['q_icbper'] = producto.q_icbper;
                                    break;
                                }
                            }
                            item['n_prec'] = item.n_prec;
                            item['s_vent'] = item.s_vent;
                            item['s_bimp'] = parseFloat(item.s_vent) * parseFloat(item.s_cant);
                            item['q_grab'] = '1';
                            item['q_store'] = '1';
                        }


                        return item;
                    }.bind(this));
                    
                    if ( count_q_envic_true > 0 || response.data.pedido.q_pago=='1' || response.data.pedido.d_anul=='1') {
                        this.anular = false;
                    }else{
                        this.anular = true;
                    }

                    this.PedItem = peditems;
                    this.Pedido = response.data.pedido;
                    this.Pedido.l_ubic = l_ubic
                    this.Pedido.l_mesa = l_mesa
                    //=========================================

                    if (q_prcue==1) {                        
                        localStorage.setItem('c_vend',response.data.vendedit.c_vend);
                        localStorage.setItem('l_vend',response.data.vendedit.l_vend);
                        this.impPreCuenta2(1)
                    }else{
                        $('#eligeSala').modal('hide');
                        $('#eligeMesa').modal('hide');

                        // if(this.$parent.Usuario.Usuario.substr(0,4) == 'CAJA' && this.Pedido.c_ubic == '01' && this.Pedido.c_mesa == '99'){
                        if(this.$parent.Usuario.Usuario.substr(0,4) == 'CAJA' && this.Pedido.c_ubic == '99' && this.Pedido.c_mesa == this.c_mesac){
                            // No se hace nada
                        }else{
                            // Ventana para cambiar vendedor
                            this.$root.showMdlCambVend({n_comp: this.Pedido.n_comp, c_ubic:this.Pedido.c_ubic, c_mesa:this.Pedido.c_mesa, l_clav: l_clav});
                        }
                    }

                }.bind(this))
            },
            aceptarSalaMesa: function(){
                if (this.Pedido.c_mesa == '') {
                    swal({
                        title: 'Mensaje',
                        text: 'Debe seleccionar mesa !!!',
                        type: 'info',
                    })
                }else if(this.Pedido.n_comp.length == 0) {
                    $('#eligeSala').modal('hide');
                    $('#eligeMesa').modal('hide');
                    this.$root.showMdlCambVend({n_comp: '', c_ubic:this.Pedido.c_ubic, c_mesa:this.Pedido.c_mesa});
                }
            },
            showMdlBuscProd: function(){
                $('#mdlBuscProd').modal('show');
                this.txtBuscProd = '';
                $('#txtBuscProd').focus();
                this.filteredProdsXSubLine = [];
            },
            impPreCuenta: function(){
                // Productos enviados a cocina
                var total_q_envic_false = 0;
                this.PedItem.map(function(item,index){
                    if(item.q_envic == '0'){
                        total_q_envic_false += 1;
                    }
                })
                
                if(this.Pedido.d_anul == 1){
                    swal({
                        title: 'Mensaje',
                        text: 'No se puede imprimir pedido Anulado!!!',
                        type: 'error',
                    }).then(function () {
                        $('#n_docu').focus();
                    });
                }
                else if(this.PedItem.length == 0){
                    swal({
                        title: 'Mensaje',
                        text: 'No se puede imprimir Pre-Cuenta con 0 items!!!',
                        type: 'error',
                    }).then(function () {
                        $('#n_docu').focus();
                    });
                }
                else if (total_q_envic_false > 0) {
                    swal({
                        title: 'Mensaje',
                        text: 'Para imprimir Pre-Cuenta Debe Enviar Todos los Items!!!',
                        type: 'error',
                    }).then(function () {
                        $('#n_docu').focus();
                    });
                }
                else{
                    if ( localStorage.getItem('q_pregimpcue') != 0 ) {
                        swal({
                            title: '',
                            text: "¿ Imprimir Pre-Cuenta ?",
                            type: 'question',
                            showCancelButton: true,
                        }).then(function () {
                            
                            this.impPreCuenta2()

                        }.bind(this), function (dismiss) {
                            // dismiss can be 'cancel', 'overlay',
                            // 'close', and 'timer'
                            if (dismiss === 'cancel') {
                            }
                        })
                    }else{
                        this.impPreCuenta2()
                    }
                }
            },
            impPreCuenta2(q_prcue=0){
                var n_comp = parseInt(String(this.Pedido.n_comp).substring(6,10)),
                    c_ubic = this.Pedido.c_ubic,
                    c_mesa = this.Pedido.c_mesa,
                    // s_tota = (parseFloat(this.Pedido.s_tota).toFixed(2)).padStart(15,' ');
                    s_tota = (parseFloat(this.s_tota).toFixed(2)).padStart(15,' '),
                    s_icbper = (parseFloat(this.s_icbper).toFixed(2)).padStart(15,' ');
                
                var l_vend = localStorage.getItem('l_vend'),
                    fecha = new Date().toLocaleString();

                var strPeditems = '',
                    strItems = []
                this.PedItem.map(function(item,index){
                    /*var l_prod = (item.l_prod.padEnd(20,' ')).substring(0,20);
                    var s_cant = (parseInt(item.s_cant)).toString().padStart(4,' ');
                    var s_bimp = (parseFloat(item.s_bimp).toFixed(2)).padStart(9,' ');

                    strPeditems += `${l_prod} ${s_cant} ${s_bimp}\r\n`;*/

                    let q_exist = false
                    strItems.some((element,index)=>{
                        if (element.c_prod == item.c_prod && element.k_medi == item.k_medi) {
                            strItems[index].s_cant = parseInt(strItems[index].s_cant) + parseInt(item.s_cant)
                            strItems[index].s_bimp = parseFloat(strItems[index].s_bimp) + parseFloat(item.s_bimp)
                            q_exist = true
                            return true;
                        }
                    })

                    if (q_exist==false) {
                        strItems.push({
                            c_prod: item.c_prod,
                            k_medi: item.k_medi,
                            l_prod: item.l_prod,
                            s_cant: item.s_cant,
                            s_bimp: item.s_bimp,
                            s_vent: item.s_vent
                        })
                    }
                });
                strItems.map(function(item,index){
                    var /*l_prod = (item.l_prod.padEnd(17,' ')).substring(0,17),*/
                        s_cant = (parseInt(item.s_cant)).toString().padStart(3,' '),
                        s_bimp = (parseFloat(item.s_bimp).toFixed(2)).padStart(6,' '),
                        s_vent = (parseFloat(item.s_vent).toFixed(2)).padStart(6,' ')

                    // strPeditems += `${s_cant} ${l_prod} ${s_vent} ${s_bimp}\r\n`;

                    let l_prod = item.l_prod.match(/.{1,17}/g)
                    l_prod.forEach((element, index)=>{
                        strPeditems += index == 0 ? `${s_cant} ${element.padEnd(17,' ')} ${s_vent} ${s_bimp}\r\n` : `    ${element.padEnd(17,' ')}\r\n`
                    })
                });

                var strTota = ('Total:').padStart(20,' ') + s_tota;
                var l_s_icbper = s_icbper > 0 ? `${('ICBPER:').padStart(20,' ')+ s_icbper}\r\n` : '';

                /*var strImp = `${this.impresorakt}\r\n-----------------------------------\r\n             Pre-Cuenta\r\nPedido N: ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nPlato                Cant.     Imp.\r\n-----------------------------------\r\n${strPeditems}-----------------------------------\r\n${strTota}\r\n-----------------------------------\r\n\r\n-----------------------------------\r\n\r\n-----------------------------------\r\n    Gracias por su preferencia \r\n   Exija su comprobante de pago \r\n-----------------------------------\r\n\r\n\r\n\r\n\r\n\r\n`;*/
                var strImp = `${this.impresorakt}\r\n-----------------------------------\r\n             Pre-Cuenta\r\nPedido N: ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato             P.Unit  Imp.\r\n-----------------------------------\r\n${strPeditems}-----------------------------------\r\n${l_s_icbper}${strTota}\r\n-----------------------------------\r\n\r\n-----------------------------------\r\n\r\n-----------------------------------\r\n    Gracias por su preferencia \r\n   Exija su comprobante de pago \r\n-----------------------------------\r\n\r\n\r\n\r\n\r\n\r\n`;

                // Generamos archivo de texto
                var blob = new Blob([strImp], {type: "text/plain"});
                saveAs(blob, "ec34imprped.txt");
                window.location = 'ec34printicket:';

                // Clipboard
                /*document.getElementById('txtimpr').value = "";
                document.getElementById('txtimpr').value = strImp;
                var copyText = document.getElementById('txtimpr');
                copyText.focus();
                copyText.select();
                var res = document.execCommand("copy");
                document.getElementById('txtimpr').value = "";
                if(res){
                    window.location = 'ec34printicket:';
                }else{
                    toastr.error('No se pudo copiar formato de impresión.')
                }*/
                //

                // Liberar edicion de mesa
                if (this.org.k_empr == '2' && q_prcue == 0) {
                    this.libEdicMesa()
                }

                // Reseteamos pedido
                this.resetPedido();

                // Mostramos tab listado lineas
                this.mostLineas()


                if (this.org.k_empr == '2') {
                    $('#eligeSala').modal('show');
                }else{
                    //this.$root.showMdlCambVend();
                    $('#pills-tab a:first').tab('show');
                }
            },
            impCocina: function(){
                var total_q_grab_false = 0, // Items modificados sin grabar
                    total_q_envic_false = 0; // Items que no fueron enviados a cocina
                this.PedItem.forEach(function(item){
                    if(item.q_grab == '0'){
                        total_q_grab_false += 1;
                    }
                    if(item.q_envic == '0'){
                        total_q_envic_false += 1;
                    }
                });

                if (total_q_grab_false>0) {
                    swal({
                        title: 'Mensaje',
                        text: "Primero debe grabar items modificados !!!",
                        type: 'info',
                    });
                }
                /*else if (total_q_envic_false == 0) {
                    swal({
                        title: 'Mensaje',
                        text: 'Todos los items ya fueron enviados !!!',
                        type: 'info',
                    }).then(function () {
                    });
                }*/
                else{
                    /*swal({
                        title: '',
                        text: "¿ Enviar pedido ?",
                        type: 'question',
                        showCancelButton: true,
                    }).then(function () {*/
                        if(this.Pedido.n_comp.trim.length > 0){
                            swal({
                                title: 'Mensaje',
                                text: "Primero debe grabar Pedido !!!",
                                type: 'info',
                            });
                        }else{
                            $('body').loadingModal({'animation': 'fadingCircle'});

                            axios.post(`/pedidos/cerrarpedido`,{n_comp:this.Pedido.n_comp})
                            .then(function(response){

                                if(response.data.info == 'q_pago'){
                                    swal({
                                        title: 'Mensaje',
                                        text: "No se puede cerrar, Pedido ha sido pagado !!!",
                                        type: 'info',
                                    }).then(function () {
                                        this.resetPedido();
                                    }.bind(this));

                                    for(let pedido of this.Pedidos){
                                        if(pedido.n_comp == this.Pedido.n_comp){
                                            pedido.q_pago = '1';
                                            localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                            break;
                                        }
                                    }
                                }
                                else if(response.data.info == 'd_anul'){
                                    swal({
                                        title: 'Mensaje',
                                        text: "No se puede cerrar, Pedido fue anulado !!!",
                                        type: 'info',
                                    }).then(function () {
                                        this.resetPedido();
                                    }.bind(this));

                                    for(let pedido of this.Pedidos){
                                        if(pedido.n_comp == this.Pedido.n_comp){
                                            pedido.d_anul = '1';
                                            localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                                            break;
                                        }
                                    }
                                }
                                else{
                                    // Buscamos productos que pertenecen a combos y buscamos el codigo de linea
                                    var combodet = [],
                                        items = [];
                                    for (let item of this.PedItem) {
                                        if (item.c_comb.length > 0) {
                                            for (let producto of this.CombosDet) {
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
                                        for (let prod of this.ProductosXMediprods) {
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
                                    
                                    // =======
                                    
                                    // Agrupamos los items del pedido por nombre de impresora
                                    var imprimir = {};
                                    let prods_impr = [] // Productos con impresora independiente
                                    combodet_items.map(function(item,index){
                                        if(item.l_impr1 == undefined || item.l_impr1.trim().length == 0){
                                            for(let LineProd of this.LineProds){
                                                if( item.q_coci=='1' && item.q_envic == '0'){
                                                    if (LineProd.c_line == item.c_line) {

                                                        if (LineProd.l_impr in imprimir == false) {
                                                            imprimir[`${LineProd.l_impr}`] = [];
                                                        }

                                                        imprimir[LineProd.l_impr].push(item);
                                                        break;
                                                    }
                                                }
                                            }
                                        }else if(item.q_coci=='1' && item.q_envic == '0'){
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
                                    var n_comp = 'Pedido N: ' + parseInt(String(this.Pedido.n_comp).substring(6,10)),
                                        c_ubic = this.Pedido.c_ubic,
                                        c_mesa = this.Pedido.c_mesa,
                                        n_pers = this.Pedido.n_pers;

                                    var l_vend = localStorage.getItem('l_vend'),
                                        fecha = new Date().toLocaleString();

                                    var strPeditems = '';
                                    var contador_imprimir = 0;
                                    for (var key in imprimir) {
                                        if (contador_imprimir == 0) {
                                            strPeditems += `${key}\r\n-----------------------------------\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                                        }else{
                                            strPeditems += `\r\nCORTAR\r\n${key}\r\n-----------------------------------\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                                        }

                                        for (var item of imprimir[key]) {
                                            var /*l_prod = (item.l_prod.padEnd(30,' ')).substring(0,30),*/
                                                s_cant = (parseInt(item.s_cant)).toString().padStart(4,' ');

                                                // strPeditems += `${l_prod} ${s_cant}\r\n`;
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

                                        strPeditems += `-----------------------------------\r\n\r\n\r\n\r\n\r\n\r\n`;
                                        contador_imprimir++;
                                    }

                                    // Generamos formato de impresion para productos con impresora independiente
                                    prods_impr.forEach((item)=>{
                                        let str_head = '',
                                            str_head2 = '',
                                            str_footer = ''

                                        // Head
                                        if (contador_imprimir == 0) {
                                            str_head += `${item.l_impr1}\r\n-----------------------------------\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;

                                        }else{
                                            str_head += `\r\nCORTAR\r\n${item.l_impr1}\r\n-----------------------------------\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                                        }
                                        
                                        str_head2 += `\r\nCORTAR\r\n${item.l_impr2}\r\n-----------------------------------\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nImpresion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                                        // 
                                        
                                        var /*l_prod = (item.l_prod.padEnd(30,' ')).substring(0,30),*/
                                            s_cant = (parseInt(item.s_cant)).toString().padStart(4,' ');

                                            // str_footer += `${l_prod} ${s_cant}\r\n`;
                                            let l_prod1 = item.l_prod.match(/.{1,30}/g)
                                            l_prod1.forEach((element, index)=>{
                                                str_footer += index == 0 ? `${s_cant} ${element.padEnd(30,' ')}\r\n` : `     ${element.padEnd(30,' ')}\r\n`
                                            })

                                            // Si hay observacion se agregara el texto
                                            if(item.l_obse.length > 0){
                                                var l_obse = item.l_obse.toLowerCase().match(/.{1,28}/g).join('\r\n     ');
                                                str_footer += `     ->${l_obse}\r\n`;
                                            }

                                        str_footer += `-----------------------------------\r\n\r\n\r\n\r\n\r\n\r\n`;

                                        strPeditems += str_head + str_footer
                                        strPeditems += item.l_impr2 == undefined || item.l_impr2.length == 0 ? '' : str_head2 + str_footer
                                        contador_imprimir++;
                                    })

                                    // Archivo de texto
                                    if(strPeditems.length > 0){
                                        // Generamos archivo de texto
                                        var blob = new Blob([strPeditems], {type: "text/plain"});
                                        saveAs(blob, "ec34imprped.txt");
                                        setTimeout(function(){ window.location = 'ec34printicket:'; }, 1500);
                                        
                                        
                                        // Clipboard
                                        /*document.getElementById('txtimpr').value = "";
                                        document.getElementById('txtimpr').value = strPeditems;
                                        var copyText = document.getElementById('txtimpr');
                                        copyText.focus();
                                        copyText.select();
                                        var res = document.execCommand("copy");
                                        document.getElementById('txtimpr').value = "";
                                        if(res){
                                            window.location = 'ec34printicket:';
                                        }else{
                                            toastr.error('No se pudo copiar formato de impresión.')
                                        }*/
                                        //
                                    }

                                    // Reseteamos pedido
                                    this.resetPedido();

                                    // Mostramos tab listado lineas
                                    this.mostLineas()

                                    if (this.org.k_empr == '2') {
                                        $('#eligeSala').modal('show');
                                    }else{
                                        //this.$root.showMdlCambVend();
                                        $('#pills-tab a:first').tab('show');
                                    }
                                }

                                $('body').loadingModal('destroy');
                                toastr.success('Pedido enviado correctamente.')

                            }.bind(this))
                            .catch(function(error){
                                // Do something with response error
                            });
                        }
                    /*}.bind(this), function (dismiss) {
                        // dismiss can be 'cancel', 'overlay',
                        // 'close', and 'timer'
                        if (dismiss === 'cancel') {
                        }
                    })*/
                }
            },
            // Mostrar tab del listado de lineas
            mostLineas(){
                $('#pills-tabLineProd a[href="#pills-lineas"]').tab('show')

                this.filterProductos.l_line = ''
                this.l_comb = ''
                this.txtBuscProd = ''

                this.SubLineProd1 = []

                this.LineProd1 = []
                this.cargLineProd1()

                $('#listProd').scrollTop(0)
            },
            showMdlCombos: function(){
                this.$parent.l_buscProd = '' // Limpiamos cuadro de busqueda de productos
                this.txtBuscProd = '' // Limpiamos variable de componente para busqueda de productos

                this.q_mostcombos = true // Mostramos listado de combos
                if(this.Pedido.d_anul == '1'){
                    swal({
                        title: 'Mensaje',
                        text: "Pedido a sido anulado, no se puede agregar mas items !!!",
                        type: 'info',
                    });
                }else if(this.Pedido.q_pago == '1'){
                    swal({
                        title: 'Mensaje',
                        text: "Pedido a sido pagado, no se puede agregar mas items !!!",
                        type: 'info',
                    });
                }else{
                    // $('#mdlCombos').modal('show');
                    this.l_comb = 'COMBOS'
                    $('#pills-tabLineProd a[href="#pills-combos"]').tab('show')
                }

            },
            addL_obse: function(l_obse){
                if(l_obse == 'LIMPIAR'){
                    this.infoItem.l_obse = '';
                }
                else if (this.infoItem.l_obse.length > 0)
                    this.infoItem.l_obse += ` - ${l_obse}`;
                else{
                    this.infoItem.l_obse += `${l_obse}`;
                }
            },
            //Mostrar Modal Informacion de Pedido(Observacion)
            showMdlInfoPed: function(){
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
            // Exonerar Items
            activeRowPeditem: function(index, c_line){
                this.active_item = index;
                this.infoItem.c_line = c_line != undefined ? c_line : "" // Para que al seleccionar item se realize la busqueda de las observaciones
            },
            toggleExonerado: function(){
                if (this.active_item != null) {
                    var item = this.PedItem[this.active_item];
                    if (item.q_envic=="0" && item.q_preparado=="0" && this.Pedido.d_anul=="0" && this.Pedido.q_pago=="0" ) {
                        if (item.c_indi=="E" || item.c_indi=="1") {
                            item.c_indi = "0";
                        }else{
                            item.c_indi = "1";
                        }
                        item.q_grab = 0;
                    }
                }
            },
            // Reimprimir Items enviados a cocina
            showMdlPedidosReimpItemsEnv: function(){
                if(localStorage.getItem('q_reimpitems') != 1){
                    // swal('','No tiene privilegio para realizar esta acción','info')
                    toastr.info('No tiene privilegio para realizar esta acción')
                }else{
                    var peditem = this.PedItem.filter(function(item){
                                    return item.q_envic == "1";
                                });

                    this.currentView = 'stock-pedidos-reimp-items-env';
                    this.currentProps = {
                        peditems: peditem,
                        n_comp: this.Pedido.n_comp,
                        c_ubic: this.Pedido.c_ubic,
                        l_ubic: this.Pedido.l_ubic,
                        c_mesa: this.Pedido.c_mesa,
                        l_mesa: this.Pedido.l_mesa,
                        n_pers: this.Pedido.n_pers,
                        l_vend: this.Pedido.l_vend
                    }
                }
            },
            hideMdlPedidosReimpItemsEnv: function(){
                this.currentView = null;
                this.currentProps = null;
            },
            // Cambiar Mesa
            hideEdithubicacion: function(){
                this.currentView = null;
                this.currentProps = null;
            },
            aceptaHubicacion: function(data){
                this.currentView = null;
                this.currentProps = null;

                this.Pedido.c_ubic = data.c_ubic;
                this.Pedido.l_ubic = data.l_ubic;
                this.Pedido.c_mesa = data.c_mesa;
                this.Pedido.l_mesa = data.l_mesa;
            },
            // Ingresar numero de personas para el pedido(numero de comensales en el caso de restaurantes)
            acepMdlNumPers: function(data){
                this.Pedido.n_pers = data.n_pers;
                this.currentView = null;
                this.currentProps = null;
            },
            // Mover Items
            showMdlPedidoMoverItems(){
                /*if(localStorage.getItem('q_movitem') != 1){
                    swal('','No tiene privilegio para realizar esta acción','info')
                }else if(typeof(this.Pedido.n_comp) == 'undefined' || this.Pedido.n_comp == ''){
                    swal('','Debe grabar pedido para realizar esta acción','info')
                }else if(this.PedItem[this.active_item] == undefined){
                    swal('','Debe seleccionar item','info')
                }else if(this.PedItem[this.active_item].c_comb != ""){
                    swal('','No se puede realizar esta acción con combos','info')
                }else if(this.PedItem[this.active_item].q_envic == "0"){
                    swal('','Item no fue enviado a cocina','info')
                }else{
                    this.currentView = 'pedido-moveritems'
                    this.currentProps = {
                        _Pedido: this.Pedido,
                        _PedItem: this.PedItem[this.active_item],
                        _n_items: this.PedItem.length
                    }
                }*/
                if(localStorage.getItem('q_movitem') != 1){
                    swal('','No tiene privilegio para realizar esta acción','info')
                }else{
                    let peditem = [],
                        s_totcant = 0
                    this.PedItem.forEach((val)=>{
                        let item = Object.assign({},val)
                        item.s_cant1 = 0 // Cantidad de items a mover

                        if(item.q_envic == "1")
                            s_totcant += parseFloat(item.s_cant)

                        if(item.q_envic == "1" && item.c_comb == "")
                            peditem.push(item)
                    })

                    this.currentView = 'pedido-moveritems'
                    this.currentProps = {
                        _Pedido: this.Pedido,
                        _PedItem: peditem,
                        _s_totcant: s_totcant
                    }
                }
            },
            hideMdlPedidoMoverItems(resul){
                this.currentView = null;
                this.currentProps = null;

                if(resul.hasOwnProperty('PedItem')){
                    /*if( Object.keys(resul.PedItem).length == 0 ){
                        this.PedItem.splice(this.active_item, 1);
                    }else{*/
                        resul.PedItem.forEach((val)=>{
                            let s_cant, item
                            this.PedItem.forEach((val1,i2)=>{
                                if (val1.n_item == val.n_item) {
                                    s_cant = parseFloat(val1.s_cant) - parseFloat(val.s_cant1)
                                    item = i2

                                    val1.s_cant = s_cant
                                    val1.s_bimp = parseFloat(val1.s_vent) * parseFloat(val1.s_cant)
                                }
                            })

                            if (s_cant == 0) {
                                this.PedItem.splice(item, 1);
                            }
                        })
                    // }
                }

                /*if(resul.hasOwnProperty('PedItem')){
                    if( Object.keys(resul.PedItem).length == 0 ){
                        this.PedItem.splice(this.active_item, 1);
                    }else{
                        this.PedItem[this.active_item].s_cant = resul.PedItem.s_cant
                        this.PedItem[this.active_item].s_bimp = resul.PedItem.s_bimp
                    }
                }*/
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

                if (resul.model == 'peditemCant') {
                    resul.val = $.isNumeric(resul.val) ? parseFloat(resul.val).toFixed(2) : (0).toFixed(2)

                    let PedItem = this.PedItem[this.active_item]
                    if(PedItem != undefined && PedItem.q_envic != 1 && resul.val > 0){
                        PedItem.s_cant = resul.val
                        PedItem.s_bimp = PedItem.s_cant * parseFloat(PedItem.s_vent)
                    }

                }else{
                    if (resul.model == 'infoItem.s_vent') {
                        resul.val = isNaN(resul.val) || (resul.val).trim() == "" ? '0.0000' : parseFloat(resul.val).toFixed(4)
                    }

                    if(resul.hasOwnProperty('val')){
                        let val = resul.model.split(".");

                        if (val.length == 1) {
                            this.$data[val[0]] = resul.val
                        }else{
                            this.$data[val[0]][val[1]] = resul.val
                        }
                    }
                }
            },
            // Eliminar Item
            showMdlElimItem(){
                /*if(localStorage.getItem('q_elimitem') != 1){
                    swal('','No tiene privilegio para realizar esta acción','info')
                }else if(this.PedItem[this.active_item] == undefined){
                    swal('','Debe seleccionar item','info')
                }else if(this.PedItem[this.active_item].c_comb != ""){
                    swal('','No se puede realizar esta acción con combos','info')
                }else if(this.PedItem[this.active_item].q_envic == "0"){
                    swal('','Item no fue enviado a cocina','info')
                }else{
                    this.currentView = 'pedido-elimitem'
                    this.currentProps = {
                        _Pedido: this.Pedido,
                        _PedItem: this.PedItem[this.active_item],
                        _n_items: this.PedItem.length
                    }
                }*/

                if(localStorage.getItem('q_elimitem') != 1){
                    swal('','No tiene privilegio para realizar esta acción','info')
                }else{
                    let peditem = [],
                        s_totcant = 0
                    this.PedItem.forEach((val)=>{
                        let item = Object.assign({},val)
                        item.s_cant1 = 0 // Cantidad de items a mover

                        if(item.q_envic == "1")
                            s_totcant += parseFloat(item.s_cant)

                        if(item.q_envic == "1" && item.c_comb == "")
                            peditem.push(item)
                    })

                    this.currentView = 'pedido-elimitem'
                    this.currentProps = {
                        _Pedido: this.Pedido,
                        _PedItem: peditem,
                        _s_totcant: s_totcant
                    }
                }
            },
            hideMdlPedidoElimItem(resul){
                this.currentView = null;
                this.currentProps = null;

                if(resul.hasOwnProperty('PedItem')){
                    // Impresion de ticket de eliminacion
                    // Buscamos productos que pertenecen a combos y buscamos el codigo de linea
                    var combodet = [],
                        items = [];
                    for (let item of resul.PedItem) {
                        /*if (item.c_comb.length > 0) {
                            for (let producto of this.CombosDet) {
                                if (producto.c_comb == item.c_comb) {
                                    var item_comb = JSON.parse(JSON.stringify(item));
                                    item_comb.c_prod = producto.c_prod;
                                    item_comb.k_medi = producto.k_medi;
                                    item_comb.s_cant = parseFloat(producto.s_cant) * parseFloat(item_comb.s_cant);
                                    item_comb.s_vent = producto.s_vent;
                                    combodet.push(item_comb);
                                }
                            }
                        }else{*/
                            // items.push(item);
                            // let item = Object.assign({}, this.PedItem[this.active_item])
                            // item.s_cant = resul.PedItem1.s_cant
                            item.s_cant = item.s_cant1
                            item.l_obse = resul.PedItem1.l_obse
                            items.push(item)
                        // }
                    }

                    /*combodet.map(function(item,index){
                        for (let prod of this.ProductosXMediprods) {
                            if (prod.c_prod == item.c_prod && prod.k_medi == item.k_medi) {
                                item.c_line = prod.c_line;
                                item.l_prod = prod.l_prod;
                                item.l_impr1 = prod.l_impr1
                                item.l_impr2 = prod.l_impr2
                                break;
                            }
                        }                            
                    }.bind(this))*/

                    var combodet_items = combodet.concat(items);
                    
                    // =======
                    
                    // Agrupamos los items del pedido por nombre de impresora
                    var imprimir = {};
                    let prods_impr = [] // Productos con impresora independiente
                    combodet_items.map(function(item,index){
                        if(item.l_impr1 == undefined || item.l_impr1.trim().length == 0/* || item.l_impr1 == null*/){
                            for(let LineProd of this.LineProds){
                                if( item.q_coci=='1' /*&& item.q_envic == '0'*/){
                                    if (LineProd.c_line == item.c_line) {

                                        if (LineProd.l_impr in imprimir == false) {
                                            imprimir[`${LineProd.l_impr}`] = [];
                                        }

                                        imprimir[LineProd.l_impr].push(item);
                                        break;
                                    }
                                }
                            }
                        }else if(item.q_coci=='1'/* && item.q_envic == '0'*/){
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
                    var n_comp = 'Pedido N: ' + parseInt(String(this.Pedido.n_comp).substring(6,10)),
                        c_ubic = this.Pedido.c_ubic,
                        c_mesa = this.Pedido.c_mesa,
                        n_pers = this.Pedido.n_pers;

                    var l_vend = localStorage.getItem('l_vend'),
                        fecha = new Date().toLocaleString();

                    var strPeditems = '';
                    var contador_imprimir = 0;
                    for (var key in imprimir) {
                        if (contador_imprimir == 0) {
                            strPeditems += `${key}\r\n===================================\r\n||          [ANULACION]          ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nAnulacion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                        }else{
                            strPeditems += `\r\nCORTAR\r\n${key}\r\n===================================\r\n||          [ANULACION]          ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nAnulacion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                        }

                        for (var item of imprimir[key]) {
                            var /*l_prod = (item.l_prod.padEnd(30,' ')).substring(0,30),*/
                                s_cant = (parseFloat(item.s_cant)).toString().padStart(4,' ');

                                // strPeditems += `${l_prod} ${s_cant}\r\n`;
                                let l_prod1 = ('(ANULAR)'+item.l_prod).match(/.{1,30}/g)
                                l_prod1.forEach((element, index)=>{
                                    // strPeditems += index == 0 ? `${element.padEnd(30,' ')} ${s_cant}\r\n` : `${element.padEnd(30,' ')}     \r\n`
                                    strPeditems += index == 0 ? `${s_cant} ${element.padEnd(30,' ')}\r\n` : `     ${element.padEnd(30,' ')}\r\n`
                                })

                                // Si hay observacion se agregara el texto
                                if(item.l_obse != null && item.l_obse.length > 0){
                                    var l_obse = item.l_obse.toLowerCase().match(/.{1,28}/g).join('\r\n     ');
                                    strPeditems += `     ->${l_obse}\r\n`;
                                }
                        }

                        strPeditems += `===================================\r\n||          [ANULACION]          ||\r\n===================================\r\n\r\n\r\n\r\n\r\n\r\n`;
                        contador_imprimir++;
                    }

                    // Generamos formato de impresion para productos con impresora independiente
                    prods_impr.forEach((item)=>{
                        let str_head = '',
                            str_head2 = '',
                            str_footer = ''

                        // Head
                        if (contador_imprimir == 0) {
                            str_head += `${item.l_impr1}\r\n===================================\r\n||          [ANULACION]          ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nAnulacion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;

                        }else{
                            str_head += `\r\nCORTAR\r\n${item.l_impr1}\r\n===================================\r\n||          [ANULACION]          ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nAnulacion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                        }
                        
                        str_head2 += `\r\nCORTAR\r\n${item.l_impr2}\r\n===================================\r\n||          [ANULACION]          ||\r\n===================================\r\n          ${n_comp}\r\nSala:(${this.Pedido.l_ubic}) ${c_ubic} Mesa:(${this.Pedido.l_mesa}) ${c_mesa}\r\nPersonas: ${n_pers}\r\nVendedor: ${this.Pedido.l_vend}\r\nAnulacion: ${l_vend}\r\nFecha: ${fecha}\r\n\r\nCant Plato                         \r\n-----------------------------------\r\n`;
                        // 
                        
                        var /*l_prod = (item.l_prod.padEnd(30,' ')).substring(0,30),*/
                            s_cant = (parseFloat(item.s_cant)).toString().padStart(4,' ');

                            // str_footer += `${s_cant} ${l_prod}\r\n`;
                            let l_prod1 = ('(ANULAR)'+item.l_prod).match(/.{1,30}/g)
                            l_prod1.forEach((element, index)=>{
                                // str_footer += index == 0 ? `${element.padEnd(30,' ')} ${s_cant}\r\n` : `${element.padEnd(30,' ')}     \r\n`
                                str_footer += index == 0 ? `${s_cant} ${element.padEnd(30,' ')}\r\n` : `     ${element.padEnd(30,' ')}\r\n`
                            })

                            // Si hay observacion se agregara el texto
                            if(item.l_obse != null && item.l_obse.length > 0){
                                var l_obse = item.l_obse.toLowerCase().match(/.{1,28}/g).join('\r\n     ');
                                str_footer += `     ->${l_obse}\r\n`;
                            }

                        str_footer += `===================================\r\n||          [ANULACION]          ||\r\n===================================\r\n\r\n\r\n\r\n\r\n\r\n`;

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

                        // Clipboard
                        /*document.getElementById('txtimpr').value = "";
                        document.getElementById('txtimpr').value = strPeditems;
                        var copyText = document.getElementById('txtimpr');
                        copyText.focus();
                        copyText.select();
                        var res = document.execCommand("copy");
                        document.getElementById('txtimpr').value = "";
                        if(res){
                            window.location = 'ec34printicket:';
                        }else{
                            toastr.error('No se pudo copiar formato de impresión.')
                        }*/
                        //
                    }

                    // Actualizamos lista de items
                    /*if( Object.keys(resul.PedItem).length == 0 ){
                        this.PedItem.splice(this.active_item, 1);
                    }else{
                        this.PedItem[this.active_item].s_cant = resul.PedItem.s_cant
                        this.PedItem[this.active_item].s_bimp = resul.PedItem.s_bimp
                    }*/
                    resul.PedItem.forEach((val)=>{
                        let s_cant, item
                        this.PedItem.forEach((val1,i2)=>{
                            if (val1.n_item == val.n_item) {
                                s_cant = parseFloat(val1.s_cant) - parseFloat(val.s_cant1)
                                item = i2

                                val1.s_cant = s_cant
                                val1.s_bimp = parseFloat(val1.s_vent) * parseFloat(val1.s_cant)
                            }
                        })

                        if (s_cant == 0) {
                            this.PedItem.splice(item, 1);
                        }
                    })
                }
            },
            // Busca pedidos
            pedidoBusc(tab){
                this.currentView = 'pedido-busc'
                this.currentProps = {
                    _tab: tab
                }
            },
            hideMdlPedidosbusc(resul){
                if(resul.hasOwnProperty('pedido')){
                    this.Pedido.l_ubic = resul.pedido.l_ubic
                    this.Pedido.c_ubic = resul.pedido.c_ubic
                    this.Pedido.l_mesa = resul.pedido.l_mesa

                    if(resul.pedido.q_prcue != 1){
                        this.currentView = null
                        this.currentProps = null
                        
                        this.devPedido(resul.pedido.c_mesa, resul.l_clav)
                    }else{
                        this.devPedido(resul.pedido.c_mesa, resul.l_clav, 1)
                    }
                }else{
                    this.currentView = null
                    this.currentProps = null
                }
            },
            // Modal pedidos agrupados(para zapatilla)
            showMdlPedAgrup(){
                // Para zapatilla
                if (this.org.k_empr != '5') {
                    swal('','Modulo no esta habilitado para empresa','info')
                    return false;
                }
                // 

                this.currentView = 'pedido-agrup'
                this.currentProps = {
                    _Pedido: this.Pedido,
                    _PedItem: this.PedItem
                }
            },
            hideMdlPedidoAgrup(PedItem){
                this.Pedidos.forEach((val)=>{
                    if(val.n_comp==this.Pedido.n_comp && this.Pedido.q_aten==1){
                        val.q_aten=1

                        localStorage.setItem('Pedidos',JSON.stringify(this.Pedidos));
                    }
                })

                this.PedItem = PedItem
                this.currentView = null
                this.currentProps = null
            },
            cambMozo(){
                if(localStorage.getItem('q_camvend') != 1){
                    swal('','No tiene privilegio para realizar esta acción','info')
                    return false
                }
                
                this.currentView = 'CAMBMOZOPRINC';
                this.currentProps = {
                    _n_comp: this.Pedido.n_comp,
                    _c_vend: this.Pedido.c_vend,
                }
            },
            hideCambMozoPrinc(vend){
                this.currentView = null
                this.currentProps = null

                if (vend != undefined) {
                    this.Pedido.c_vend = vend.c_vend
                    this.Pedido.l_vend = vend.l_vend
                }
            },
        },
        watch: {
            'infoItem.n_prec': function(newVal, oldVal){
                if (newVal=='1') {
                    this.infoItem.s_vent=this.infoItem.s_pre1;
                }else if(newVal=='2'){
                    this.infoItem.s_vent=this.infoItem.s_pre2;
                }else if(newVal=='3'){
                    this.infoItem.s_vent=this.infoItem.s_pre3;
                }else if(newVal=='4'){
                    this.infoItem.s_vent=this.infoItem.s_pre4;
                }else if(newVal=='5'){
                    this.infoItem.s_vent=this.infoItem.s_pre5;
                }
            },
            'Pedido.n_comp': function(){
                this.active_item = null;
            },
            txtBuscProd(newVal){
                if (newVal.trim().length > 0) {
                    this.filterProductos.l_line = ''
                    this.l_comb = 'BUSQUEDA'
                    $('#pills-tabLineProd a[href="#pills-busc"]').tab('show')
                }else{
                    this.l_comb = ''
                    $('#pills-tabLineProd a[href="#pills-lineas"]').tab('show')
                }
            },
            ProdObseFilter(newVal){
                this.Prodobse1 = []
                newVal.some((val)=>{
                    if (this.Prodobse1.length < 4) {
                        this.Prodobse1.push(val)
                    }
                })

                let n_items = this.Prodobse1.length
                if(n_items < 4){
                    const length = 4 - n_items
                    for (var i = 0; i < length; i++) {
                        this.Prodobse1.push({c_line:"",l_obse:""})
                    }
                }
            },
            filteredSubLineProdsxLine(newVal){
                this.SubLineProd1 = []

                if (newVal.length == 0) {
                    return false;
                }

                newVal.some((val)=>{
                    if (this.SubLineProd1.length < 9) {
                        this.SubLineProd1.push(val)
                    }
                })

                let n_items = this.SubLineProd1.length
                if(n_items < 9){
                    const length = 9 - n_items
                    for (var i = 0; i < length; i++) {
                        this.SubLineProd1.push({c_line: "",c_subl: "--",l_subl: ""})
                    }
                }
            }
        }
    }
</script>