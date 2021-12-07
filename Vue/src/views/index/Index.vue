<template>
  <el-container id="cover">
    <el-aside :width="width">
      <v-sideBar></v-sideBar>
    </el-aside>

    <el-container direction="vertical">
      <v-headerBar></v-headerBar>
      <el-main>
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
import Sidebar from "../../components/Sidebar.vue";
import Headerbar from "../../components/Headerbar.vue";

export default {
  data() {
    return {
      width: "230px"
    };
  },
  components: {
    "v-sideBar": Sidebar,
    "v-headerBar": Headerbar
  }
};
</script>

<style lang='scss'>
.el-main {
  min-height: 100vh;
}
.el-aside {
  color: #333;
  overflow: hidden;
}
.el-container {
  background: #f1f4f6;
}
</style>
