import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
	state: {
		languages: []
	},

	mutations: {
		setLanguages (state, languages) {
			state.languages = languages;
		}
	}
});

export default store;