<template>
    <div class="panel panel-default" v-if="parameters.length">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-cogs"></i> Parameters
            </span>

            <ul class="nav nav-tabs nav-xs">
                <li :class="{active: parameter.id === currentParameter}" v-for="parameter in parameters">
                    <a @click.prevent="currentParameter = parameter.id">{{ parameter.name }}</a>
                </li>
            </ul>
        </div>

        <div class="panel-body" v-if="currentParameter">
            <div class="parameter" v-for="parameter in parameters" v-show="parameter.id === currentParameter">
                <label :for="`product-${product.id}-parameter-enabled-${parameter.id}`" class="switcher switcher-success">
                    <input value="1"
                           type="checkbox"
                           v-model="product.parameters[parameter.id].enabled"
                           :id="`product-${product.id}-parameter-enabled-${parameter.id}`">
                    <div class="switcher-indicator">
                        <div class="switcher-yes">Yes</div>
                        <div class="switcher-no">No</div>
                    </div>
                    Enabled
                </label>

                <hr>

                <template v-if="parameter.type === 'range'">
                    <div class="form-group">
                        <label :for="`parameter-value-${parameter.id}`">
                            Enter value between {{ parameter.value_from }} to {{ parameter.value_to }}
                        </label>

                        <input step="1" type="number" class="form-control"
                               :id="`parameter-value-${parameter.id}`"
                               :max="parameter.value_to" :min="parameter.value_from"
                               v-model.number="product.parameters[parameter.id].value">
                    </div>
                </template>

                <template v-else>
                    <div v-if="!parameter.attributes || !parameter.attributes.length">
                        <div class="text-danger">No attributes added for this parameter.</div>
                        <router-link :to="{name: 'parameters.edit', params: {id: parameter.id}}" target="_blank">
                            Edit parameter
                        </router-link>
                    </div>

                    <table class="table table-striped" v-else>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Attribute</th>
                                <th v-if="parameter.is_countable">Quantity</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="attribute in parameter.attributes">
                                <td>
                                    <input type="checkbox"
                                           title="Checkbox"
                                           :value="attribute.id"
                                           :name="`product-${product.id}-parameter-${parameter.id}-enabled-${attribute.id}`"
                                           v-model="product.parameters[parameter.id].attributes[attribute.id].enabled"
                                           v-if="getInputType(parameter) === 'checkbox'">

                                    <input type="radio"
                                           title="Radio"
                                           :value="attribute.id"
                                           :name="`product-${product.id}-parameter-${parameter.id}-radio`"
                                           v-model="product.parameters[parameter.id].value"
                                           v-if="getInputType(parameter) === 'radio'">
                                </td>
                                <td>{{ attribute.name}}</td>
                                <td v-if="parameter.is_countable">
                                    <input type="number"
                                           title="Quantity"
                                           class="form-control-sm count-input"
                                           v-model.number="product.parameters[parameter.id].attributes[attribute.id].quantity">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
	import { mapState } from 'vuex';

	export default {
		computed: mapState({
			parameters: 'productParameters'
		}),

		props: {
			product: {
				type: Object,
				required: true
			}
		},

		data () {
			return {
				currentParameter: null
			};
		},

		methods: {
			getInputType (parameter) {
				if (parameter.is_countable || parameter.type === 'checkbox') {
					return 'checkbox';
				}

				return 'radio';
			}
		}
	};
</script>

<style lang="scss" scoped>
    .table {
        thead, tbody {
            tr > th:first-child,
            tr > td:first-child {
                width: 35px;
                text-align: center;

                input {
                    margin-top: -2px;
                }
            }

            .count-input {
                max-width: 70px;
            }

            tr td {
                vertical-align: middle;
            }
        }
    }
</style>