
import Vue from 'vue';
import 'element-ui/lib/theme-default/index.css';
import App from './views/login.vue';
import ElementUI from 'element-ui';
import VueResource from 'vue-resource';

Vue.use(VueResource);
Vue.use(ElementUI);

Vue.http.options.emulateJSON = true;
Vue.http.interceptors.push(function(request, next) {
    let loading = this.$loading({
        fullscreen: true,
        lock: true
    });

    request.headers.set('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

    next(function(response) {
        loading.close();

        if (typeof response.body === 'object') {
            var body = response.body;

            if (body.code === 0) {
                return response;
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

new Vue({
    el: '#app',
    render: h => h(App),
});