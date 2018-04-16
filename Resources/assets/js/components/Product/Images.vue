<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <span class="label"><i class="fa fa-picture-o"></i></span> Images
            </span>

            <div class="panel-heading-btn">
                <button type="button" class="btn btn-xs btn-success" @click="addImageInput()">
                    <i class="fa fa-plus-circle"></i> Add image
                </button>
            </div>
        </div>

        <div class="panel-body">
            <span class="text-danger" v-show="!showImagesTable">
                Please add images!
            </span>

            <table class="table table-stripped table-bordered m-b-0" v-show="showImagesTable">
                <thead>
                    <tr>
                        <th class="handle-cell"><i class="fa fa-arrows"></i></th>
                        <th>Preview</th>
                        <th>Image info</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="image in images" :data-id="image.id" class="image-tr">
                        <td class="handle-cell">
                            <i class="fa fa-arrows handle-arrows"></i>
                        </td>
                        <td class="preview-td">
                            <img :src="image.image" alt="Preview image" class="center-block">
                        </td>
                        <td>
                            <ul class="m-b-0">
                                <li>Size: {{ image.info.size }}</li>
                                <li>Name: {{ image.info.name }}</li>
                                <li>Modified: {{ image.info.modfied }}</li>
                            </ul>
                        </td>
                        <td class="text-right">
                            <button type="button" class="btn btn-xs btn-success" :disabled="!! image.is_preview"
                                    @click="setAsPreview(image)">
                                <i class="fa fa-eye"></i> Set as preview
                            </button>

                            <button type="button" class="btn btn-xs btn-danger" @click="removeUploadedImage(image)">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>

                    <tr class="image-row not-initialized" v-for="(input, index) in imageFileInputs" :key="input.id">
                        <td colspan="4">
                            <div class="image-upload-row">
                                <div class="col file-col">
                                    <label class="custom-file px-file not-initialized" :for="`image-${input.id}`">
                                        <input type="file" class="custom-file-input" :name="getInputName()"
                                               :id="`image-${input.id}`">
                                        <span class="custom-file-control form-control">Choose image...</span>
                                        <div class="px-file-buttons">
                                            <button type="button" class="btn btn-xs px-file-clear">Clear</button>
                                            <button type="button" class="btn btn-primary btn-xs px-file-browse">Browse
                                            </button>
                                        </div>
                                    </label>
                                </div>

                                <div class="col button-col">
                                    <button type="button" class="btn btn-xs btn-danger"
                                            @click="removeImageInput(index)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash';
    import axios from 'axios';
    import Helpers from '../../global-helpers';
    import EventBus from '../../event-bus';

    import 'jquery-ui/ui/widgets/sortable';

    export default {
        /**
         * Component props.
         */
        props: {
            images: {
                type: Array,
                required: false
            },

            reorderRoute: {
                type: String,
                required: true
            }
        },

        /**
         * Component data.
         */
        data() {
            return {
                imageFileInputs: [],
                sortableInitialized: false
            };
        },

        /**
         * Component methods.
         */
        methods: {
            /**
             * Create new image file input field.
             */
            addImageInput() {
                this.imageFileInputs.push({
                    id: Helpers.randomString(10)
                });

                setTimeout(() => {
                    let fileRows = $(this.$el).find('.image-row.not-initialized');

                    fileRows.find('.custom-file').pxFile();
                    fileRows.removeClass('not-initialized');
                });
            },

            /**
             * Remove image file input field.
             *
             * @param index
             */
            removeImageInput(index) {
                this.imageFileInputs.splice(index, 1);
            },

            /**
             * Remove all image file input fields.
             */
            removeImageInputs() {
                this.imageFileInputs = [];
            },

            /**
             * Init sortable plugin.
             */
            initSortable() {
                let self = this;
                let tableBody = $(this.$el).find('table tbody');

                if (this.sortableInitialized) {
                    tableBody.sortable('destroy');
                }

                tableBody.sortable({
                    handle: '.handle-arrows',
                    axis: 'y',
                    helper(event, ui) {
                        ui.children().each(function () {
                            $(this).width($(this).width());
                        });

                        return ui;
                    },
                    update(event, ui) {
                        let order = [];

                        _.each($(event.target).find('tr'), tr => {
                            order.push($(tr).data('id'));
                        });

                        self.saveOrder(order);
                    }
                });

                this.sortableInitialized = true;
            },

            /**
             * Set image as preview handler.
             */
            setAsPreview(image) {
                EventBus.$emit('product::loadingState', true);

                _.each(this.images, productImage => {
                    productImage.is_preview = false;
                });

                let promise = axios({
                    url: image.routes.asPreview.route,
                    method: image.routes.asPreview.method
                });

                promise
                    .then(({data}) => {
                        if (data.success) {
                            $.growl.success({
                                message: data.success
                            });
                        }

                        image.is_preview = true;
                    }).catch(Helpers.showServerError)
                    .then(() => {
                        EventBus.$emit('product::loadingState', false);
                    });
            },

            /**
             * Save images order.
             */
            saveOrder(order) {
                EventBus.$emit('product::loadingState', true);

                axios
                    .post(this.reorderRoute, {
                        product_id: this.$parent.product,
                        order: order
                    })
                    .then(({data}) => {
                        if (data.success) {
                            $.growl.success({
                                message: data.success
                            });
                        }
                    })
                    .catch(Helpers.showServerError)
                    .then(() => {
                        EventBus.$emit('product::loadingState', false);
                    });
            },

            /**
             * Get the name of file input.
             */
            getInputName() {
                return this.$parent.isVariable ? `variants[${this.$parent.product.key}][images][]` : 'images[]';
            },

            /**
             * Remove image from server.
             */
            removeUploadedImage(image) {
                EventBus.$emit('product::loadingState', true);

                axios({
                    url: image.routes.destroy.route,
                    method: image.routes.destroy.method
                })
                    .then(res => {
                        $.growl.success({
                            message: res.data.success
                        });

                        this.images.splice(
                            this.images.indexOf(image), 1
                        );
                    })
                    .catch(Helpers.showServerError)
                    .then(() => {
                        EventBus.$emit('product::loadingState', false);
                    });
            }
        },

        /**
         * Created event.
         */
        created() {
            EventBus.$on('product::saved', this.removeImageInputs);
        },

        /**
         * Mounted event.
         */
        mounted() {
            this.initSortable();
        },

        /**
         * Computed properties.
         */
        computed: {
            showImagesTable() {
                return this.imageFileInputs.length || (this.images && this.images.length);
            }
        }
    };
</script>

<style lang="scss">
    .image-upload-row {
        display: flex;
        align-items: center;

        .col {
            flex: 1;

            &.button-col {
                flex: 0 0 30px;
                text-align: right;
            }
        }
    }

    table {
        tr {
            > th, > td {
                vertical-align: middle !important;
            }
        }

        .handle-cell {
            width: 50px;
            text-align: center;
            background: #eee;
        }

        .image-tr {
            .preview-td {
                width: 100px;
            }
        }
    }
</style>