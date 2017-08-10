@extends('admin.layout')

@section('title', '贷款信息管理')

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
            <h1 class="page-header">贷款信息管理</h1>
        </el-row>

        <el-row>
            <el-col>
                <el-table :data="tableData">
                    <el-table-column prop="id" label="ID"></el-table-column>
                    <el-table-column prop="user.username" label="手机"></el-table-column>
                    <el-table-column prop="name" label="姓名"></el-table-column>
                    <el-table-column prop="id_card" label="身份证"></el-table-column>
                    <el-table-column prop="loan_amount" label="借款金额"></el-table-column>
                    <el-table-column :formatter="formatLoanAmount" label="借款期限"></el-table-column>
                    <el-table-column prop="job" :formatter="formatJob" label="职业信息"></el-table-column>
                    <el-table-column prop="_extends" :formatter="formatExtends" label="补充信息"></el-table-column>
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
<script id="baseData" type="text/html">{!! json_encode($baseData) !!}</script>
<script>
    var paginate = JSON.parse(document.querySelector('#paginate').innerHTML);
    var baseData = JSON.parse(document.querySelector('#baseData').innerHTML);

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
                dialogProductVisible: false,
                productData: {},
                baseData: {},
            };
        },
        methods: {
            formatLoanAmount: function(row) {
                return parseInt(row.loan_deadline) + row.loan_deadline_type;
            },
            formatJob: function(row) {
                return this.baseData.jobs[row.job] || '未知';
            },
            formatExtends: function(row) {
                var exts = [];
                for (var ext of row._extends) {
                    if (this.baseData._extends[ext.extend]) {
                        exts.push(this.baseData._extends[ext.extend]);
                    }
                }
                return exts.join('、');
            }
        },
        mounted: function() {
            this.baseData.jobs = {};
            this.baseData._extends = {};

            for (var item of baseData.jobs) {
                this.baseData.jobs[item.value] = item.label;
            }

            for (var item of baseData.extend) {
                this.baseData._extends[item.value] = item.label;
            }
        }
    });
</script>
@endsection
