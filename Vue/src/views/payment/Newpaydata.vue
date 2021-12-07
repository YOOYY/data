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
      id: "newpaydata",
      apiUrl: "payment/newpaydata",
      chartTitle: "新增付费数据",
      dataMap: [
        { label: "日期", value: "date" },
        { label: "渠道", value: "channel" },
        { label: "金额", value: "num" }
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
#newpaydata {
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