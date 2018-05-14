<template>
    <section id="product-form">
        <breadcrumb :breadcrumb="breadcrumb"/>

        <div class="page-header">
            <div class="row">
                <div class="col-md-4 text-xs-center text-md-left text-nowrap">
                    <h1>
                        <span class="text-muted font-weight-light">
                            <i class="page-header-icon fa fa-shopping-cart"></i> Products
                        </span>
                    </h1>
                </div>

                <hr class="page-wide-block visible-xs visible-sm">

                <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
                    <router-link :to="{name: 'products.index'}" class="btn btn-danger btn-block">
                        <span class="btn-label-icon left fa fa-times"></span> Back to the list
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Form -->
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

                <div class="form-group" :class="{'has-error': formErrors.has('categories')}">
                    <label class="control-label">Categories:</label>
                    <select2 :multiple="true" :data="productCategories" v-model="product.categories"/>
                    <span class="help-block" v-if="formErrors.has('categories')">
                        {{ formErrors.getFirstOf('categories') }}
                    </span>
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

                <hr>

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
                            <li v-for="variant in product.variants" :class="{active: selectedVariant === variant.id}">
                                <a href="javascript:" @click="selectedVariant = variant.id">{{ getVariantTitle(variant) }}</a>
                            </li>
                        </ul>

                        <div class="tab-content tab-content-bordered">
                            <div :key="variant.key"
                                 class="tab-pane fade"
                                 v-for="(variant, index) in product.variants"
                                 :class="{'active in': selectedVariant === variant.id}">

                                <product-variant
                                        :index="index"
                                        :product="variant"
                                        :is-variable="true"
                                        :form-errors="formErrors"
                                        @remove="removeProductVariant(variant)"/>
                            </div>
                        </div>
                    </div>
                </div>

                <product-variant :product="product" :form-errors="formErrors" v-if="!product.is_variable"/>
            </div>

            <div class="panel-footer text-right">
                <button type="button" class="btn btn-success" @click="saveProduct">
                    <i class="fa fa-check"></i> {{ isEdit ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </section>
</template>

<script>
	import _ from 'lodash';
	import { mapState } from 'vuex';
	import ProductField from './_partials/ProductField';
	import ProductVariant from './_partials/ProductVariant';

	let Select2 = window.Select2;

	export default {
		components: {
			Select2,
			ProductField,
			ProductVariant
		},

		computed: {
			...mapState([
				'languages',
				'currencies',
				'productCategories',
				'productParameters'
			]),

			translatableFields () {
				return _.filter(this.productFields, field => {
					return field.is_translatable;
				});
			},

			nonTranslatableFields () {
				return _.filter(this.productFields, field => {
					return !field.is_translatable;
				});
			},

			isEdit () {
				return !!this.$route.params.id;
			},

			isLoading () {
				return _.filter(this.loadingStates, state => state).length;
			},

			breadcrumb () {
				let breadcrumb = {
					'products.index': 'Products'
				};

				if (this.isEdit) {
					breadcrumb['products.edit'] = {
						title: 'Edit product',
						params: this.$route.params
					};
				} else {
					breadcrumb['products.create'] = 'Create product';
				}

				return breadcrumb;
			}
		},

		watch: {
			'product.categories' (newCategories, oldCategories) {
				if (!this.initialCompleted) {
					return;
				}

				if (!_.isEqual(newCategories, oldCategories)) {
					this.fetchCategoryData();
				}
			},

			'product.is_variable' () {
				this.mockProductParameters();
			},

			productFields (newFields, oldFields) {
				if (!_.isEqual(newFields, oldFields)) {
					this.mockProductFields();
				}
			},

			'$route.params.id' () {
				this.init();
			},

			selectedVariant (variantId) {
				if (this.product.is_variable && (!this.$route.query.variant || parseInt(this.$route.query.variant) !== variantId)) {
					this.$router.push({
						query: Object.assign({}, this.$route.query, {variant: variantId})
					});
				}
			}
		},

		data () {
			return {
				product: this.getFormData(),

				loadingStates: {
					fields: false,
					product: false
				},

				productFields: [],
				currentLanguage: null,
				currentParameter: null,
				initialCompleted: false,
				selectedVariant: null,
				formErrors: this.$helpers.getErrorsBag()
			};
		},

		created () {
			this.init();
		},

		methods: {
			getFormData () {
				let translations = this.$helpers.mockTranslations({
					title: 'New product',
					slug: ''
				});

				return {
					is_variable: 0,
					prices: {},
					images: [],
					variants: [],
					translations,
					categories: [],
					fieldsData: [],
					parameters: {},
					uploadableImages: []
				};
			},

			async init () {
				console.log('[Product] Initializing application.');

				this.product = this.getFormData();
				this.mockProductPrices();

				if (this.isEdit) {
					await this.fetchProduct();
					await this.fetchCategoryData();
				} else {
					await this.fetchCategoryData();
				}

				this.initialCompleted = true;
				this.currentLanguage = _.first(this.languages).iso_code;

				let firstVariant = this.product.variants[0];
				this.selectedVariant = parseInt(this.$route.query.variant || firstVariant.id);
			},

			async fetchProduct () {
				console.log('[Product] Fetching product data.');

				this.loadingStates.product = true;
				let id = this.$route.params.id;

				let {data: product} = await this.$http.get(`/admin/products/api/products/${id}`);

				this.product = product;
				this.loadingStates.product = false;
			},

			async fetchCategoryData () {
				console.log('[Product] Getting categories data.');

				this.loadingStates.fields = true;

				let {data: {fields, parameters}} = await this.$http.get('/admin/products/api/category-data', {
					params: {categories: this.product.categories}
				});

				this.productFields = fields;

				console.log('[Product] Setting product parameters.');

				this.$store.commit('setProductParameters', parameters);
				this.mockProductParameters();
				this.loadingStates.fields = false;
			},

			mockProductParameters () {
				_.each(this.productParameters, parameter => {
					let parameterData = {
						enabled: false,
						value: null,
						attributes: {}
					};

					if (parameter.type === 'checkbox' || parameter.is_countable) {
						_.each(parameter.attributes, attribute => {
							parameterData.attributes[attribute.id] = {
								enabled: false,
								quantity: 0
							};
						});
					}

					if (!this.product.is_variable && !_.has(this.product.parameters, parameter.id)) {
						if (!Object.keys(this.product.parameters).length) {
							this.product.parameters = {};
						}

						Vue.set(this.product.parameters, parameter.id, _.cloneDeep(parameterData));

						return;
					}

					_.each(this.product.variants, variant => {
						if (!Object.keys(variant.parameters).length) {
							variant.parameters = {};
						}

						if (!_.has(variant.parameters, parameter.id)) {
							Vue.set(variant.parameters, parameter.id, _.cloneDeep(parameterData));
						}
					});
				});
			},

			mockProductFields () {
				console.log('[Product] Setting product fields.');

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

			addProductVariant () {
				let variant = _.cloneDeep(
					_.merge(this.getFormData(), {key: this.$helpers.randomString()})
				);

				this.product.variants.push(variant);
				this.mockProductPrices();
				this.mockProductParameters(variant);
			},

			getVariantTitle (variant) {
				return _.get(variant, `translations.${this.currentLanguage}.title`, 'New variant');
			},

			removeProductVariant (variant) {
				this.product.variants.splice(
					this.product.variants.indexOf(variant), 1
				);
			},

			mockProductPrices () {
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

				if (!this.product.is_variable && !Object.keys(this.product.prices).length) {
					return this.product.prices = _.cloneDeep(prices);
				}

				_.each(this.product.variants, variant => {
					if (!Object.keys(variant.prices).length) {
						variant.prices = _.cloneDeep(prices);
					}
				});
			},

			async saveProduct () {
				this.loadingStates.product = true;
				this.formErrors.clearAll();
				this.product._method = this.isEdit ? 'PUT' : 'POST';

				let route = this.isEdit ? `/admin/products/api/products/${this.$route.params.id}` : `/admin/products/api/products`;
				let payload = this.$helpers.getFormDataFromObject(this.product);

				try {
					let {data: response} = await this.$http.post(route, payload);

					if (response.success) {
						$.growl.success({
							message: response.success
						});
					}

					if (response.redirect) {
						this.$router.push(response.redirect);
					}

					if (response.product) {
						this.product = response.product;
						this.$root.$emit('productUpdated', this.product);
					}

					$('html, body').animate({scrollTop: 0}, 'slow');
				} catch (e) {
					if (e.response && e.response.status === 422) {
						$.growl.error({
							message: 'Please check and fill form with correct data!'
						});

						this.formErrors.set(e.response.data.errors);
					} else {
						this.$helpers.showServerError(e);
					}
				}

				this.loadingStates.product = false;
			}
		}
	};
</script>

<style lang="scss">
    .table > tbody + tbody {
        border-top: 1px solid rgba(0, 0, 0, .05);
    }

    @keyframes pulseAnimation {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.15);
        }
        100% {
            transform: scale(1);
        }
    }

    .pulse {
        animation-name: pulseAnimation;
        animation-duration: 1s;
        transform-origin: 70% 70%;
        animation-iteration-count: infinite;
        animation-timing-function: linear;
    }
</style>