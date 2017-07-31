define(['vue', 'jquery'], function(Vue, $) {
    Vue.component('datetimepicker', {
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
            this.val = this.value;
            console.log(this.$el);
        }
    });
});
//# sourceMappingURL=datetimepicker.js.map
