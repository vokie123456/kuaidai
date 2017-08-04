<style lang="less">
    @import "../../less/vars";
    .choice-box{
        background: #fff;
        color: @mainColor;
        border: 1px @mainColor solid;
        border-radius: 15px;
        font-size: 1em;
        padding: 0.3em 0.75em;
        text-align: center;
        display: inline;

        &.active{
            background: @mainColor;
            color: #fff;
        }
    }
</style>

<template>
    <div class="choice-box" @click="handleClick" :class="{active: isActive()}">
        <slot/>
    </div>
</template>

<script>
    import _ from 'lodash';

    export default {
        data() {
            return {};
        },
        model: {
            prop: 'checked',
            event: 'change',
        },
        props: ['checked', 'value', 'multiple'],
        methods: {
            handleClick() {

                if (this.isMultiple) {
                    let newVal = this.checked;
                    let index = _.indexOf(this.checked, this.value);

                    if (index >= 0) {
                        newVal.splice(index, 1);
                    } else {
                        newVal.push(this.value);
                    }

                    newVal = _.uniq(newVal);

                    this.$emit('change', newVal);
                } else {
                    let newVal = this.value;
                    this.$emit('change', newVal);
                }

            },
            isActive() {
                if (this.isMultiple) {
                    return _.indexOf(this.checked, this.value) >= 0;
                } else {
                    return this.checked === this.value;
                }
            },
        },
        mounted() {
            this.isMultiple = false;
            if (typeof this.multiple !== 'undefined') {
                this.isMultiple = !!this.multiple;
            }
        }
    }
</script>