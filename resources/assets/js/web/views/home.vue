<style lang="less">
    @import "../less/vars";

    #home{
        .notify{
            background: #fff;
            position: relative;
            padding-left: 5em;
            padding-right: 1em;
            color: #999;
            font-size: 0.8em;
            height: 3em;
            line-height: 3em;
            border-radius: 0.3em;
            margin-top: -1.5em;
            box-shadow: 0 0 5px 0 #aaa;

            .icon {
                position: absolute;
                width: 3em;
                height: 1.88em;
                left: 1em;
                top: 0.6em;
            }
        }

        .form{
            .desc-text .mint-cell-text{
                color: #ff3239;
                font-size: 0.8em;
            }
            .mint-field .mint-cell-title{
                width: 130px;
            }

            .choice-box-group{
                display: flex;
                background: #fff;
                padding-bottom: 1em;

                .choice-box{
                    flex: 1;
                    justify-content:center;
                    align-items:center;
                    margin: 0 1em;
                }
            }
        }
    }

</style>

<template>
    <div id="home">
        <div class="banner">
            <img src="/images/web/banner.png" class="img-responsive">
        </div>

        <div class="page-offset page-part">
            <div class="notify">
                <img src="/images/icon/icon-05.png" class="icon">
                广州市陈先生通过蚂蚁借呗成功借款1000元
            </div>
        </div>

        <div class="page-part">
            <div class="form">
                <mt-cell title="完善贷款需求，有助于系统精准分析" class="desc-text"></mt-cell>
                <mt-field label="本人姓名" v-model="form.name" placeholder="请输入真实姓名"></mt-field>
                <mt-field label="身份证号" v-model="form.id_card" placeholder="请输入身份证号"></mt-field>
                <mt-field label="借款金额" v-model="form.loan_amount">元</mt-field>

                <mt-field label="借款期限" v-model="form.loan_deadline">天</mt-field>

                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper" @click="openPicker">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">用款日期</span>
                        </div>
                        <div class="mint-cell-value">
                            <p class="mint-field-core">
                                {{form.use_loan_time}}
                            </p>
                        </div>
                    </div>
                </a>

                <popup-date-picker :show.sync="option.showuse_loan_time"></popup-date-picker>

                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">职业信息</span>
                        </div>
                    </div>
                </a>

                <div class="choice-box-group">
                    <choice-box v-model="form.job" value="1">上班族</choice-box>
                    <choice-box v-model="form.job" value="2">做生意</choice-box>
                    <choice-box v-model="form.job" value="3">学生党</choice-box>
                </div>

                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">补充信息（多选）</span>
                        </div>
                    </div>
                </a>

                <div class="choice-box-group">
                    <choice-box v-model="form.moreInfo" value="a" :multiple="true">有信用卡</choice-box>
                    <choice-box v-model="form.moreInfo" value="b" :multiple="true">淘宝账号</choice-box>
                    <choice-box v-model="form.moreInfo" value="c" :multiple="true">芝麻分高</choice-box>
                </div>

                <div class="choice-box-group">
                    <choice-box v-model="form.moreInfo" value="d" :multiple="true">征信良好</choice-box>
                    <choice-box v-model="form.moreInfo" value="e" :multiple="true">有公积金</choice-box>
                    <choice-box v-model="form.moreInfo" value="f" :multiple="true">有社保</choice-box>
                </div>
            </div>

        </div>

        <div class="page-part page-offset">
            <mt-button class="btn-primary" size="large" @click="handleParse">快速分析</mt-button>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import ChoiceBox from './compoments/choice-box.vue';
    import PopupDatePicker from './compoments/popup-date-picker.vue';

    Vue.component('choice-box', ChoiceBox);
    Vue.component('popup-date-picker', PopupDatePicker);

    let date = new Date();

    export default {
        data() {
            return {
                form: {
                    name: null,
                    id_card: null,
                    loan_amount: null,
                    loan_deadline: null,
                    loan_deadline_type: null,
                    use_loan_time: null,
                    job: null,
                    more_info: []
                },
                option:{
                    showuse_loan_time: false,
                },
            };
        },
        methods:{
            openPicker(){
                this.option.showuse_loan_time = true;
            },
            handleParse() {
                this.$http.post('/loan/parse', this.form).then(resp => {
                    if (resp.body.code === 0) {
                        this.$router.push('/loan/cases');
                    }
                });
            }
        },
        mounted() {
//            this.form = {
//                name: '林博',
//                id_card: '123456789123456789',
//                loan_amount: 100,
//                loan_deadline: 12,
//                loan_deadline_type: '月',
//                use_loan_time: '2017-12-01',
//                job: 1,
//                more_info: [3,4,5]
//            };
        }
    }
</script>