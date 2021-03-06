<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingOptionTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__shipping_option_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locale',
        'name'
    ];
}