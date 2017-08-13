<style lang="less">
    @import "../less/vars.less";
    #login{
        .login-dialog {
            width: 90%;
        }

        .mint-field .mint-cell-title{
            width: 4em;
        }

        .mint-cell-wrapper{
            background-size: 100% 1px;
            background-position: bottom left;
        }

        .login-header {
            &:before,
            &:after{
                content: ' ';
                clear: both;
                display: block;
            }

            .icon-close{
                color: #aaa;
                float: right;
                margin-right: 0.5em;
                font-size: 1.3em;
                margin-top: 0.5em;
            }
        }
    }
</style>

<template>
    <div id="login">
        <mt-popup v-model="isShow" class="login-dialog" :closeOnClickModal="false">
            <div class="page-part login-header">
                <i class="mintui mintui-field-error icon-close" @click="isShow = false"></i>
            </div>

            <mt-field label="手机号" placeholder="请输入您的手机号" type="tel" v-model="form.mobile"></mt-field>

            <mt-field label="验证码" v-model="form.captcha" type="number">
                <mt-button @click="handleCaptcha" size="small" :disabled="form.mobile.length != 11 || !canGetCaptcha">{{captchaText}}</mt-button>
            </mt-field>

            <div class="page-part">&nbsp;</div>

            <div class="page-part page-offset">
                <mt-button class="btn-primary" size="large" @click="handleLogin">登录</mt-button>
            </div>
        </mt-popup>
    </div>
</template>

<script>
    import Vue from 'vue';

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
                        self.$router.go(0);
                        self.isShow = false;
                        window.app.showLoginDialog = false;
                        window.app.isLogin = true;
                    }
                });
            }
        }
    }
</script>