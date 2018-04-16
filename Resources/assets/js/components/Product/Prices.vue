<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <span class="label"><i class="fa fa-euro"></i></span> Product prices
            </span>
        </div>
        <div class="panel-body prices-body">
            <product-price-option
                    :key="currency.id"
                    :currency="currency"
                    v-for="currency in currencies"
                    :price="$parent.product.prices[currency.id]"
                    :variant-key="$parent.isVariable ? $parent.product.key : null">
            </product-price-option>
        </div>
    </div>
</template>
<script>
    export default {
        /**
         * Component props.
         */
        props: {
            currencies: {
                type: Array,
                required: true
            }
        },

        /**
         * Child components.
         */
        components: {
            'product-price-option': require('./PriceOption.vue')
        }
    };
</script>

<style lang="scss">
    .prices-body {
        .price-group:last-child {
            margin-bottom: 0;
        }
    }
</style>