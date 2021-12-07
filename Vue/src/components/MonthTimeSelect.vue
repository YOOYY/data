<template>
  <div id="timeSelect">
    <div class="block">
      <el-tag type="success">{{ label }}月份段</el-tag>
      <el-date-picker
        size="small"
        v-model="timeline"
        @change="changeHandle('timeline')"
        range-separator="至"
        start-placeholder="开始月份"
        end-placeholder="结束月份"
        type="monthrange"
        align="right"
        unlink-panels
        value-format="timestamp"
        :picker-options="pickerOptions"
      ></el-date-picker>
    </div>

    <div class="block">
      <el-tag type="success">{{ label }}月份至今</el-tag>
      <el-date-picker
        type="month"
        size="small"
        v-model="timedot"
        @change="changeHandle('timedot')"
        placeholder="选择一个月份"
        value-format="timestamp"
        format="yyyy-MM"
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
        new Date(new Date().toLocaleDateString()),
        new Date(
          new Date(new Date().toLocaleDateString()).getTime() +
            24 * 60 * 60 * 1000 -
            1000
        ),
      ],
      timedot: [],
      pickerOptions: {
        shortcuts: [
          {
            text: "最近三个月(含本月)",
            onClick(picker) {
              const start = new Date(new Date().toLocaleDateString());
              const end = new Date(
                start.getTime() + 24 * 60 * 60 * 1000 - 1000
              );
              start.setMonth(start.getMonth() - 2);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: "最近6个月(含本月)",
            onClick(picker) {
              const start = new Date(new Date().toLocaleDateString());
              const end = new Date(
                start.getTime() + 24 * 60 * 60 * 1000 - 1000
              );
              start.setMonth(start.getMonth() - 5);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: "最近12个月(含本月)",
            onClick(picker) {
              const start = new Date(new Date().toLocaleDateString());
              const end = new Date(
                start.getTime() + 24 * 60 * 60 * 1000 - 1000
              );
              start.setMonth(start.getMonth() - 11);
              picker.$emit("pick", [start, end]);
            },
          },
        ],
      },
    };
  },
  props: {
    label: {
      type: String,
      default: "注册",
    },
    type: {
      type: String,
      default: "register",
    },
  },
  methods: {
    getTimes(type) {
      if (type === "timeline" && this.timeline) {
        this.timedot = [];
        this.timeline[1] = this.timeline[1] + 24 * 60 * 60 * 1000 - 1000;
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
          message: "没有选择" + label + "时间!",
        });
        return;
      }
      this.$emit("timesChange", this.type, value);
    },
  },
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