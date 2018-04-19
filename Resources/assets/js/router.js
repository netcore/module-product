import VueRouter from 'vue-router';
import store from './store';

import ProductsIndex from './components/Product/Index';
import ProductsForm from './components/Product/Form';

import ParametersIndex from './components/Parameter/Index';
import ParametersForm from './components/Parameter/Form';

import FieldsIndex from './components/Field/Index';
import FieldsForm from './components/Field/Form';

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
		},

		// Fields.
		{
			path: '/fields',
			name: 'fields.index',
			component: FieldsIndex
		},
		{
			path: '/fields/create',
			name: 'fields.create',
			component: FieldsForm,
			beforeEnter(to, from, next) {
				store.dispatch('getDataForFieldsForm').then(next).catch(router.app.$helpers.showServerError);
			}
		},
		{
			path: '/fields/:id/edit',
			name: 'fields.edit',
			component: FieldsForm,
			beforeEnter(to, from, next) {
				store.dispatch('getDataForFieldsForm').then(next).catch(router.app.$helpers.showServerError);
			}
		},
	]
});

router.beforeEach((to, from, next) => {
	store.dispatch('initialize').then(next).catch(router.app.$helpers.showServerError);
});

export default router;