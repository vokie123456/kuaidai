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
                <mt-field label="身份证号" v-model="form.idCard" placeholder="请输入身份证号"></mt-field>
                <mt-field label="借款金额" v-model="form.loanAmount">元</mt-field>

                <mt-field label="借款期限" v-model="form.loanDeadline">天</mt-field>

                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper" @click="openPicker">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">用款日期</span>
                        </div>
                        <div class="mint-cell-value">
                            <p class="mint-field-core">{{form.useLoanTime}}</p>
                        </div>
                    </div>
                    <mt-datetime-picker ref="picker" v-model="form.useLoanTime" type="date"></mt-datetime-picker>
                </a>

                <mt-field label="职业信息" v-model="form.job">

                </mt-field>

                <mt-field label="补充信息（多选）" v-model="form.moreInfo">

                </mt-field>

            </div>

        </div>

        <div class="page-part page-offset">
            <mt-button type="primary" size="large" @click="">快速分析</mt-button>
        </div>
    </div>
</template>

<script>
    let date = require('element-ui/lib/utils/date');
    export default {
        data() {
            return {
                form: {
                    name: '',
                    idCard: '',
                    loanAmount: '',
                    loanDeadline: '',
                    useLoanTime: '',
                    job: '',
                    moreInfo: []
                },
            };
        },
        methods:{
            openPicker(){
                this.$refs.picker.open();
            },
            format(dateObj, mask) {
                return date.format(dateObj, mask);
            }
        },
        watch:{
            'form.useLoanTime'(param) {
                let _t = null;
                _t = ((_t = typeof (param)) === 'object' ? Object.prototype.toString.call(param).slice(8, -1) : _t);
                if (_t === 'Date') {
                    this.form.useLoanTime = date.format(param, 'yyyy-mm-dd');
                }
            }
        },
        mounted() {
        }
    }
</script>