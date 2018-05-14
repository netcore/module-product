<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Admin\Models\Menu;
use Modules\Admin\Models\MenuItem;
use Netcore\Translator\Helpers\TransHelper;

class MenuSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run(): void
    {
        if (!method_exists(menu(), 'seedItems')) {
            $this->command->error('Menu seeding skipped - old admin module used in this project!');
            return;
        }

        menu()->seedItems([
            'leftAdminMenu' => [
                [
                    'name'            => 'Products',
                    'icon'            => 'fa fa-shopping-cart',
                    'type'            => 'url',
                    'value'           => '#',
                    'module'          => 'Product',
                    'is_active'       => 1,
                    'active_resolver' => null,
                    'parameters'      => json_encode([]),
                    'children'        => [
                        [
                            'name'            => 'Product fields',
                            'type'            => 'url',
                            'value'           => '/admin/products/#/fields',
                            'module'          => 'Product',
                            'is_active'       => 1,
                            'active_resolver' => '',
                            'parameters'      => json_encode([]),
                        ],
                        [
                            'name'            => 'Product parameters',
                            'type'            => 'url',
                            'value'           => '/admin/products/#/parameters',
                            'module'          => 'Product',
                            'is_active'       => 1,
                            'active_resolver' => '',
                            'parameters'      => json_encode([]),
                        ],
                        [
                            'name'            => 'Products',
                            'type'            => 'url',
                            'value'           => '/admin/products/#/products',
                            'module'          => 'Product',
                            'is_active'       => 1,
                            'active_resolver' => '',
                            'parameters'      => json_encode([]),
                        ],
                        [
                            'name'            => 'Shipping options',
                            'type'            => 'url',
                            'value'           => '/admin/products/#/shipping-options',
                            'module'          => 'Product',
                            'is_active'       => 1,
                            'active_resolver' => '',
                            'parameters'      => json_encode([]),
                        ],
                    ],
                ],
            ],
        ]);
    }
}