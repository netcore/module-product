<template>
    <div class="form-group price-group">
        <div class="input-group prices-group">
            <span class="input-group-addon">
                <b>{{ currency.code }}</b> - VAT {{ currency.vat }}%
            </span>

            <span class="input-group-addon">Without VAT:</span>
            <input type="number" step="0.01" min="0.01" class="form-control without-vat"
                   @change="recalculatePricesAndDiscounts($event, true)"
                   @keyup="recalculatePricesAndDiscounts($event, true)"
                   v-model.number="price.without_vat_without_discount">

            <span class="input-group-addon">With VAT:</span>
            <input type="number" step="0.01" min="0.01" class="form-control with-vat"
                   @change="recalculatePricesAndDiscounts($event, true)"
                   @keyup="recalculatePricesAndDiscounts($event, true)"
                   v-model.number="price.with_vat_without_discount">

            <span class="input-group-addon">
                <input type="checkbox" v-model="price.has_discount">
                Has discount
            </span>
        </div>

        <div class="input-group discounts-group" v-if="price.has_discount">
            <span class="input-group-addon">
                Discount type:
            </span>

            <span class="input-group-addon">
                <input type="radio" value="percent" v-model="price.discount_type">
                Percent
            </span>

            <span class="input-group-addon">
                <input type="radio" value="money" v-model="price.discount_type">
                Money
            </span>

            <span class="input-group-addon">
                Discount amount:
            </span>

            <input type="number" step="0.01" min="0.01" class="form-control" v-model.number="price.discount_amount">
        </div>

        <ul class="prices-list">
            <li>
                Without discount / With VAT: {{ parseFloat(price.with_vat_without_discount).toFixed(2) }} {{ currency.symbol }}
            </li>

            <li>
                Without discount / Without VAT: {{ parseFloat(price.without_vat_without_discount).toFixed(2) }} {{ currency.symbol }}
            </li>
        </ul>

        <ul class="prices-list" v-if="price.has_discount">
            <li>
                With discount / With VAT: {{ parseFloat(price.with_vat_with_discount).toFixed(2) }} {{ currency.symbol }}
            </li>

            <li>
                With discount / Without VAT: {{ parseFloat(price.without_vat_with_discount).toFixed(2) }} {{ currency.symbol }}
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            currency: {
                type: Object,
                required: true
            },

            price: {
                type: Object,
                required: true
            }
        },

        methods: {
            recalculatePricesAndDiscounts(event, isPriceChange) {
                if(isPriceChange) {
                    let element = $(event.target);
                    let value = parseFloat(element.val());
                    let isWithoutVat = element.hasClass('without-vat');

                    let vat = this.currency.vat;
                    let percentageVat = parseFloat(1 + parseFloat(parseFloat(vat / 100).toFixed(2)));

                    let oppositeValue;

                    if (isWithoutVat) {
                        oppositeValue = parseFloat(parseFloat(value * percentageVat).toFixed(2));
                    } else {
                        oppositeValue = parseFloat(parseFloat(value / percentageVat).toFixed(2));
                    }

                    if (!isNaN(oppositeValue)) {
                        let key = `${isWithoutVat ? 'with' : 'without'}_vat_without_discount`;
                        this.price[key] = oppositeValue;
                    }
                }

                // Calculate discounts.
                if(!this.price.has_discount || this.price.discount_type === 'none') {
                    return;
                }

                if (this.price.discount_type === 'money') {
                    this.price.with_vat_with_discount = this.price.with_vat_without_discount - this.price.discount_amount;
                    this.price.without_vat_with_discount = this.price.with_vat_without_discount - this.price.discount_amount;
                } else {
                    let percent = parseFloat(parseFloat(this.price.discount_amount / 100).toFixed(4));
                    let amountWithoutVat = parseFloat(this.price.without_vat_without_discount * percent);
                    let amountWithVat = parseFloat(this.price.with_vat_without_discount * percent);

                    this.price.with_vat_with_discount = this.price.with_vat_without_discount - amountWithVat;
                    this.price.without_vat_with_discount = this.price.without_vat_without_discount - amountWithoutVat;
                }
            }
        },

        computed: {
            discountPropWatchers() {
                this.price.discount_type;
                this.price.discount_amount;
                this.price.has_discount;

                return Date.now();
            }
        },

        watch: {
            discountPropWatchers() {
                this.recalculatePricesAndDiscounts();
            }
        }
    };
</script>

<style lang="scss">
    .discounts-group {
        padding-top: 5px;
    }

    .prices-list {
        margin-top: 5px;
    }
</style>