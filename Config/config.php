<?php

return [
    /**
     * Category group config used by forum module.
     *
     * @see https://github.com/netcore/module-category#category-groups
     */
    'used_category_group'  => [
        'key'                  => 'product',
        'title'                => 'Product categories',
        'has_icons'            => true,
        'icons_for_only_roots' => false,
        'icons_type'           => 'file',
        'levels'               => null,
    ],

    /**
     * Default currency code.
     */
    'default_currency'     => 'EUR',

    /**
     * Image size settings (for Laravel Stapler).
     */
    'product_image_config' => [
        'url'           => '/uploads/products/product_images/:attachment/:id_partition/:style/:filename',
        'default_url'   => '//placehold.it/400',
        'default_style' => 'thumb',
        'styles'        => [
            'thumb'  => '150x150',
            'medium' => '300x300',
            'large'  => '500x500',
            'full'   => '1000x1000',
        ],
    ],
];
