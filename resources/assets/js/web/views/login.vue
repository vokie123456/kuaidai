<style lang="less">
    @import "../less/vars.less";
    .el-dialog__body{
        padding-left: 0;
        padding-right: 0;
    }
    .el-form-item{
        background: #fff;
        border-bottom: 1px solid #f4f4f4;
        margin-bottom: 0;

        &:last-child{
            border-bottom: 0;
        }

        .el-input__inner{
            border: 0;
        }
    }
    .captcha{
        width: 100%;
        color: @mainColor;
    }
    .btn-login{
        background-color: @mainColor;
        color: #fff;
        width: 100%;
    }
    .agreement{
        margin-top: 5px;

        p {
            text-align: center;
            color: #999;
            font-size: 12px;

            a{
                color: @mainColor;
                text-decoration: none;
            }
        }
    }
</style>

<template>
    <div>
        <el-dialog :visible.sync="isShow" size="large" :close-on-click-modal="false" :close-on-press-escape="false">
            <mt-field placeholder="请输入您的手机号" v-model="form.mobile"></mt-field>
            <mt-field placeholder="请输入验证码" v-model="form.captcha">
                <mt-button @click="handleCaptcha" :disabled="form.mobile.length != 11 || !canGetCaptcha">{{captchaText}}</mt-button>
            </mt-field>
            <mt-button type="primary" size="large" @click="handleLogin">登录</mt-button>
        </el-dialog>
    </div>
</template>

<script>
    import Vue from 'vue';
    import { Dialog } from 'element-ui';
    import 'element-ui/lib/theme-default/dialog.css';
    import 'element-ui/lib/theme-default/icon.css';
    Vue.use(Dialog);

    export default {
        data() {
            return {
                form: {
                    mobile: '',
                    captcha: ''
                },
                canGetCaptcha: true,
                captchaText: '获取验证码',
                isShow: false,
            };
        },
        props: ['show'],
        watch: {
            show(value) {
                this.isShow = value;
            },
            isShow(value) {
                window.app.showLoginDialog = value;
            }
        },
        methods: {
            handleCaptcha() {
                let self = this;
                this.$http.post('/captcha', {mobile: this.form.mobile}).then(() => {
                    self.canGetCaptcha = false;
                    let i = 60;
                    let timer = setInterval(() => {
                        self.captchaText = i + '秒后获取';

                        if (i <= 0) {
                            self.canGetCaptcha = true;
                            self.captchaText = '获取验证码';
                            clearInterval(timer);
                        }

                        i--;
                    }, 1000);
                });
            },

            handleLogin() {
                let self = this;
                this.$http.post('/login', this.form).then(resp => {
                    if (resp.body.code === 0) {
                        self.isShow = false;
                        self.$router.go(0);
                    }
                });
            }
        }
    }
</script>