<style lang="less">
    @import "../less/basic.less";
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
            <el-form ref="form" label-width="60px">
                <el-form-item label="手机号">
                    <el-input v-model="form.mobile"></el-input>
                </el-form-item>

                <el-form-item label="验证码">
                    <el-col :span="12">
                        <el-input v-model="form.captcha"></el-input>
                    </el-col>

                    <el-col :span="10" :offset="2">
                        <el-button @click="handleCaptcha" class="captcha" :disabled="form.mobile.length != 11 || !canGetCaptcha" type="text">{{captchaText}}</el-button>
                    </el-col>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button @click="handleLogin" class="btn-login">登录</el-button>

                <div class="agreement">
                    <p>登录即代表您同意 <a href="javascript:void(0);">借贷专家服务协议</a></p>
                </div>
            </span>
        </el-dialog>
    </div>
</template>

<script>
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
                this.$http.post('/login', this.form).then(() => {
                    self.isShow = false;
                    self.$router.go(0);
                });
            }
        }
    }
</script>