<script>
    export default {
        template: '#tmpTeclado',
        props: ['_model','_type','_value','_tecl','_q_edit'],
                // _tecl => numeric, alfanum(default)
                // q_edit => 1(true),0(false)
        data: function(){
            return{
                texto: this._value != undefined ? this._value.toString().toUpperCase() : ''
            }
        },
        mounted() {
            if(this._tecl == 'numeric'){
                $('#mdlTecladoNum').modal()
            }else{
                $('#mdlTeclado').modal()
            }

            this.$refs.l_text.focus()
        },
        methods: {
            backspace(){
                this.texto = this.texto.substr(0,this.texto.length - 1)
            },
            hideMdlTeclado(params){
                $('#mdlTeclado').modal('hide')
                $('#mdlTecladoNum').modal('hide')
                this.$emit('hide-mdlteclado',params);
            },
            aceptar(){
                this.hideMdlTeclado({model: this._model, val: this.texto});
            }
        }
    }
</script>