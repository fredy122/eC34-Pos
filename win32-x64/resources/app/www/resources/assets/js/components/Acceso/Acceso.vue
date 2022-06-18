<script>
    export default {
        data: function(){
            return{
                currentView: null,
                currentProps: null,
                errors: null,
                usuario:{
                    n_ruc: '',
                    Usuario: '',
                    l_pass: ''
                },
                configuracion:{
                    q_tactil: false
                }
            }
        },
        mounted() {
            this.configuracion.q_tactil = JSON.parse(localStorage.getItem('q_tactil'));

            //
            if ( localStorage.getItem('n_ruc') != null ) {
                this.usuario.n_ruc = localStorage.getItem('n_ruc');
                this.usuario.Usuario = localStorage.getItem('Usuario');
                this.usuario.l_pass = localStorage.getItem('l_pass');
                this.$el.querySelector('#Usuario').focus();
            }else{
                this.$el.querySelector('#n_ruc').focus();
            }

            if (this.configuracion.q_tactil == true) {
                window.location = 'ec34tabtip:';
            }

            localStorage.clear();
            if ( this.usuario.n_ruc.length > 0 ) {
                localStorage.setItem('n_ruc',this.usuario.n_ruc);
                localStorage.setItem('Usuario', this.usuario.Usuario.toUpperCase() );
                localStorage.setItem('l_pass', this.usuario.l_pass );
            }
        },
        methods: {
            postLogin: function(){

                $('*').blur();
                $('body').loadingModal({'animation': 'fadingCircle'});
                this.errors = null;

                var instanceAxios = axios.create({baseURL:''});
                instanceAxios.post('/acceso',this.usuario)
                .then(function(response){

                    //Eliminando Tablas alamacenadas en localStorage del modulo de pedidos
                    localStorage.clear();
                    //
                    
                    $('body').loadingModal('destroy');
                    localStorage.setItem('n_ruc',this.usuario.n_ruc);
                    
                    localStorage.setItem('Usuario', this.usuario.Usuario.toUpperCase() );
                    localStorage.setItem('l_pass', this.usuario.l_pass );
                    localStorage.setItem('c_vend',response.data.usuario.c_vend);localStorage.setItem('c_vend1',response.data.usuario.c_vend);
                    localStorage.setItem('l_vend',response.data.usuario.l_vend);localStorage.setItem('l_vend1',response.data.usuario.l_vend);
                    localStorage.setItem('k_tipp',response.data.usuario.k_tipp);
                    localStorage.setItem('p_pudefecto',response.data.usuario.p_pudefecto);
                    localStorage.setItem('q_movitem',response.data.usuario.q_movitem);
                    localStorage.setItem('q_elimitem',response.data.usuario.q_elimitem);
                    localStorage.setItem('q_reimpitems',response.data.usuario.q_reimpitems);
                    localStorage.setItem('c_mesac',response.data.usuario.c_mesac);
                    localStorage.setItem('q_anulped',response.data.usuario.q_anulped);
                    localStorage.setItem('q_camvend',response.data.usuario.q_camvend);
                    localStorage.setItem('q_editpv',response.data.usuario.q_editpv);
                    localStorage.setItem('q_vera',response.data.usuario.q_vera);
                    localStorage.setItem('q_tactil',this.configuracion.q_tactil);

                    // Sisprop
                    localStorage.setItem('ped_gencomp',response.data.sisprop.ped_gencomp);
                    localStorage.setItem('q_npers',response.data.sisprop.q_npers);
                    localStorage.setItem('q_pregimpcue',response.data.sisprop.q_pregimpcue);
                    localStorage.setItem('q_pregenvped',response.data.sisprop.q_pregenvped);
                    localStorage.setItem('icbper_v',response.data.sisprop.icbper_v);

                    // SispropPDV
                    localStorage.setItem('SispropPDV',JSON.stringify(response.data.SispropPDV));

                    // Organizacion
                    localStorage.setItem('org',JSON.stringify(response.data.org));

                    // Apertcaja
                    localStorage.setItem('Apertcaja',JSON.stringify(response.data.Apertcaja));

                    window.location = '/app';

                }.bind(this))
                .catch(function(error){
                    $('body').loadingModal('destroy');

                    if(error.message == "Network Error"){
                        swal({
                            title: 'Mensaje',
                            text: "Error de red",
                            type: 'error',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                    }else if(error.response.status == 422){
                        this.errors = error.response.data.errors;
                        for( var key in  error.response.data.errors ){
                            this.usuario.l_pass = '';
                            this.$el.querySelector('#'+key).focus();
                            break;
                        }
                    }else{
                        swal({
                            title: 'Mensaje',
                            text: error.response.data.message,
                            type: 'error',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                    }
                }.bind(this));
            },
            mdlShowConf: function(){
                this.configuracion.q_tactil = JSON.parse(localStorage.getItem('q_tactil'));
                $('#mdlConf').modal('show');
            },
            setConf: function(){
                localStorage.setItem('q_tactil',this.configuracion.q_tactil);
                $('#mdlConf').modal('hide');
                location.reload();
            },
            // Teclado Virtual
            showMdlTeclado(model,value,type,tecl){
                this.currentView = 'teclado'
                this.currentProps = {
                    _model: model,
                    _value: value,
                    _type: type,
                    _tecl: tecl
                }
            },
            hideMdlTeclado(resul){
                this.currentView = null;
                this.currentProps = null;

                if(resul.hasOwnProperty('val')){
                    let val = resul.model.split(".");

                    if (val.length == 1) {
                        this.$data[val[0]] = resul.val
                    }else{
                        this.$data[val[0]][val[1]] = resul.val
                    }
                }
            },
        }
    }
</script>