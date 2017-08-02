<style lang="less">
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
</style>

<template>
    <div>
        {{userInfo}}

        <router-link to="/setting">设置</router-link>
    </div>
</template>

<script type="es6">
    export default {
        data() {
            return {
                userInfo: {
                    nickname: null,
                    username: null,
                    id: null
                }
            };
        },
        methods: {
            getUserInfo() {
                let self = this;
                this.$http.get('/user/me').then(resp => {
                    self.userInfo = resp.body.data.user;
                });
            }
        },
        mounted() {
            this.getUserInfo();
        }
    }
</script>