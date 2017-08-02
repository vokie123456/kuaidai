<style lang="less">
    #loan-cases {
        background-color: #4970f3;

        .lc-notify {
            background: #6d8df5;
            color: #fff;
            font-size: 0.8em;
            text-align: center;
            padding: 5px 0;
        }

        .lc-cases{
            padding: 1em;

            .case-item {
                border-radius: 5px;
                background-color: #fff;
                height: 7.5em;
                padding: 1em 0.5em;
                margin-bottom: 1em;
            }

            .case-body {
                height: 5em;
                border-bottom: 1px #f4f4f4 solid;
                padding-bottom: 0.5em;

                .icon{
                    height: 100%;
                    width: auto;
                    max-width: 33%;
                    float: left;
                    margin: 0 0.5em;
                }

                img{
                    height: 100%;
                    width: auto;
                }

                .title{
                    font-weight: normal;
                    font-size: 1.3em;
                    color: #545454;
                    margin-bottom: 0.4em;
                }

                .desc{
                    color: #545454;
                }
            }

            .case-footer {
                height: 2.5em;
                margin-top: 0.5em;

                .icon{
                    width: 1.2em;
                    height: 1.2em;
                    float: left;
                    margin-right: 0.2em;
                    margin-top: 0.2em;
                }

                .desc{
                    color: #999999;
                }
                .money {
                    color: #545454;
                }
            }

            .el-col{
                padding: 5px;
            }
        }
    }
</style>

<template>
    <div id="loan-cases">
        <div class="lc-notify">根据系统测算，为您推荐以下贷款产品，通过率80%</div>

        <div class="lc-cases">
            <div v-for="caseInfo in cases" :key="caseInfo.id" @click="handleCase(caseInfo.id)" class="case-item">
                <el-row class="case-body">
                    <img :src="caseInfo.icon" class="icon">

                    <el-col :span="16">
                        <h2 class="title">{{caseInfo.title}}</h2>
                        <p class="desc">已有<span class="red">{{caseInfo.loan_num}}</span>人申请成功</p>
                    </el-col>
                </el-row>

                <el-row class="case-footer">
                    <el-col :span="14">
                        <img src="/images/icon/money.png" class="icon">
                        <span class="desc">额度</span>
                        <span class="money">{{caseInfo.loan_limit}}</span>
                    </el-col>

                    <el-col :span="10">
                        <img src="/images/icon/date.png" class="icon">
                        <span class="desc">期限</span>
                        <span class="money">{{caseInfo.deadline}}</span>
                    </el-col>
                </el-row>
            </div>
        </div>
    </div>
</template>

<script type="es6">
    export default {
        data() {
            return {
                cases: []
            };
        },
        methods: {
            getCases() {
                let self = this;
                this.$http.get('/loan/cases').then(resp => {
                    if (resp.body.code === 0) {
                        self.cases = resp.body.data.cases;
                    }
                });
            },
            handleCase(id) {
                this.$router.push('case/' + id);
            }
        },
        mounted() {
            this.getCases();
        }
    }
</script>