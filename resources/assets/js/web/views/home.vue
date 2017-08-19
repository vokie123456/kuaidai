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
        <parse-loading :load="parseLoad" :labels="parseLoadLabels"></parse-loading>

        <div class="banner">
            <img src="/images/web/banner.png" class="img-responsive">
        </div>

        <div class="page-offset page-part">
            <div class="notify">
                <img src="/images/icon/icon-05.png" class="icon">
                {{notifyMsg}}
            </div>
        </div>

        <div class="page-part">
            <div class="form">
                <mt-cell title="完善贷款需求，有助于系统精准分析" class="desc-text"></mt-cell>
                <mt-field label="本人姓名" v-model="form.name" placeholder="请输入真实姓名"></mt-field>
                <mt-field label="身份证号" v-model="form.id_card" placeholder="请输入身份证号" type="number"></mt-field>
                <mt-field label="借款金额" v-model="form.loan_amount" type="number">元</mt-field>

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
    import ParseLoading from './compoments/parse-loading.vue';

    Vue.component('choice-box', ChoiceBox);
    Vue.component('parse-loading', ParseLoading);

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
                parseLoad: false,
                parseLoadLabels: [],
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
                notifyMsg: '广州市陈先生通过蚂蚁借呗成功借款1000元',
                products: [],
            };
        },
        methods:{
            handleParse() {
                let area = '';
                let self = this;
                if (window.remote_ip_info.ret === 1) {
                    area = window.remote_ip_info.province + window.remote_ip_info.city;
                }

                // 登录
                if (!window.app.isLogin) {
                    window.app.showLoginDialog = true;
                    return;
                }

                this.form.area = area;
                self.parseLoad = true;
                self.$http.post('/loan/parse', this.form, {unload: true}).then(resp => {
                    if (resp.body.code === 0) {
                        setTimeout(() => {
                            self.parseLoad = false;
                            zhuge.track('快速分析', {
                                '姓名': self.form.name,
                                '身份证': self.form.id_card,
                                '手机号': window.app.userInfo.username,
                                '借贷金额': self.form.loan_amount,
                            });

                            self.$router.push({path: '/loan/cases'});
                        }, 1000);
                    } else {
                        self.parseLoad = false;
                    }
                }).catch(function() {
                    self.parseLoad = false;
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
            updateNotify() {
                let firstNames = ['赵','钱','孙','李','周','吴','郑','王','冯','陈','褚','卫','蒋','沈','韩','杨','朱','秦','尤','许','何','吕','施','张','孔','曹','严','华','金','魏','陶','姜','戚','谢','邹','喻','柏','水','窦','章','云','苏','潘','葛','奚','范','彭','郎','鲁','韦','昌','马','苗','凤','花','方','俞','任','袁','柳','酆','鲍','史','唐','费','廉','岑','薛','雷','贺','倪','汤','滕','殷','罗','毕','郝','邬','安','常','乐','于','时','傅','皮','卞','齐','康','伍','余','元','卜','顾','孟','平','黄','和','穆','萧','尹','姚','邵','湛','汪','祁','毛','禹','狄','米','贝','明','臧','计','伏','成','戴','谈','宋','茅','庞','熊','纪','舒','屈','项','祝','董','梁','杜','阮','蓝','闵','席','季','麻','强','贾','路','娄','危','江','童','颜','郭','梅','盛','林','刁','钟','徐','邱','骆','高','夏','蔡','田','樊','胡','凌','霍','虞','万','支','柯','昝','管','卢','莫','经','房','裘','缪','干','解','应','宗','丁','宣','贲','邓','郁','单','杭','洪','包','诸','左','石','崔','吉','钮','龚','程','嵇','邢','滑','裴','陆','荣','翁','荀','羊','於','惠','甄','麴','家','封','芮','羿','储','靳','汲','邴','糜','松','井','段','富','巫','乌','焦','巴','弓','牧','隗','山','谷','车','侯','宓','蓬','全','郗','班','仰','秋','仲','伊','宫','宁','仇','栾','暴','甘','钭','厉','戎','祖','武','符','刘','景','詹','束','龙','叶','幸','司','韶','郜','黎','蓟','薄','印','宿','白','怀','蒲','邰','从','鄂','索','咸','籍','赖','卓','蔺','屠','蒙','池','乔','阴','郁','胥','能','苍','双','闻','莘','党','翟','谭','贡','劳','逄','姬','申','扶','堵','冉','宰','郦','雍','舄','璩','桑','桂','濮','牛','寿','通','边','扈','燕','冀','郏','浦','尚','农','温','别','庄','晏','柴','瞿','阎','充','慕','连','茹','习','宦','艾','鱼','容','向','古','易','慎','戈','廖','庾','终','暨','居','衡','步','都','耿','满','弘','匡','国','文','寇','广','禄','阙','东','殴','殳','沃','利','蔚','越','夔','隆','师','巩','厍','聂','晁','勾','敖','融','冷','訾','辛','阚','那','简','饶','空','曾','毋','沙','乜','养','鞠','须','丰','巢','关','蒯','相','查','後','荆','红',];
                let citys = ['广州市','深圳市','珠海市','汕头市','佛山市','韶关市','湛江市','肇庆市','江门市','茂名市','惠州市','梅州市','汕尾市','河源市','阳江市','清远市','东莞市','中山市','潮州市','揭阳市','云浮市',];
                let sexs = ['先生', '女士'];
                let self = this;

                setInterval(function() {
                    let city = citys[Math.floor((Math.random() * citys.length))];
                    let firstName = firstNames[Math.floor((Math.random() * firstNames.length))];
                    let product = self.products[Math.floor((Math.random() * self.products.length))];
                    let sex = sexs[Math.floor((Math.random() * sexs.length))];

                    self.notifyMsg = city + firstName + sex + '通过' + product.name + '成功借款' + parseFloat(product.loan_limit_max) + '元';
                }, 5000);
            }
        },
        mounted() {
            this.picker.slotsLoanDeadline[0].defaultIndex = 5;
            this.picker.slotsLoanDeadline[2].defaultIndex = 1;
            this.parseLoadLabels = [];
            this.products = JSON.parse(document.querySelector('#products').innerText);
            for (let i in this.products) {
                this.parseLoadLabels.push(this.products[i].name);
            }

            if (this.parseLoadLabels.length < 30) {
                for (let i = 0; i < 30 - this.parseLoadLabels.length; i++) {
                    this.parseLoadLabels.push(this.parseLoadLabels[i]);
                }
            }

            let config = JSON.parse(document.querySelector('#global').innerText);
            if (config) {
                this.globalData = config;
            }

//            this.form = {
//                name: '林博',
//                id_card: '123123123123123123',
//                loan_amount: 100,
//                loan_deadline: 1,
//                loan_deadline_type: '日',
//                use_loan_time: '2017-08-08',
//                job: 1,
//                more_info: [1]
//            };

            this.updateNotify();
        }
    }
</script>