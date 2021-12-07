<template>
  <div :id="chartId" class="myChart"></div>
</template>
<script>
export default {
  data() {
    return {
      myChart: null,
      dimensions: []
    };
  },
  props: {
    title: {
      type: String,
      default: "标题"
    },
    //
    dataMap: {
      type: Array,
      default: []
    },
    chartData: {
      type: Array,
      default: []
    },
    chartId: {
      type: String,
      default: "myChart"
    }
  },
  methods: {
    drawChart() {
      this.myChart = this.$echarts.init(document.getElementById(this.chartId));
      this.dimensions = this.dataMap.map(val => {
        return val.value;
      });
      let legend = this.dataMap.map(val => {
        return val.label;
      });
      let xAxisName = legend.shift();
      let series = legend.map(val => {
        return { name: val, type: "line" };
      });
      // debugger;
      this.myChart.setOption({
        title: { text: this.title },
        tooltip: { trigger: "axis" },
        legend: {
          data: legend
        },
        grid: {
          left: "3%",
          right: "4%",
          bottom: "3%",
          containLabel: true
        },
        toolbox: {
          feature: {
            saveAsImage: {}
          }
        },
        dataset: {
          dimensions: this.dimensions,
          source: this.chartData
        },
        xAxis: {
          name: xAxisName,
          type: "category",
          boundaryGap: false
        },
        yAxis: {},
        series: series
      });
    }
  },
  watch: {
    chartData: function(value) {
      this.myChart.setOption({
        dataset: {
          dimensions: this.dimensions,
          source: value
        }
      });
    }
  },
  mounted() {
    this.drawChart();
  }
};
</script>