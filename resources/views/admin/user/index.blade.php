@extends('admin.layout')

@section('title', '用户管理')

@section('head-extend')
    <link href="//cdn.bootcss.com/element-ui/1.4.1/theme-default/index.css" rel="stylesheet">
    <style>
        .el-row {
            margin-bottom: 20px;
        }
        input.el-upload__input[type=file]{
            display: none;
        }
    </style>
@endsection

@section('page-wrapper')
    <div id="app">
        <el-row>
            <h1 class="page-header">用户管理</h1>
        </el-row>

        <el-row>
            <el-col>
                <el-table :data="tableData">
                    <el-table-column prop="id" label="ID"></el-table-column>
                    <el-table-column prop="username" label="手机"></el-table-column>
                    <el-table-column prop="nickname" label="昵称"></el-table-column>
                    <el-table-column prop="created_at" label="注册时间"></el-table-column>
                </el-table>
            </el-col>
        </el-row>

        <el-row>
            <el-col>
                {!! $paginate->render() !!}
            </el-col>
        </el-row>
    </div>

<script src="//cdn.bootcss.com/vue/2.4.2/vue.min.js"></script>
<script src="//cdn.bootcss.com/vue-resource/1.3.4/vue-resource.min.js"></script>
<script src="//cdn.bootcss.com/element-ui/1.4.1/index.js"></script>
<script id="paginate" type="text/html">{!! $paginate->toJson() !!}</script>
<script>
    var paginate = JSON.parse(document.querySelector('#paginate').innerHTML);

    Vue.http.options.emulateJSON = true;
    Vue.http.interceptors.push(function(request, next) {
        var loading = this.$loading({
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

    var app = new Vue({
        el: '#app',
        data: function() {
            return {
                tableData: paginate.data,
            };
        },
        methods: {
        },
        mounted: function() {
        }
    });
</script>
@endsection
