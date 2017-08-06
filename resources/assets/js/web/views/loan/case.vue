<style lang="less">
    @import "../../less/vars";

    #loan-case {
        padding-bottom: 4em;

        .case-header{
            background: @mainColor;
            padding: 1em;
        }

        .case{
            border-radius: 0.6em;
            background: #fff;
            height: 9.5em;
            box-shadow: 0 0 5px 0 #555;
            padding: 1em;
        }

        .case-body{
            display: flex;
            height: 4em;
            margin-bottom: 1em;

            .case-icon{
                justify-content:center;
                align-items:center;
                width: 4em;
                height: 4em;
            }

            .case-content{
                display: flex;
                flex-direction: column;
                flex:1;
                margin: auto auto auto 1em;
            }


            .case-title{
                font-weight: normal;
                font-size: 1.3em;
                color: #545454;
            }

            .case-apply{
                color: #545454;
            }
        }

        .case-footer{
            display: flex;
            height: 2.5em;

            .case-footer-item{
                display: flex;
                flex: 1;
                flex-direction: column;
                color: #999;
                font-size: 0.9em;
                margin: auto;
            }
        }

        .case-loan-icon{
            width: 1em;
            height: 1em;
            margin: 0.3em;
        }

        .case-weight{
            color: #555;
        }

        .case-weight{
            color: #555;
            font-size: 1.2em;
        }
    }

    .fixed-bottom{
        position: fixed;
        bottom: 1em;
        width: 100%;
        background: #fff;
        padding: 1em;
    }

    .desc-cell{
        background-color: #fff;
    }
    .desc-header{
        height: 2em;
        padding: 0.45em;
        border-bottom: 1px #f4f4f4 solid;
    }
    .desc-body{
        padding: 1em 0;
        color: #555;
        position: relative;
    }

    .choice-box-group{
        background: #fff;
        padding-bottom: 1em;
        position: relative;

        &::before,
        &::after {
            content: ' ';
            clear: both;
            display: table;

        }

        .choice-box{
            margin: 1.6%;
            float: left;
            width: 30%;
            padding: 0.3em;
        }
    }

</style>

<template>
    <div id="loan-case">
        <div class="page-part page-offset case-header">
            <div class="case">
                <div class="case-body">
                    <div class="case-icon">
                        <img :src="caseInfo.logo" class="img-responsive">
                    </div>

                    <div class="case-content">
                        <h3 class="case-title">{{caseInfo.name}}</h3>
                        <p class="case-apply">已有<span class="red">{{caseInfo.loaneders}}</span>人申请成功</p>
                    </div>
                </div>

                <div class="line"></div>

                <div class="case-footer">
                    <div class="case-footer-item">
                        <p class="case-weight">{{caseInfo.loan_limit_min*1}}-{{caseInfo.loan_limit_max*1}}元</p>
                        <p>额度</p>
                    </div>
                    <div class="case-footer-item">
                        <span class="case-weight">{{caseInfo.deadline_min}}-{{caseInfo.deadline_max}}{{caseInfo.deadline_type}}</span>
                        <span>期限</span>
                    </div>
                    <div class="case-footer-item">
                        <span class="case-weight">{{caseInfo.rate_min*1}}-{{caseInfo.rate_max*1}}%</span>
                        <span>{{caseInfo.rate_type}}利率</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="desc-cell page-part page-offset">
            <div class="desc-header">
                <img src="/images/web/condition.png" class="img-flex" alt="申请条件">
            </div>

            <div class="desc-body" v-html="caseInfo.condition"></div>
        </div>

        <div class="desc-cell page-part page-offset">
            <div class="desc-header">
                <img src="/images/web/process.png" class="img-flex" alt="申请流程">
            </div>

            <div class="desc-body">
                <div class="times">
                    <ul>
                        <li v-for="process in caseInfo.process">
                            <b></b>
                            <span>{{process}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="desc-cell page-part page-offset">
            <div class="desc-header">
                <img src="/images/web/audit_instructions.png" class="img-flex" alt="审核说明">
            </div>

            <div class="desc-body">
                <p><span class="audit-label">审核方式：</span>{{caseInfo.audit_type_label}}</p>
                <p><span class="audit-label">审核周期：</span>{{caseInfo.audit_cycle}}小时</p>
                <p><span class="audit-label">放款时间：</span>{{caseInfo.loan_time}}小时</p>
                <p><span class="audit-label">还款方式：</span>{{caseInfo.loan_give_type_label}}</p>
            </div>
        </div>

        <div class="desc-cell page-part page-offset">
            <div class="desc-header">
                <img src="/images/web/remind.png" class="img-flex" alt="关键提醒">
            </div>

            <div class="desc-body choice-box-group">
                <template v-for="(item, index) in caseInfo._extends">
                    <choice-box>{{item.label}}</choice-box>

                    <div v-if="(index + 1) % 3 == 0" class="clearfix"></div>
                </template>
            </div>
        </div>

        <div class="desc-cell page-part page-offset">
            <div class="desc-header">
                <img src="/images/web/detail.png" class="img-flex" alt="详情介绍">
            </div>

            <div class="desc-body" v-html="caseInfo.detail"></div>
        </div>

        <div class="fixed-bottom page-offset">
            <mt-button class="btn-primary" size="large" @click="">立即申请</mt-button>
        </div>
    </div>
</template>

<script type="es6">
    import Vue from 'vue';
    import ChoiceBox from '../compoments/choice-box.vue';

    Vue.component('choice-box', ChoiceBox);

    export default {
        data() {
            return {
                id: null,
                caseInfo: {
                    audit_instructions: null,
                    condition: null,
                    deadline_max: null,
                    deadline_min: null,
                    deadline_type: null,
                    detail: null,
                    loan_limit_max: null,
                    loan_limit_min: null,
                    loaneders:null,
                    logo: null,
                    name: null,
                    rate_max: null,
                    rate_min: null,
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