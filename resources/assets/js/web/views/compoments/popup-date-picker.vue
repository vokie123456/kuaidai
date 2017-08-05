<style lang="less">
    .popup-date-picker{
        width: 100%;
    }
</style>

<template>
    <!--<mt-popup position="bottom" class="popup-date-picker">-->
        <!--<mt-button @click.native="popupVisible3 = false" size="large" type="primary">关闭 popup</mt-button>-->
        <!--<mt-picker :slots="dateSlots" @change="onDateChange" :visible-item-count="5" :show-toolbar="false"></mt-picker>-->
    <!--</mt-popup>-->

    <mt-popup v-model="isShow" class="popup-date-picker" position="bottom">
        <mt-picker :slots="dateSlots" @change="" :visible-item-count="7"></mt-picker>
    </mt-popup>
</template>

<script>
    export default {
        data() {
            return {
                isShow: false,
                dateSlots: [
                    {
                        flex: 1,
                        values: ['2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016'],
                        textAlign: 'center'
                    }, {
                        divider: true,
                        content: '-',
                    }, {
                        flex: 1,
                        values: ['2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016'],
                        textAlign: 'center'
                    }, {
                        divider: true,
                        content: '-',
                    }, {
                        flex: 1,
                        values: ['2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016'],
                        textAlign: 'center'
                    }
                ]
            };
        },
        props: ['show'],
        watch: {
            show(val){
                this.isShow = val;
            },
            isShow(val) {
                this.$emit('update:show', val);
            },
        },
    };
</script>