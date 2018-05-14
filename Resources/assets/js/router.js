import VueRouter from 'vue-router';
import store from './store';

import ProductsIndex from './components/Product/Index';
import ProductsForm from './components/Product/Form';

import ParametersIndex from './components/Parameter/Index';
import ParametersForm from './components/Parameter/Form';

import FieldsIndex from './components/Field/Index';
import FieldsForm from './components/Field/Form';

import ShippingOptionsIndex from './components/ShippingOption/Index';
import ShippingOptionsForm from './components/ShippingOption/Form';

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
			component: {template: '<router-view />'},
			children: [
				{
					path: '',
					name: 'parameters.index',
					component: ParametersIndex
				},
				{
					path: 'create',
					name: 'parameters.create',
					component: ParametersForm
				},
				{
					path: ':id/edit',
					name: 'parameters.edit',
					component: ParametersForm
				}
			]
		},

		// Fields.
		{
			path: '/fields',
			component: {template: '<router-view />'},
			children: [
				{
					path: '',
					name: 'fields.index',
					component: FieldsIndex
				},
				{
					path: 'create',
					name: 'fields.create',
					component: FieldsForm
				},
				{
					path: ':id/edit',
					name: 'fields.edit',
					component: FieldsForm
				}
			]
		},

		// Shipping options.
		{
			path: '/shipping-options',
			component: {template: '<router-view />'},
			children: [
				{
					path: '',
					name: 'shipping-options.index',
					component: ShippingOptionsIndex
				},
				{
					path: 'create',
					name: 'shipping-options.create',
					component: ShippingOptionsForm
				},
				{
					path: ':id/edit',
					name: 'shipping-options.edit',
					component: ShippingOptionsForm
				}
			]
		}
	]
});

router.beforeEach(async (to, from, next) => {
	try {
		await store.dispatch('initialize');
		next();
	} catch (e) {
		router.app.$helpers.showServerError(e);
		next(false);
	}
});

export default router;