require('./bootstrap');
window.Vue = require('vue').default;

Vue.component('home', require('./components/site/home.vue').default);

import Vue from 'vue'
import App from './App.vue'

import Axios from 'axios'
Vue.prototype.$http = Axios;

import {store} from './store/index'

import "vue-multiselect/dist/vue-multiselect.min.css";
import './../assets/css/remixicon.css'

import 'vue2-dropzone/dist/vue2Dropzone.min.css'

import 'bootstrap-vue/dist/bootstrap-vue.css'


import './../assets/css/font-awesome.min.css';
import "./../assets/css/fonts.css"


import './../assets/css/bootstrap.min.css'

import "./../assets/css/app.min.css"


import "./../assets/css/style.css"

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)


import Echo from 'laravel-echo'

window.io = require('socket.io-client')

// Socket.io
window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: window.location.hostname + ':6001'
});



import VueTelInput from "vue-tel-input";

Vue.use(VueTelInput);


import imageViewer  from 'image-viewer-vue'
Vue.use(imageViewer)

import vuescroll from 'vuescroll';
Vue.use(vuescroll);

Vue.prototype.$vuescrollConfig = {
  bar: {
    background: '#bab3ae',
    size:'5px'
  },
  scrollPanel: {
    initialScrollY: false,
    initialScrollX: false,
    scrollingX: false,
    scrollingY: true,
    speed: 3000,
    easing: undefined,
    verticalNativeBarPos: 'left'
  }
};


import "./filters";






import ScrollLoader from 'vue-scroll-loader'

Vue.use(ScrollLoader);

 

Vue.config.productionTip = false

new Vue({
  render: h => h(App),
  store
}).$mount('#app')
