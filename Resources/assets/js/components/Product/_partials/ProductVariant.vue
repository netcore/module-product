<template>
    <div class="product-variant">
        <input type="hidden" v-model="product.id" :name="isVariable ? `variants[${product.key}][id]` : `id`">

        <div class="text-right" v-if="isVariable">
            <button type="button" class="btn btn-xs btn-danger" @click="removeProductVariant()">
                <i class="fa fa-trash"></i> Remove variant
            </button>
            <hr>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="panel-title">
                    <i class="fa fa-list-alt"></i> Local product fields
                </span>

                <ul class="nav nav-tabs nav-xs">
                    <li :class="{active: language.iso_code === currentLanguage}" v-for="language in languages">
                        <a @click.prevent="currentLanguage = language.iso_code">{{ language.title }}</a>
                    </li>
                </ul>
            </div>

            <div class="panel-body">
                <div v-for="language in languages" v-show="language.iso_code === currentLanguage">
                    <div class="form-group">
                        <label class="control-label">Title</label>
                        <input type="text" class="form-control" v-model="product.translations[language.iso_code].title">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Slug <i class="text-light">(leave blank to generate automatically)</i></label>
                        <input type="text" class="form-control" v-model="product.translations[language.iso_code].slug">
                    </div>
                </div>
            </div>
        </div>

        <product-variant-images :product="product"/>
        <product-variant-prices :product="product"/>
    </div>
</template>

<script>
	import { mapState } from 'vuex';
	import ProductVariantImages from './ProductVariantImages';
	import ProductVariantPrices from './ProductVariantPrices';

	export default {
		components: {
			ProductVariantImages,
			ProductVariantPrices
		},

		props: {
			product: {
				type: Object,
				required: true
			},

			isVariable: {
				default: false
			}
		},

		computed: mapState(['languages']),

		data () {
			return {
				currentLanguage: null
			};
		},

		created () {
			this.currentLanguage = _.first(this.languages).iso_code;
		},

		methods: {
			removeProductVariant () {
				let self = this;

				bootbox.confirm({
					message: 'Are you really want to delete this product variant?',
					className: 'bootbox-sm',

					callback (result) {
						if (result) {
							self.$emit('remove');
						}
					}
				});
			},

			getTranslatableInputName (language, inputName) {
				if (this.isVariable) {
					return `variants[${this.product.key}][translations][${language.iso_code}][${inputName}]`;
				}

				return `translations[${language.iso_code}][${inputName}]`;
			}
		}
	};
</script>