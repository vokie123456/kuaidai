import Vue from 'vue';
import VueRouter from 'vue-router';
import VueResource from 'vue-resource';

// 引入Loading组件
import { Indicator } from 'mint-ui';

Vue.use(VueResource);
Vue.use(VueRouter);

Vue.http.options.emulateJSON = true;
Vue.http.interceptors.push(function(request, next) {
    let unload = false;
    if (request.unload) {
        unload = true;
    }

    if (!unload) {
        Indicator.open();
    }

    request.headers.set('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

    next(function(response) {
        if (!unload) {
            Indicator.close();
        }

        if (typeof response.body === 'object') {
            let body = response.body;
            let code = body.code;

            if (code === 0) {
                return response;
            } else if (body.code === 200403) {
                // 登录
                window.app.showLoginDialog = true;
            } else if (code === 200003) {
                window.alert(body.msg).then(() => {
                    // 跳转到首页填写贷款信息
                    this.$router.push('/');
                });
            } else {
                alert(body.msg);

                throw response;
            }
        } else {
            alert('网络异常！');

            throw response;
        }
    });
});

let routes = [
    { path: '/home', component: require('./views/home.vue') },
    { path: '/wode', component: require('./views/wode.vue'), meta: {title: '我的'} },
    { path: '/setting', component: require('./views/setting.vue'), meta: {title: '设置', showTabBar: false, checkLogin: true} },
    { path: '/loan/cases', component: require('./views/loan/cases.vue'), meta: {title: '借贷方案', showTabBar: false, checkLogin: true} },
    { path: '/loan/case/:id', component: require('./views/loan/case.vue'), meta: {title: '借贷方案', showTabBar: false, checkLogin: true} },

    // 默认跳首页
    { path: '*', redirect: {path: '/home'} },
];

let router = new VueRouter({
    routes
});

router.beforeEach((to, from, next) => {
    // 验证登录
    if (typeof to.meta.checkLogin !== 'undefined' && to.meta.checkLogin && !window.app.isLogin) {
        // 登录
        window.app.showLoginDialog = true;
        next(false);
        return false;
    }

    // 设置底栏
    let showTabBar = true;
    if (typeof to.meta.showTabBar !== 'undefined') {
        showTabBar = !!to.meta.showTabBar;
    }
    window.app.showTabBar = showTabBar;
    window.app.router = to;

    // 设置标题
    document.title = to.meta.title || window.app.appName || '借贷专家';

    // 每次跳转都让页面回到顶部
    document.body.scrollTop = document.documentElement.scrollTop = 0;

    next();
});

export default router;