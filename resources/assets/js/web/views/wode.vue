<style lang="less">
    #wode{
        .avatar {
            width: 58px;
            height: 58px;
            margin: 1em 1em 1em 0;
        }

        .icon{
            width: 2em;
            height: 2em;
        }
    }
</style>

<template>
    <div id="wode">
        <div class="page-part">
            <mt-cell :title="userInfo.nickname">
                <img slot="icon" class="avatar" :src="userInfo.avatar">
            </mt-cell>
        </div>

        <div class="page-part">
            <mt-cell title="借款方案" :to="{path: '/loan/cases'}" is-link>
                <img slot="icon" class="icon" src="/images/icon/icon-01.png">
            </mt-cell>
            <mt-cell title="推荐好友" to="/#/setting" is-link>
                <img slot="icon" class="icon" src="/images/icon/icon-02.png">
            </mt-cell>
        </div>

        <div class="page-part">
            <mt-cell title="关于我们" to="/#/setting" is-link>
                <img slot="icon" class="icon" src="/images/icon/icon-03.png">
            </mt-cell>
            <mt-cell title="设置" :to="{path: '/setting'}" is-link>
                <img slot="icon" class="icon" src="/images/icon/icon-04.png">
            </mt-cell>
        </div>

    </div>
</template>

<script type="es6">
    export default {
        data() {
            return {
                userInfo: {
                    nickname: '请登录',
                    username: null,
                    id: null,
                    avatar: '/images/icon/avatar.png',
                }
            };
        },
        methods: {
            getUserInfo() {
                let self = this;
                this.$http.get('/user/me').then(resp => {
                    if (resp.body.code === 0) {
                        self.userInfo = resp.body.data.user;
                    }
                });
            }
        },
        mounted() {
            this.getUserInfo();
        }
    }
</script>