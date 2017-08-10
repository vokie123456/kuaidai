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

        .picker-large{
            width: 100%;
            .picker-toolbar {
                border-bottom: solid 1px #eaeaea;
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

                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper" @click="handleOpenLoanDeadline">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">借款期限</span>
                        </div>
                        <div class="mint-cell-value">
                            <p class="mint-field-core">
                                {{form.loan_deadline}}
                            </p>
                            <div class="mint-field-other">{{form.loan_deadline_type}}</div>
                        </div>
                    </div>
                </a>

                <mt-popup v-model="picker.isShowLoanDeadline" position="bottom" class="picker-large">
                    <mt-picker :slots="picker.slotsLoanDeadline" @change="handleLoanDeadlineChange" :visible-item-count="5" :show-toolbar="true">
                        <span class="mint-datetime-action mint-datetime-cancel" @click="picker.isShowLoanDeadline = false;">取消</span>
                        <span class="mint-datetime-action mint-datetime-confirm" @click="handleConLoanDeadlineConfirm">确定</span>
                    </mt-picker>
                </mt-popup>

                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper" @click="handleOpenUseLoanTime">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">用款日期</span>
                        </div>
                        <div class="mint-cell-value">
                            <p class="mint-field-core">
                                {{form.use_loan_time}}
                            </p>
                        </div>
                    </div>

                    <mt-datetime-picker
                            ref="pickerUseLoanTime"
                            type="date"
                            @confirm="handleUseLoanTimeChange"
                            :startDate="picker.startDateUseLoanTime"
                            :endDate="picker.endDateUseLoanTime"
                            year-format="{value} 年"
                            month-format="{value} 月"
                            date-format="{value} 日">
                    </mt-datetime-picker>
                </a>

                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">职业信息</span>
                        </div>
                    </div>
                </a>

                <div class="choice-box-group">
                    <choice-box v-for="item in globalData.jobs" v-model="form.job" :value="item.value">{{item.label}}</choice-box>
                </div>

                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">补充信息（多选）</span>
                        </div>
                    </div>
                </a>

                <template v-for="(item, index) in globalData.extend">
                    <template v-if="index % 3 == 0">
                        <div class="choice-box-group">
                            <choice-box v-model="form.more_info" :value="globalData.extend[index+0].value" :multiple="true">{{globalData.extend[index+0].label}}</choice-box>
                            <choice-box v-model="form.more_info" :value="globalData.extend[index+1].value" :multiple="true">{{globalData.extend[index+1].label}}</choice-box>
                            <choice-box v-model="form.more_info" :value="globalData.extend[index+2].value" :multiple="true">{{globalData.extend[index+2].label}}</choice-box>
                        </div>
                    </template>
                </template>

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
    import DateUtil from 'element-ui/lib/utils/date';

    Vue.component('choice-box', ChoiceBox);

    let now = new Date();
    let nextYear = new Date();
    nextYear.setFullYear(now.getFullYear() + 1);

    let arr = [];
    for (let i = 1; i <= 36; i++) {
        arr.push(i);
    }
    let slotsLoanDeadline = [
        {
            flex: 1,
            defaultIndex: 0,
            values: arr,
        }, {
            divider: true,
            content: '-',
        }, {
            flex: 1,
            defaultIndex: 0,
            values: ['月', '日'],
        }
    ];

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
                picker: {
                    isShowUseLoanTime: false,
                    modelUseLoanTime: null,
                    startDateUseLoanTime: now,
                    endDateUseLoanTime: nextYear,

                    isShowLoanDeadline: false,
                    modelLoanDeadline: null,
                    slotsLoanDeadline: slotsLoanDeadline,
                },
                globalData: {},
            };
        },
        methods:{
            handleParse() {
                this.$http.post('/loan/parse', this.form).then(resp => {
                    if (resp.body.code === 0) {
                        this.$router.push('/loan/cases');
                    }
                });
            },
            handleUseLoanTimeChange(value) {
                this.form.use_loan_time = DateUtil.format(value, 'yyyy-MM-dd');
            },
            handleLoanDeadlineChange(picker, values) {
                this.picker.modelLoanDeadline = values;
            },
            handleConLoanDeadlineConfirm() {
                this.form.loan_deadline = this.picker.modelLoanDeadline[0];
                this.form.loan_deadline_type = this.picker.modelLoanDeadline[1];
                this.picker.isShowLoanDeadline = false;
            },
            handleOpenLoanDeadline() {
                this.picker.isShowLoanDeadline = true;
                this.afterOpenPicker();
            },
            handleOpenUseLoanTime() {
                this.$refs.pickerUseLoanTime.open();
                this.afterOpenPicker();
            },
            afterOpenPicker() {
                window.addEventListener('touchmove', this.moveHandler, false);
                window.addEventListener('touch', this.touchHandler, false);
                window.addEventListener('click', this.touchHandler, false);
            },
            moveHandler() {
                event.preventDefault();
                event.stopPropagation();
            },
            touchHandler() {
                if (event.target.className.includes('mint-datetime-cancel')
                    || event.target.className.includes('mint-datetime-confirm')
                    || event.target.className.includes('v-modal')
                ) {
                    window.removeEventListener('touchmove', this.moveHandler, false);
                    window.removeEventListener('touch', this.touchHandler, false);
                    window.removeEventListener('click', this.touchHandler, false);
                }
            },
        },
        mounted() {
            this.picker.slotsLoanDeadline[0].defaultIndex = 5;
            this.picker.slotsLoanDeadline[2].defaultIndex = 1;


            let config = JSON.parse(document.querySelector('#global').innerText);
            if (config) {
                this.globalData = config;
            }
        }
    }
</script>