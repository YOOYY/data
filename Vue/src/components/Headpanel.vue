<template>
    <el-row>
        <el-col class="panelHeading">
            <slot></slot>
        </el-col>
    </el-row>
</template>
<style lang="scss">
    .panelHeading{
        color:#333333;
        font-size: 13px;
        padding: 5px 0 6px 15px;
        background: #e8edf0;
        border-radius: 6px;
        margin-bottom: 15px;
        p{
            margin:4px auto;
        }
        em{
            display: block;
            font-style: normal;
            font-weight: bold;
        }
    }
</style>
