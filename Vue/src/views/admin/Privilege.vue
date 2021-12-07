<template>
  <div id="privilege">
    <v-headpanel>
      <em>权限组</em>权限分组操作面板。
    </v-headpanel>

    <el-table :data="tableData">
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-form label-position="left" inline>
            <el-form-item label="权限">
              <el-tag
                v-for="(value,index) in props.row.privilege"
                :key="index"
              >{{value | tozh(sideMenu)}}</el-tag>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>

      <el-table-column label="权限名称" prop="name"></el-table-column>
      <el-table-column label="权限描述" prop="note"></el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button size="mini" @click="updateHandle(scope.$index, scope.row)">修改</el-button>
          <el-button size="mini" type="danger" @click="delHandle(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <h3>创建权限组</h3>
    <el-row :gutter="20">
      <el-col :span="6">
        <el-input v-model="group.name" placeholder="权限组名"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input v-model="group.note" placeholder="备注"></el-input>
      </el-col>
      <el-col :span="6">
        <el-button type="success" plain @click="openPanel('create')">选择权限</el-button>
      </el-col>
    </el-row>

    <el-dialog title="选择权限" :visible.sync="panelShow">
      <el-row :gutter="20" v-show="updateName" class="marginBottom15">
        <el-col :span="3" class="updateNameLabel">权限组名:</el-col>
        <el-col :span="6">
          <el-input v-model="panelData.row.name" placeholder="权限组名"></el-input>
        </el-col>
      </el-row>
      <span
        v-for="(value,index) in allPrivilege"
        :key="index"
        @click="select(value)"
        :class="['el-tag',value.selected?'on':'']"
      >{{value.value | tozh(sideMenu)}}</span>

      <div slot="footer" class="dialog-footer">
        <el-button @click="panelShow = false">取 消</el-button>
        <el-button type="primary" @click="submit(type)">确 定</el-button>
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
      panelShow: false,
      updateName: false,
      type: "",
      apiUrl: "privilege/",
      tableData: [],
      panelData: {
        row: [],
        index: 0
      },
      group: {
        name: "",
        note: ""
      },
      allPrivilege: []
    };
  },
  methods: {
    updateHandle(index, row) {
      if (index === 0) {
        this.$message({
          type: "warning",
          message: "不能修改!"
        });
      } else {
        this.privilegeHandle(row.privilege);
        this.panelData.row = Object.assign({}, row);
        this.panelData.index = index;
        this.openPanel("update");
      }
    },
    delHandle(index, row) {
      if (row.id === 0) {
        this.$message({
          type: "warning",
          message: "不能删除!"
        });
      } else {
        var data = { id: row.id },
          url = this.apiUrl + "delgroup";
        this.deleteAjax(url, data, this)
          .then(res => {
            this.tableData.splice(index, 1);
            // this.updateState({
            //   name: "admin",
            //   attr: "privilegeGroup",
            //   data: this.tableData
            // });
            this.$message({
              type: "success",
              message: "删除成功"
            });
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
      this.type = type;
      if (type === "create") {
        if (this.group.name == "") {
          this.$message({
            type: "warning",
            message: "请填写组名!"
          });
          return;
        }
        this.updateName = false;
      } else {
        this.updateName = true;
      }
      this.panelShow = true;
    },
    //渠道选择
    select(value) {
      value.selected = !value.selected;
    },
    submit(type) {
      switch (type) {
        case "create":
          var url = this.apiUrl + "addgroup";
          var data = Object.assign({}, this.group);
          data.privilege = this.currentPrivilege;
          this.ajax(url, data).then(res => {
            console.log(data);
            data["id"] = res;
            this.tableData.push(data);
            // this.update({
            //   name: "admin",
            //   attr: "privilegeGroup",
            //   data: this.admin["privilegeGroup"]
            // });
            this.$message({
              type: "success",
              message: "添加成功!"
            });
            this.group.name = "";
            this.group.note = "";
          });
          break;
        case "update":
          var url = this.apiUrl + "updategroup";
          var data = this.panelData.row;
          data.privilege = this.currentPrivilege;
          console.log(data);
          this.ajax(url, data).then(res => {
            this.tableData.splice([this.panelData.index], 1, data);
            // this.update({
            //   name: "admin",
            //   attr: "privilegeGroup",
            //   data: this.admin["privilegeGroup"]
            // });
            this.$message({
              type: "success",
              message: "修改成功!"
            });
          });
          break;
      }
      this.closePanel();
    },
    closePanel() {
      this.allPrivilege.forEach(function(item) {
        item.selected = false;
      });
      this.panelShow = false;
    },
    privilegeHandle(arr) {
      arr.forEach(item => {
        this.allPrivilege.forEach(function(nitem) {
          if (nitem.value === item) {
            nitem.selected = true;
          }
        });
      });
    },
    ...mapMutations(["updateState"])
  },
  computed: {
    currentPrivilege() {
      var arr = [];
      this.allPrivilege.forEach(function(item) {
        if (item.selected) {
          arr.push(item.value);
        }
      });
      return arr;
    },
    ...mapState(["admin", "sideMenu"])
  },
  filters: {
    tozh: function(val, sideMenu) {
      let res = val;
      sideMenu.forEach(item => {
        if (item.content) {
          item.content.forEach(citem => {
            if (citem.url === val) {
              res = citem.title;
            }
          });
        } else if (item.url === val) {
          res = item.title;
        }
      });
      return res;
    }
  },
  mounted() {
    this.ajax("privilege/getallgroup").then(res => {
      this.tableData = res;
    });
    this.ajax("privilege/getlist").then(res => {
      this.allPrivilege = res.map(function(item) {
        return { value: item, selected: false };
      });
    });
  },
  components: {
    "v-headpanel": Headpanel
  }
};
</script>
<style lang='scss'>
#privilege {
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

  .on {
    background-color: rgba(245, 108, 108, 0.1);
    border-color: rgba(245, 108, 108, 0.2);
    color: #f56c6c;
  }
  .updateNameLabel {
    line-height: 40px;
    text-align: center;
    font-size: 16px;
  }
}
</style>