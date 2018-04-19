<template>
    <section id="parameters-index">
        <breadcrumb :breadcrumb="breadcrumb"></breadcrumb>

        <div class="page-header">
            <div class="row">
                <div class="col-md-4 text-xs-center text-md-left text-nowrap">
                    <h1>
                        <span class="text-muted font-weight-light">
                            <i class="page-header-icon fa fa-check"></i> Product parameters
                        </span>
                    </h1>
                </div>

                <hr class="page-wide-block visible-xs visible-sm">

                <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
                    <router-link :to="{name: 'parameters.index'}" class="btn btn-danger btn-block">
                        <span class="btn-label-icon left fa fa-times"></span> Back to the list
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="panel panel-default" :class="{'form-loading': isLoading}">
            <div class="panel-body">
                <div id="translatable-content">
                    <ul class="nav nav-tabs" role="tablist">
                        <li :key="language.id"
                            role="presentation"
                            v-for="language in languages"
                            :class="{active: !languages.indexOf(language)}">
                            <a role="tab"
                               data-toggle="tab"
                               :href="`#translations-${language.iso_code}`"
                               :aria-controls="`translations-${language.iso_code}`">
                                <i class="fa fa-warning" v-if="formErrors.has(`translations.${language.iso_code}`)"></i>
                                {{ language.title }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel"
                             :key="language.id"
                             class="tab-pane fade"
                             v-for="language in languages"
                             :id="`translations-${language.iso_code}`"
                             :class="{'active in': !languages.indexOf(language)}">

                            <div class="form-group" :class="{'has-error': formErrors.has(`translations.${language.iso_code}.name`)}">
                                <label :for="`name-${language.iso_code}`">Parameter name:</label>

                                <input type="text"
                                       class="form-control"
                                       :id="`name-${language.iso_code}`"
                                       v-model="form.translations[language.iso_code].name"
                                       @keyup="formErrors.clear(`translations.${language.iso_code}.name`)">

                                <span class="help-block" v-if="formErrors.has(`translations.${language.iso_code}.name`)">
                                {{ formErrors.get(`translations.${language.iso_code}.name`) }}
                            </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="parameter-type">Parameter type:</label>
                    <select name="type" id="parameter-type" class="form-control" v-model="form.type">
                        <option value="radio">Radio</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="range">Range</option>
                    </select>
                </div>

                <div class="form-group" v-if="isTypeRange">
                    <label class="control-label">Ranges:</label>
                    <div class="input-group">
                        <span class="input-group-addon">Value from:</span>
                        <input type="number" class="form-control" v-model="form.value_from">

                        <span class="input-group-addon">Value to:</span>
                        <input type="number" class="form-control" v-model="form.value_to">
                    </div>
                </div>

                <template v-else>
                    <label for="attributes-has-icon" class="switcher switcher-success m-t-1 m-b-2">
                        <input type="checkbox" id="attributes-has-icon" v-model="form.iconable_attributes">
                        <div class="switcher-indicator">
                            <div class="switcher-yes">YES</div>
                            <div class="switcher-no">NO</div>
                        </div>
                        Attributes with icons/images
                    </label>

                    <div class="panel panel-default m-b-0">
                        <div class="panel-heading">
                            <span class="panel-title">Parameter attributes ({{ form.attributes.length }})</span>
                            <div class="panel-heading-btn">
                                <button type="button" class="btn btn-xs btn-success" @click="addAttribute()">
                                    <i class="fa fa-plus"></i> Add attribute
                                </button>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="alert alert-danger" v-if="formErrors.has('attributes.')">
                                {{ formErrors.getFirstOf('attributes.') }}
                            </div>

                            <table class="table table-stripped m-a-0">
                                <tbody>
                                    <parameter-attribute
                                            :key="attribute.id"
                                            :attribute="attribute"
                                            :has-icon="form.iconable_attributes"
                                            v-for="attribute in form.attributes"
                                            @remove="removeAttribute(attribute)">
                                    </parameter-attribute>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>

            <div class="panel-body text-right">
                <button type="button" class="btn btn-success" @click="save()">
                    <i class="fa fa-check"></i> Save
                </button>
            </div>
        </div>
    </section>
</template>

<script>
	import { mapState } from 'vuex';
	import ParameterAttribute from './_partials/Attribute';

	export default {
		data () {
			return {
				isLoading: true,
				formErrors: this.$helpers.getErrorsBag(),
				form: {}
			};
		},

		computed: {
			...mapState(['languages']),

			breadcrumb () {
				let breadcrumb = {
					'products.index': 'Products',
					'parameters.index': 'Parameters'
				};

				if (this.$route.params.id) {
					breadcrumb['parameters.edit'] = {
						title: 'Edit parameter',
						params: {id: this.$route.params.id}
					};
				} else {
					breadcrumb['parameters.create'] = 'Create parameter';
				}

				return breadcrumb;
			},

			isTypeRange () {
				return this.form.type === 'range';
			}
		},

		created () {
			this.init();
		},

		methods: {
			init () {
				this.isLoading = true;

				this.form = {
					type: 'radio',
					attributes: [],
					value_to: null,
					value_from: null,
					iconable_attributes: false,
					translations: this.$helpers.mockTranslations({name: ''})
				};

				if (this.$route.params.id) {
					this.fetchParameter();
				}
				else {
					this.isLoading = false;
				}
			},

			fetchParameter () {
				this.$http.get(`/admin/products/api/parameters/${this.$route.params.id}`).then(({data: parameter}) => {
					this.form = parameter;
				}).catch(this.$helpers.showServerError).then(() => this.isLoading = false);
			},

			addAttribute () {
				this.form.attributes.push({
					key: this.$helpers.randomString(10),
					image: null,
					translations: this.$helpers.mockTranslations({name: ''}, this.languages)
				});
			},

			removeAttribute (attribute) {
				this.form.attributes.splice(
					this.form.attributes.indexOf(attribute), 1
				);
			},

			save () {
				this.isLoading = true;
				this.formErrors.clearAll();

				let id = this.$route.params.id;
				let url = id ? `/admin/products/api/parameters/${id}` : `/admin/products/api/parameters`;
				let method = id ? 'PUT' : 'POST';

				let data = this.$helpers.getFormDataFromObject(
					_.merge(this.form, {_method: method})
				);

				this.$http.post(url, data).then(({data: res}) => {
					if (res.success) {
						$.growl.success({
							message: res.success
						});
					}

					if (res.redirect) {
						this.$router.push(res.redirect);
					}
				}).catch(err => {
					if (err.response && err.response.status === 422 && err.response.data && typeof err.response.data.errors === 'object') {
						return this.formErrors.set(err.response.data.errors);
					}

					this.$helpers.showServerError(err);
				}).then(() => {
					this.isLoading = false;
				});
			}
		},

		watch: {
			$route () {
				this.init();
			}
		},

		components: {
			ParameterAttribute
		}
	};
</script>

<style lang="scss" scoped>
    .table-stripped {
        tr:nth-child(even) {
            background-color: #f2f2f2 !important;
        }
    }
</style>