import Vue from 'vue'
import App from './App'
import { request } from './utils/request'

Vue.config._mpTrace = true
Vue.config.productionTip = false
Vue.prototype.$request = request
Vue.prototype.$baseUrl = 'https://www.youlings.cn'
Vue.prototype.Bus = new Vue()
App.mpType = 'app'

const app = new Vue(App)
app.$mount()
