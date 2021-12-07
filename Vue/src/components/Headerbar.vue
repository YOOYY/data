<template>
  <el-header>
    <el-menu
      mode="horizontal"
      background-color="#18bc9c"
      text-color="#f6f6f6"
      active-text-color="#f6f6f6"
      class="headerbar"
      @select="switchPlatforms"
      default-active="0"
    >
      <el-menu-item
        v-for="(item,index) in platform"
        :key="index"
        :index="item.id"
        style="visibility:hidden"
      >{{item.title}}</el-menu-item>
    </el-menu>
  </el-header>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  methods: {
    switchPlatforms(key) {
      console.log(key);
    },
    ...mapMutations(["updateState"])
  },
  computed: {
    ...mapState(["platform", "currentPlatform"])
  }
};
</script>

<style lang='scss'>
.el-header {
  padding: 0;
  .headerbar {
    .el-menu-item {
      height: 50px;
      min-width: 70px;
      line-height: 50px;
      color: white;
      padding: 0 5px;
      border: transparent solid;
      border-width: 0 1px;
      text-align: center;
      cursor: pointer;
    }
    .is-active {
      background: #15a589 !important;
    }
    div:hover {
      background: #15a589;
      border-color: rgb(19, 155, 130);
    }
  }
}
</style>
