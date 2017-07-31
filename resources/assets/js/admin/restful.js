define(['ajax', 'jquery'], function(ajax, $) {

    return {
        deny: function(url) {
            if (confirm('确认禁用？')) {
                ajax.loading();

                ajax.apiPatch(url).done(function() {
                    alert('操作成功，点击确定刷新页面！');
                    window.location.reload();
                }).always(function () {
                    ajax.removeLoading();
                });
            }
        },
        restore: function(url) {
            if (confirm('确认恢复？')) {
                ajax.loading();

                ajax.apiPatch(url).done(function() {
                    alert('操作成功，点击确定刷新页面！');
                    window.location.reload();
                }).always(function () {
                    ajax.removeLoading();
                });
            }
        },
        get: function (url, params) {
            ajax.loading();

            return ajax.apiGet(url, params).always(function () {
                ajax.removeLoading();
            });
        },
        post: function (url, params) {
            ajax.loading();

            ajax.apiPost(url, params).done(function() {
                alert('操作成功，点击确定刷新页面！');
                window.location.reload();
            }).always(function () {
                ajax.removeLoading();
            });
        },
        put: function(url, params) {
            ajax.loading();

            ajax.apiPut(url, params).done(function() {
                alert('操作成功，点击确定刷新页面！');
                window.location.reload();
            }).always(function () {
                ajax.removeLoading();
            });
        },
        patch: function(url, params) {
            if (confirm('是否继续？')) {
                ajax.loading();

                ajax.apiPatch(url, params).done(function() {
                    alert('操作成功，点击确定刷新页面！');
                    window.location.reload();
                }).always(function () {
                    ajax.removeLoading();
                });
            }
        },
        del: function(url, params) {
            if (confirm('确认删除？')) {
                ajax.loading();

                ajax.apiDelete(url, params).done(function() {
                    alert('操作成功，点击确定刷新页面！');
                    window.location.reload();
                }).always(function () {
                    ajax.removeLoading();
                });
            }
        }
    };
});