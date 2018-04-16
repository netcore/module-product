<?php

return [
    /**
     * Category group config used by forum module.
     *
     * @see https://github.com/netcore/module-category#category-groups
     */
    'used_category_group' => [
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
    'default_currency'    => 'EUR',
];
