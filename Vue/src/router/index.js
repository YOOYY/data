import Vue from 'vue'
import Vuex from '../store/'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
    {
        path: '/login',
        name: 'Login',
        // route level code-splitting
        // this generates a separate chunk (about.[hash].js) for this route
        // which is lazy-loaded when the route is visited.
        component: () => import(/* webpackChunkName: "login" */ '../views/index/Login.vue')
    },
    {
        path: '/',
        name: 'index',
        component: () => import(/* webpackChunkName: "index" */ '../views/index/Index.vue'),
        children:[
            {
                path: '/admin',
                name: 'admin',
                component: () => import(/* webpackChunkName: "admin" */ '../views/admin/Admin.vue')
            },
            {
                path: '/privilege',
                name: 'privilege',
                component: () => import(/* webpackChunkName: "privilege" */ '../views/admin/Privilege.vue')
            },
            {
                path: '/onlinetime',
                name: 'onlinetime',
                component: () => import(/* webpackChunkName: "onlinetime" */ '../views/online/Onlinetime.vue')
            },
            {
                path: '/newpaydata',
                name: 'newpaydata',
                component: () => import(/* webpackChunkName: "onlinetime" */ '../views/payment/Newpaydata.vue')
            },
            {
                path: '/paydata',
                name: 'paydata',
                component: () => import(/* webpackChunkName: "paydata" */ '../views/payment/Paydata.vue')
            },
            {
                path: '/paytratio',
                name: 'paytratio',
                component: () => import(/* webpackChunkName: "paytratio" */ '../views/payment/Paytratio.vue')
            },
            {
                path: '/excel',
                name: 'excel',
                component: () => import(/* webpackChunkName: "excel" */ '../views/excel/Index.vue')
            },
            {
                path: '/channel',
                name: 'channel',
                component: () => import(/* webpackChunkName: "channel" */ '../views/admin/Channel.vue')
            },
            {
                path: '/adddata',
                name: 'adddata',
                component: () => import(/* webpackChunkName: "adddata" */ '../views/user/Adddata.vue')
            },
            {
                path: '/iosdata',
                name: 'iosdata',
                component: () => import(/* webpackChunkName: "iosdata" */ '../views/user/Iosdata.vue')
            },
            {
                path: '/todaypay',
                name: 'todaypay',
                component: () => import(/* webpackChunkName: "todaypay" */ '../views/user/Todaypay.vue')
            },
            {
                path: '/daypay',
                name: 'daypay',
                component: () => import(/* webpackChunkName: "daypay" */ '../views/user/Daypay.vue')
            },
            {
                path: '/retention',
                name: 'retention',
                component: () => import(/* webpackChunkName: "retention" */ '../views/user/Retention.vue')
            },
            {
                path: '/monthpayment',
                name: 'monthpayment',
                component: () => import(/* webpackChunkName: "retention" */ '../views/user/Monthpayment.vue')
            },
        ]
      }
]

const router = new VueRouter({
    routes
})

router.beforeEach((to, from, next) => {
    if(to.name !== "Login" && Vuex.state.admin.adminID === -1){
        next({name:"Login"})
    }else{
        next();
    }
})
export default router;