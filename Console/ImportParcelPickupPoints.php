<?php

namespace Modules\Product\Console;

use stdClass;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Netcore\Translator\Models\Language;
use Modules\Product\Models\ShippingOption;

class ImportParcelPickupPoints extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'product:import-parcel-pickup-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import DPD and Omniva pickup points.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $points = [
            'dpd'    => $this->fetchDPDLocations(),
            'omniva' => $this->fetchOmnivaLocations(),
        ];

        foreach ($points as $type => $locations) {
            $shippingOption = ShippingOption::firstOrCreate(['type' => $type], ['price' => 5]);

            if ($shippingOption->wasRecentlyCreated) {
                $shippingOption->syncTranslations(
                    languages()->mapWithKeys(function (Language $language) use ($type) {
                        return [$language->iso_code => ['name' => 'Piegāde ar ' . $type]];
                    })
                );
            }

            $newLocations = 0;

            foreach ($locations as $location) {
                $entry = $shippingOption->locations()->updateOrCreate([
                    'longitude' => data_get($location, 'lng'),
                    'latitude'  => data_get($location, 'lat'),
                ], [
                    'name'          => data_get($location, 'name', 'Unknown'),
                    'address'       => data_get($location, 'address', 'Unknown'),
                    'city'          => data_get($location, 'city', 'Unknown'),
                    'zip'           => data_get($location, 'zip', 'Unknown'),
                    'imported_from' => $type,
                ]);

                if ($entry->wasRecentlyCreated) {
                    $newLocations++;
                }
            }

            $this->info('[' . strtoupper($type) . '] Imported ' . $newLocations . ' new locations.');
        }
    }

    /**
     * Fetch Omniva parcel pickup location points.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fetchOmnivaLocations(): Collection
    {
        try {
            $data = json_decode(
                file_get_contents('https://www.omniva.lv/locations.json')
            );

            return collect($data)->where('A0_NAME', 'LV')->map(function (stdClass $location) {
                return [
                    'name'    => object_get($location, 'NAME'),
                    'address' => object_get($location, 'A2_NAME'),
                    'city'    => object_get($location, 'A1_NAME'),
                    'zip'     => object_get($location, 'ZIP'),
                    'lat'     => object_get($location, 'Y_COORDINATE'),
                    'lng'     => object_get($location, 'X_COORDINATE'),
                ];
            });
        } catch (Exception $e) {
            return collect();
        }
    }

    /**
     * Fetch DPD parcel pickup location points.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fetchDPDLocations(): Collection
    {
        try {
            $data = json_decode(
                file_get_contents('ftp://ftp@ftp.dpdbaltics.com/PickupParcelShopData.json')
            );

            return collect($data)->where('countryCode', 'LV')->map(function (stdClass $location) {
                $address = '';
                $address .= object_get($location, 'street') . ' ';
                $address .= object_get($location, 'houseNo') . ', ';
                $address .= object_get($location, 'city');

                return [
                    'name'    => object_get($location, 'companyShortName'),
                    'address' => $address,
                    'city'    => object_get($location, 'city'),
                    'zip'     => object_get($location, 'zipCode'),
                    'lat'     => object_get($location, 'latitude'),
                    'lng'     => object_get($location, 'longitude'),
                ];
            });
        } catch (Exception $e) {
            return collect();
        }
    }
}
