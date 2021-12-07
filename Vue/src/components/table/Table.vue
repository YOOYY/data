<template>
  <el-table
    ref="table"
    size="small"
    :data="tableData"
    stripe
    border
    highlight-current-row
    fit
    show-summary
    :height="tableHeight"
  >
    <el-table-column
      v-for="(item, index) in dataMap"
      :key="index"
      :prop="item.value"
      :label="item.label"
      :fixed="index===0?true:false"
      sortable
    ></el-table-column>
  </el-table>
</template>

<script>
export default {
  props: {
    dataMap: {
      type: Array,
      default: []
    },
    tableData: {
      type: Array,
      default: []
    },
    tableHeight: {
      type: Number,
      default: 650
    }
  }
};
</script>

<style lang="scss">
.el-table__fixed {
  height: 690px !important;
}
</style>