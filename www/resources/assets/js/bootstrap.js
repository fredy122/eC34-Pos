
//window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    // Bootstrap V4 requiere de popper.js
  	window.Popper = require('../../../node_modules/popper.js/dist/umd/popper.js');
    require('../../../node_modules/bootstrap/dist/js/bootstrap.min.js');

    require('../../../node_modules/jquery-loadingModal/js/jquery.loadingModal.min.js');

    require('../../../node_modules/bootstrap-modal-fullscreen/dist/bs-modal-fullscreen.min.js');

    require('./plugins/jquery-ui/jquery-ui.js');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.baseURL = '/app';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.interceptors.request.use(function (config) {
    $('body').loadingModal({'animation': 'fadingCircle'});

    // Do something before request is sent
    return config;
}, function (error) {
    // Do something with request error
    return Promise.reject(error);
});

// Add a response interceptor
axios.interceptors.response.use(function (response) {
    $('body').loadingModal('destroy');

    // Do something with response data
    return response;
}, function (error) {

    $('body').loadingModal('destroy');

    if(error.message == "Network Error"){
        swal({
            title: 'Mensaje',
            text: "Error de red",
            type: 'error',
        });
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
        var mensaje = '';
        var name_field = '';
        for( var key in  error.response.data.errors ){
            mensaje = error.response.data.errors[key][0];
            name_field = key;
            break;
        }

        swal({
            title: 'Mensaje',
            text: mensaje,
            type: 'error',
        }).then(function () {
        	$('#'+name_field).focus();
        }.bind(this));

    }else{
        swal({
            title: 'Mensaje',
            text: error.response.data.message,
            type: 'error',
        });
    }
    
    // Do something with response error
    return Promise.reject(error);
});

/*
 * SweetAlert2
*/
window.swal = require('../../../node_modules/sweetalert2/dist/sweetalert2.js');
swal.setDefaults({
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar',
    animation: false,
    allowOutsideClick: false,
    allowEscapeKey: false,
});

/*
 * toastr
*/
window.toastr = require('toastr');
toastr.options = {"progressBar": true,"positionClass": "toast-bottom-left", "timeOut": "2000","preventDuplicates": true};

//window.saveAs = require('file-saver');
//window.saveAs = require('../../../node_modules/file-saver/FileSaver.min.js');

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

/*
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
*/

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

$.fn.modal.Constructor.prototype._enforceFocus = function() {};