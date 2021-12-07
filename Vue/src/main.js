import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

//Element UI
import ElementUI from 'element-ui';
Vue.use(ElementUI);

// echarts
// 引入基本模板
let echarts = require('echarts/lib/echarts')
// 引入柱状图组件
require('echarts/lib/chart/line')
// 引入提示框和title组件
require('echarts/lib/component/tooltip')
require('echarts/lib/component/title')
require('echarts/lib/component/legend')
Vue.prototype.$echarts = echarts

//工具函数
import util from './common/util.vue'
Vue.use(util);

Vue.config.productionTip = true

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
