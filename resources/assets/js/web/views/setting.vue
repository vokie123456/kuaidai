<style lang="less">
    #setting{

    }
</style>

<template>
    <div id="setting">
        <div class="page-part">&nbsp;</div>

        <div class="page-part">
            <mt-field label="昵称" v-model="form.nickname"></mt-field>
        </div>

        <div class="page-part page-offset">
            <mt-button class="btn-primary" size="large" @click="handleSubmit">保存</mt-button>
        </div>

        <div class="page-part page-offset">
            <mt-button size="large" @click="handleLogout">退出登录</mt-button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    id: null,
                    nickname: null
                }
            };
        },
        methods: {
            getUserInfo() {
                let self = this;
                this.$http.get('/user/me').then(resp => {
                    self.form = resp.body.data.user;
                });
            },
            handleSubmit() {
                let self = this;
                let params = {
                    nickname: this.form.nickname
                };
                this.$http.post('/user/set-user-info', params).then(() => {
                    alert('修改成功');
                    self.$router.go(-1);
                });
            },
            handleLogout() {
                let self = this;
                this.$http.get('/user/logout').then((resp) => {
                    if (resp.body.code === 0) {
                        self.$router.push({path: '/'});
                        window.app.isLogin = false;
                    }
                });
            }
        },
        mounted() {
            this.getUserInfo();
        }
    }
</script>