<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-list-alt"></i> Local product fields
            </span>

            <ul class="nav nav-tabs nav-xs">
                <li :class="{active: language.iso_code === currentLanguage}" v-for="language in languages">
                    <a @click.prevent="currentLanguage = language.iso_code">
                        <i class="fa fa-exclamation-triangle pulse" v-if="$parent.hasError(`translations.${language.iso_code}`)"></i>
                        {{ language.title }}
                    </a>
                </li>
            </ul>
        </div>

        <div class="panel-body">
            <div v-for="language in languages" v-show="language.iso_code === currentLanguage">
                <div class="form-group" :class="{'has-error': $parent.hasError(`translations.${language.iso_code}.title`)}">
                    <label class="control-label">Title</label>
                    <input type="text" class="form-control" v-model="product.translations[language.iso_code].title">
                    <span class="help-block" v-if="$parent.hasError(`translations.${language.iso_code}.title`)">
                        {{ $parent.getError(`translations.${language.iso_code}.title`) }}
                    </span>
                </div>

                <div class="form-group" :class="{'has-error': $parent.hasError(`translations.${language.iso_code}.slug`)}">
                    <label class="control-label">Slug <i class="text-light">(leave blank to generate automatically)</i></label>
                    <input type="text" class="form-control" v-model="product.translations[language.iso_code].slug">
                    <span class="help-block" v-if="$parent.hasError(`translations.${language.iso_code}.slug`)">
                        {{ $parent.getError(`translations.${language.iso_code}.slug`) }}
                    </span>
                </div>

                <div class="form-group" :class="{'has-error': $parent.hasError(`translations.${language.iso_code}.description`)}">
                    <label class="control-label">Description</label>
                    <textarea class="form-control" v-model="product.translations[language.iso_code].description"></textarea>
                    <span class="help-block" v-if="$parent.hasError(`translations.${language.iso_code}.description`)">
                        {{ $parent.getError(`translations.${language.iso_code}.description`) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import { mapState } from 'vuex';

	export default {
		computed: mapState(['languages']),

		props: {
			product: {
				type: Object,
				required: true
			}
		},

        data() {
			return {
				currentLanguage: null
            };
        },

        created() {
			this.currentLanguage = this.languages[0].iso_code;
        }
	};
</script>