import Vue from 'vue';
import VueRouter from 'vue-router';
import VueResource from 'vue-resource';

Vue.use(VueResource);
Vue.use(VueRouter);

Vue.http.options.emulateJSON = true;
Vue.http.interceptors.push(function(request, next) {
    let self = this;
    let loading = this.$loading({
        fullscreen: true,
        lock: true
    });

    request.headers.set('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

    next(function(response) {
        loading.close();

        if (typeof response.body === 'object') {
            let body = response.body;

            if (body.code === 0) {
                return response;
            } else if (body.code === 200403) {
                window.app.showLoginDialog = true;
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
    { path: '/', component: require('./views/home.vue') },
    { path: '/me', component: require('./views/me.vue'), meta: {title: '我的'} },
    { path: '/setting', component: require('./views/setting.vue'), meta: {title: '设置', showBottomBar: false} },
    { path: '/loan/cases', component: require('./views/loan/cases.vue'), meta: {title: '借贷方案', showBottomBar: false} },
    { path: '/loan/case/:id', component: require('./views/loan/case.vue'), meta: {title: '借贷方案', showBottomBar: false} },
];

let router = new VueRouter({
    routes
});

router.beforeEach((to, from, next) => {
    // 设置底栏
    let showBottomBar = true;
    if (typeof to.meta.showBottomBar !== 'undefined') {
        showBottomBar = !!to.meta.showBottomBar;
    }
    window.app.showBottomBar = showBottomBar;

    // 设置标题
    document.title = to.meta.title || window.app.appName || '借贷专家';

    next();
});

export default router;