<template>
    <div class="panel panel-default" :class="{ 'form-loading': isLoading }">
        <div class="panel-heading">
            <span class="panel-title" v-if="isEditAction">Edit product field</span>
            <span class="panel-title" v-else>Create product field</span>
        </div>

        <div class="panel-body">
            <div class="translatable-content">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" :class="{ active: !languages.indexOf(language) }"
                        v-for="language in languages">
                        <a role="tab" data-toggle="tab" :href="`#translations-${language.iso_code}`"
                           :aria-controls="`translations-${language.iso_code}`" v-text="language.title_localized"></a>
                    </li>
                </ul>

                <div class="tab-content p-b-0">
                    <div role="tabpanel" class="tab-pane fade" v-for="language in languages"
                         :class="{ 'active in': !languages.indexOf(language) }"
                         :id="`translations-${language.iso_code}`">

                        <div class="form-group m-b-0">
                            <label :for="`name-${language.iso_code}`">Name</label>
                            <input type="text" class="form-control" :id="`name-${language.iso_code}`"
                                   v-model="form.translations[language.iso_code].name">
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="form-group">
                <label for="type">Field type:</label>
                <select id="type" class="form-control" v-model="form.type" :disabled="isEditAction">
                    <option :value="key" v-for="(type, key) in types">{{ type.name }}</option>
                </select>
            </div>

            <div class="field-options" v-if="isRadioType">
                <button type="button" class="btn btn-xs btn-success" @click="addOption()">
                    <i class="fa fa-plus"></i> Add option
                </button>

                <table class="table table-stripped m-y-2">
                    <tbody>
                    <tr v-for="option in form.options">
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

            <label for="is_translatable" class="switcher switcher-success"
                   v-if="selectedType && selectedType.translatable">
                <input type="checkbox" id="is_translatable" value="1" v-model="form.is_translatable">
                <div class="switcher-indicator">
                    <div class="switcher-yes">Yes</div>
                    <div class="switcher-no">No</div>
                </div>
                Is translatable?
            </label>
        </div>

        <div class="panel-footer text-right">
            <span class="text-danger m-r-1" v-if="!canBeSaved">Please fill out all fields with correct data!</span>

            <button class="btn btn-success" @click="saveField()" :disabled="! canBeSaved">
                <i class="fa fa-check"></i> {{ isEditAction ? 'Save' : 'Create' }}
            </button>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash';
    import axios from 'axios';
    import Helpers from '../../global-helpers';

    /**
     * Component props.
     */
    export default {
        props: {
            action: {
                type: String,
                default: 'create'
            },
            languages: {
                required: true,
                type: Array
            },
            route: {
                type: String,
                required: true
            },
            types: {
                required: true,
                type: Object
            },
            categories: {
                required: true,
                type: Array
            },
            field: {
                type: Object
            }
        },

        /**
         * Component data.
         *
         * @returns {{form: {translations: {}, type: string, is_translatable: boolean, is_global: boolean, options: Array, categories: Array}, isLoading: boolean}}
         */
        data() {
            return {
                form: {
                    translations: {},
                    type: 'text',
                    is_translatable: true,
                    is_global: true,
                    options: [],
                    categories: []
                },
                isLoading: false
            };
        },

        /**
         * Created event.
         */
        created() {
            this.mockTranslations();

            if (this.field) {
                this.form = this.field;
            }
        },

        /**
         * Mounted event.
         */
        mounted() {
            $(document).keydown(event => {
                    if ((event.ctrlKey || event.metaKey) && event.which === 83) {
                        event.preventDefault();

                        if (this.canBeSaved) {
                            this.saveField();
                        }
                    }
                }
            );
        },

        /**
         * Computed properties.
         */
        computed: {
            /**
             * Is field radio.
             */
            isRadioType() {
                return this.form.type === 'radio';
            },

            /**
             * Is edit form.
             */
            isEditAction() {
                return !!this.field;
            },

            /**
             * Selected field type.
             */
            selectedType() {
                return _.get(this.types, this.form.type);
            },

            /**
             * Can be saved, some kind of validation.
             */
            canBeSaved() {
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

        /**
         * Component methods.
         */
        methods: {
            /**
             * Add option to radio field.
             */
            addOption() {
                let translations = {};

                _.each(this.languages, language => {
                    translations[language.iso_code] = {
                        name: ''
                    };
                });

                this.form.options.push({
                    id: null,
                    translations: translations
                });
            },

            /**
             * Remove radio option.
             *
             * @param option
             */
            removeOption(option) {
                this.form.options.splice(
                    this.form.options.indexOf(option), 1
                );
            },

            /**
             * Mock translations.
             */
            mockTranslations() {
                let translations = {};

                _.each(this.languages, language => {
                    translations[language.iso_code] = {
                        name: ''
                    };
                });

                this.form.translations = translations;
            },

            /**
             * Save handler.
             */
            saveField() {
                this.isLoading = true;

                let promise = axios({
                    url: this.route,
                    method: this.isEditAction ? 'PUT' : 'POST',
                    data: this.form
                });

                promise
                    .then(({data}) => {
                        if (data.success) {
                            $.growl.success({
                                message: data.success
                            });
                        }

                        if (data.redirect) {
                            window.location.replace(data.redirect);
                        }

                        if (data.field) {
                            this.form = data.field;
                        }

                        // Remove xEditable .editable-unsaved class
                        $(this.$el).find('a.vue-editable').removeClass('editable-unsaved');
                    })
                    .catch(Helpers.showServerError)
                    .then(() => {
                        this.isLoading = false;
                    });
            }
        },

        /**
         * Component watchers.
         */
        watch: {
            'form.type'(type) {
                if (this.selectedType || !this.selectedType.translatable) {
                    this.form.is_translatable = false;
                }
            }
        },

        /**
         * Register child components.
         */
        components: {
            'x-editable': require('../XEditable.vue'),
            'select2': window.Select2
        }
    };
</script>