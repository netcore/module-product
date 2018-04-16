<template>
    <div class="modal fade" id="settings-modal" tabindex="-1">
        <div class="modal-dialog" :class="{ 'form-loading' : processing }">
            <form class="modal-content" @submit.prevent="saveSettings()">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title" id="myModalLabel">Product settings</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group" v-for="currency in currencies">
                        <div class="input-group">
                            <span class="input-group-addon">VAT for {{ currency.code }} ({{ currency.symbol }})</span>
                            <input type="number" min="0" max="100" class="form-control" v-model="currency.vat" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">
                        <i class="fa fa-check"></i> Save settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import Helpers from '../../global-helpers';

    export default {
        /**
         * Component props.
         */
        props: {
            currencies: {
                type: Array,
                required: false,
                default() {
                    return [];
                }
            },

            route: {
                type: String,
                required: true
            }
        },

        /**
         * Component data.
         */
        data() {
            return {
                processing: false
            };
        },

        /**
         * Component methods.
         */
        methods: {
            /**
             * Save settings.
             */
            saveSettings() {
                this.processing = true;

                let data = {
                    currencies: this.currencies
                };

                axios.post(this.route, data).then(res => {
                    $.growl.success({
                        message: res.data.success
                    });
                }).catch(err => {
                    Helpers.showServerError(err);
                }).then(() => {
                    this.processing = false;
                });
            }
        }
    };
</script>