<style lang="less">
    @import "../less/basic.less";
    #app{
        margin-top: 30px;
    }
    .el-form-item{
        padding: 5px 15px;
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
    .box-login{
        margin-top: 50px;
        padding: 0 15px;

        .btn-login{
            background: @mainColor;
            color: #FFF;
            width: 100%;
            padding: 10px;
        }
    }
    .agreement{
        position: fixed;
        bottom: 5px;
        left: 0;
        right: 0;

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
    <div id="app">
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

        <el-col class="box-login">
            <el-button @click="handleLogin" class="btn-login">登录</el-button>
        </el-col>

        <div class="agreement">
            <p>登录即代表您同意 <a href="#">借贷专家服务协议</a></p>
        </div>
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
            };
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
                this.$http.post('/login', this.form).then(() => {
                    window.location.href = '/';
                });
            }
        }
    }
</script>