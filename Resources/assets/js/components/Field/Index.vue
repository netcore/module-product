<template>
    <section id="fields-index" :class="{'panel-loading': isLoading}">
        <breadcrumb :breadcrumb="breadcrumb"></breadcrumb>

        <div class="page-header">
            <div class="row">
                <div class="col-md-4 text-xs-center text-md-left text-nowrap">
                    <h1>
                        <span class="text-muted font-weight-light">
                            <i class="page-header-icon fa fa-pencil"></i> Product fields
                        </span>
                    </h1>
                </div>

                <hr class="page-wide-block visible-xs visible-sm">

                <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
                    <router-link :to="{name: 'fields.create'}" class="btn btn-primary btn-block">
                        <span class="btn-label-icon left fa fa-plus-circle"></span> Create field
                    </router-link>
                </div>
            </div>
        </div>

        <div class="table-primary">
            <table class="table table-bordered" id="products-datatable" style="width: 100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Is translatable</th>
                        <th>Is global</th>
                        <th>Type</th>
                        <th>Categories</th>
                        <th>Actions</th>
                    </tr>
                </thead>
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
					'fields.index': 'Product fields'
				};
			}
		},

		data () {
			return {
				isLoading: true,
				datatable: null
			};
		},

		mounted () {
			this.initDatatable();
		},

		methods: {
			initDatatable () {
				this.datatable = $('#products-datatable').dataTable({
					processing: true,
					serverSide: true,
					ajax: '/admin/products/api/fields',
					responsive: true,
					columns: [
						{
							data: 'name',
							name: 'translations.name',
							orderable: true,
							searchable: true
						},
						{
							data: 'is_translatable',
							name: 'is_translatable',
							orderable: true,
							searchable: true,
							className: 'text-center',
							width: 150
						},
						{
							data: 'is_global',
							name: 'is_global',
							orderable: true,
							searchable: true,
							className: 'text-center',
							width: 130
						},
						{
							data: 'type',
							name: 'type',
							orderable: true,
							searchable: true,
							className: 'text-center'
						},
						{
							data: 'categories',
							name: 'categories',
							orderable: false,
							searchable: false
						},
						{
							data: 'actions',
							orderable: false,
							searchable: false,
							className: 'text-right',
							width: 250
						}
					]
				});

				// Some proxy, in future refactor with vue-datatables..
				this.datatable.on('click', '.vue-proxy', e => {
					let action = $(e.target).data('method');
					let params = $(e.target).data('params');

					this[action](...params);
				});
			},

			deleteField (id) {
				let route = `/admin/products/api/fields/${id}`;

				swal({
					title: 'Are you sure?',
					text: 'Product field will be deleted!',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No'
				}).then(() => {
					this.isLoading = true;

					return this.$http.delete(route).then(({data}) => {
						this.datatable.fnDraw();
						$.growl.success({message: data.message});
					});
				}).catch(this.$helpers.showServerError).then(() => {
					this.isLoading = false;
				});
			}
		}
	};
</script>