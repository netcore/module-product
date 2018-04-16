<div>
    <div class="product-variant">
        <input type="hidden" v-model="product.id" :name="isVariable ? `variants[${product.key}][id]` : `id`">

        <!-- Product variant remove button -->
        <div class="text-right" v-if="isVariable">
            <button type="button" class="btn btn-xs btn-danger" @click="removeProductVariant()">
                <i class="fa fa-trash"></i> Remove variant
            </button>
            <hr>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="panel-title">
                    <i class="fa fa-list-alt"></i> Local product fields
                </span>

                <ul class="nav nav-tabs nav-xs">
                    <li :class="{active: language.iso_code === currentLanguage}" v-for="language in languages">
                        <a @click.prevent="currentLanguage = language.iso_code">{{ language.title_localized }}</a>
                    </li>
                </ul>
            </div>

            <div class="panel-body">

                <div v-for="language in languages" v-show="language.iso_code === currentLanguage"></div>

            </div>
        </div>

        <div class="translatable-content">
            <div class="form-group" v-for="language in languages">
                <label :for="`title-${language.iso_code}`">Title {{ language.title_localized }}:</label>
                <input type="text"
                       class="form-control"
                       :name="getTranslatableInputName(language, 'title')"
                       v-model="product.translations[language.iso_code].title">
            </div>
        </div>

        <hr>

        <product-images :images="product.images" :reorder-route="imageReorderRouteReplaced"></product-images>
        <product-prices :currencies="currencies" :prices="product.prices"></product-prices>
    </div>
</template>

<script>
    export default {
        /**
         * Props.
         */
        props: {
            product: {
                type: Object,
                required: true
            },

            isVariable: {
                default: false
            },

            currencies: {
                type: Array,
                required: true
            },

            languages: {
                type: Array,
                required: true
            },

            imageReorderRoute: {
                type: String,
                required: false,
                default: ''
            }
        },

        /**
         * Component methods.
         */
        methods: {
            /**
             * Delete button handler.
             */
            removeProductVariant() {
                let self = this;

                bootbox.confirm({
                    message: 'Are you really want to delete this product variant?',
                    className: 'bootbox-sm',

                    callback(result) {
                        if (result) {
                            self.$emit('remove');
                        }
                    }
                });
            },

            getTranslatableInputName(language, inputName) {
                if (this.isVariable) {
                    return `variants[${this.product.key}][translations][${language.iso_code}][${inputName}]`;
                }

                return `translations[${language.iso_code}][${inputName}]`;
            }
        },

        /**
         * Computed properties.
         */
        computed: {
            imageReorderRouteReplaced() {
                return this.imageReorderRoute.replace('--ID--', this.product.id);
            }
        },

        /**
         * Child components.
         */
        components: {
            'product-images': require('./Images.vue'),
            'product-prices': require('./Prices.vue')
        }
    };
</script>