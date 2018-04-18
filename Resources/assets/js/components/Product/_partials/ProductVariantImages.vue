<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-picture-o"></i> Images
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
                    <tr v-for="image in product.images" :data-id="image.id" class="image-tr">
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
                            <button type="button" class="btn btn-xs btn-success" :disabled="!! image.is_preview" @click="setAsPreview(image)">
                                <i class="fa fa-eye"></i> Set as preview
                            </button>

                            <button type="button" class="btn btn-xs btn-danger" @click="removeUploadedImage(image)">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>

                    <tr class="image-row not-initialized" v-for="image in product.uploadableImages" :key="image.key">
                        <td colspan="4">
                            <div class="image-upload-row">
                                <div class="col file-col">
                                    <label class="custom-file px-file not-initialized" :for="`image-${image.key}`">
                                        <input type="file" class="custom-file-input" :id="`image-${image.key}`" @change="handleFileChange($event, image)">
                                        <span class="custom-file-control form-control">Choose image...</span>
                                        <div class="px-file-buttons">
                                            <button type="button" class="btn btn-xs px-file-clear">Clear</button>
                                            <button type="button" class="btn btn-primary btn-xs px-file-browse">Browse</button>
                                        </div>
                                    </label>
                                </div>

                                <div class="col button-col">
                                    <button type="button" class="btn btn-xs btn-danger" @click="removeImageInput(image)">
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
	import 'jquery-ui/ui/widgets/sortable';

	export default {
		props: {
			product: {
				type: Object,
				required: true
			}
		},

		computed: {
			showImagesTable () {
				return this.product.uploadableImages.length || (this.product.images && this.product.images.length);
			}
		},

		mounted () {
			this.initSortable();
		},

		data () {
			return {
				sortableInitialized: false
			};
		},

		methods: {
			initSortable () {
				let self = this;
				let tableBody = $(this.$el).find('table tbody');

				if (this.sortableInitialized) {
					tableBody.sortable('destroy');
				}

				tableBody.sortable({
					handle: '.handle-arrows',
					axis: 'y',

					helper (event, ui) {
						ui.children().each(function () {
							$(this).width($(this).width());
						});

						return ui;
					},

					update (event,) {
						let order = [];

						_.each($(event.target).find('tr'), tr => {
							order.push($(tr).data('id'));
						});

						self.saveOrder(order);
					}
				});

				this.sortableInitialized = true;
			},

			setAsPreview (image) {
				_.each(this.product.images, productImage => {
					productImage.is_preview = false;
				});

				let promise = axios({
					url: image.routes.asPreview.route,
					method: image.routes.asPreview.method
				});

				promise.then(({data}) => {
					if (data.success) {
						$.growl.success({
							message: data.success
						});
					}

					image.is_preview = true;
				}).catch(this.$helpers.showServerError);
			},

			saveOrder (order) {
				EventBus.$emit('product::loadingState', true);

				axios.post(this.reorderRoute, {
					product_id: this.$parent.product,
					order: order
				}).then(({data}) => {
					if (data.success) {
						$.growl.success({
							message: data.success
						});
					}
				}).catch(Helpers.showServerError).then(() => {
					EventBus.$emit('product::loadingState', false);
				});
			},

			removeUploadedImage (image) {

				axios({
					url: image.routes.destroy.route,
					method: image.routes.destroy.method
				}).then(res => {
					$.growl.success({
						message: res.data.success
					});

					this.images.splice(
						this.images.indexOf(image), 1
					);
				}).catch(Helpers.showServerError).then(() => {
					EventBus.$emit('product::loadingState', false);
				});
			},

			// Methods for non-uploaded files.

			addImageInput () {
				this.product.uploadableImages.push({
					key: this.$helpers.randomString(10),
					file: null
				});

				this.$nextTick(() => {
					let fileRows = $(this.$el).find('.image-row.not-initialized');

					fileRows.find('.custom-file').pxFile();
					fileRows.removeClass('not-initialized');
				});
			},

			removeImageInput (image) {
				this.product.uploadableImages.splice(
					this.product.uploadableImages.indexOf(image), 1
				);
			},

			handleFileChange (event, image) {
				let file = _.first(event.target.files);

				if (!file) {
					return;
				}

				image.file = file;
			}
		}
	};
</script>

<style lang="scss" scoped>
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