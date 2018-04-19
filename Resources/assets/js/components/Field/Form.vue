<template>
    <div id="field-form" :class="{ 'form-loading': isLoading }">
        <breadcrumb :breadcrumb="breadcrumb"/>

        <!-- Form -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="panel-title">{{ isEdit ? 'Edit' : 'Create' }} product field</span>
            </div>

            <div class="panel-body">
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

                <div class="form-group">
                    <label for="type">Field type:</label>
                    <select id="type" class="form-control" v-model="form.type" :disabled="isEdit">
                        <option :value="key" v-for="(type, key) in types">{{ type.name }}</option>
                    </select>
                </div>

                <div class="field-options" v-if="isRadioType">
                    <button type="button" class="btn btn-xs btn-success" @click="addOption()">
                        <i class="fa fa-plus"></i> Add option
                    </button>

                    <table class="table table-stripped m-y-2">
                        <tbody>
                            <tr v-for="option in form.options" :key="option.key">
                                <td width="1%">
                                    <button type="button" class="btn btn-xs btn-danger" @click="removeOption(option)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                                <td>
                                    <ul class="list-unstyled m-a-0">
                                        <li v-for="language in languages">
                                            <b>{{ language.iso_code | uppercase }}:</b>
                                            <x-editable v-model="option.translations[language.iso_code].name"></x-editable>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-group" v-if="!form.is_global">
                    <label for="categories">Categories:</label>
                    <select2 :multiple="true" :data="categories" v-model="form.categories"></select2>
                </div>

                <label for="is_global" class="switcher switcher-success">
                    <input type="checkbox" id="is_global" value="1" v-model="form.is_global">
                    <div class="switcher-indicator">
                        <div class="switcher-yes">Yes</div>
                        <div class="switcher-no">No</div>
                    </div>
                    Global for all categories?
                </label>

                <label for="is_translatable" class="switcher switcher-success" v-if="selectedType && selectedType.translatable">
                    <input type="checkbox" id="is_translatable" value="1" v-model="form.is_translatable">
                    <div class="switcher-indicator">
                        <div class="switcher-yes">Yes</div>
                        <div class="switcher-no">No</div>
                    </div>
                    Is translatable?
                </label>
            </div>

            <div class="panel-footer text-right">
                <span class="text-danger m-r-1" v-if="!canBeSaved">
                    Please fill out all fields with correct data!
                </span>

                <button class="btn btn-success" @click="saveField()" :disabled="! canBeSaved">
                    <i class="fa fa-check"></i> {{ isEdit ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
	import { mapState } from 'vuex';
	import XEditable from '../XEditable';
	import _ from 'lodash';

	export default {
		computed: {
			...mapState({
				languages: 'languages',
				categories: 'productCategories',
				types: 'productFieldTypes'
			}),

			breadcrumb () {
				let breadcrumb = {
					'products.index': 'Products',
					'fields.index': 'Product fields'
				};

				if (this.$route.params.id) {
					breadcrumb['fields.edit'] = {
						title: 'Edit product field',
						params: {id: this.$route.params.id}
					};
				} else {
					breadcrumb['fields.create'] = 'Create product field';
				}

				return breadcrumb;
			},

			isRadioType () {
				return this.form.type === 'radio';
			},

			isEdit () {
				return !!this.$route.params.id;
			},

			selectedType () {
				return _.get(this.types, this.form.type);
			},

			canBeSaved () {
				let canBeSaved = true;

				// Empty translations.
				_.each(this.form.translations, translation => {
					if (_.isEmpty(translation.name)) {
						canBeSaved = false;
					}
				});

				// Empty option translations or no options for radio type.
				if (this.isRadioType) {
					if (!this.form.options.length) {
						canBeSaved = false;
					} else {
						_.each(this.form.options, option => {
							_.each(option.translations, translation => {
								if (_.isEmpty(translation.name)) {
									canBeSaved = false;
								}
							});
						});
					}
				}

				return canBeSaved;
			}
		},

		data () {
			return {
				isLoading: true,
                form: {}
			};
		},

        created() {
			this.init();
        },

		methods: {
			init() {
				this.isLoading = true;
				this.form = {
					type: 'text',
						options: [],
						categories: [],
						is_global: true,
						is_translatable: false,
						translations: this.$helpers.mockTranslations({
						name: ''
					})
				};

				if(this.isEdit) {
					this.fetchField();
                } else {
					this.isLoading = false;
                }
            },

			addOption () {
				this.form.options.push({
					id: null,
					key: this.$helpers.randomString(10),
					translations: this.$helpers.mockTranslations({
						name: ''
					})
				});
			},

			removeOption (option) {
				this.form.options.splice(
					this.form.options.indexOf(option), 1
				);
			},

			async saveField () {
				this.isLoading = true;

				try {
					let {data: response} = await this.$http({
						url: this.isEdit ? `/admin/products/api/fields/${this.$route.params.id}` : '/admin/products/api/fields',
						method: this.isEdit ? 'PUT' : 'POST',
						data: this.form
					});

					if (response.success) {
						$.growl.success({
							message: response.success
						});
					}

					if (response.redirect) {
						this.$router.push(response.redirect);
					}
				}
				catch (e) {
					this.$helpers.showServerError(e);
				}

				$(this.$el).find('a.vue-editable').removeClass('editable-unsaved');
				this.isLoading = false;
			},

			async fetchField () {
                let {data: field} = await this.$http.get(`/admin/products/api/fields/${this.$route.params.id}`);

                this.form = field;
                this.isLoading = false;
			}
		},

		watch: {
			'form.type' () {
				if (!this.selectedType || (!this.selectedType.translatable && this.form.is_translatable)) {
					this.form.is_translatable = false;
				}
			},

            $route() {
				this.init();
            }
		},

		components: {
			XEditable,
			select2: window.Select2
		}
	};
</script>