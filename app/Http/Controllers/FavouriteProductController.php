<?php

namespace App\Http\Controllers;

use App\Models\Slider;

class FavouriteProductController extends ProductSliderController
{
    protected function type(): string
    {
        return Slider::TYPE_FAVOURITE_PRODUCT;
    }

    protected function viewName(): string
    {
        return 'slider.favourite_product';
    }

    protected function indexRoute(): string
    {
        return 'sliders.favourite-product';
    }
}
