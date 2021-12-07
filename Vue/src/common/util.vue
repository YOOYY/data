<script>
var Qs = require("qs");
var Axios = require("../common/axios.js").default;

exports.install = function(Vue, options) {
  Vue.prototype.ajax = (url, data = {}) => {
    return Axios.post(url, data);
  };

  Vue.prototype.deleteAjax = (url, data, v) => {
    return v
      .$confirm("此操作将删除该项, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
      .then(() => {
        return Axios.post(url, data);
      });
  };

  Vue.prototype.arrClone = arr => {
    return [].concat(arr);
  };

  Vue.prototype.passVerify = (name, rename, v) => {
    if (name != rename) {
      v.$message({
        type: "warning",
        message: "渠道名称不一致!"
      });
      return false;
    }
    if (!name || !rename) {
      v.$message({
        type: "warning",
        message: "渠道名称未填写!"
      });
      return false;
    }
    return true;
  };
};
</script>