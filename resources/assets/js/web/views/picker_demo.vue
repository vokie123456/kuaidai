<template>
    <div class="picker_demo">
        <comp-picker v-model="picker_val"
                     @scroll="picker_scroll"
                     :is_show.sync="is_show_picker"
                     :title="'选择地区、车牌、车型'"
                     :item_title="['地区','车牌','车型']"
                     :data="picker_data"
                     :selected_color="'#eabf50'"
                     :width_ratio="[1,1,3]"
                     @close="picker_close"
        ></comp-picker>
        <div class="mod">
            <h1>
                选择器组件演示
            </h1>
            <div class="developerInfo">
                作者：何世华
                <br>
                版本：1.0.0（20170802）
            </div>
            <button @click="show_picker" type="button">
                {{picker_val}}
            </button>
        </div>
    </div>
</template>

<script>
    export default{
        components: {
            "comp-picker": require('./compoments/picker.vue'),
        },
        data:function(){
            return {
                is_show_picker:false,
                picker_data:[],
                picker_val:['粤','A','本田CR-V'],
            }
        },
        mounted:function(){
            this.init();
        },
        methods:{
            init(){
                var _self = this;
                _self.picker_data = [
                    ['京','冀','晋','蒙','辽','吉','黑','沪','苏','浙','皖','闽','赣','鲁','豫','鄂','湘','粤','琼','渝','川','贵','藏','陕','甘','青','宁','新'],
                    ['A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z'],
                    ['奥迪Q7','本田CR-V','兰博基尼第六元素'],
                ]
            },
            //选择器滚动事件
            picker_scroll(val){
                var _self = this;
                console.log('当前选择：',val);
                /*if(val[0]!=_self.old_picker_val[0]){
                    // console.log('行政区划简称有改变',val,_self.old_picker_val);
                    _self.initPickerData(val);
                    _self.old_picker_val = val;
                }*/
            },
            picker_close(){
                console.log('关闭picker');
            },
            show_picker(){
                this.is_show_picker = true;
            },
        },
    };
</script>