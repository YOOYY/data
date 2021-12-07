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
      id: "Todaydata",
      apiUrl: "user/todaypay",
      chartTitle: "今日充值",
      dataMap: [
        { label: "充值渠道", value: "platform" },
        { label: "充值金额", value: "amount" }
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
#todaypay {
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