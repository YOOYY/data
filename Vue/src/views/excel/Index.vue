<template>
  <div :id="id">
    <div class="nav">
      <v-timeselect @timesChange="timesChange"></v-timeselect>
      <v-channelSelect @channelChange="channelChange"></v-channelSelect>
      <el-button type="success" size="small" @click="exportExcel" class="export_button">点击生成Excel</el-button>
    </div>

    <el-table :data="resData">
      <el-table-column label="日期">
        <template slot-scope="scope">
          <span>{{ scope.row.date }}</span>
        </template>
      </el-table-column>
      <el-table-column label="渠道">
        <template slot-scope="scope">
          <span>{{ scope.row.channel }}</span>
        </template>
      </el-table-column>
      <el-table-column label="展示">
        <template slot-scope="scope">
          <el-input v-model="scope.row.show" placeholder="请输入内容" @change="handleCount(scope.row)"></el-input>
        </template>
      </el-table-column>
      <el-table-column label="点击">
        <template slot-scope="scope">
          <el-input v-model="scope.row.click" placeholder="请输入内容" @change="handleCount(scope.row)"></el-input>
        </template>
      </el-table-column>
      <el-table-column label="消费">
        <template slot-scope="scope">
          <el-input
            v-model="scope.row.payment"
            placeholder="请输入内容"
            @change="handleCount(scope.row)"
          ></el-input>
        </template>
      </el-table-column>
      <el-table-column label="新增用户">
        <template slot-scope="scope">
          <span>{{ scope.row.adduser }}</span>
        </template>
      </el-table-column>
      <el-table-column label="点击率">
        <template slot-scope="scope">
          <span>{{ scope.row.clickrate }}</span>
        </template>
      </el-table-column>
      <el-table-column label="平均点击价格">
        <template slot-scope="scope">
          <span>{{ scope.row.avgclick }}</span>
        </template>
      </el-table-column>
      <el-table-column label="转化率">
        <template slot-scope="scope">
          <span>{{ scope.row.tratiorate }}</span>
        </template>
      </el-table-column>
      <el-table-column label="用户成本">
        <template slot-scope="scope">
          <span>{{ scope.row.usercost }}</span>
        </template>
      </el-table-column>
      <el-table-column label="当日付费金额">
        <template slot-scope="scope">
          <span>{{ scope.row.todaypayment }}</span>
        </template>
      </el-table-column>
      <el-table-column label="当日付费用户数">
        <template slot-scope="scope">
          <span>{{ scope.row.todaypaycount }}</span>
        </template>
      </el-table-column>
      <el-table-column label="渠道充值金额">
        <template slot-scope="scope">
          <span>{{ scope.row.channelpayment }}</span>
        </template>
      </el-table-column>
      <el-table-column label="次日留存数">
        <template slot-scope="scope">
          <span>{{ scope.row.twolive }}</span>
        </template>
      </el-table-column>
      <el-table-column label="次日留存率">
        <template slot-scope="scope">
          <span>{{ scope.row.twoliverate }}</span>
        </template>
      </el-table-column>
      <el-table-column label="七日留存数">
        <template slot-scope="scope">
          <span>{{ scope.row.sevenlive }}</span>
        </template>
      </el-table-column>
      <el-table-column label="七日留存率">
        <template slot-scope="scope">
          <span>{{ scope.row.sevenliverate }}</span>
        </template>
      </el-table-column>
      <el-table-column label="次日成本">
        <template slot-scope="scope">
          <span>{{ scope.row.twocost }}</span>
        </template>
      </el-table-column>
      <el-table-column label="七日成本">
        <template slot-scope="scope">
          <span>{{ scope.row.sevencost }}</span>
        </template>
      </el-table-column>
    </el-table>

    <download-excel
      :data="resData"
      :fields="json_fields"
      :name="excelTitle+'.xls'"
      style="display:none"
      ref="DownExcel"
    ></download-excel>
  </div>
</template>

<script>
import TimeSelect from "../../components/TimeSelect.vue";
import ChannelSelect from "../../components/ChannelSelect.vue";
import JsonExcel from "vue-json-excel";

export default {
  data() {
    return {
      id: "excel",
      apiUrl: "excel",
      excelTitle: "",
      panelShow: false,
      times: [
        new Date().setTime(new Date().getTime() - 3600 * 1000 * 24),
        new Date().getTime()
      ],
      channels: [],
      isSum: false,
      timeLine: true,

      resData: [],
      json_fields: {
        日期: "date",
        渠道名: "channel",
        展示: "show",
        点击: "click",
        消费: "payment",
        新增用户: "adduser",
        点击率: "clickrate",
        平均点击价格: "avgclick",
        转化率: "tratiorate",
        用户成本: "usercost",
        当日付费金额: "todaypayment",
        当日付费用户数: "todaypaycount",
        渠道充值金额: "channelpayment",
        次日留存数: "twolive",
        次日留存率: "twoliverate",
        七日留存数: "sevenlive",
        七日留存率: "sevenliverate",
        次日成本: "twocost",
        七日成本: "sevencost"
      }
      //   resData: [
      //     {
      //       date: "2016-05-02",
      //       channel: "fishworld",
      //       show: 285,
      //       click: 396,
      //       payment: 321,
      //       adduser: 869,
      //       clickrate: "0%",
      //       avgclick: 0,
      //       tratiorate: "0%",
      //       usercost: 0,
      //       todaypayment: "12335",
      //       tpdaypaycount: "1234",
      //       channelpayment: "33.584",
      //       twolive: "123",
      //       twoliverate: "123",
      //       sevenlive: "24",
      //       sevenliverate: "24",
      //       twocost: 0,
      //       sevencost: 0,
      //       hehe: "adfss"
      //     }
      //   ]
    };
  },
  methods: {
    exportExcel() {
      this.$prompt("请输入生成文件名", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消"
      })
        .then(({ value }) => {
          this.excelTitle = value;
          if (this.resData.length === 0) {
            this.$message({
              type: "error",
              message: "数据不能为空！"
            });
            return;
          }
          this.$nextTick(() => {
            this.$refs.DownExcel.$el.click();
            this.$message({
              type: "success",
              message: "Excel已导出"
            });
          });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "取消生成"
          });
        });
    },
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
      if (this.channels.length <= 0) {
        return;
      }
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
    },
    handleCount(item) {
      item.clickrate = this.handleError(
        (item.click / item.show).toFixed(4) * 100 + "%"
      );
      item.avgclick = this.handleError((item.payment / item.click).toFixed(2));
      item.tratiorate = this.handleError(
        (item.adduser / item.click).toFixed(4) * 100 + "%"
      );
      item.usercost = this.handleError(
        (item.payment / item.adduser).toFixed(2)
      );
      item.twocost = this.handleError(
        (item.payment / (item.twolive + item.adduser)).toFixed(2)
      );
      item.twoliverate = this.handleError(
        (item.twolive / item.adduser).toFixed(4) * 100 + "%"
      );
      item.sevencost = this.handleError(
        (item.payment / (item.sevenlivesum + item.adduser)).toFixed(2)
      );
      item.sevenliverate = this.handleError(
        (item.sevenlive / item.adduser).toFixed(4) * 100 + "%"
      );
    },
    handleError(data) {
      if (data.charAt(data.length - 1) == "%") {
        var ndata = parseFloat(data);
      } else {
        var ndata = data;
      }
      console.log(ndata);
      if (
        isNaN(ndata) ||
        ndata == Infinity ||
        ndata == -Infinity ||
        ndata == undefined
      ) {
        return "";
      } else {
        return data;
      }
    }
  },
  components: {
    "v-timeselect": TimeSelect,
    "v-channelSelect": ChannelSelect,
    downloadExcel: JsonExcel
  }
};
</script>

<style lang="scss">
#excel {
  .nav {
    margin-bottom: 15px;
    overflow: hidden;
    float: none;
    .export_button {
      margin-left: 10px;
    }
    & > div {
      float: left;
    }
  }
}
</style>