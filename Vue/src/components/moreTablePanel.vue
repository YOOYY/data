<template>
  <div :id="id">
    <div class="nav">
      <v-timeselect @timesChange="timesChange"></v-timeselect>
      <v-channelSelect
        @channelChange="channelChange"
        :sumBottomShow="sumBottomShow"
      ></v-channelSelect>
    </div>
    <div v-for="(val, name, index) in resData" :key="index">
      <v-table
        :dataMap="dataMap"
        ref="table"
        :tableHeight="tableHeight"
        :tableData="val"
        class="table"
      ></v-table>
      <v-chart
        :dataMap="dataMap"
        :title="chartTitle[index]"
        ref="chart"
        :chartData="val"
        :chartId="chartId + index"
      ></v-chart>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

import Chart from "./chart/Chart.vue";
import Table from "./table/Table.vue";
import TimeSelect from "./TimeSelect.vue";
import ChannelSelect from "./ChannelSelect.vue";

export default {
  data() {
    return {
      times: [
        new Date().setTime(new Date().getTime() - 3600 * 1000 * 24),
        new Date().getTime(),
      ],
      channels: [],
      isSum: false,
      timeLine: true,
      resData: [],
      chartId: "myChart",
    };
  },
  props: {
    id: {
      type: String,
      default: "",
    },
    apiUrl: {
      type: String,
      default: "",
    },
    chartTitle: {
      type: Array,
      default: [],
    },
    dataMap: {
      type: Array,
      default: [],
    },
    tableHeight: {
      type: Number,
      default: 650,
    },
    sumBottomShow: {
      type: Boolean,
      default: true,
    },
  },
  methods: {
    timesChange(times) {
      this.times = times.times;
      this.timeLine = times.timeline;
      this.getData();
    },
    channelChange(channels, isSum) {
      this.channels = channels;
      this.isSum = isSum;
      this.getData();
    },
    //合计
    sum(arr, num) {
      arr.forEach((val) => {
        var sum = 0;
        for (var i = 0; i < num; i++) {
          if (val[i]) {
            sum = sum + val[i];
          }
        }
        val["sum" + num] = sum;
      });
    },
    resultData(res) {
      Object.keys(res).forEach((key) => {
        let element = res[key];
        this.sum(element, 7);
        this.sum(element, 30);
      });
      console.log(res);
      return res;
    },
    getData() {
      var url = this.apiUrl;
      var data = {
        timeLine: this.timeLine,
        channels: this.channels,
        times: this.times,
        channelTagFlag: this.isSum,
      };
      console.log(data);

      this.ajax(url, data)
        .then((res) => {
          this.resData = this.resultData(res);
        })
        .catch((e) => {
          console.log(e);
          this.$message({
            type: "error",
            message: "数据请求错误:" + e,
          });
        });
    },
  },
  computed: {
    ...mapState(["admin"]),
  },
  mounted() {
    if (this.sumBottomShow) {
      this.isSum = true;
    }
    this.channels = [...this.admin.channels];
    this.getData();
  },
  components: {
    "v-chart": Chart,
    "v-timeselect": TimeSelect,
    "v-table": Table,
    "v-channelSelect": ChannelSelect,
  },
};
</script>

<style lang="scss">
.nav {
  float: left;
  margin-bottom: 15px;
  & > div {
    float: left;
  }
}
.el-table__fixed {
  height: 650px !important;
}
.table {
  clear: both;
}
</style>