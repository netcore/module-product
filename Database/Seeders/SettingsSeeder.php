<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $settings = [
            // VAT.
            [
                'group'           => 'products',
                'name'            => 'VAT percent',
                'key'             => 'vat',
                'value'           => '21',
                'type'            => 'text',
                'meta'            => [
                    'attributes' => [
                        'required' => 'required',
                        'min'      => '0',
                        'max'      => '100',
                    ],
                ],
                'is_translatable' => 0,
                'has_manager'     => 0,
            ],

            // Enabled currencies.
            [
                'group'           => 'products',
                'name'            => 'Enabled currencies',
                'key'             => 'currencies',
                'value'           => '',
                'type'            => 'select',
                'meta'            => [
                    'attributes' => [
                        'required' => 'required',
                        'multiple' => true,
                    ],
                    'options'    => 'getSelectListOfCurrencies',
                ],
                'is_translatable' => 0,
                'has_manager'     => 0,
            ],
        ];

        setting()->seed($settings);
    }
}