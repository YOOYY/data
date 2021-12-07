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
import View from "../../components/moreTablePanel.vue";

export default {
  data() {
    return {
      id: "daypay",
      apiUrl: "user/daypay",
      chartTitle: ["充值金额", "充值人数"],
      dataMap: [
        { label: "注册日期", value: "regdays" },
        { label: "0", value: "0" },
        { label: "1", value: "1" },
        { label: "2", value: "2" },
        { label: "3", value: "3" },
        { label: "4", value: "4" },
        { label: "5", value: "5" },
        { label: "6", value: "6" },
        { label: "7", value: "7" },
        { label: "8", value: "8" },
        { label: "9", value: "9" },
        { label: "10", value: "10" },
        { label: "11", value: "11" },
        { label: "12", value: "12" },
        { label: "13", value: "13" },
        { label: "14", value: "14" },
        { label: "15", value: "15" },
        { label: "16", value: "16" },
        { label: "17", value: "17" },
        { label: "18", value: "18" },
        { label: "19", value: "19" },
        { label: "20", value: "20" },
        { label: "21", value: "21" },
        { label: "22", value: "22" },
        { label: "23", value: "23" },
        { label: "24", value: "24" },
        { label: "25", value: "25" },
        { label: "26", value: "26" },
        { label: "27", value: "27" },
        { label: "28", value: "28" },
        { label: "29", value: "29" },
        { label: "7天合计", value: "sum7" },
        { label: "30天合计", value: "sum30" }
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
#daypay {
  .el-table {
    width: 100%;
    float: left;
    margin-bottom: 100px;
  }
  .myChart {
    width: 100%;
    height: 650px;
    float: left;
  }
}
</style>