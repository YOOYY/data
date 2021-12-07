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
import View from "../../components/Monthpay.vue";

export default {
  data() {
    return {
      id: "monthpayment",
      apiUrl: "user/monthpayment",
      chartTitle: "每月充值",
      dataMap: [
        { label: "注册月份", value: "createdate" },
        { label: "充值月份", value: "rechargedate" },
        { label: "渠道", value: "channel" },
        { label: "金额", value: "amount" },
      ],
      tableHeight: 650,
    };
  },
  components: {
    "v-view": View,
  },
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