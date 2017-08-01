<style lang="less">
</style>

<template>
    <div>
        <el-form ref="form" label-width="40px">
            <el-form-item label="昵称">
                <el-input v-model="form.nickname"></el-input>
            </el-form-item>
        </el-form>

        <el-col class="box-login">
            <el-button @click="handleSubmit" class="btn-login">保存</el-button>
        </el-col>
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
            }
        },
        mounted() {
            this.getUserInfo();
        }
    }
</script>