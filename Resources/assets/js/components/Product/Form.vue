<template>
    <div class="panel panel-default" :class="{'form-loading': isLoading}">
        <div class="panel-heading">
            <span class="panel-title">{{ isEdit ? 'Edit' : 'Create'}} product</span>
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
                <select2 :multiple="true" :data="productCategories" v-model="product.categories"/>
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
                                v-model="field.model.values[language.iso_code]"/>
                        </tbody>

                        <tbody>
                        <product-field
                                :tr="true"
                                :field="field"
                                :key="field.id"
                                v-for="field in nonTranslatableFields"
                                v-model="field.model.value"/>
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
                            <product-variant :product="variant" @remove="removeProductVariant(variant)"/>
                        </div>
                    </div>
                </div>
            </div>

            <product-variant :product="product" v-if="!product.is_variable"/>
        </div>

        <div class="panel-footer text-right">
            <button type="button" class="btn btn-success" @click="saveProduct">
                <i class="fa fa-check"></i> {{ isEdit ? 'Update' : 'Create' }}
            </button>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash';
    import {mapState} from 'vuex';
    import ProductField from './_partials/ProductField';
    import ProductVariant from './_partials/ProductVariant';

    export default {
        computed: {
            ...mapState(['languages', 'productCategories', 'currencies']),

            translatableFields() {
                return _.filter(this.productFields, field => {
                    return field.is_translatable;
                });
            },

            nonTranslatableFields() {
                return _.filter(this.productFields, field => {
                    return !field.is_translatable;
                });
            },

            isEdit() {
                return !!this.$route.params.id;
            },

            isLoading() {
                return _.filter(this.loadingStates, state => state).length;
            }
        },

        data() {
            return {
                loadingStates: {
                    product: false,
                    fields: false,
                    categories: false
                },

                initialCompleted: false,
                productFields: [],
                currentLanguage: null,
                product: {
                    categories: []
                }
            };
        },

        created() {
            this.init();
        },

        mounted() {
            this.equalizeNameColumns();
        },

        methods: {
            init() {
                this.resetForm();

                this.fetchProductCategories().then(() => {
                    return this.isEdit ? this.fetchProduct() : this.fetchFields();
                });

                this.currentLanguage = _.first(this.languages).iso_code;
            },

            resetForm() {
                this.product = {
                    is_variable: 0,
                    categories: [],
                    fieldsData: [],
                    variants: [],
                    images: [],
                    uploadableImages: [],
                    prices: {},
                    translations: this.$helpers.mockTranslations({title: 'New product', slug: ''})
                };

                this.setProductPrices(this.product);
            },

            fetchProductCategories() {
                this.loadingStates.categories = true;

                return this.$http.get('/admin/products/api/categories').then(({data: categories}) => {
                    this.$store.commit('setProductCategories', categories);
                    this.loadingStates.categories = false;
                }).catch(this.$helpers.showServerError);
            },

            fetchProduct() {
                this.loadingStates.product = true;
                let id = this.$route.params.id;

                return this.$http.get(`/admin/products/api/products/${id}`).then(({data: product}) => {
                    this.product = product;
                }).then(this.fetchFields).then(() => {
                    this.loadingStates.product = false;
                    this.initialCompleted = true;
                }).catch(this.$helpers.showServerError).then();
            },

            fetchFields() {
                let payload = {
                    params: {
                        categories: this.product.categories
                    }
                };

                this.loadingStates.fields = true;

                return this.$http.get('/admin/products/api/products/fields', payload).then(({data: fields}) => {
                    this.productFields = fields;
                    this.mockProductFields();
                    this.loadingStates.fields = false;
                }).catch(this.$helpers.showServerError);
            },

            mockProductFields() {
                _.each(this.productFields, field => {
                    let productField = _.find(this.product.fieldsData, {id: field.id});

                    if (productField) {
                        // Bind reference.
                        field.model = productField;
                        return;
                    }

                    let fieldObject = {
                        id: field.id
                    };

                    if (field.is_translatable) {
                        fieldObject.values = {};

                        _.each(this.languages, language => {
                            fieldObject.values[language.iso_code] = '';
                        });
                    } else {
                        fieldObject.value = '';
                    }

                    this.product.fieldsData.push(fieldObject);

                    field.model = fieldObject;
                });
            },

            addProductVariant() {
                let product = {
                    key: this.$helpers.randomString(10),
                    parameters: [],
                    prices: {},
                    images: [],
                    uploadableImages: [],
                    translations: this.$helpers.mockTranslations({title: 'New variant', slug: ''})
                };

                this.setProductPrices(product);
                this.product.variants.push(product);
            },

            getVariantTitle(variant) {
                return _.get(variant, `translations.${this.currentLanguage}.title`, 'New variant');
            },

            removeProductVariant(variant) {
                this.product.variants.splice(
                    this.product.variants.indexOf(variant), 1
                );
            },

            setProductPrices(product) {
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

                product.prices = _.cloneDeep(prices);
            },

            equalizeNameColumns() {
                let maxWidth = 100;

                _.each($(this.$el).find('table:visible .field-name__span'), span => {
                    maxWidth = $(span).width() > maxWidth ? $(span).width() : maxWidth;
                });

                $(this.$el).find('table:visible .field-name__column').width(
                    Math.round(maxWidth) + 30
                );

                setTimeout(this.equalizeNameColumns, 500);
            },

            saveProduct() {
                this.loadingStates.product = true;

                let route = this.isEdit ? `/admin/products/api/products/${this.$route.params.id}` : `/admin/products/api/products`;
                let payload = this.$helpers.getFormDataFromObject(this.product);

                this.$http.post(route, payload).then(({data}) => {
                    if (data.success) {
                        $.growl.success({
                            message: data.success
                        });
                    }

                    if (data.redirect) {
                        this.$router.push(data.redirect);
                    }

                    if (data.product) {
                        this.product = product;
                    }
                }).catch(this.$helpers.showServerError).then(() => this.loadingStates.product = false);
            },
        },

        components: {
            select2: window.Select2,
            ProductField,
            ProductVariant
        },

        watch: {
            'product.categories'(newCategories, oldCategories) {
                if (!this.initialCompleted) {
                    return;
                }

                if (_.isEqual(newCategories, oldCategories)) {
                    return;
                }

                this.fetchFields();
            },

            $route() {
                this.init();
            }
        }
    };
</script>

<style lang="scss">
    .table > tbody + tbody {
        border-top: 1px solid rgba(0, 0, 0, .05);
    }
</style>