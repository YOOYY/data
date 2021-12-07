<template>
  <div id="channel">
    <v-headpanel>
      <em>渠道组</em>渠道分组及渠道修改操作。
    </v-headpanel>
    <el-table :data="tableData">
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-form label-position="left" inline>
            <el-form-item label="渠道">
              <el-tag
                v-for="(value,index) in props.row.channel"
                :key="index"
                :title="value.channel"
              >{{value.note}}</el-tag>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>

      <el-table-column label="渠道名称" prop="name"></el-table-column>
      <el-table-column label="渠道描述" prop="note"></el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button size="mini" @click="updateHandle(scope.$index, scope.row)">修改</el-button>
          <el-button size="mini" type="danger" @click="delHandle(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <h3>创建渠道组</h3>
    <el-row :gutter="20">
      <el-col :span="6">
        <el-input v-model="createGroup.name" placeholder="渠道组名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input v-model="createGroup.note" placeholder="备注"></el-input>
      </el-col>
      <el-col :span="6">
        <el-button type="success" plain @click="openPanel('create')">选择渠道</el-button>
      </el-col>
    </el-row>

    <h3>添加渠道</h3>
    <el-row :gutter="20">
      <el-col :span="6">
        <el-input v-model="add.name" placeholder="英文渠道名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input v-model="add.rename" placeholder="确认渠道名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input v-model="add.note" placeholder="渠道别名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-button type="primary" @click="dochannel('add')">确 定</el-button>
      </el-col>
    </el-row>

    <h3>修改渠道别名</h3>
    <el-row :gutter="20">
      <el-col :span="6">
        <el-input v-model="update.name" placeholder="英文渠道名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input v-model="update.rename" placeholder="确认渠道名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input v-model="update.note" placeholder="别名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-button type="primary" @click="dochannel('update')">确 定</el-button>
      </el-col>
    </el-row>

    <h3>删除渠道</h3>
    <el-row :gutter="20">
      <el-col :span="6">
        <el-input v-model="del.name" placeholder="英文渠道名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input v-model="del.rename" placeholder="确认渠道名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-button type="primary" @click="dochannel('del')">确 定</el-button>
      </el-col>
    </el-row>

    <el-dialog title="修改渠道" :visible.sync="updateGroup.show" width="80%">
      <el-row :gutter="20" class="marginBottom15">
        <el-col :span="2" class="updateNameLabel">渠道组名:</el-col>
        <el-col :span="6">
          <el-input v-model="updateGroup.name" placeholder="渠道组名"></el-input>
        </el-col>
        <el-col :span="2" class="updateNameLabel">备注:</el-col>
        <el-col :span="6">
          <el-input v-model="updateGroup.note" placeholder="备注"></el-input>
        </el-col>
      </el-row>

      <el-row>
        <el-col :span="2" class="tagtitle">
          <span class="channelgroup el-tag">{{updateGroup.groupName}}</span>
        </el-col>
        <el-col :span="21" class="channeltag">
          <span
            class="el-tag"
            v-for="(val,index) in updateGroup.currentChannel"
            :key="index"
            @click="chooseItem(val,index,'current')"
          >{{val.note}}</span>
        </el-col>
      </el-row>

      <el-row :show="updateGroup.lastChannel.length>0">
        <el-col :span="2" class="tagtitle">
          <span class="channelgroup el-tag">剩余渠道</span>
        </el-col>
        <el-col :span="21" class="channeltag">
          <span
            class="el-tag"
            v-for="(val,index) in updateGroup.lastChannel"
            :key="index"
            @click="chooseItem(val,index,'last')"
          >{{val.note}}</span>
        </el-col>
      </el-row>

      <div slot="footer" class="dialog-footer">
        <el-button @click="closePanel">取 消</el-button>
        <el-button type="primary" @click="dogroup(type)">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="选择渠道" :visible.sync="createGroup.show" width="60%">
      <el-row>
        <span
          :class="['el-tag',val.selected?'on':'tag']"
          v-for="(val,index) in createGroup.lastChannel"
          :key="index"
          @click="chooseCreateItem(val)"
        >{{val.name}}</span>
      </el-row>

      <div slot="footer" class="dialog-footer">
        <el-button @click="closePanel">取 消</el-button>
        <el-button type="primary" @click="dogroup(type)">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

import Headpanel from "../../components/Headpanel.vue";
export default {
  data() {
    return {
      type: "",
      apiUrl: "channel/",
      tableData: [],
      updateGroup: {
        show: false,
        name: "",
        userid: "",
        groupName: "",
        currentChannel: [],
        lastChannel: [],
        note: ""
      },
      createGroup: {
        show: false,
        name: "",
        currentChannel: [],
        lastChannel: [],
        note: ""
      },
      add: {
        name: "",
        rename: "",
        note: ""
      },
      update: {
        name: "",
        rename: "",
        note: ""
      },
      del: {
        name: "",
        rename: ""
      }
    };
  },
  methods: {
    updateHandle(index, row) {
      if (row.id === "-1") {
        this.$message({
          type: "warning",
          message: "不能修改!"
        });
      } else {
        this.updateGroup.groupName = row.name;
        this.updateGroup.name = row.name;
        this.updateGroup.note = row.note;
        this.updateGroup.userid = row.id;
        this.updateGroup.currentChannel = row.channel;
        this.openPanel("update");
      }
    },
    delHandle(index, row) {
      if (row.id === "-1") {
        this.$message({
          type: "warning",
          message: "不能删除!"
        });
      } else {
        var data = { id: row.id },
          url = this.apiUrl + "delgroup";
        this.deleteAjax(url, data, this)
          .then(res => {
            return this.updateChannelGroup();
            // this.tableData.splice(index, 1);
            // this.updateState({
            //   name: "admin",
            //   attr: "channelGroup",
            //   data: this.tableData
            // });
          })
          .catch(e => {
            this.$message({
              type: "info",
              message: "已取消删除"
            });
          });
      }
    },
    openPanel(type) {
      if (type === "create") {
        if (this.createGroup.name == "") {
          this.$message({
            type: "warning",
            message: "请填写组名!"
          });
          return;
        }
        this.createGroup.show = true;
      } else {
        this.updateGroup.show = true;
      }
      this.type = type;
    },
    dochannel(type) {
      if (type === "add") {
        if (!this.passVerify(this.add.name, this.add.rename, this)) return;
        var url = this.apiUrl + "addlist",
          data = { channel: this.add.name, note: this.add.note };
      } else if (type == "update") {
        if (!this.passVerify(this.update.name, this.update.rename, this)) {
          return;
        }
        var url = this.apiUrl + "updatelist",
          data = { channel: this.update.name, note: this.update.note };
      } else {
        if (!this.passVerify(this.del.name, this.del.rename, this)) return;
        var url = this.apiUrl + "dellist",
          data = { channel: this.del.name };
      }
      this.ajax(url, data)
        .then(res => {
          //重新获取渠道数据;
          var url = this.apiUrl + "getlist",
            data = { id: this.admin.adminID };
          return this.ajax(url, data);
        })
        .then(res => {
          // this.updateState({
          //   name: "admin",
          //   attr: "channels",
          //   data: res
          // });
          return this.updateChannelGroup();
        })
        .catch(e => {
          this.$message({
            type: "error",
            message: "操作失败!失败原因:" + e
          });
        })
        .then(res => {
          this.add.name = "";
          this.add.rename = "";
          this.add.note = "";
          this.update.name = "";
          this.update.rename = "";
          this.update.note = "";
          this.del.name = "";
          this.del.rename = "";
        });
    },
    chooseCreateItem(value) {
      value.selected = !value.selected;
      let index = this.createGroup.currentChannel.indexOf(value.value);
      if (index >= 0 && !value.selected) {
        this.createGroup.currentChannel.splice(index, 1);
      } else if (index < 0 && value.selected) {
        this.createGroup.currentChannel.push(value.value);
      }
      console.log(this.createGroup.currentChannel);
    },
    chooseItem(val, index, type) {
      if (type === "current") {
        this.updateGroup.currentChannel.splice(index, 1);
        this.updateGroup.lastChannel.push(val);
      } else {
        this.updateGroup.lastChannel.splice(index, 1);
        this.updateGroup.currentChannel.push(val);
      }
    },
    dogroup(type) {
      switch (type) {
        case "create":
          var url = this.apiUrl + "addgroup",
            data = {
              name: this.createGroup.name,
              note: this.createGroup.note,
              channel: this.createGroup.currentChannel
            };
          this.ajax(url, data).then(res => {
            this.updateChannelGroup();
          });
          break;
        case "update":
          var url = this.apiUrl + "updategroup",
            channels = this.updateGroup.currentChannel.map(val => {
              return val.channel;
            }),
            data = {
              id: this.updateGroup.userid,
              name: this.updateGroup.name,
              note: this.updateGroup.note,
              channel: channels
            };
          this.ajax(url, data).then(res => {
            this.updateChannelGroup();
          });
          break;
      }
      this.closePanel();
    },
    closePanel() {
      this.createGroup.name = "";
      this.createGroup.note = "";
      this.createGroup.show = false;
      this.updateGroup.show = false;
    },
    //获取渠道组
    getChannelGroup() {
      return this.ajax(this.apiUrl + "getallgroup").then(res => {
        this.tableData = res;
        this.updateGroup.lastChannel = JSON.parse(
          JSON.stringify(res[res.length - 1].channel)
        );
        let arr = [];
        res[res.length - 1].channel.forEach(item => {
          arr.push({
            value: item.channel,
            selected: false,
            name: item.note
          });
        });
        this.createGroup.lastChannel = arr;
      });
    },
    updateChannelGroup() {
      return this.getChannelGroup()
        .then(res => {
          this.$message({
            type: "success",
            message: "操作成功!"
          });
        })
        .catch(e => {
          this.$message({
            type: "error",
            message: "操作失败!失败原因:" + e
          });
        });
    },
    ...mapMutations(["updateState"])
  },
  computed: {
    ...mapState(["admin"])
  },
  mounted() {
    this.getChannelGroup();
  },
  components: {
    "v-headpanel": Headpanel
  }
};
</script>
<style lang='scss'>
#channel {
  .marginBottom15 {
    margin-bottom: 15px;
  }
  .updateNameLabel {
    line-height: 40px;
    font-size: 18px;
    margin-left: 10px;
  }
  .el-dialog h2 {
    margin-top: 0;
  }
  .el-dialog__body {
    padding-top: 10px;
  }
  .el-tag {
    cursor: pointer;
    margin: 5px 5px;
  }
  .tag {
    background-color: rgba(103, 194, 58, 0.1);
    border-color: rgba(103, 194, 58, 0.2);
    margin: 5px 5px;
    color: #67c23a;
  }

  //	margin-top:20px;
  .on {
    background-color: rgba(245, 108, 108, 0.1);
    border-color: rgba(245, 108, 108, 0.2);
    margin: 5px 5px;
    color: #f56c6c;
  }
  .channelgroup {
    font-size: 16px;
  }
}
</style>