<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;

class BaseController extends Controller
{
    /**
     * Get available languages.
     *
     * @return \Illuminate\Support\Collection
     */
    public function languages(): Collection
    {
        return languages();
    }
}