<template>
    <div id="shipping-option-form">
        <breadcrumb :breadcrumb="breadcrumb"/>

        <!-- Form -->
        <div class="panel panel-default" :class="{'form-loading': isLoading}">
            <div class="panel-heading">
                <span class="panel-title">{{ isEdit ? 'Edit' : 'Create' }} shipping option</span>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" class="form-control" v-model.number="form.price" min="0" step="0.01">
                </div>

                <div class="form-group">
                    <label for="price-when-free">Price when free:</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="checkbox" class="checkbox-inline" v-model="form.free_possible">
                        </span>
                        <input type="number" id="price-when-free" class="form-control" step="0.01" min="0.01"
                               v-model.number="form.price_when_free" :disabled="!form.free_possible">
                    </div>
                </div>

                <div class="translatable-content">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation"
                            v-for="language in languages"
                            :class="{active: !languages.indexOf(language)}">
                            <a role="tab"
                               data-toggle="tab"
                               :href="`#translations-${language.iso_code}`">
                                {{ language.title_localized }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-b-0">
                        <div role="tabpanel"
                             class="tab-pane fade"
                             v-for="language in languages"
                             :id="`translations-${language.iso_code}`"
                             :class="{ 'active in': !languages.indexOf(language) }">
                            <div class="form-group m-b-0">
                                <label :for="`name-${language.iso_code}`">Name</label>
                                <input type="text"
                                       class="form-control"
                                       :id="`name-${language.iso_code}`"
                                       v-model="form.translations[language.iso_code].name">
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">Locations ({{ form.locations.length }})</span>
                        <div class="panel-heading-btn">
                            <button class="btn btn-xs btn-success" type="button" data-toggle="collapse"
                                    data-target="#locations-collapse" aria-expanded="false" aria-controls="locations-collapse">
                                Show/hide locations
                            </button>
                        </div>
                    </div>

                    <div class="collapse" id="locations-collapse">
                        <div class="panel-body">
                            <table class="table table-striped m-a-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="location in form.locations">
                                        <td>{{ location.name }}</td>
                                        <td>{{ location.address }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-right">
                <button class="btn btn-success" @click="saveShippingOption()">
                    <i class="fa fa-check"></i> {{ isEdit ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
	import { mapState } from 'vuex';

	export default {
		computed: {
			...mapState(['languages']),

			breadcrumb () {
				let breadcrumb = {
					'products.index': 'Products',
					'shipping-options.index': 'Shipping options'
				};

				if (this.$route.params.id) {
					breadcrumb['shipping-options.edit'] = {
						title: 'Edit shipping option',
						params: {id: this.$route.params.id}
					};
				} else {
					breadcrumb['shipping-options.create'] = 'Create shipping option';
				}

				return breadcrumb;
			},

			isEdit () {
				return !!this.$route.params.id;
			}
		},

		watch: {
			$route () {
				this.init();
			}
		},

		data () {
			return {
				isLoading: true,
				form: {}
			};
		},

		created () {
			this.init();
		},

		methods: {
			async init () {
				this.isLoading = true;

				this.form = {
					translations: this.$helpers.mockTranslations({name: ''}),
                    locations: [],
                    price: 0,
                    price_when_free: 0,
                    free_possible: false
				};

				if (this.isEdit) {
					await this.fetchShippingOption();
				}

				this.isLoading = false;
			},

            async fetchShippingOption() {
				let {data: shippingOption} = await this.$http.get(`/admin/products/api/shipping-options/${this.$route.params.id}`);

				this.form = shippingOption;
                this.form.free_possible = this.form.price_when_free !== null;
			}
		}
	};
</script>