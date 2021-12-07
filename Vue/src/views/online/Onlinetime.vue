<template>
  <v-view
    :id="id"
    :apiUrl="apiUrl"
    :chartTitle="chartTitle"
    :dataMap="dataMap"
    :tableHeight="tableHeight"
  ></v-view>
</template>

<script>
import View from "../../components/TablePanel.vue";

export default {
  data() {
    return {
      id: "onlinetime",
      apiUrl: "online/onlinetime",
      chartTitle: "在线时间分布",
      dataMap: [
        { label: "小时", value: "hour" },
        { label: "平均在线", value: "avg" },
        { label: "最高在线", value: "max" }
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
#onlinetime {
  .el-table {
    width: 50%;
    float: left;
  }
  #myChart {
    width: 750px;
    height: 650px;
    float: left;
  }
}
</style>