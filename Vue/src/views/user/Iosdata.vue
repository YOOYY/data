<template>
  <v-view
    :id="id"
    :apiUrl="apiUrl"
    :chartTitle="chartTitle"
    :dataMap="dataMap"
    :tableHeight="tableHeight"
    :sumBottomShow="true"
  ></v-view>
</template>

<script>
import View from "../../components/TablePanel.vue";

export default {
  data() {
    return {
      id: "iosdata",
      apiUrl: "user/iosdata",
      chartTitle: "Ios用户数据",
      dataMap: [
        { label: "日期", value: "date" },
        { label: "新增用户", value: "user" },
        { label: "用户充值", value: "recharge" }
      ],
      tableHeight: 650
    };
  },
  components: {
    "v-view": View
  }
};
</script>

<style lang="scss">
#iosdata {
  .el-table {
    width: 100%;
    float: left;
    margin-bottom: 100px;
  }
  #myChart {
    width: 100%;
    height: 650px;
    float: left;
  }
}
</style>