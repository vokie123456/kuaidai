const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
*/

elixir(function(mix) {
    // Web
    mix.webpack('web/app.js', 'public/js/app.js');
    mix.version([
        'js/app.js'
    ]);

    mix.browserSync({
        proxy: 'kd-l.lbog.cn'
    });

    return;

    mix.scripts('require-config.js');
    mix.scripts('utils.js');
    mix.scripts('ajax.js');
    mix.scripts('admin/sb-admin-2.js', 'public/js/admin');
    mix.scripts('admin/restful.js', 'public/js/admin');
    mix.scripts('admin/article.js', 'public/js/admin');
    mix.scripts('admin/markdown.js', 'public/js/admin');
    mix.less('admin/article.less', 'public/css/admin');

    mix.scripts('vue/vue-datetime-picker.js', 'public/js');
    mix.scripts('uploader.js', 'public/js');

    mix.styles('sb-admin-2.css');
    // mix.styles('style.css');
    mix.less('blog/style.less', 'public/css/style.css');

    mix.scripts('ueditor.requirejs.js', 'public/plugins/ueditor/ueditor.requirejs.min.js');
    mix.scripts('ueditor.requirejs.js', 'public/plugins/ueditor/ueditor.requirejs.js');
});
