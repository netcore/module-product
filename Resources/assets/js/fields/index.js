'use strict';

import axios from 'axios';

$(function () {
    let datatable = $('#fields-datatable').dataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/products/fields/paginate',
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

    // Delete button.
    $('#fields-datatable').on('click', '.delete-field', e => {
        let route = $(e.target).data('route');

        if (!confirm('Are you sure?')) {
            return;
        }

        $(e.target).data('loading-text', $('<i>').addClass('fa fa-spin fa-spinner')).button('loading');

        axios.delete(route).then(({data}) => {
            datatable.fnDraw();

            $.growl.success({
                message: data.message
            });
        });
    });
});