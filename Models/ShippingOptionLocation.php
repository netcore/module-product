<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingOptionLocation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__shipping_option_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zip',
        'city',
        'name',
        'address',
        'latitude',
        'longitude',
        'imported_from',
    ];
}