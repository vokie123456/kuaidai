define(['vue', 'jquery', 'datetimepicker', 'datetimepicker-lang'], function(Vue, $) {
    Vue.component('vue-datetime-picker', {
        template: '<input :class="cls" v-model="val" type="text" readonly>',
        data: function() {
            return {
                val: ''
            };
        },
        props: ['cls', 'value'],
        watch: {
            val: function(val) {
                this.$emit('update:value', val);
            }
        },
        mounted: function () {
            var self = this;
            this.val = this.value;

            $(this.$el).datetimepicker({
                format: 'yyyy-mm-dd',
                language: 'zh-CN',
                minView: 2,
                maxView: 'year',
                autoclose: true,
                todayBtn: true,
                todayHighlight: true,
                weekStart: 0
            }).on('changeDate', function(ev) {
                self.val = ev.target.value;
            });
        }
    });
});