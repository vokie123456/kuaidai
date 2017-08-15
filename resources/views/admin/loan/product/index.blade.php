@extends('admin.layout')

@section('title', '贷款产品管理')

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
            <h1 class="page-header">贷款产品管理</h1>
        </el-row>

        <el-row>
            <div class="pull-right">
                <el-col>
                    <el-button type="primary" @click="handleCreate">添加</el-button>
                </el-col>
            </div>
        </el-row>

        <el-row>
            <el-col>
                <el-table :data="tableData">
                    <el-table-column prop="id" label="ID"></el-table-column>
                    <el-table-column prop="name" label="名称"></el-table-column>
                    <el-table-column label="Logo">
                        <template scope="scope">
                            <img :src="scope.row.logo" class="img-responsive">
                        </template>
                    </el-table-column>
                    <el-table-column :formatter="formatLoanLimit" label="贷款额度"></el-table-column>
                    <el-table-column :formatter="formatDeadline" label="贷款期限"></el-table-column>
                    <el-table-column :formatter="formatRate" label="利率"></el-table-column>
                    <el-table-column :formatter="formatStatus" label="是否启用"></el-table-column>
                    <el-table-column :formatter="formatRecommend" label="是否推荐"></el-table-column>
                    <el-table-column label="操作" width="220px">
                        <template scope="scope">
                            <el-button size="small" type="text" @click="handleShow(scope.row)">详情</el-button>
                            <el-button size="small" type="text" @click="handleStatus(1, scope.row, scope.$index)" v-if="scope.row.status != 1">启用</el-button>
                            <el-button size="small" type="text" @click="handleStatus(0, scope.row, scope.$index)" v-if="scope.row.status == 1">禁用</el-button>
                            <el-button size="small" type="text" @click="handleRecommend(1, scope.row, scope.$index)" v-if="scope.row.recommend != 1">设为推荐</el-button>
                            <el-button size="small" type="text" @click="handleRecommend(0, scope.row, scope.$index)" v-if="scope.row.recommend == 1">取消推荐</el-button>
                            <el-button size="small" type="danger" @click="handleDelete(scope.row, scope.$index)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>

        <el-dialog :visible.sync="dialogProductVisible" :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form ref="form" label-width="80px">
                <el-form-item label="产品名称">
                    <el-input v-model="productData.name"></el-input>
                </el-form-item>

                <el-form-item label="申请人数">
                    <el-input v-model="productData.loaneders"></el-input>
                </el-form-item>

                <el-form-item label="跳转URL">
                    <el-input v-model="productData.go_url"></el-input>
                </el-form-item>

                <el-form-item label="Logo">
                    <el-col :span="8">
                        <el-upload action="/loan/product/uploadLogo" :show-file-list="false" :http-request="handleUpload" :data="{option: 'create'}" name="logo">
                            <el-button size="small" type="primary">选择上传</el-button>
                        </el-upload>
                    </el-col>
                    <el-col :span="8">
                        <img v-if="productData.logo" :src="productData.logo" class="img-responsive">
                    </el-col>
                </el-form-item>

                <el-form-item label="额度">
                    <el-col :span="8"><el-input v-model="productData.loan_limit_min" placeholder="最小额度"></el-input></el-col>
                    <el-col :span="2" class="text-center">-</el-col>
                    <el-col :span="8"><el-input v-model="productData.loan_limit_max" placeholder="最大额度"></el-input></el-col>
                    <el-col :span="4" :offset="2">元</el-col>
                </el-form-item>

                <el-form-item label="期限">
                    <el-col :span="8"><el-input v-model="productData.deadline_min" placeholder="最小期限"></el-input></el-col>
                    <el-col :span="2" class="text-center">-</el-col>
                    <el-col :span="8"><el-input v-model="productData.deadline_max" placeholder="最大期限"></el-input></el-col>
                    <el-col :span="4" :offset="2">
                        <el-select v-model="productData.deadline_type" placeholder="单位">
                            {{--<el-option value="年">年</el-option>--}}
                            <el-option value="月">月</el-option>
                            <el-option value="日">日</el-option>
                        </el-select>
                    </el-col>
                </el-form-item>

                <el-form-item label="利率">
                    <el-col :span="8"><el-input v-model="productData.rate_min" placeholder="最小利率"></el-input></el-col>
                    <el-col :span="2" class="text-center">-</el-col>
                    <el-col :span="8"><el-input v-model="productData.rate_max" placeholder="最大利率"></el-input></el-col>
                    <el-col :span="4" :offset="2">
                        <el-select v-model="productData.rate_type" placeholder="单位">
                            <el-option value="年">年</el-option>
                            <el-option value="月">月</el-option>
                            <el-option value="日">日</el-option>
                        </el-select>
                    </el-col>
                </el-form-item>

                <el-form-item label="审核方式">
                    <el-col :span="8">
                        <el-select v-model="productData.audit_type" placeholder="请选择">
                            <el-option v-for="item in baseData.auditTypes" :key="item.value" :value="item.value" :label="item.label"></el-option>
                        </el-select>
                    </el-col>
                </el-form-item>

                <el-form-item label="审核周期">
                    <el-col :span="8"><el-input v-model="productData.audit_cycle"></el-input></el-col>
                    <el-col :span="2" :offset="1">小时</el-col>
                </el-form-item>

                <el-form-item label="放款时间">
                    <el-col :span="8"><el-input v-model="productData.loan_time"></el-input></el-col>
                    <el-col :span="2" :offset="1">小时</el-col>
                </el-form-item>

                <el-form-item label="还款方式">
                    <el-col :span="8">
                        <el-select v-model="productData.loan_give_type" placeholder="请选择">
                            <el-option v-for="item in baseData.loanGiveType" :key="item.value" :value="item.value" :label="item.label"></el-option>
                        </el-select>
                    </el-col>
                </el-form-item>

                <el-form-item label="申请条件">
                    <el-input v-model="productData.condition" type="textarea"></el-input>
                </el-form-item>

                <el-form-item label="关键提醒">
                    <el-select v-model="productData.extend" multiple placeholder="请选择" style="width: 100%;">
                        <el-option v-for="item in baseData.extend" :key="item.value" :value="item.value" :label="item.label"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="职业信息">
                    <el-select v-model="productData.jobs" multiple placeholder="请选择" style="width: 100%;">
                        <el-option v-for="item in baseData.jobs" :key="item.value" :value="item.value" :label="item.label"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="申请流程">
                    <el-input v-model="productData.process" type="textarea"></el-input>
                </el-form-item>

                <el-form-item label="详情介绍">
                    <el-input v-model="productData.detail" type="textarea"></el-input>
                </el-form-item>

            </el-form>
            <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="handleStore">提交</el-button>
        </span>
        </el-dialog>

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
                baseData: baseData,
            };
        },
        methods: {
            handleShow: function(row) {
                var self = this;
                var id = row.id;
                this.$http.get('/loan/product/' + id).then(function(resp) {
                    if (resp.body.code === 0) {
                        self.productData = resp.body.data;
                        self.dialogProductVisible = true;
                    }
                });
            },
            handleDelete: function(scope, index) {
                if (confirm('确认删除')) {
                    var self = this;
                    this.$http.delete('/loan/product/' + scope.id).then(function(resp) {
                        if (resp.body.code === 0) {
                            alert('删除成功');
                            self.tableData.splice(index, 1);
                        }
                    });
                }
            },
            handleStatus: function(status, scope, index) {
                if (confirm('确认操作')) {
                    var self = this;
                    this.$http.put('/loan/product/status/' + scope.id, {status: status}).then(function(resp) {
                        if (resp.body.code === 0) {
                            alert('操作成功');
                            self.tableData[index].status = resp.body.data.status;
                        }
                    });
                }
            },
            handleRecommend: function(recommend, scope, index) {
                if (confirm('确认操作')) {
                    var self = this;
                    this.$http.put('/loan/product/recommend/' + scope.id, {recommend: recommend}).then(function(resp) {
                        if (resp.body.code === 0) {
                            alert('操作成功');
                            self.tableData[index].recommend = resp.body.data.recommend;
                        }
                    });
                }
            },
            handleCreate: function() {
                this.dialogProductVisible = true;
                this.productData = {
                    name: null,
                    go_Url: null,
                    logo: null,
                    loan_limit_min: null,
                    loan_limit_max: null,
                    deadline_min: null,
                    deadline_max: null,
                    deadline_type: null,
                    rate_min: null,
                    rate_max: null,
                    rate_type: null,
                    audit_type: null,
                    audit_cycle: null,
                    loan_time: null,
                    loan_give_type: null,
                    condition: null,
                    extend: [],
                    jobs: [],
                    process: null,
                    detail: null,
                    loaneders: 0,
                };

//                this.productData = {
//                    name: '最优贷',
//                    logo: '/upload/56f5c4ba62a4b631.jpg',
//                    loan_limit_min: 1000,
//                    loan_limit_max: 5000,
//                    deadline_min: 6,
//                    deadline_max: 12,
//                    deadline_type: '月',
//                    rate_min: 0.2,
//                    rate_max: 0.4,
//                    rate_type: '年',
//                    audit_type: 1,
//                    audit_cycle: 72,
//                    loan_time: 12,
//                    loan_give_type: 1,
//                    condition: "条件1\n条件2\n条件3",
//                    extend: [1, 3],
//                    jobs: [1, 2],
//                    process: "流程1\n流程2\n流程N\n流程N1",
//                    detail: '这是详情',
//                };
            },
            handleStore: function() {
                var self = this;
                if (this.productData.id) {
                    this.$http.put('/loan/product/' + this.productData.id, this.productData).then(function(resp) {
                        if (resp.body.code === 0) {
                            alert('提交成功');
                            self.dialogProductVisible = false;
                            window.location.reload();
                        }
                    });
                } else {
                    this.$http.post('/loan/product', this.productData).then(function(resp) {
                        if (resp.body.code === 0) {
                            alert('提交成功');
                            self.dialogProductVisible = false;
                            self.tableData.unshift(resp.body.data);
                        }
                    });
                }
            },
            handleUpload: function(req) {
                var form = new FormData();
                var self = this;
                form.append(req.filename, req.file);
                this.$http.post(req.action, form).then(function(resp) {
                    if (resp.body.code === 0) {
                        if (req.data.option === 'create') {
                            self.productData.logo = resp.body.data.path;
                        }
                    }
                });
            },
            formatLoanLimit: function(row) {
                return parseFloat(row.loan_limit_min) + '-' + parseFloat(row.loan_limit_max) + '元';
            },
            formatRate: function(row) {
                return parseFloat(row.rate_min) + '-' + parseFloat(row.rate_max) + row.rate_type;
            },
            formatDeadline: function(row){
                return row.deadline_min + '-' + row.deadline_max + row.deadline_type;
            },
            formatStatus: function (row) {
                return parseInt(row.status) === 1 ? '是' : '否';
            },
            formatRecommend: function (row) {
                return parseInt(row.recommend) === 1 ? '是' : '否';
            }
        },
    });
</script>
@endsection
