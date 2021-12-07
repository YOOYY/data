<template>
  <div :id="id">
    <div class="nav">
      <v-timeselect @timesChange="timesChange"></v-timeselect>
      <v-channelSelect @channelChange="channelChange" :sumBottomShow="sumBottomShow"></v-channelSelect>
    </div>
    <v-table
      :dataMap="dataMap"
      ref="table"
      :tableHeight="tableHeight"
      :tableData="resData"
      class="table"
    ></v-table>
    <v-chart :dataMap="dataMap" :title="chartTitle" ref="chart" :chartData="resData"></v-chart>
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
        new Date().getTime()
      ],
      channels: [],
      isSum: false,
      timeLine: true,
      resData: []
    };
  },
  props: {
    id: {
      type: String,
      default: ""
    },
    apiUrl: {
      type: String,
      default: ""
    },
    chartTitle: {
      type: String,
      default: ""
    },
    dataMap: {
      type: Array,
      default: []
    },
    tableHeight: {
      type: Number,
      default: 650
    },
    sumBottomShow: {
      type: Boolean,
      default: true
    }
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
    getData() {
      var url = this.apiUrl;
      var data = {
        timeLine: this.timeLine,
        channels: this.channels,
        times: this.times,
        channelTagFlag: this.isSum
      };
      console.log(data);

      this.ajax(url, data)
        .then(res => {
          this.resData = res;
        })
        .catch(e => {
          this.$message({
            type: "error",
            message: "数据请求错误:" + e
          });
        });
    }
  },
  computed: {
    ...mapState(["admin"])
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
    "v-channelSelect": ChannelSelect
  }
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
.table {
  clear: both;
}
.el-table__fixed {
  height: 690px !important;
}
</style>