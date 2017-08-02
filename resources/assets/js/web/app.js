
import Vue from 'vue';
import 'element-ui/lib/theme-default/index.css';
import './views/base.vue';
import ElementUI from 'element-ui';
import router from './router';
import BottomBar from './views/bottom-bar.vue';
import login from './views/login.vue';

Vue.use(ElementUI);

Vue.component('bottom-bar', BottomBar);
Vue.component('login', login);

const app = new Vue({
    router,
    data() {
        return {
            appName: '借贷专家',
            showBottomBar: true,
            showLoginDialog: false
        };
    }
}).$mount('#app');
window.app = app;