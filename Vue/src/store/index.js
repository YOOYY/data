import Vue from 'vue'
import Vuex from 'vuex'
import Axios from '../common/axios.js'

Vue.use(Vuex)
var end = new Date(new Date().toLocaleDateString()).getTime(),
    start = end - 86400000;
export default new Vuex.Store({
    state: {
        title:'数据管理系统',
        admin:{
            'adminID':-1,
            'nickName':'',
            'channelGroup':[],
            'privilege':[],
            'userImg':'/static/img/user.jpg'
        },
        sideMenu: [
            {title:'用户管理',url:'admin',icon:'el-icon-menu'},
            {title:'用户分组',icon:'el-icon-location',content:[{title:'权限组',url:'privilege',icon:'el-icon-menu'},{title:'渠道组',url:'channel',icon:'el-icon-menu'}]},
            {title:'玩家数据',icon:'iconfont icon-tubiao',content:[{title:'新增数据',url:'adddata',icon:'el-icon-menu'},{title:'ios用户数据',url:'iosdata',icon:'el-icon-menu'},{title:'用户留存',url:'retention',icon:'el-icon-menu'},{title:'每日充值',url:'daypay',icon:'el-icon-menu'},{title:'今日充值',url:'todaypay',icon:'el-icon-menu'},{title:'每月充值',url:'monthpayment',icon:'el-icon-menu'}]},
            {title:'付费数据',icon:'el-icon-menu',content:[{title:'付费数据',url:'paydata',icon:'el-icon-menu'},{title:'付费转化',url:'paytratio',icon:'el-icon-menu'},{title:'新增付费数据',url:'newpaydata',icon:'el-icon-menu'}]},
            {title:'在线数据',icon:'el-icon-menu',content:[{title:'在线时段分布',url:'onlinetime'}]},
            {title:'Excel生成',url:'excel',icon:'el-icon-menu'}
        ],
        platform: [
            {title:'牌缘',id:'paiyuan'},
            {title:'牌缘3D',id:'paiyuan3D'},
            {title:'掼蛋',id:'guandan'},
        ],
        currentPlatform: 'paiyuan3D'
    },
    getters: {

    },
    mutations: {
        updateState(state,payload) {
            if(payload.attr){
                state[payload.name][payload.attr] = payload.data;
            }else{
                state[payload.name] = payload.data;
            }
        },
    },
    actions: {
        login ({dispatch,commit},{name,password}) {
            //请求用户数据
            return Axios.post('index/login', {
                "name": name,
                "password": password
            })
            .then((data) => {
                if(data === true){
                    return dispatch('updateAdmin').then(admin=>{
                        commit('updateState',{
                            name:'admin',
                            data:admin
                        });
                        return admin;
                    });
                }else{
                    return Promise.reject('登录名或密码错误')
                }
            })

        },
        updateAdmin(){
            let admin = {};
            return Axios.get('index/getLogininfo')
            .then((data) => {
                admin = data;
                return Axios.get('index/getcgroup')
            })
            .then((data) => {
                admin.channelGroup = data;
                return admin;
                // return Axios.post('index/getplist', {
                //     id:admin.privilegeID
                // })
            })
            // .then((data) => {
            //     admin.privilege = data;
            //     return Axios.post('index/getpgroup', {
            //         id:admin.privilegeID
            //     })
            // })
            // .then((data) => {
            //     admin.privilegeGroup = data;
            //     return admin;
            // });
        }
    }
})