import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'
import Routes from './routes'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import { BPagination } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import { BTable } from 'bootstrap-vue'

Vue.component('b-table', BTable)
Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.component('b-pagination', BPagination)
Vue.config.productionTip = false

const router = new VueRouter({
  mode: 'history',
  routes: Routes
})

new Vue({
  render: h => h(App),
  router:router
}).$mount('#app')
