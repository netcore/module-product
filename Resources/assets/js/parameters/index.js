'use strict';

import axios from 'axios';

$(function () {
    let datatable = $('#parameters-datatable').dataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/products/parameters/paginate',
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

    // Delete button.
    $('#parameters-datatable').on('click', '.delete-parameter', e => {
        e.preventDefault();
        let route = $(e.target).attr('href');

        swal({
            title: 'Are you sure?',
            text: 'You will not be able to recover this parameter with it\'s attributes!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it'
        })
            .then(() => {
                $(e.target).data('loading-text', $('<i>').addClass('fa fa-spin fa-spinner')).button('loading');

                axios.delete(route).then(({data}) => {
                    datatable.fnDraw();

                    $.growl.success({
                        message: data.message
                    });
                });
            })
            .catch(() => {
            })
            .then(() => {
                $(e.target).data('loading-text', $('<i>').addClass('fa fa-spin fa-spinner')).button('loading');
            });
    });
});