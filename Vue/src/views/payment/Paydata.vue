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
      id: "paydata",
      apiUrl: "payment/paydata",
      chartTitle: "付费数据",
      dataMap: [
        { label: "日期", value: "date" },
        { label: "付费人数(新)", value: "user" },
        { label: "付费次数(新)", value: "num" },
        { label: "付费金额(新)", value: "amount" },
        { label: "付费人数(活跃玩家)", value: "payuser" },
        { label: "付费次数(活跃玩家)", value: "paynum" },
        { label: "付费金额(活跃玩家)", value: "payamount" },
        { label: "付费人数(首次付费)", value: "fuser" },
        { label: "付费次数(首次付费)", value: "fnum" },
        { label: "付费金额(首次付费)", value: "famount" }
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
#paydata {
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