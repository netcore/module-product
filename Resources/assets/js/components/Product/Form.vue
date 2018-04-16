<template>
    <form class="panel panel-default" id="product-form" :class="{'form-loading': isLoading}"
          @submit.prevent="saveProduct()">

        <input type="hidden" name="is_variable" v-model="product.is_variable">

        <div class="panel-heading">
            <span class="panel-title" v-if="productRoute">Edit product</span>
            <span class="panel-title" v-else>Create product</span>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label for="is_variable">Product type:</label>
                <select id="is_variable" class="form-control" v-model.number="product.is_variable">
                    <option value="0">Simple product</option>
                    <option value="1">Variable product</option>
                </select>
            </div>

            <div class="form-group">
                <label class="control-label">Categories:</label>
                <select2 :multiple="true" :data="categories" v-model="product.categories" :name="`categories[]`"/>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="panel-title">
                        <i class="fa fa-list-alt"></i> Global product fields
                    </span>

                    <ul class="nav nav-tabs nav-xs" v-if="translatableFields.length">
                        <li :class="{active: language.iso_code === currentLanguage}" v-for="language in languages">
                            <a @click.prevent="currentLanguage = language.iso_code">{{ language.title_localized }}</a>
                        </li>
                    </ul>
                </div>

                <div class="panel-body">
                    <table class="table table-stripped">
                        <tbody v-for="language in languages" v-show="currentLanguage === language.iso_code">
                            <product-field
                                    :tr="true"
                                    :field="field"
                                    :key="field.id"
                                    :language="language"
                                    v-for="field in translatableFields"
                                    v-model="product.fieldsData[field.id].translations[language.iso_code].value">
                            </product-field>
                        </tbody>

                        <tbody>
                            <product-field
                                    :tr="true"
                                    :field="field"
                                    :key="field.id"
                                    v-for="field in nonTranslatableFields"
                                    v-model="product.fieldsData[field.id].value">
                            </product-field>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel panel-default" v-if="product.is_variable">
                <div class="panel-heading">
                    <span class="panel-title">Product variants</span>
                    <div class="panel-heading-btn">
                        <button type="button" class="btn btn-xs btn-success" @click="addProductVariant()">
                            <i class="fa fa-plus"></i> Add product variant
                        </button>
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li v-for="variant in product.variants">
                            <a :href="`#variant-${variant.key}`" data-toggle="tab">{{ getVariantTitle(variant) }}</a>
                        </li>
                    </ul>

                    <div class="tab-content tab-content-bordered">
                        <div class="tab-pane fade" :id="`variant-${variant.key}`" :key="variant.key"
                             v-for="variant in product.variants">
                            <product-variant
                                    :product="variant"
                                    :languages="languages"
                                    :currencies="currencies"
                                    :is-variable="product.is_variable"
                                    :image-reorder-route="imageReorderRoute"
                                    @remove="removeProductVariant(variant)">
                            </product-variant>
                        </div>
                    </div>
                </div>
            </div>

            <product-variant
                    :product="product"
                    :languages="languages"
                    :currencies="currencies"
                    :image-reorder-route="imageReorderRoute"
                    v-if="! product.is_variable">
            </product-variant>
        </div>

        <div class="panel-footer text-right">
            <button class="btn btn-success">
                <i class="fa fa-check"></i> {{ productRoute ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</template>

<script>
    import _ from 'lodash';
    import axios from 'axios';
    import Helpers from '../../global-helpers';
    import EventBus from '../../event-bus';

    export default {
        props: {
            method: {
                required: true,
                type: String
            },

            route: {
                required: true,
                type: String
            },

            fieldsRoute: {
                required: true,
                type: String
            },

            productRoute: {
                required: false,
                type: String
            },

            imageReorderRoute: {
                required: false,
                type: String
            },

            languages: {
                required: true,
                type: Array
            },

            categories: {
                required: true,
                type: Array
            },

            currencies: {
                required: true,
                type: Array
            }
        },

        computed: {
            translatableFields() {
                return _.filter(this.productFields, field => {
                    return field.is_translatable;
                });
            },

            nonTranslatableFields() {
                return _.filter(this.productFields, field => {
                    return !field.is_translatable;
                });
            }
        },

        data() {
            return {
                productFields: [],
                product: {
                    is_variable: 0,
                    categories: [],
                    fieldsData: {},
                    variants: [],
                    images: [],
                    prices: this.mockProductPrices(),
                    translations: this.mockTranslationsObject({ title: 'New product' })
                },
                currentLanguage: _.get(_.first(this.languages), 'iso_code', 'en'),
                isLoading: false
            };
        },

        created() {
            if (this.productRoute) {
                this.fetchProduct();
            } else {
                this.fetchFields();
            }

            EventBus.$on('product::loadingState', state => {
                this.isLoading = state;
            });
        },

        mounted() {
            this.equalizeNameColumns();
        },

        methods: {
            fetchFields() {
                this.isLoading = true;

                axios
                    .get(this.fieldsRoute, {
                        params: {
                            categories: this.product.categories
                        }
                    })
                    .then(({data}) => {
                        this.productFields = data;
                        this.mockProductFields();
                    })
                    .catch(Helpers.showServerError)
                    .then(() => {
                        this.isLoading = false;
                    });
            },

            fetchProduct() {
                this.isLoading = true;

                axios
                    .get(this.productRoute)
                    .then(({data}) => {
                        this.product = data.product;
                    })
                    .catch(Helpers.showServerError)
                    .then(() => {
                        this.isLoading = false;
                    });
            },

            equalizeNameColumns() {
                let maxWidth = 100;

                _.each($(this.$el).find('table:visible .field-name__span'), span => {
                    maxWidth = $(span).width() > maxWidth ? $(span).width() : maxWidth;
                });

                $(this.$el).find('table:visible .field-name__column').width(
                    Math.round(maxWidth) + 30
                );

                setTimeout(this.equalizeNameColumns, 300);
            },

            mockProductFields() {
                _.each(this.productFields, field => {
                    // Keep existing field.
                    if (_.has(this.product.fieldsData, field.id)) {
                        return;
                    }

                    let fieldObject = {};

                    if (field.is_translatable) {
                        fieldObject.translations = {};

                        _.each(this.languages, language => {
                            fieldObject.translations[language.iso_code] = {
                                value: ''
                            };
                        });
                    } else {
                        fieldObject.value = '';
                    }

                    this.product.fieldsData[field.id] = fieldObject;
                });
            },

            saveProduct() {
                this.isLoading = true;

                let form = new FormData(
                    this.$el
                );

                form.append('_method', this.method);

                axios
                    .post(this.route, form)
                    .then(({data}) => {
                        if (data.success) {
                            $.growl.success({
                                message: data.success
                            });
                        }

                        if (data.redirect) {
                            window.location.replace(data.redirect);
                        }

                        if (data.refresh) {
                            this.fetchProduct();

                            EventBus.$emit('product::saved');
                        }
                    })
                    .catch(err => {
                        console.log(err.response);
                    })
                    .then(() => {
                        this.isLoading = false;
                    });
            },

            addProductVariant() {
                this.product.variants.push({
                    key: Helpers.randomString(10),
                    parameters: [],
                    images: [],
                    prices: this.mockProductPrices(),
                    translations: this.mockTranslationsObject({
                        title: 'New variant'
                    })
                });
            },

            getVariantTitle(variant) {
                return _.get(variant, `translations.${this.currentLanguage}.title`, 'New variant');
            },

            removeProductVariant(variant) {
                this.product.variants.splice(
                    this.product.variants.indexOf(variant), 1
                );
            },

            mockTranslationsObject(fields) {
                let translations = {};

                _.each(this.languages, language => {
                    translations[language.iso_code] = _.clone(fields);
                });

                return translations;
            },

            mockProductPrices(product) {
                let prices = {};

                _.each(this.currencies, currency => {
                    prices[currency.id] = {
                        id: currency.id,
                        has_discount: false,
                        discount_type: 'none',
                        discount_amount: 0,

                        with_vat_with_discount: 0,
                        with_vat_without_discount: 0,

                        without_vat_with_discount: 0,
                        without_vat_without_discount: 0
                    };
                });

                if (product) {
                    product.prices = prices;
                } else {
                    return prices;
                }
            }
        },

        components: {
            'product-variant': require('./ProductVariant.vue'),
            'product-field': require('./Field.vue'),
            'select2': window.Select2
        },

        watch: {
            'product.categories'() {
                this.fetchFields();
            }
        }
    };
</script>

<style lang="scss">
    .table > tbody + tbody {
        border-top: 1px solid rgba(0, 0, 0, .05);
    }
</style>