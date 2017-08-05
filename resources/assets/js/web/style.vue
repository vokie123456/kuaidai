<style lang="less">
    @import "./less/vars";
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html{
        font-size: 100%;
    }
    body{
        font-size: 1em;
        background: #f4f4f4;
    }

    // 左右边距
    .mint-cell-wrapper{
        padding: 0 1.5em;
    }

    #app {
        position: relative;
    }

    .img-responsive{
        display: block;
        height: auto;
        max-width: 100%;
    }

    .img-flex {
        height: 100%;
        width: auto;
        display: block;
    }

    .page-part {
        margin-bottom: 1em;
        &:last-child {
            margin-bottom: 0;
            padding-bottom: 1em;
        }
    }

    .page-offset {
        padding-left: 1em;
        padding-right: 1em;
    }

    .btn-primary {
        background: @mainColor;
        color: #fff;
    }

    // 重写tabbar选中时的背景样式
    .mint-tabbar > .mint-tab-item.is-selected{
        background-color: transparent;
    }

    .red{
        color: #ff504d;
    }

    .clearfix,
    .clearfix:after,
    .clearfix:before {
        clear: both;
    }

    // 时间轴
    .times {
        display: block;
        margin: 15px 0;

        ul {
            @pointColor: #aaa;
            @pointSize: 10px;

            border-left: 1px solid @pointColor;
            list-style: none;

            li {
                width: 100%;
                margin-left: -(@pointSize + 1);
                line-height: 30px;
                font-weight: normal;

                b {
                    width: @pointSize;
                    height: @pointSize;
                    background: @pointColor;
                    margin: 10px 5px;
                    border-radius: 6px;
                    overflow: hidden;
                    display: inline-block;
                    float: left;
                }

                span {
                    padding-left: 7px;
                    line-height: 30px;
                }

                p {
                    padding-left: 15px;
                    font-size: 14px;
                    line-height: 25px;
                }
            }
        }
    }

    .mint-msgbox-confirm{
        font-size:1em;
    }

</style>