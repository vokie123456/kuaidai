define(['jquery', 'ajax'], function($, ajax) {
    var uploader  = {
        token: null,
        upload: function(callback) {
            var $form = $('<form><input type="file" accept="image/*" name="file"><input type="hidden" name="token"></form>');
            $form.find('[name=token]').val(this.token);
            $('body').append($form);

            $form.find('[name=file]').on('change', function() {

                $.ajax({
                    url: 'http://up-z2.qiniu.com/',
                    type: 'POST',
                    data: new FormData($form[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        ajax.loading();
                    },
                    complete: function() {
                        $form.remove();
                        ajax.removeLoading();
                    },
                    success: function(resp) {
                        if(resp.state === 'SUCCESS' && resp.url) {
                            callback(resp.url);
                        } else {
                            console.log(resp);

                            alert(resp.state);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);

                        alert('上传失败');
                    }
                });
            }).click();
        }
    };

    return function(token) {
        uploader.token = token;

        return uploader;
    };
});