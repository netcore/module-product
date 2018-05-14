<template>
    <section id="shipping-options-index">
        <breadcrumb :breadcrumb="breadcrumb"></breadcrumb>

        <div class="page-header">
            <div class="row">
                <div class="col-md-4 text-xs-center text-md-left text-nowrap">
                    <h1>
                        <span class="text-muted font-weight-light">
                            <i class="page-header-icon fa fa-truck"></i> Shipping options
                        </span>
                    </h1>
                </div>

                <hr class="page-wide-block visible-xs visible-sm">

                <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
                    <router-link :to="{name: 'shipping-options.create'}" class="btn btn-primary btn-block">
                        <span class="btn-label-icon left fa fa-plus-circle"></span> Create shipping option
                    </router-link>
                </div>
            </div>
        </div>

        <div class="table-primary" :class="{'form-loading': isLoading}">
            <table class="table table-bordered" style="width: 100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Price when free</th>
                        <th class="text-center">Active</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="option in shippingOptions">
                        <td>{{ option.name }}</td>
                        <td>{{ option.price }}</td>
                        <td>{{ option.is_free_enabled ? option.price_when_free : 'Disabled' }}</td>
                        <td style="width: 70px;">
                            <label class="switcher switcher-blank switcher-success" style="margin-bottom: 0;">
                                <input type="checkbox" v-model="option.is_active" @change="toggleOptionActiveState(option)">
                                <div class="switcher-indicator">
                                    <div class="switcher-yes">YES</div>
                                    <div class="switcher-no">NO</div>
                                </div>
                            </label>
                        </td>
                        <td class="text-right" style="width: 150px;">
                            <router-link :to="{name: 'shipping-options.edit', params: {id: option.id}}" class="btn btn-xs btn-primary">
                                <i class="fa fa-edit"></i> Edit
                            </router-link>

                            <button type="button" class="btn btn-xs btn-danger" @click="deleteShippingOption(option)" :disabled="!isOptionDeleteable(option)">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>

                    <tr v-if="!shippingOptions.length">
                        <td colspan="5">
                            <div class="alert alert-warning m-b-0">
                                No shipping options found!
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script>
	export default {
		computed: {
			breadcrumb () {
				return {
					'products.index': 'Products',
					'shipping-options.index': 'Shipping options'
				};
			}
		},

		data () {
			return {
				isLoading: true,
				shippingOptions: []
			};
		},

		created () {
			this.fetchShippingOptions();
		},

		methods: {
			async fetchShippingOptions () {
				try {
					let {data: options} = await this.$http.get('/admin/products/api/shipping-options');
					this.shippingOptions = options;
					this.isLoading = false;
				} catch (e) {
					alert(`Unable to load shipping options: ${e.message}`);
				}
			},

			async toggleOptionActiveState (option) {
				this.isLoading = true;

				try {
					await this.$http.post(`/admin/products/api/shipping-options/toggle-state/${option.id}`);
				} catch (e) {
					option.is_active = !option.is_active;
					alert(`Unable to change option active state: ${e.message}`);
				}

				this.isLoading = false;
			},

			isOptionDeleteable (option) {
				return option.type !== 'dpd' && option.type !== 'omniva';
			},

			async deleteShippingOption (option) {
				try {
					await swal({
						title: 'Are you sure?',
						text: 'Shipping option will be deleted!',
						type: 'warning',
						showCancelButton: true,
						confirmButtonText: 'Yes',
						cancelButtonText: 'No'
					});

					try {
						let {data: response} = await this.$http.delete(`/admin/products/api/shipping-options/${option.id}`);

						$.growl.success({
                            message: _.get(response, 'data.message', 'Shipping option successfully deleted!')
                        });

						this.shippingOptions.splice(
							this.shippingOptions.indexOf(option), 1
						);
                    } catch(e) {
						$.growl.error({
                            message: _.get(e, 'response.data.message', e.message)
                        });
                    }
				} catch (e) {}

				this.isLoading = false;
			}
		}
	};
</script>