<template>
    <a class="vue-editable" v-model="value"></a>
</template>

<script>
    export default {
        /**
         * Component props.
         */
        props: {
            value: {
                require: true
            },

            mode: {
                type: String,
                default: 'inline'
            }
        },

        /**
         * Mounted event.
         */
        mounted() {
            let self = this;

            $(this.$el).editable({
                mode: self.mode,
                value: self.value,
                highlight: false,
                success(response, value) {
                    self.$emit('input', value);
                    self.$emit('change', value);
                }
            }).on('save', e => {
                let nextEditable = $(e.target).parent().next().find('a.vue-editable');

                if (nextEditable.length) {
                    nextEditable.editable('show');
                }
            });
        },

        /**
         * Component watchers.
         */
        watch: {
            value(value) {
                $(this.$el).editable('setValue', value);
            }
        }
    };
</script>