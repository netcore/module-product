<template>
    <div class="product-variant">
        <div class="text-right" v-if="isVariable">
            <button type="button" class="btn btn-xs btn-danger" @click="removeProductVariant()">
                <i class="fa fa-trash"></i> Remove variant
            </button>
            <hr>
        </div>

        <product-variant-local-fields :product="product"/>
        <product-variant-images :product="product"/>
        <product-variant-prices :product="product"/>
        <product-variant-parameters :product="product"/>
    </div>
</template>

<script>
	import ProductVariantImages from './ProductVariantImages';
	import ProductVariantPrices from './ProductVariantPrices';
	import ProductVariantParameters from './ProductVariantParameters';
	import ProductVariantLocalFields from './ProductVariantLocalFields';

	export default {
		components: {
			ProductVariantImages,
			ProductVariantPrices,
			ProductVariantParameters,
			ProductVariantLocalFields
		},

		props: {
			product: {
				type: Object,
				required: true
			},

			isVariable: {
				default: false
			},

			index: {
				required: false
			},

			formErrors: {
				required: true
			}
		},

		methods: {
			removeProductVariant () {
				let self = this;

				bootbox.confirm({
					className: 'bootbox-sm',
					message: 'Are you really want to delete this product variant?',
					callback: (result) => result ? self.$emit('remove') : _.noop()
				});
			},

			hasError (key) {
				return this.formErrors.has(this.isVariable ? `variants.${this.index}.${key}` : key);
			},

			getError (key) {
				return this.formErrors.get(this.isVariable ? `variants.${this.index}.${key}` : key);
			}
		}
	};
</script>