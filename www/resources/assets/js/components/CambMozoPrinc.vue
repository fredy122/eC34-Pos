<script>
    export default {
        template: '#tmpCambMozoPrinc',
        props: ['_n_comp','_c_vend'],
        data: function(){
            return{
                Vendedor: [],
                c_vend: this._c_vend,
            }
        },
        mounted() {
            $("#CambMozoPrinc").modal()

            if(localStorage.getItem('Vendedor') != null){
                this.Vendedor = JSON.parse(localStorage.getItem('Vendedor'))
            }else{
                axios.post("/cambmozoprinc/index")
                .then((resp)=>{
                    this.Vendedor = resp.data.Vendedor
                    localStorage.setItem('Vendedor',JSON.stringify(resp.data.Vendedor));
                })
            }

        },
        methods: {
            grabar(){
                axios.post("/cambmozoprinc/grabar",{n_comp:this._n_comp,c_vend:this.c_vend})
                .then((resp)=>{
                    const vend = this.Vendedor.find((i)=>{
                        return i.c_vend == this.c_vend
                    })

                    $('#CambMozoPrinc').modal('hide');
                    this.$emit('hide-cambmozoprinc',vend);
                })
            },
            hideCambMozoPrinc(){
                $('#CambMozoPrinc').modal('hide');
                this.$emit('hide-cambmozoprinc');
            },
        }
    }
</script>