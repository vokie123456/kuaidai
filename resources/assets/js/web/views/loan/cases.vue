<style lang="less">
    #loan-cases {
        background-color: #4970f3;
        position: relative;

        .lc-notify {
            background: #6d8df5;
            color: #fff;
            font-size: 0.9em;
            text-align: center;
            padding: 0.5em 0;
        }

        .case{
            border-radius: 1em;
            background: #fff;
            height: 9em;
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

        .line{
            background: #f4f4f4;
            height: 1px;
            width: 100%;
        }

        .red{
            color: #ff504d;
        }

        .page-part:last-child {
            margin-bottom: 0;
            padding-bottom: 1em;
        }
    }
</style>

<template>
    <div id="loan-cases">
        <div class="page-part">
            <div class="lc-notify">根据系统测算，为您推荐以下贷款产品，通过率 <span class="red">80%</span></div>
        </div>

        <div v-for="caseInfo in cases" :key="caseInfo.id" @click="handleCase(caseInfo.id)" class="page-part page-offset">
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
                        <img src="/images/icon/coins.png" class="case-loan-icon">
                        <span>额度</span>
                        <span class="case-weight">{{caseInfo.loan_limit_min*1}}-{{caseInfo.loan_limit_max*1}}元</span>
                    </div>
                    <div class="case-footer-item">
                        <img src="/images/icon/dashboard.png" class="case-loan-icon">
                        <span>期限</span>
                        <span class="case-weight">{{caseInfo.deadline_min}}-{{caseInfo.deadline_max}}{{caseInfo.deadline_type}}</span>
                    </div>
                </div>
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
            console.log(document.querySelector('#loan-cases'));
            document.querySelector('#loan-cases').style.minHeight = window.screen.availHeight + 'px';
        }
    }
</script>