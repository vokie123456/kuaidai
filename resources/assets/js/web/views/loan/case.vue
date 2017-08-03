<style lang="less">
    @import "../../less/vars";

    #loan-case {
        padding-bottom: 1em;

        .case-info{
            background-color: #4970f3;
            padding: 1em 0;

            .case-item {
                border-radius: 5px;
                background-color: #fff;
                height: 8.5em;
                padding: 1em 0.5em;
                margin: 0 1em 1em;
            }
        }

        .case-body {
            height: 5em;
            padding-bottom: 0.5em;

            .icon{
                height: 100%;
                width: auto;
                max-width: 33%;
                float: left;
                margin: 0 0.5em;
            }
        }

        .case-footer{
            margin-top: 1em;

            .key{
                color: #999;
                text-align: center;
            }
            .value{
                text-align: center;
                color: #545454;
                margin-bottom: 0.5em;
            }
        }

        .detail-box{

            .detail-item {
                margin-bottom: 1em;
                background: #fff;

                .header{
                    height: 1.25em;
                    border-bottom: 1px #f4f4f4 solid;
                    margin: 0 1em;
                    padding: 0.5em;

                    img{
                        height: 100%;
                    }
                }

                .body{
                    margin: 0 1em;
                    padding: 0.5em;
                }
            }
        }
    }

</style>

<template>
    <div id="loan-case">

        <div class="case-info">
            <div class="case-item">
                <el-row class="case-body">
                    <img :src="caseInfo.icon" class="icon">

                    <el-col :span="16">
                        <h2 class="title">{{caseInfo.title}}</h2>
                        <p class="desc">已有<span class="red">{{caseInfo.loan_num}}</span>人申请成功</p>
                    </el-col>
                </el-row>

                <el-row class="case-footer">
                    <el-col :span="8">
                        <p class="value">{{caseInfo.loan_limit}}</p>
                        <p class="key">额度</p>
                    </el-col>

                    <el-col :span="8">
                        <p class="value">{{caseInfo.deadline}}</p>
                        <p class="key">期限</p>
                    </el-col>

                    <el-col :span="8">
                        <p class="value">{{caseInfo.monthly_rate}}</p>
                        <p class="key">月利率</p>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div class="detail-box">
            <div class="detail-item">
                <div class="header">
                    <img src="/images/web/condition.png">
                </div>
                <div class="body">
                    {{caseInfo.condition}}
                </div>
            </div>

            <div class="detail-item">
                <div class="header">
                    <img src="/images/web/process.png">
                </div>
                <div class="body">
                    {{caseInfo.process}}
                </div>
            </div>

            <div class="detail-item">
                <div class="header">
                    <img src="/images/web/audit_instructions.png">
                </div>
                <div class="body">
                    {{caseInfo.audit_instructions}}
                </div>
            </div>

            <div class="detail-item">
                <div class="header">
                    <img src="/images/web/remind.png">
                </div>
                <div class="body">
                    {{caseInfo.remind}}
                </div>
            </div>

            <div class="detail-item">
                <div class="header">
                    <img src="/images/web/detail.png">
                </div>
                <div class="body">
                    {{caseInfo.detail}}
                </div>
            </div>
        </div>

        <el-row class="offset">
            <el-col>
                <el-button class="btn-primary btn-block">立即申请</el-button>
            </el-col>
        </el-row>
    </div>
</template>

<script type="es6">
    export default {
        data() {
            return {
                id: null,
                caseInfo: {
                    icon: null,
                    title: null,
                    loan_num: null,
                    loan_limit: null,
                    deadline: null,
                    monthly_rate: null,
                    condition: null,
                    process: null,
                    audit_instructions: null,
                    remind: null,
                    detail: null,
                },
            };
        },
        methods: {
            getCase() {
                let self = this;
                this.$http.get('/loan/case/' + this.id).then(resp => {
                    if (resp.body.code === 0) {
                        self.caseInfo = resp.body.data.case;
                    }
                });
            }
        },
        mounted() {
            this.id = this.$route.params.id;
            this.getCase();
        }
    }
</script>