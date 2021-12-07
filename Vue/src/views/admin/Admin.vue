<template>
  <div>
    <v-headpanel>
      <em>管理员面板</em>
      管理员账号操作
    </v-headpanel>

    <el-button size="mini" type="danger" @click="openPanel('create')" class="marginBottom15">新增用户</el-button>

    <el-table :data="tableData">
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-form label-position="left" inline>
            <el-form-item label="渠道">
              <el-tag v-for="(value,index) in props.row.channel" :key="index">{{value.note}}</el-tag>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column
        v-for="(value,index) in tablehead"
        :key="index"
        :prop="value.prop"
        :label="value.label"
      ></el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button size="mini" @click="updateHandle(scope.$index, scope.row)">修改</el-button>
          <el-button size="mini" type="danger" @click="delHandle(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog
      :title="panelType === 'create'?'创建用户':'修改用户'"
      :visible.sync="panelShow"
      width="80%"
      :before-close="closePanel"
    >
      <el-form :model="userData" inline ref="userData">
        <el-form-item label="账号" label-width="100px" prop="name">
          <el-input v-model="userData['name']" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="密码" label-width="100px" prop="password">
          <el-input v-model="userData['password']" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="昵称" label-width="100px" prop="nickname">
          <el-input v-model="userData['nickname']" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="权限" label-width="100px" prop="privilege">
          <el-select v-model="userData['privilege']" placeholder="请选择">
            <el-option
              v-for="item in privilegeGroup"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <br />
        <el-form-item label="选择渠道" label-width="100px">
          <el-row v-for="(value, index) in channelGroup" :key="index">
            <el-col :span="2" class="tagtitle">
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
        </el-form-item>
        <el-form-item label="全部渠道" label-width="100px" prop="channeltag">
          <el-switch v-model="userData.channeltag" active-color="#13ce66" style="margin-left:25px"></el-switch>
        </el-form-item>
        <el-form-item label="备注" label-width="100px" prop="note">
          <el-input v-model="userData['note']" autocomplete="off"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="closePanel">取 消</el-button>
        <el-button type="primary" @click="submit()">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import Headpanel from "../../components/Headpanel.vue";
export default {
  data() {
    return {
      tablehead: [
        { label: "用户名", prop: "name" },
        { label: "昵称", prop: "nickname" },
        { label: "权限组", prop: "privilegename" },
        { label: "最后登录ip", prop: "last_login_ip" },
        { label: "最后登录日期", prop: "last_login_date" },
        { label: "备注", prop: "note" }
      ],
      tableData: [],
      panelShow: false,
      panelType: "create",
      privilegeGroup: [],
      channelGroup: [],
      userID: "",
      userData: {
        name: "",
        password: "",
        channel: "",
        privilege: "",
        note: "",
        nickname: "",
        channeltag: false
      },
      apiUrl: "admin/"
    };
  },
  methods: {
    openPanel(type) {
      this.panelType = type;
      this.panelShow = true;
    },
    closePanel() {
      this.$refs["userData"].resetFields();
      this.channelGroup.forEach(function(item) {
        item.name.selected = false;
        item.channel.forEach(function(nitem) {
          nitem.selected = false;
        });
      });
      this.panelShow = false;
    },
    submit() {
      this.userData.channel = this.currentChannel;
      if (this.panelType === "create") {
        var url = this.apiUrl + "adduser",
          data = Object.assign({}, this.userData);
        data["channeltag"] = this.userData.channeltag ? "*" : "";
        console.log(data);
        this.ajax(url, data)
          .then(res => {
            console.log(res);
            var url = this.apiUrl + "getlist",
              data = { id: this.admin["adminID"] };
            return this.ajax(url, data);
          })
          .then(res => {
            this.tableData = res;
            this.closePanel();
            this.$message({
              type: "success",
              message: "新增成功"
            });
          })
          .catch(e => {
            this.closePanel();
            this.$message({
              type: "error",
              message: "新增失败!"
            });
          });
      } else {
        var url = this.apiUrl + "updateuser",
          data = Object.assign({}, this.userData);
        data["channeltag"] = this.userData.channeltag ? "*" : "";
        data.adminid = this.admin.adminID;
        data.id = this.userID;
        if (!data.password) {
          delete data.password;
        }
        this.ajax(url, data).then(res => {
          this.tableData = res;
          // if (data.adminid === parseInt(data.id)) {
          //   this.updateAdmin({
          //     name: this.admin.nickname,
          //     password: this.admin.password
          //   });
          // }
          this.$message({
            type: "success",
            message: "修改成功"
          });
          this.closePanel();
        });
      }
    },
    updateHandle(index, row) {
      this.userData.nickname = row.nickname;
      this.userData.name = row.name;
      this.userData.privilege = row.privilege;
      this.userData.note = row.note;
      this.userData.channeltag = row.channeltag === "*" ? true : false;
      this.channelHandle(row.channel);
      this.userID = row.auid;
      this.openPanel("update");
    },
    delHandle(index, row) {
      if (row.auid === 1) {
        this.$message({
          type: "warning",
          message: "不能删除!"
        });
        return;
      }
      var data = { id: row.auid },
        url = this.apiUrl + "useduser";
      this.deleteAjax(url, data, this)
        .then(res => {
          this.tableData.splice(index, 1);
          this.$message({
            type: "success",
            message: "删除成功!"
          });
        })
        .catch(() => {
          console.log(2);
          v.$message({
            type: "info",
            message: "已取消删除"
          });
        });
    },
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
    channelHandle(arr) {
      arr.forEach(item => {
        this.channelGroup.forEach(function(citem) {
          citem.channel.forEach(function(nitem) {
            if (nitem.value === item.channel) {
              nitem.selected = true;
            }
          });
        });
      });
      this.channelGroup.forEach(function(item) {
        let flag = item.channel.every(function(citem) {
          return citem.selected;
        });
        if (flag) {
          item.name.selected = true;
        }
      });
    },
    ...mapActions(["updateAdmin"])
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
    var url = this.apiUrl + "getlist",
      data = { id: this.admin["adminID"] };
    this.ajax(url, data).then(res => {
      //   console.log(res);
      this.tableData = res;
    });
    this.ajax("channel/getallgroup", {}).then(res => {
      res.forEach(function(item) {
        item.name = { value: item.name, selected: false };
        item.channel = item.channel.map(function(nitem) {
          return { value: nitem.channel, selected: false, name: nitem.note };
        });
      });

      this.channelGroup = res;
    });
    this.ajax("privilege/getallgroup", {}).then(res => {
      this.privilegeGroup = res;
    });
  },
  components: {
    "v-headpanel": Headpanel
  }
};
</script>
<style lang='scss'>
.marginBottom15 {
  margin-bottom: 15px;
}
.tagtitle {
  span {
    text-align: center;
    width: 110px;
    overflow: hidden;
  }
}
.el-dialog h2 {
  margin-top: 0;
}
.el-dialog__body {
  padding-top: 10px;
}
.el-tag {
  cursor: pointer;
  margin: 5px 5px !important;
}
.channel_group {
  width: 100%;
  .el-form-item__content {
    width: 90%;
  }
}
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