<?php

namespace App\Http\Controllers;

use App\Models\Slider;

class NewArrivalController extends ProductSliderController
{
    protected function type(): string
    {
        return Slider::TYPE_HOME_NEW_ARRIVAL;
    }

    protected function viewName(): string
    {
        return 'slider.new_arrival';
    }

    protected function indexRoute(): string
    {
        return 'sliders.new-arrival';
    }
}
