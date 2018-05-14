<template>
    <section id="product-index" :class="{'panel-loading': isLoading}">
        <breadcrumb :breadcrumb="breadcrumb"></breadcrumb>

        <div class="page-header">
            <div class="row">
                <div class="col-md-4 text-xs-center text-md-left text-nowrap">
                    <h1>
                        <span class="text-muted font-weight-light">
                            <i class="page-header-icon fa fa-shopping-cart"></i> Products
                        </span>
                    </h1>
                </div>

                <hr class="page-wide-block visible-xs visible-sm">

                <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
                    <router-link :to="{name: 'products.create'}" class="btn btn-primary btn-block">
                        <span class="btn-label-icon left fa fa-plus-circle"></span> Create product
                    </router-link>
                </div>
            </div>
        </div>

        <div class="table-primary">
            <table class="table table-bordered" id="products-datatable" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Is variant</th>
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
					'products.index': 'Products'
				};
			}
		},

		data() {
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
					ajax: '/admin/products/api/products',
					responsive: true,
					columns: [
						{
							data: 'id',
                            name: 'id',
							orderable: true,
							searchable: true
						},
						{
							data: 'image',
							orderable: false,
							searchable: false,
                            width: 150
						},
						{
							data: 'title',
							name: 'translations.title',
							orderable: true,
							searchable: true
						},
						{
							data: 'parent_id',
							name: 'parent_id',
							orderable: true,
							searchable: true
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

			deleteProduct (id) {
				let route = `/admin/products/api/products/${id}`;

				swal({
					title: 'Are you sure?',
					text: 'Product will be deleted!',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No'
				}).then(() => {
					this.isLoading = true;

					this.$http.delete(route).then(({data}) => {
						this.datatable.fnDraw();

						$.growl.success({
							message: data.message
						});
					}).catch(err => {
						console.log(err);
					});
				}).catch(() => {}).then(() => {
					this.isLoading = false;
				});
			}
		}
	};
</script>