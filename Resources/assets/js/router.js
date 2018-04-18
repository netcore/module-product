import VueRouter from 'vue-router';
import store from './store';

import ProductsIndex from './components/Product/Index';
import ProductsForm from './components/Product/Form';

import ParametersIndex from './components/Parameter/Index';
import ParametersForm from './components/Parameter/Form';

Vue.use(VueRouter);

// Router.
const router = new VueRouter({
	routes: [
		// Product routes.
		{
			path: '/products',
			name: 'products.index',
			component: ProductsIndex
		},
		{
			path: '/products/create',
			name: 'products.create',
			component: ProductsForm
		},
		{
			path: '/products/:id/edit',
			name: 'products.edit',
			component: ProductsForm
		},

		// Parameter routes.
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
	const $http = router.app.$http;

	if (!store.state.languages.length) {
		$http.get('/admin/products/api/languages').then(({data: languages}) => {
			store.commit('setLanguages', languages);
		}).then(() => $http.get('/admin/products/api/currencies')).then(({data: currencies}) => {
			store.commit('setCurrencies', currencies);
		}).then(next);
	} else {
		next();
	}
});

export default router;