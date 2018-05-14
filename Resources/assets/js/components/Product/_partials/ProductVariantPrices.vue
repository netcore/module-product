<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-euro"></i> Product prices
            </span>
        </div>

        <div class="panel-body prices-body">
            <product-variant-price-option
                    :key="currency.id"
                    :currency="currency"
                    v-for="currency in currencies"
                    :price="product.prices[currency.id]"/>
        </div>
    </div>
</template>

<script>
	import { mapState } from 'vuex';
	import ProductVariantPriceOption from './ProductVariantPriceOption';

	export default {
		computed: mapState(['currencies']),

		props: {
			product: {
				type: Object,
				required: true
			}
		},

		components: {
			ProductVariantPriceOption
		}
	};
</script>

<style lang="scss" scoped>
    .prices-body {
        .price-group:last-child {
            margin-bottom: 0;
        }
    }
</style>