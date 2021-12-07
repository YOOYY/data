<template>
  <div id="timeSelect">
    <div class="block">
      <el-tag type="success">时间段</el-tag>
      <el-date-picker
        size="small"
        v-model="timeline"
        @change="changeHandle('timeline')"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        type="daterange"
        align="right"
        unlink-panels
        value-format="timestamp"
        :picker-options="pickerOptions"
      ></el-date-picker>
    </div>

    <div class="block">
      <el-tag type="success">时间点</el-tag>
      <el-date-picker
        type="dates"
        size="small"
        v-model="timedot"
        @change="changeHandle('timedot')"
        placeholder="选择一个或多个日期"
        value-format="timestamp"
        format="MM-dd"
      ></el-date-picker>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      timeline: [
        new Date().setTime(new Date().getTime() - 3600 * 1000 * 24),
        new Date().getTime()
      ],
      timedot: [],
      pickerOptions: {
        shortcuts: [
          {
            text: "最近两天",
            onClick(picker) {
              const end = new Date();
              const start = new Date().getTime() - 3600 * 1000 * 24 * 2;
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "最近一周",
            onClick(picker) {
              const end = new Date();
              const start = new Date().getTime() - 3600 * 1000 * 24 * 7;
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "最近一个月",
            onClick(picker) {
              const end = new Date();
              const start = new Date().getTime() - 3600 * 1000 * 24 * 30;
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "最近三个月",
            onClick(picker) {
              const end = new Date();
              const start = new Date().getTime() - 3600 * 1000 * 24 * 90;
              picker.$emit("pick", [start, end]);
            }
          }
        ]
      }
    };
  },
  methods: {
    getTimes(type) {
      if (type === "timeline" && this.timeline) {
        this.timedot = [];
        return { timeline: true, times: this.timeline };
      } else if (type === "timedot" && this.timedot) {
        this.timeline = [];
        return { timeline: false, times: this.timedot };
      } else {
        return false;
      }
    },
    changeHandle(type) {
      let value = this.getTimes(type);
      if (!value) {
        this.$message({
          type: "error",
          message: "没有选择时间!"
        });
        return;
      }
      this.$emit("timesChange", value);
    }
  }
};
</script>

<style lang="scss">
#timeSelect {
  .el-tag {
    margin-right: 10px;
  }
}
.block {
  float: left;
  margin: 0 5px;
}
.el-range-editor {
  width: 250px !important;
}
</style>