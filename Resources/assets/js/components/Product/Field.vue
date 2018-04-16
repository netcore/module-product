<template>

    <!-- Table row field -->
    <tr v-if="tr">
        <td class="field-name__column">
            <span class="field-name__span">{{ field.name }}</span>
        </td>
        <td>
            <template v-if="isTypeRadio">
                <label class="custom-control custom-radio radio-inline" v-for="option in field.options">
                    <input type="radio" class="custom-control-input" :name="inputName" :value="option.id" v-model="val">
                    <span class="custom-control-indicator"></span>
                    {{ option.name }}
                </label>
            </template>

            <template v-if="isTypeCheckbox">
                <label class="custom-control custom-checkbox checkbox-inline">
                    <input type="checkbox" class="custom-control-input" value="1" v-model="val" :name="inputName">
                    <span class="custom-control-indicator"></span>
                    Yes
                </label>
            </template>

            <template v-if="isTypeText">
                <input type="text" class="form-control" :name="inputName" v-model="val">
            </template>

            <template v-if="isTypeTextarea">
                <textarea :id="`field-${field.name}`" class="form-control" :name="inputName" v-model="val"></textarea>
            </template>

            <template v-if="isTypeNumber">
                <input type="number" class="form-control" :name="inputName" v-model="val">
            </template>
        </td>
    </tr>

    <!-- Simple field, non table row -->

</template>

<script>
    export default {
        /**
         * Component props.
         */
        props: {
            value: {},

            field: {
                required: true,
                type: Object
            },

            language: {
                required: false,
                type: Object
            },

            tr: {
                required: false,
                type: Boolean,
                default: false
            }
        },

        /**
         * Computed properties.
         */
        computed: {
            inputName() {
                if (this.language) {
                    return `fields[${this.field.id}][${this.language.iso_code}]`;
                }

                return `fields[${this.field.id}]`;
            },

            isTypeRadio() {
                return this.field.type === 'radio';
            },

            isTypeCheckbox() {
                return this.field.type === 'checkbox';
            },

            isTypeText() {
                return this.field.type === 'text';
            },

            isTypeTextarea() {
                return this.field.type === 'textarea';
            },

            isTypeNumber() {
                return this.field.type === 'number';
            }
        },

        data() {
            return {
                val: null
            };
        },

        /**
         * Watchers.
         */
        watch: {
            value(value) {
                this.val = value;
            },

            val(value) {
                this.$emit('input', value);
            }
        },

        /**
         * Created event.
         */
        created() {
            this.val = this.value;
        }
    };
</script>