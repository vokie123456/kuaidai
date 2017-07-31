@extends('admin.layout')

@section('title', '编辑文章')

@section('page-wrapper')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">编辑文章</h1>
        </div>
    </div>

    <div class="row hide" id="container">
        <div class="col-xs-12">
            <form class="form-horizontal" @submit.prevent="submit">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <div class="col-xs-8">
                                <input type="text" class="form-control" v-model="form.title" placeholder="标题">
                            </div>
                            <div class="col-xs-4">
                                <input type="text" class="form-control" v-model="form.author" placeholder="作者">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <textarea class="form-control" rows="30" v-model="form.content" @keydown="textareaKeyEvent"></textarea>
                            </div>
                            <div class="col-xs-6" v-html="preview"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-4">
                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> 选项</div>
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <div class="pull-left"><label class="control-label">状态</label></div>
                                        <div class="col-xs-8">
                                            <label class="radio-inline">
                                                <input type="radio" v-model="form.status" value="2">下线
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" v-model="form.status" value="1">发布
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="pull-left"><label class="control-label">类型</label></div>
                                        <div class="col-xs-8">
                                            <label class="radio-inline">
                                                <input type="radio" v-model="form.type" value="1">文章
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" v-model="form.type" value="2">页面
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="pull-left"><label class="control-label">写作时间</label></div>
                                        <div class="col-xs-8">
                                            <vue-datetime-picker cls="form_datetime form-control" :value.sync="form.write_time" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="pull-left"><label class="control-label">封面</label></div>
                                        <div class="col-xs-8">
                                            <label class="radio-inline">
                                                <input type="radio" v-model="form.cover_type" value="1">无
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" v-model="form.cover_type" value="2">小图
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" v-model="form.cover_type" value="3">大图
                                            </label>
                                        </div>
                                    </div>

                                    <div v-show="isShowCoverBox">
                                        <div class="form-group">
                                            <div class="pull-left"><label class="control-label">&nbsp;</label></div>
                                            <div class="col-xs-10">
                                                <button class="btn btn-default" @click="coverChoose" type="button">从正文中选择</button>
                                                <button class="btn btn-default" @click="coverUpload" type="button">重新上传</button>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="pull-left"><label class="control-label">&nbsp;</label></div>
                                            <div class="col-xs-10">
                                                <img :src="form.cover_url" class="img-responsive img-rounded" :class="{big: form.cover_type == 3, small: form.cover_type == 2}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> 栏目</div>
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        @foreach($columns as $column)
                                            <div class="checkbox"><label><input type="checkbox" v-model="form.column" value="{{$column['id']}}">{{$column['column_name']}}</label></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="glyphicon glyphicon-tag" aria-hidden="true"></span> 标签</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-xs-8">
                                        <input type="text" class="form-control" v-model="iptTags">
                                    </div>
                                    <div class="col-xs-4">
                                        <button class="btn btn-default btn-block" @click="addIptTags" type="button">添加</button>
                                    </div>

                                    <br>
                                    <div class="col-xs-12">
                                        <span id="helpBlock" class="help-block">多个标签请用英文逗号（,）分开</span>
                                    </div>
                                </div>

                                <div class="form-group" v-show="form.tag.length > 0">
                                    <div class="col-xs-12">
                                        <div v-for="(tag, index) in form.tag" class="tag pull-left" style="margin-right: 5px;">
                                            <span class="glyphicon glyphicon-remove-sign" @click="removeTag(index)"></span>
                                            <a href="javascript:void(0);">@{{tag}}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <a href="javascript:void(0);" @click="isShowAllTagBox = !isShowAllTagBox">从常用标签中选择</a>
                                    </div>
                                </div>

                                <div class="form-group" v-show="isShowAllTagBox">
                                    <div class="col-xs-12">
                                        @foreach($tags as $tag)
                                            <a href="javascript:void(0);"><u class="tag" @click="addTag('{{$tag['tag']}}')">{{$tag['tag']}}</u></a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        {{ method_field('PUT') }}
                        <button class="btn btn-primary" type="submit">提交</button>
                        <button class="btn btn-default" @click="showPreview" type="button">预览</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('body-extend')
    <script type="text/plain" id="uploadToken">{{$uploadToken}}</script>
    <script type="text/plain" id="formData">{!! json_encode($form) !!}</script>
    <script>
        var form = JSON.parse(document.getElementById('formData').innerText);
        form._method = 'PUT';
        require(['markdown']);
    </script>
@endsection