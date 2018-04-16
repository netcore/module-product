import VueRouter from 'vue-router';
import store from './store';

Vue.use(VueRouter);

// Imports.
import ParametersIndex from './components/Parameter/Index';
import ParametersForm from './components/Parameter/Form';

// Router.
const router = new VueRouter({
	routes: [
		{
			path: '/parameters',
			name: 'parameters.index',
			component: ParametersIndex
		},
		{
			path: '/parameters/create',
			name: 'parameters.create',
			component: ParametersForm
		},
		{
			path: '/parameters/:id/edit',
			name: 'parameters.edit',
			component: ParametersForm
		}
	]
});

router.beforeEach((to, from, next) => {
	if (!store.state.languages.length) {
		router.app.$http.get('/admin/products/api/languages').then(({data: languages}) => {
			store.commit('setLanguages', languages);
		}).then(next);
	}
	else {
		next();
	}
});

export default router;