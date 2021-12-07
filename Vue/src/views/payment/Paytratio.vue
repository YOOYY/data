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
      id: "paytratio",
      apiUrl: "payment/paytratio",
      chartTitle: "付费转化",
      dataMap: [
        { label: "日期", value: "date" },
        { label: "今日新增玩家", value: "adduser" },
        { label: "首日付费人数", value: "dnum" },
        { label: "首周付费人数", value: "wnum" },
        { label: "首月付费人数", value: "mnum" },
        { label: "首日付费率", value: "drate" },
        { label: "首周付费率", value: "wrate" },
        { label: "首月付费率", value: "mrate" }
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
#paytratio {
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