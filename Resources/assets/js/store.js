import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
	state: {
		languages: [],
		currencies: [],
		productCategories: []
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
		}
	}
});

export default store;