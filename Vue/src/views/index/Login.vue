<template>
  <div id="login">
    <el-col
      class="main"
      :xs="{span:10,offset:7}"
      :sm="{span:8,offset:8}"
      :lg="{span:5,offset:9}"
      v-loading="loading"
      element-loading-text="登录中"
      element-loading-background="rgba(255, 255, 255, 0.3)"
    >
      <img :src="admin.userImg" />
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" hide-required-asterisk>
        <el-form-item prop="name">
          <el-input v-model.trim="ruleForm.name" autofocus clearable autocomplete="on">
            <i slot="prefix" class="iconfont icon-user"></i>
          </el-input>
        </el-form-item>

        <el-form-item prop="password">
          <el-input v-model.trim="ruleForm.password" clearable type="password">
            <i slot="prefix" class="iconfont icon-password"></i>
          </el-input>
        </el-form-item>

        <el-form-item>
          <el-button type="success" @click="submitForm('ruleForm')">登录</el-button>
        </el-form-item>
      </el-form>
    </el-col>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";

export default {
  name: "Login",
  data() {
    return {
      ruleForm: {
        name: "",
        password: ""
      },
      rules: {
        name: [
          { required: true, message: "请输入用户名", trigger: "blur" },
          { min: 3, max: 11, message: "长度在 3 到 11 个字符", trigger: "blur" }
        ],
        password: [
          { required: true, message: "请输入密码", trigger: "blur" },
          { min: 3, max: 11, message: "长度在 3 到 11 个字符", trigger: "blur" }
        ]
      },
      loading: false
    };
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.loading = true;
          this.login(this.ruleForm)
            .then(admin => {
              localStorage.setItem("name", this.ruleForm.name);
              localStorage.setItem("password", this.ruleForm.password);
              localStorage.setItem("userImg", this.admin.userImg);
              if (admin.privilege.length > 0) {
                this.$router.push({ name: admin.privilege[0] });
              } else {
                this.$message({
                  type: "warning",
                  message: "登陆失败!失败原因:账号无权限"
                });
                this.loading = false;
              }
            })
            .catch(e => {
              this.$message({
                type: "error",
                message: "登陆失败!失败原因:" + e
              });
              this.loading = false;
            });
        } else {
          this.$message({
            type: "warning",
            message: "登录格式错误!"
          });
          return false;
        }
      });
    },
    ...mapActions(["login"])
  },
  created() {
    let name = localStorage.getItem("name");
    let password = localStorage.getItem("password");
    if (name && password) {
      this.ruleForm.name = name;
      this.ruleForm.password = password;
      this.loading = true;
      this.login(this.ruleForm)
        .then(admin => {
          localStorage.setItem("userImg", this.admin.userImg);
          if (admin.privilege.length > 0) {
            this.$router.push({ name: admin.privilege[0] });
          } else {
            this.$message({
              type: "warning",
              message: "登陆失败!失败原因:账号无权限"
            });
            this.loading = false;
          }
        })
        .catch(e => {
          this.$message({
            type: "warning",
            message: "登陆失败!失败原因:" + e
          });
          this.loading = false;
        });
    }
  },
  computed: {
    ...mapState(["admin"])
  }
};
</script>

<style lang="scss">
#login {
  background: url(/static/img/bg.jpg) 0 0 / cover;
  height: 100vh;
  .main {
    top: 20%;
    position: fixed;
    padding: 20px;
    border: 1px solid #e3e3e3;
    border-radius: 3px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background: rgba(255, 255, 255, 0.2);
  }
  button {
    width: 100%;
  }
  img {
    width: 50%;
    margin: 0 auto;
    padding-bottom: 20px;
    display: block;
    border-radius: 50%;
  }
  .iconfont {
    font-size: 20px;
  }
}
</style>
