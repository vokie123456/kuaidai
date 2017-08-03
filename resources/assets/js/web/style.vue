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

    .page-part {
        margin-bottom: 1em;
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
</style>