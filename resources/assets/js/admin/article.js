define(['jquery', 'ueditor', 'zeroclipboard', 'ajax', 'datetimepicker', 'datetimepicker-lang', 'ueditor-lang', 'bootstrap'], function($, UE, zcl, ajax) {
    // 添加标签
    function addTag(tag) {
        if (!tag) {
            return;
        }

        var exists = false;
        $('.tag', '#tagBox').each(function() {
            if ($(this).data('tag') == tag) {
                exists = true;
                return false;
            }
        });

        if (!exists) {
            var $tag = $('<div class="tag pull-left" style="margin-right: 5px;">\
                    <span class="glyphicon glyphicon-remove-sign"></span>\
                    <a href="javascript:void(0);"></a>\
                    <input type="hidden" name="tag[]" role="tag">\
                    </div>');

            $tag.attr('data-tag', tag).data('tag', tag);
            $tag.find('a').html(tag);
            $tag.find('[role=tag]').val(tag);
            $('#tagBox').append($tag);
            $tag.find('.glyphicon-remove-sign').click(function() {
                $tag.remove();
            });
        }
    }

    var obj = {
        // 标签操作
        tagOption: function() {
            // 添加标签按钮
            $('#btnAddTag').click(function() {
                var tags = $('#iptTags').val().split(',');
                $('#iptTags').val('');
                for (var i in tags) {
                    addTag(tags[i]);
                }
            });

            // 显示隐藏常用标签
            $('#btnAllTagBox').click(function() {
                if ($('#allTagBox').hasClass('hide')) {
                    $('#allTagBox').removeClass('hide');
                } else {
                    $('#allTagBox').addClass('hide');
                }
            });

            // 选择常用标签
            $('.tag', '#allTagBox').click(function() {
                addTag($(this).html());
            });
        },
        // 预览
        preview: function() {
            $('#btnPreview').click(function() {
                var form = document.createElement('form');
                var data = $.extend(this.form, {});
                data._token = $('meta[name="csrf-token"]').attr('content');
                form.action = '/article-manage/markdown/preview';
                form.method = 'POST';
                form.style.display = 'none';
                form.target = '_blank';

                $.each($('#dataForm').serializeArray(), function(i, obj) {
                    var ipt = document.createElement('textarea');
                    ipt.name = obj.name;
                    ipt.value = obj.value;
                    form.appendChild(ipt);
                });

                document.body.appendChild(form);
                form.submit();
                form.remove();
            });
        },
        // 封面操作
        coverOption: function(ue) {
            var self = this;

            // 封面选择
            $('input[name=cover_type]').click(function() {
                if ($(this).val() == 1) {
                    $('#cover-box').addClass('hide');

                    $('#img-cover-url').attr('src', '');
                    $('[name=cover_url]').val('');
                } else {
                    if ($(this).val() == 2) {
                        $('#img-cover-url').addClass('small').removeClass('big');
                    } else {
                        $('#img-cover-url').addClass('big').removeClass('small');
                    }

                    $('#cover-box').removeClass('hide');
                }
            });

            // 从正文中选择
            $('#btn-cover-choose').click(function() {
                var $modal = $('<div class="modal fade" tabindex="-1" role="dialog">\
                                      <div class="modal-dialog" role="document">\
                                        <div class="modal-content">\
                                          <div class="modal-header">\
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                                            <h4 class="modal-title">请选择</h4>\
                                          </div>\
                                          <div class="modal-body">\
                                            <div class="row"><div class="col-xs-12 modal-images-list"></div></div>\
                                          </div>\
                                          <div class="modal-footer">\
                                            <button type="button" class="btn btn-primary btn-ok">确定</button>\
                                          </div>\
                                        </div>\
                                      </div>\
                                    </div>');
                var $list = $modal.find('.modal-images-list');
                var srcs = [];

                $(ue.getContent()).find('img').each(function() {
                    if ($.inArray($(this).attr('src'), srcs) < 0) {
                        srcs.push($(this).attr('src'));
                    }
                });

                $.each(srcs, function(i, src) {
                    var $li = $('<div class="modal-images-item"><img></div>');

                    $li.children('img').attr('src', src);

                    $li.click(function() {
                        $list.find('.modal-images-item').removeClass('active');
                        $(this).addClass('active');
                    });

                    $list.append($li);
                });

                $modal.find('.btn-ok').click(function() {
                    self.loadCover($list.find('.active img').attr('src'));
                    $modal.modal('toggle').on('hidden.bs.modal', function() {
                        $modal.remove();
                    });
                });

                $modal.modal();
            });

            // 重新上传
            $('#btn-cover-upload').click(function() {
                var $form = $('<form><input type="file" accept="image/*" name="file"><input type="hidden" name="token"></form>');
                $form.find('[name=token]').val($('#uploadToken').html());
                $form.hide();
                $('body').append($form);

                $form.find('[name=file]').on('change', function() {
                    var form = $form[0];

                    $.ajax({
                        url: 'http://up-z2.qiniu.com/',
                        type: 'POST',
                        data: new FormData(form),
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
                            if(resp.state == 'SUCCESS' && resp.url) {
                                self.loadCover(resp.url);
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

            });
        },
        // 加载封面
        loadCover: function(src) {
            $('#img-cover-url').attr('src', src);
            $('[name=cover_url]').val(src);
        },
        init: function() {
            window.ZeroClipboard = zcl;
            window.uploadToken = $('#uploadToken').html();
            // 初始化编辑器
            var ue = UE.getEditor('editor');

            // 日期控件
            $('.datetimepicker').datetimepicker({
                format: 'yyyy-mm-dd',
                language: 'zh-CN',
                minView: 2,
                maxView: 'year',
                autoclose: true,
                todayBtn: true,
                todayHighlight: true,
                weekStart: 0
            });

            this.tagOption();
            this.preview();
            this.coverOption(ue);
        },
        addTag: addTag
   };

    obj.init();

    return obj;
});