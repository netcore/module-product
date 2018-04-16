<template>
    <section id="parameters-index" :class="{'panel-loading': isLoading}">
        <breadcrumb :breadcrumb="breadcrumb"></breadcrumb>

        <div class="page-header">
            <div class="row">
                <div class="col-md-4 text-xs-center text-md-left text-nowrap">
                    <h1>
                        <span class="text-muted font-weight-light">
                            <i class="page-header-icon fa fa-check"></i> Product parameters
                        </span>
                    </h1>
                </div>

                <hr class="page-wide-block visible-xs visible-sm">

                <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
                    <router-link :to="{name: 'parameters.create'}" class="btn btn-primary btn-block">
                        <span class="btn-label-icon left fa fa-plus-circle"></span> Create parameter
                    </router-link>
                </div>
            </div>
        </div>

        <div class="table-primary">
            <table class="table table-bordered" id="parameters-datatable" style="width: 100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
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
					'parameters.index': 'Parameters'
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
				this.datatable = $('#parameters-datatable').dataTable({
					processing: true,
					serverSide: true,
					ajax: '/admin/products/api/parameters',
					responsive: true,
					columns: [
						{
							data: 'name',
							name: 'translations.name',
							orderable: true,
							searchable: true
						},
						{
							data: 'type',
							name: 'type',
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

			deleteParameter (id) {
				let route = `/admin/products/api/parameters/${id}`;

				swal({
					title: 'Are you sure?',
					text: 'Parameter with it\'s attributes will be deleted!',
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