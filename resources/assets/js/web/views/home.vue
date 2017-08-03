<style lang="less">
    @import "../less/vars";

    #home{
        .notify{
            background: #fff;
            position: relative;
            height: 2.5em;
            line-height: 2em;
            margin-bottom: 1em;

            .icon{
                width: 2em;
                height: auto;
                position: absolute;
            }

            .content{
                padding-left: 2.5em;
                padding-right: 0.5em;
            }
        }

        .jiedai {
            background: #fff;
            margin-bottom: 1em;

            .el-form-item{
                border-bottom: #f4f4f4 1px solid;

                .el-date-editor--date {
                    width: 100%;

                    i{
                        display: none;
                    }
                }
            }

            .job-box{
                padding: 0 0.5em;

                .job{
                    width: 100%;
                    border-radius: 5em;
                    border: @mainColor 1px solid;
                    text-align: center;
                    color: @mainColor;
                    line-height: normal;
                    padding: 0.5em 0;
                    margin-bottom:0.5em;
                }
            }
        }
    }

</style>

<template>
    <div id="home">
        <div class="banner">
            <img src="/images/web/banner.png" class="img-responsive">
        </div>

        <el-row class="offset">
            <div class="notify">
                <img src="/images/icon/shenghuoquan.png" class="icon">
                <p class="content">广州市陈先生通过蚂蚁借呗成功借款1000元</p>
            </div>
        </el-row>

        <div class="jiedai">
            <el-form class="offset" ref="form" label-width="5em">
                <el-form-item label="本人姓名">
                    <el-input placeholder="请输入真实姓名"></el-input>
                </el-form-item>

                <el-form-item label="身份证号">
                    <el-input placeholder="请输入身份证号"></el-input>
                </el-form-item>

                <el-form-item label="借款金额">
                    <el-col :span="23"><el-input></el-input></el-col>
                    <el-col :span="1">元</el-col>
                </el-form-item>

                <el-form-item label="借款期限">
                    <el-col :span="23"><el-input></el-input></el-col>
                    <el-col :span="1">天</el-col>
                </el-form-item>

                <el-form-item label="用款日期">
                    <el-date-picker type="date" placeholder="请选择" :editable="false" style="width: 100%;"></el-date-picker>
                </el-form-item>

                <div class="el-form-item">
                    <label class="el-form-item__label" style="width: 5em;">职业入息</label>
                    <div class="clearfix"></div>
                    <div class="el-form-item__content">
                        <el-row>
                            <el-col class="job-box" :span="8">
                                <div class="job">上班族</div>
                            </el-col>
                            <el-col class="job-box" :span="8">
                                <div class="job">做生意</div>
                            </el-col>
                            <el-col class="job-box" :span="8">
                                <div class="job">学生党</div>
                            </el-col>
                        </el-row>
                    </div>
                </div>

                <div class="el-form-item">
                    <label class="el-form-item__label" style="width: 10em;">补充信息（多选）</label>
                    <div class="clearfix"></div>
                    <div class="el-form-item__content">
                        <el-row>
                            <el-col class="job-box" :span="8">
                                <div class="job">有信用卡</div>
                            </el-col>
                            <el-col class="job-box" :span="8">
                                <div class="job">淘宝账号</div>
                            </el-col>
                            <el-col class="job-box" :span="8">
                                <div class="job">芝麻分高</div>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col class="job-box" :span="8">
                                <div class="job">征信良好</div>
                            </el-col>
                            <el-col class="job-box" :span="8">
                                <div class="job">有公积金</div>
                            </el-col>
                            <el-col class="job-box" :span="8">
                                <div class="job">有社保</div>
                            </el-col>
                        </el-row>
                    </div>
                </div>
            </el-form>
        </div>

        <el-row class="offset">
            <el-col>
                <el-button class="btn-primary btn-block">快速分析</el-button>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    export default {
        data() {
            return {
            };
        },
        mounted() {
        }
    }
</script>