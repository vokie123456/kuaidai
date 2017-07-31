define(['vue', 'uploader', 'jquery', 'restful', 'bootstrap', 'hyperdown', 'vue-datetime-picker', 'css!/css/admin/article.css'], function(Vue, Uploader, $, restful) {
    Vue.config.debug = true;
    var HyperDown = window.HyperDown;
    var parser = new HyperDown;
    var uploader = new Uploader($('#uploadToken').html());

    var container = new Vue({
        el: '#container',
        data: {
            form: {},
            preview: '',
            isShowCoverBox: false,
            isShowAllTagBox: false,
            isCreated: false,
            iptTags: ''
        },
        methods: {
            // 上传封面
            coverUpload: function() {
                var self = this;
                uploader.upload(function(url) {
                    self.form.cover_url = url;
                });
            },
            // 从正文选择封面
            coverChoose: function () {
                var self = this;
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

                $(this.preview).find('img').each(function() {
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
                    self.form.cover_url = $list.find('.active img').attr('src');
                    $modal.modal('toggle').on('hidden.bs.modal', function() {
                        $modal.remove();
                    });
                });

                $modal.modal();
            },
            // 添加标签
            addIptTags: function () {
                var tags = this.iptTags.split(',');
                this.iptTags = '';
                var self = this;
                tags.map(function(tag) {
                    self.addTag(tag);
                });
            },
            // 移除已选标签
            removeTag: function (index) {
                this.form.tag.splice(index, 1);
            },
            // 添加标签
            addTag: function (tag) {
                if (tag && $.inArray(tag, this.form.tag) === -1) {
                    this.form.tag.push(tag);
                }
            },
            // 提交表单
            submit: function (event) {
                var form = event.target;
                restful.post(form.action, this.form);
            },
            // 文章预览
            showPreview: function () {
                var form = document.createElement('form');
                var data = $.extend(this.form, {});
                data._token = $('meta[name="csrf-token"]').attr('content');
                form.action = '/article-manage/markdown/preview';
                form.method = 'POST';
                form.style.display = 'none';
                form.target = '_blank';

                $.each(data, function(field, value) {
                    if (field === '_method') {
                        return;
                    }

                    if (typeof value === 'object') {
                        $.each(value, function(i, val) {
                            var ipt = document.createElement('textarea');
                            ipt.name = field + '[]';
                            ipt.value = val;
                            form.appendChild(ipt);
                        });
                    } else {
                        var ipt = document.createElement('textarea');
                        ipt.name = field;
                        ipt.value = value;
                        form.appendChild(ipt);
                    }
                });

                document.body.appendChild(form);
                form.submit();
                form.remove();
            },

            // 输入框按键事件
            textareaKeyEvent: function (e) {
                if(e.keyCode === 9){
                    e.preventDefault();
                    var target = e.target;
                    var tabLength = 4;
                    var selected = window.getSelection().toString();

                    if (e.shiftKey) {
                        var lineNum = target.value.substring(0, target.selectionEnd).replace(/\r\n/, "\n").replace(/\r/, "\n").split("\n").length - 1;
                        var lines = target.value.replace(/\r\n/, "\n").replace(/\r/, "\n").split("\n");
                        var start = target.selectionStart;
                        var end = target.selectionEnd;
                        var offset = 0;

                        for (var i = 0; i < tabLength; i++) {
                            if (lines[lineNum].substring(0, 1) === ' ') {
                                offset++;
                                lines[lineNum] = lines[lineNum].substring(1, lines[lineNum].length);
                            }
                        }

                        target.value = lines.join("\r\n");
                        target.setSelectionRange(start - offset, end - offset);
                    } else {
                        var indent = '';
                        for (var i = 0; i < tabLength; i++) indent += ' ';

                        var start = target.selectionStart;
                        var end = target.selectionEnd;
                        selected = indent + selected.replace(/\n/g, '\n' + indent);
                        target.value = target.value.substring(0, start) + selected + target.value.substring(end);
                        target.setSelectionRange(start + indent.length, start + selected.length);
                    }
                }
            }
        },
        watch: {
            'form.content': function(val) {
                this.preview = parser.makeHtml(val);
            },
            'form.cover_type': function(val) {
                this.isShowCoverBox = parseInt(val) !== 1;
            }
        },
        created: function() {
            this.form = window.form;
        },
        mounted: function () {
            $(this.$el).removeClass('hide');
        }
    });
});