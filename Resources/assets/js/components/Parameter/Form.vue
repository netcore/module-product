<template>
    <div class="panel panel-default" :class="{ 'form-loading': isLoading }">
        <div class="panel-heading">
            <span class="panel-title">Create parameter</span>
        </div>
        <div class="panel-body">
            <div id="translatable-content">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" :key="language.id" v-for="language in languages"
                        :class="{ active: !languages.indexOf(language) }">
                        <a role="tab" data-toggle="tab" :href="`#translations-${language.iso_code}`"
                           :aria-controls="`translations-${language.iso_code}`" v-text="language.title_localized"></a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade" v-for="language in languages"
                         :class="{ 'active in': !languages.indexOf(language) }"
                         :id="`translations-${language.iso_code}`" :key="language.id">
                        <div class="form-group">
                            <label :for="`name-${language.iso_code}`">Parameter name:</label>
                            <input type="text" :id="`name-${language.iso_code}`" class="form-control"
                                   v-model="form.translations[language.iso_code].name">
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

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="panel-title">Parameter attributes ({{ form.attributes.length }})</span>
                        <div class="panel-heading-btn">
                            <button type="button" class="btn btn-xs btn-success" @click="addAttribute()">
                                <i class="fa fa-plus"></i> Add attribute
                            </button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-stripped m-a-0">
                            <tbody>
                                <parameter-attribute
                                        :key="attribute.id"
                                        :attribute="attribute"
                                        :languages="languages"
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
</template>

<script>
	import _ from 'lodash';
	import axios from 'axios';
	import Helpers from '../../global-helpers';

	export default {
		props: {
			languages: {
				type: Array,
				required: true
			},

			method: {
				type: String,
				required: true
			},

			route: {
				type: String,
				required: true
			},

			parameter: {
				type: Object
			}
		},

		data () {
			return {
				form: {
					type: 'radio',
					translations: {},
					attributes: [],
					iconable_attributes: false,
					value_from: null,
					value_to: null
				},
				isLoading: false
			};
		},

		created () {
			this.mockTranslations();

			if (this.parameter) {
				this.form = this.parameter;
			}
		},

		computed: {
			isTypeRange () {
				return this.form.type === 'range';
			}
		},

		methods: {
			mockTranslations () {
				let translations = {};

				_.each(this.languages, language => {
					translations[language.iso_code] = {
						name: ''
					};
				});

				this.form.translations = translations;
			},

			addAttribute () {
				let translations = {};

				_.each(this.languages, language => {
					translations[language.iso_code] = {
						name: ''
					};
				});

				this.form.attributes.push({
					id: Helpers.randomString(10),
					image: null,
					translations
				});
			},

			removeAttribute (attribute) {
				this.form.attributes.splice(
					this.form.attributes.indexOf(attribute), 1
				);
			},

			save () {
				this.isLoading = true;

				let data = _.merge(this.form, {_method: this.method});

				let formData = new FormData;
				Helpers.buildFormData(formData, data);

				let request = axios({
					url: this.route,
					method: 'POST',
					data: formData
				});

				request.then(({data: res}) => {
					if (res.success) {
						$.growl.success({
							message: res.success
						});
					}

					if (res.redirect) {
						window.location.replace(
							res.redirect
						);
					}
				}).catch(Helpers.showServerError).then(() => {
					this.isLoading = false;
				});
			}
		},

		components: {
			'parameter-attribute': require('./Attribute.vue')
		}
	};
</script>