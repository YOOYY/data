<template>
  <el-menu
    :default-active="admin.privilege[0]"
    background-color="#222d32"
    text-color="#b8c7ce"
    active-text-color="#fff"
    :default-openeds="['1','2']"
    id="sidebar"
    router
  >
    <div class="logo">{{title}}</div>
    <div class="user">
      <img :src="admin['userImg']" />
      <div class="text">
        <p>{{admin['nickName']}}</p>
        <i class="success"></i>
        在线
        <i class="el-icon-delete red" title="注销" @click="logout"></i>
      </div>
    </div>

    <el-menu-item
      v-for="(item, index) in mySideMenu"
      :key="index"
      :index="item.url"
      v-if="!('content' in item)"
    >
      <i :class="item.icon"></i>
      <span slot="title">{{item.title}}</span>
    </el-menu-item>

    <el-submenu :index="index.toString()" :key="index" v-else>
      <template slot="title">
        <i :class="item.icon"></i>
        <span>{{item.title}}</span>
      </template>

      <el-menu-item :index="content.url" v-for="(content, sindex) in item.content" :key="sindex">
        <i :class="content.icon"></i>
        {{content.title}}
      </el-menu-item>
    </el-submenu>
  </el-menu>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  methods: {
    logout() {
      localStorage.removeItem("name");
      localStorage.removeItem("password");
      this.$router.push({ name: "Login" });
    }
  },
  computed: {
    mySideMenu() {
      var arr = [];
      //用户权限
      var menu = ";" + this.admin.privilege.join(";") + ";";
      var sarr = this.sideMenu,
        l = sarr.length;
      for (var i = 0; i < l; i++) {
        if (sarr[i].url) {
          if (menu.match(";" + sarr[i].url + ";")) {
            arr.push(sarr[i]);
          }
        } else {
          var sm = sarr[i].content;
          var content = sm.filter(val => {
            var flag = menu.match(";" + val.url + ";");
            if (flag) return true;
          });
          if (content.length > 0) {
            var obj = {};
            obj.content = content;
            obj.title = sarr[i].title;
            obj.icon = sarr[i].icon;
            arr.push(obj);
          }
        }
      }
      return arr;
    },
    ...mapState(["title", "admin", "sideMenu"])
  }
};
</script>

<style lang='scss'>
#sidebar {
  height: 100%;
  border: 0;

  .logo {
    height: 50px;
    text-align: center;
    line-height: 50px;
    font-size: 20px;
    font-weight: bold;
    color: white;
    background-color: #15a589;
  }
  .user {
    overflow: hidden;
    img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      padding: 10px;
      float: left;
    }
    .text {
      float: left;
      margin-left: 20px;
      color: white;
      font-size: 13px;
      p {
        margin: 8px 0 4px 6px;
        padding: 3px 0;
      }
      .red {
        margin-left: 3px;
        color: #aaa;
        cursor: pointer;
      }
    }
    .success {
      display: block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #18bc9c;
      margin: 4px 5px;
      float: left;
    }
  }
  .is-active {
    background: #1e282c !important;
    border-left: #18bc9c 4px solid;
  }
  .el-submenu {
    border-left: none;
  }
}
</style>
