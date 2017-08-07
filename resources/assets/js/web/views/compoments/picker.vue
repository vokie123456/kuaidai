<!--
选择器组件
@author:何世华
-->
<template>
    <div class="picker" :style="is_show?'right:0':'right:-100%'" @click="close">
        <div class="picker_body" @click.stop="">
            <div>
                {{msg}}
            </div>
            <div class="picker_title">
                <!--<div @click="clickCancel">
                    取消
                </div>-->
                <div class="picker_text">
                    {{title}}
                </div>
                <!--<div @click="clickOk">
                    确定
                </div>-->
            </div>
            <div class="picker_item_title">
                <div class="picker_item_title_item" v-for="(list,index) in data"
                    :style="{'flex':width_ratio&&width_ratio[index]?width_ratio[index]:1}"
                >
                    {{item_title[index]}}
                </div>
            </div>
            <div class="picker_main">
                <div class="picker_list" :data-index="index" v-for="(list,index) in data"
                     :style="{'flex':width_ratio&&width_ratio[index]?width_ratio[index]:1}"
                     @touchstart="scrollStart"
                     @touchmove="scrolling"
                     @touchend="scrollEnd">
                    <div class="picker_item">&nbsp;</div>
                    <div class="picker_item">&nbsp;</div>
                    <div class="picker_item"
                         :data-index="sub_index"
                         :class="{'selected':cur_val[index]==item}"
                         :style="{'color': (cur_val[index]==item&&selected_color)?selected_color:''}"
                         v-for="(item,sub_index) in list">
                        {{item}}
                    </div>
                    <div class="picker_item">&nbsp;</div>
                    <div class="picker_item">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .picker{
        position:fixed;
        top:0;
        width:100%;
        height:100%;
        background: rgba(0,0,0,0.5);
        z-index: 10000;
        /*display:none;*/
    }
    .picker_item_title{
        display: flex;
    }
    .picker_item_title .picker_item_title_item{
        flex:1;
        padding:0.5rem 0;
        text-align: center;
        font-size: 0.7rem;
    }
    .picker_body{
        position:absolute;
        bottom:0;
        width:100%;
        background: #fff;
        z-index: 10001;
    }
    .picker_title{
        display: flex;
        text-align: center;
        border-bottom: 1px solid #e4e4e4;
    }
    .picker_title>*{
        padding:0.5rem 0;
        flex:1;
    }
    .picker_title>.picker_text{
        flex:3
    }
    .picker_main{
        display: flex;
        overflow: hidden;
        height:10rem;/*高度=80px/40*5*/
        position: relative;
    }
    .picker_list{
        flex:1;
        text-align: center;
        /*transition: transform 0.1s;*/
        /*overflow: hidden;*/
        height: 100000%;
    }
    .picker_main:before{
        content: '';
        position: absolute;
        top: 4rem;
        width:100%;
        background: rgba(239,239,239,1);
        height: 2rem;
        z-index: -1;
    }
    .picker_item{
        height:2rem;
        line-height: 300%;
        z-index:10001;
    }
    .picker_item.selected{
        color: #09b6f2;
    }
</style>
<script>
//    var iScroll = require('iscroll');
//    import utils from 'utils'
//    import iScroll from 'iscroll'
    import Vue from 'vue';
    import $ from 'jquery';

    module.exports = Vue.extend({
        props: [
            'value',//v-model绑定value
            'data',//列表数据，二维数组
            'title',//标题
            'item_title',//选项标题
            'is_show',//是否展示
            'selected_color',//选项颜色
            'width_ratio',//宽度比
        ],
        data(){
            return{
//                currentValue:this.value,
                curData:null,
                cur_val:[],
                token:'',
                hadInited:false,
                scroll_data:{
                    picker_item_height:0,
                    cur_translate_val:0,
                    scrolling_event:null,
                },
                isScrolling:false,
                msg:'',
            }
        },
        beforeMount:function(){
            var _self = this;
            //初始化选项
            for(var i=0;i<_self.data.length;i++){
                _self.cur_val.push(_self.data[i][0]);
            }
        },
        mounted: function() {
            var _self = this;
//            _self.$refs.picker.value = ['粤','A'];
//            _self.$emit('value', ['粤','A']);
//            _self.$emit('value', ['粤','A']);
//            _self.model = ['粤','A'];
//            console.log(_self.data);
//            _self.init();
        },
        updated:function () {
            var _self = this;
            if(_self.is_show && !_self.hadInited){
                _self.init();
            }
        },
        methods:{
            init(){
//                console.log('初始化数据');
                var _self = this;
                _self.scroll_data.picker_item_height
                    = parseFloat($(".picker_item").css('height').replace('px',''));

                //自动选择
//                console.log(_self.value);
                if(_self.value&&_self.data.length>0){
                    _self.cur_val = _self.value;
//                console.log($('.picker_list'));
                    for(var i=0;i<_self.value.length;i++){
                        for(var j=0;j<_self.data[i].length;j++){
                            if(_self.value[i] == _self.data[i][j]){
//                                console.log(i,j);
//                                _self.scrollToItem(i,j);
                                _self.scrollToItem(i,j);
                            }
                        }
                    }
                }
                _self.hadInited = true;
            },
            //点击item
            clickItem(e){
                var _self = this;
                var $parent = $(e.target).parents('.picker_list');
                var $this = $(e.target);
                var cur_list_index = $parent[0].dataset.index;
                var cur_item_index = $this[0].dataset.index;
//                console.log('点击item',cur_list_index,cur_item_index);
                _self.scrollToItem(cur_list_index,cur_item_index);
                _self.$emit('scroll',_self.cur_val);//发射到滚动事件
            },
            //滚动
            scrollStart(e){
//                this.msg='滚动开始';
                e.preventDefault();
                var _self = this;
                var $this = $(e.target).parents('.picker_list');
                _self.scroll_data.scrolling_event = e;
                //当前tansform的Y轴值
                var cur_translate_val = _self.getMarginTop($this);
                _self.scroll_data.cur_translate_val = cur_translate_val;
            },
            scrolling(e){
                var _self = this;
                _self.isScrolling = true;
                var $this = $(_self.scroll_data.scrolling_event.target).parents('.picker_list');
                if(_self.scroll_data.scrolling_event){
//                    console.log('滚动中');
//                    console.log('滚动中',_self.scrolling_event,$this);
//                    console.log('鼠标位置:',
//                        _self.scrolling_event.touches[0].pageY,
//                        e.touches[0].pageY);
                    var distance = _self.scroll_data.cur_translate_val+e.touches[0].pageY-_self.scroll_data.scrolling_event.touches[0].pageY;
//                    console.log('当前tansform',cur_translate_val);
//                    console.log('滑动距离',distance);
                    $this.css({
                        /*'transition': 'transform 0s',
                        'webkitTransition': 'transform 0s',
                        'webkitTransform':'translateY('+distance+'px)',
                        'transform':'translateY('+distance+'px)'*/
                        'transition': 'margin-top 0s',
                        'webkitTransition': 'margin-top 0s',
                        'margin-top':distance+'px'
                    });
                }
            },
            scrollEnd(e){
//                this.msg='';
                var _self = this;
                var pre_event = _self.scroll_data.scrolling_event;
                var cur_event = e;
                var $this = $(pre_event.target).parents('.picker_list');
//                console.log('滚动结束',$this);
                if(_self.isScrolling){
                    _self.isScrolling = false;
                }else{
//                    console.log('滚动结束',$this[0].dataset.index,$(pre_event.target)[0].dataset.index);
                    _self.scrollToItem($this[0].dataset.index,$(pre_event.target)[0].dataset.index);
                    return;
                }



                var time_distance = cur_event.timeStamp-pre_event.timeStamp;
//                console.log('滚动时间差',time_distance);
                var scroll_distance = cur_event.changedTouches[0].pageY-pre_event.touches[0].pageY;
//                scroll_distance = scroll_distance<0?-scroll_distance:scroll_distance;
//                console.log('滚动距离差',scroll_distance);

                //之前的tansform的Y轴值
                var pre_translate_val = _self.scroll_data.cur_translate_val;
//                console.log(_self.scroll_data);
                //当前tansform的Y轴值
                var cur_translate_val = _self.getMarginTop($this);
                if(time_distance<100){
                    cur_translate_val+=scroll_distance*5;
                }

                //上拉选择当前的上一个，下拉选择当前的下一个
                var item_height = _self.scroll_data.picker_item_height;
                if(pre_translate_val>cur_translate_val){
//                    console.log('上拉');
                    cur_translate_val = cur_translate_val-cur_translate_val%item_height-item_height;
                }else{
//                    console.log('下拉');
                    cur_translate_val = cur_translate_val-cur_translate_val%item_height;
                }
                //处理滚动超出范围
                var max_distance = -($this.find('.picker_item').length-5)*item_height;
//                console.log('最大距离',max_distance);
                if(cur_translate_val>0){
                    cur_translate_val = 0;
                }else if(cur_translate_val<max_distance){
                    cur_translate_val = max_distance;
                }
                //设置当前选择
                var cur_list_index = $this[0].dataset.index;
                var cur_item_index = parseInt(-cur_translate_val/item_height);
//                console.log('当前index',cur_list_index);
//                console.log('当前选择的index',cur_item_index);
//                console.log('当前选择的item',_self.cur_val);
                _self.scroll_data.scrolling_event = null;
                _self.scrollToItem(cur_list_index,cur_item_index);
                _self.$emit('scroll',_self.cur_val);//发射到滚动事件
            },
            //滚到指定项
            scrollToItem(i,j){
                var _self = this;
                j = j||0;
//                _self.cur_val[i] = _self.data[i][j];
                var distance = -_self.scroll_data.picker_item_height*j;
                $($(".picker_list")[i]).css({
                    /*'transition': 'transform 0.2s',
                    'webkitTransition': 'transform 0.2s',
                    'webkitTransform':'translateY('+distance+'px)',
                    'transform':'translateY('+distance+'px)'*/
                    'transition': 'margin-top 0.2s',
                    'webkitTransition': 'margin-top 0.2s',
                    'margin-top':distance+'px'
                });

                //深度复制
                var val_arr = [];
                for(var key=0;key<_self.cur_val.length;key++){
                    val_arr.push(_self.cur_val[key]);
                }
                val_arr[i] = _self.curData[i][j];
                _self.cur_val = val_arr;
            },
            /*//确定回调
            clickOk(){
                var _self = this;
                _self.close();
                _self.$emit('ok',_self.cur_val);
            },
            //取消回调
            clickCancel(e){
                var _self = this;
                _self.init();
                _self.close();
                _self.$emit('cancel');
            },*/
            close(){
//                this.cur_is_show = false;
                this.$emit('update:is_show', false);//触发.sync
                this.$emit('close');//触发.sync
            },
            /*getTranformY($obj){
                var transform = '0px';
                var transform_match ='';
                if($obj.css('transform').indexOf('matrix')>-1){
                    transform_match = $obj.css('transform').replace(' ','');
                    transform_match = transform_match.replace(')','');
                    transform = transform_match.split(',')[5]+'px';
                }else if($obj.css('transform').indexOf('translate')>-1){
                    transform_match = $obj.css('transform').match(/translate(Y|\dd)?\(\s*(\w+\s*,)?\s*([^,]+)(\s*,[^)]+)?\s*\)/);
                    transform = transform_match?transform_match[3]:'0px';
                }
                transform = parseFloat(transform.replace('px',''));
//                console.log(transform);
                return transform;
            },*/
            getMarginTop($obj){
                return parseFloat($obj.css('margin-top').replace('px',''));
            }
        },
        watch: {
            /*is_show(val) {
//                this.cur_is_show = val;
                this.$emit('update:is_show', val);
            }*/
            cur_val(val){
//                console.log('自动同步数据',val);
                this.$emit('input', val);
            },
            data(val){
                var _self = this;
                if(_self.curData==null){
                    _self.curData = val;
                    return;
                }
                if(_self.curData.toString() != val.toString()){
                    for(var i=0;i<val.length;i++){
//                        console.log(_self.curData[i].toString(),val[i].toString());
                        if(_self.curData[i].toString()!=val[i].toString()){
//                            console.log('data数据有变化',i);
                            _self.curData[i]=val[i];

                            _self.scrollToItem(i,0);
                        }
                    }
                }
            }
        },
    });
</script>