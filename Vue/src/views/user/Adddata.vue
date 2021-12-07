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
      id: "adddata",
      apiUrl: "user/adddata",
      chartTitle: "新增数据",
      dataMap: [
        { label: "日期", value: "date" },
        { label: "设备激活", value: "user" },
        { label: "新增账户", value: "machine" },
        { label: "新增设备", value: "activeuser" },
        { label: "转化率", value: "rate" }
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
#adddata {
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