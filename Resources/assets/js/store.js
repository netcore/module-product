import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

const store = new Vuex.Store({
	state: {
		initialized: false,
		languages: [],
		currencies: [],
		productCategories: [],
		productFieldTypes: {},
		productParameters: []
	},

	mutations: {
		appInitialized (state) {
			state.initialized = true;
		},

		setLanguages (state, languages) {
			state.languages = languages;
		},

		setCurrencies (state, currencies) {
			state.currencies = currencies;
		},

		setProductCategories (state, categories) {
			state.productCategories = categories;
		},

		setProductFieldTypes (state, types) {
			state.productFieldTypes = types;
		},

		setProductParameters (state, parameters) {
			state.productParameters = parameters;
		}
	},

	actions: {
		async initialize ({commit, state}) {
			if (state.initialized) {
				return;
			}

			const {data} = await axios.get('/admin/products/api/init');

			commit('setLanguages', data.languages);
			commit('setCurrencies', data.currencies);
			commit('setProductFieldTypes', data.fieldTypes);
			commit('setProductCategories', data.categories);

			commit('appInitialized');
		}
	}
});

export default store;