import Vue from 'vue';
import MintUI from 'mint-ui';
import 'mint-ui/lib/style.css';
import router from './router';
import _ from 'lodash';
import style from './style.vue';
import login from './views/login.vue';
import { MessageBox } from 'mint-ui';

Vue.use(MintUI);
Vue.component('login', login);

let app = new Vue({
    el: '#app',
    router,
    data() {
        return {
            appName: '借贷专家',
            tabbars: {
                home: {id: 'home', name: '借贷', icon: null, active: 'tabbar-jiedai-1.png', unactive: 'tabbar-jiedai-2.png', },
                xinyongka: {id: 'xinyongka', name: '信用卡', icon: null, active: 'tabbar-xinyongka-1.png', unactive: 'tabbar-xinyongka-2.png', },
                wode: {id: 'wode', name: '我的', icon: null, active: 'tabbar-wode-1.png', unactive: 'tabbar-wode-2.png', },
            },
            tabbarSelected: null,
            showLoginDialog: false,
            showTabBar: null,
            router: null,
            tabbarHeight: '55px',
        };
    },
    watch: {
        tabbarSelected(val) {
            _.forEach(this.tabbars, function(tabbar) {
                tabbar.icon = tabbar.id === val ? tabbar.active : tabbar.unactive;
                tabbar.icon = '/images/icon/' + tabbar.icon;
            });

            this.$router.push(val);
        },
        router(val){
            let tabbarId = val.path.substr(1, val.path.length);
            if (this.tabbars[tabbarId]) {
                this.tabbarSelected = this.tabbars[tabbarId].id;
            }
        },
        showTabBar(val) {
            if (val) {
                document.body.style.paddingBottom = this.tabbarHeight;
            } else {
                document.body.style.paddingBottom = 0;
            }
        }
    },
    compoments: [style],
    mounted() {
        this.$el.style.display = 'block';
        let tabbarId = this.$route.path.substr(1, this.$route.path.length);
        if (this.tabbars[tabbarId]) {
            this.tabbarSelected = this.tabbars[tabbarId].id;
        }
        if (typeof this.$route.meta.showTabBar !== 'undefined') {
            this.showTabBar = !!this.$route.meta.showTabBar;
        } else {
            this.showTabBar = true;
        }
    }
});

// 替换原生弹窗
window.alert = function(msg, title) {
    title = title || app.appName;
    MessageBox(title, msg);
};

window.app = app;