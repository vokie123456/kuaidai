<style lang="less">
    .bottom-bar{
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40px;
        background: #fff;
        border-top: 1px solid #aaa;

        .bar-item{
            text-align: center;
        }
    }
</style>

<template>
    <el-row class="bottom-bar" v-show="show">
        <router-link to="/">
            <el-col class="bar-item" :span="8">借款</el-col>
        </router-link>

        <router-link to="/xinyongka">
            <el-col class="bar-item" :span="8">信用卡</el-col>
        </router-link>

        <router-link to="/me">
            <el-col class="bar-item" :span="8">我的</el-col>
        </router-link>
    </el-row>
</template>

<script>
    export default {
        props: ['show'],
        watch: {
            show(val) {
                if (val) {
                    document.body.style.paddingBottom = '40px';
                } else {
                    document.body.style.paddingBottom = 0;
                }
            }
        },
    }
</script>