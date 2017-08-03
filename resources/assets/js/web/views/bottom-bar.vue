<style lang="less">
    .bottom-bar{
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3.5em;
        background: #fff;
        border-top: 1px solid #aaa;

        .bar-item{
            text-align: center;
            padding: 0.1em 0;

            .icon{
                height: 1.875em;
                width: 1.875em;
            }

            .text{
                font-size: 0.68em;
                color: #545454;
            }
        }
    }
</style>

<template>
    <el-row class="bottom-bar" v-show="show">
        <router-link to="/">
            <el-col class="bar-item" :span="8">
                <img src="/images/icon/jiedai.png" class="icon">
                <p class="text">借款</p>
            </el-col>
        </router-link>

        <router-link to="/xinyongka">
            <el-col class="bar-item" :span="8">
                <img src="/images/icon/xinyongka.png" class="icon">
                <p class="text">信用卡</p>
            </el-col>
        </router-link>

        <router-link to="/me">
            <el-col class="bar-item" :span="8">
                <img src="/images/icon/wode.png" class="icon">
                <p class="text">我的</p>
            </el-col>
        </router-link>
    </el-row>
</template>

<script>
    export default {
        props: ['show'],
        watch: {
            show(val) {
                if (val) {
                    document.body.style.paddingBottom = '3.5em';
                } else {
                    document.body.style.paddingBottom = 0;
                }
            }
        },
    }
</script>