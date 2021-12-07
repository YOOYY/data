<template>
  <div>
    <el-button type="success" icon="el-icon-search" size="small" @click="panelOpen">选择渠道</el-button>
    <el-dialog title="渠道选择" :visible.sync="panelShow">
      <el-row v-for="(value, index) in channelGroup" :key="index">
        <el-col :span="3" class="tagtitle">
          <span
            :class="['channelgroup el-tag',value.name.selected?'on':'tag']"
            @click="allselect(value.name,value.channel)"
          >{{value.name.value}}</span>
        </el-col>
        <el-col :span="21" class="channeltag">
          <span
            :class="['el-tag',data.selected?'on':'tag']"
            v-for="(data,sindex) in value.channel"
            :key="sindex"
            @click="chooseItem(data,value)"
          >{{data.name}}</span>
        </el-col>
      </el-row>
      <div slot="footer">
        <el-switch v-if="sumBottomShow" v-model="isSum" active-text="累加" class="fl sumBotton"></el-switch>
        <el-button @click="cleanChannel">清 空</el-button>
        <el-button type="primary" @click="channelChange">确 定</el-button>
        <el-button @click="close">取 消</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  data() {
    return {
      panelShow: false,
      isSum: false,
      channelGroup: [],
      test: ""
    };
  },
  props: {
    sumBottomShow: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    chooseItem(data, value) {
      data.selected = !data.selected;
      let flag = value.channel.every(function(item) {
        return item.selected;
      });
      value.name.selected = flag;
      console.log(this.currentChannel);
    },
    allselect(value, channels) {
      value.selected = !value.selected;
      channels.forEach(function(item) {
        item.selected = value.selected;
      });
      console.log(this.currentChannel);
    },
    panelOpen() {
      this.cleanChannel();
      this.panelShow = true;
    },
    close() {
      this.panelShow = false;
    },
    channelChange() {
      this.close();
      if (this.currentChannel.length > 0) {
        this.$emit("channelChange", this.currentChannel, this.isSum);
      } else {
        this.$message({
          type: "error",
          message: "没有选择渠道！"
        });
      }
    },
    cleanChannel() {
      this.channelGroup.forEach(function(item) {
        item.name.selected = false;
        item.channel.forEach(function(nitem) {
          nitem.selected = false;
        });
      });
    }
  },
  computed: {
    currentChannel() {
      var arr = [];
      this.channelGroup.forEach(function(item) {
        item.channel.forEach(function(nitem) {
          if (nitem.selected) {
            arr.push(nitem.value);
          }
        });
      });
      return arr;
    },
    ...mapState(["admin"])
  },
  mounted() {
    this.channelGroup = JSON.parse(JSON.stringify(this.admin.channelGroup));
    this.channelGroup.forEach(function(item) {
      item.name = { value: item.name, selected: false };
      item.channel = item.channel.map(function(nitem) {
        return { value: nitem.channel, selected: false, name: nitem.note };
      });
    });
    console.log(this.channelGroup);
  }
};
</script>
<style lang="scss">
.el-dialog {
  .el-tag {
    cursor: pointer;
  }

  .tag {
    background-color: rgba(103, 194, 58, 0.1);
    border-color: rgba(103, 194, 58, 0.2);
    margin: 5px 5px;
    color: #67c23a;
  }

  .on {
    background-color: rgba(245, 108, 108, 0.1);
    border-color: rgba(245, 108, 108, 0.2);
    margin: 5px 5px;
    color: #f56c6c;
  }

  .channelgroup {
    font-size: 16px;
  }
  .sumBotton {
    margin-left: 20px;
    margin-top: 5px;
  }
}
</style>