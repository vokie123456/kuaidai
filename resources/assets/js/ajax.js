define(['jquery'], function($) {
    function handle(url, params, type, showError) {
        showError = showError || true;

        return $.ajax({
            url: url,
            data: params || {},
            type: type || 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).then(function (resp, status, xhr) {
            // 成功回调
            if (resp.code == 0) {
                // 直接返回要处理的数据，作为默认参数传入之后done()方法的回调
                return resp.data;
            } else {
                // 返回一个失败状态的deferred对象，把错误代码作为默认参数传入之后fail()方法的回调
                return $.Deferred().reject(xhr, resp.msg);
            }
        }, function (xhr) {
            return $.Deferred().reject(xhr);
        }).fail(function(xhr, msg) {
            if (!msg) {
                msg = 'URL：' + url + "\n" + '网络请求失败！' + "\n" + xhr.status + '：' + xhr.statusText;
                console.log(xhr);
            }

            if (showError) {
                alert(msg);
            }
        });
    }

    var $backDrop;

    return {
        apiGet: function(url, params) {
            return handle(url, params);
        },
        apiPost: function(url, params) {
            return handle(url, params, 'POST');
        },
        apiPut: function(url, params) {
            return handle(url, params, 'PUT');
        },
        apiPatch: function(url, params) {
            return handle(url, params, 'PATCH');
        },
        apiDelete: function(url, params) {
            return handle(url, params, 'DELETE');
        },
        handle: handle,
        loading: function() {
            $backDrop = $('<div class="modal-backdrop fade in" style="z-index: 9999;"></div>\
                <div style="position: fixed;top: 50%; left: 50%; background: #FFF; border: 1px #000 solid;z-index: 10000; padding: 5px;">\
                    <img src="/images/loading.gif" alt="loading">加载中...\
                </div>\
                ');

            // 使当前激活的元素失去焦点，防止按住空格后，多次点击
            document.activeElement.blur();

            $('body').append($backDrop);
        },
        removeLoading: function() {
            $backDrop.remove();
        }
    };
});