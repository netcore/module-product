import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

const store = new Vuex.Store({
	state: {
		initializedStates: {
			app: false,
			fieldsForm: false
		},

		languages: [],
		currencies: [],
		productCategories: [],
		productFieldTypes: null
	},

	mutations: {
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

		setInitializedState (state, {of, value}) {
			state['initializedStates'][of] = value;
		}
	},

	actions: {
		async initialize ({commit, state}) {
			if (state.initializedStates.app) {
				return;
			}

			const [{data: languages}, {data: currencies}] = await Promise.all([
				axios.get('/admin/products/api/languages'),
				axios.get('/admin/products/api/currencies')
			]);

			commit('setLanguages', languages);
			commit('setCurrencies', currencies);
			commit('setInitializedState', {of: 'app', value: true});
		},

		async getDataForFieldsForm ({commit, state}) {
			if (state.initializedStates.fieldsForm) {
				return;
			}

			const [{data: categories}, {data: types}] = await Promise.all([
				axios.get('/admin/products/api/categories'),
				axios.get('/admin/products/api/fields/get-types')
			]);

			commit('setProductCategories', categories);
			commit('setProductFieldTypes', types);
			commit('setInitializedState', {of: 'fieldsForm', value: true});
		}
	}
});

export default store;