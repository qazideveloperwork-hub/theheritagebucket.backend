<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeSliderController extends Controller
{
    public function edit(): View
    {
        return view('slider.home_product', [
            'slider' => Slider::ofType(Slider::TYPE_HOME_BANNER)->first() ?? new Slider(['type' => Slider::TYPE_HOME_BANNER]),
            'mediaLibrary' => Media::with('uploader')->latest()->limit(500)->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'heading_1' => ['nullable', 'string', 'max:255'],
            'heading_2' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:2048'],
            'image_media_id' => ['nullable', 'exists:media,id'],
        ]);

        $slider = Slider::ofType(Slider::TYPE_HOME_BANNER)->first() ?? new Slider(['type' => Slider::TYPE_HOME_BANNER]);

        $slider->fill([
            'heading_1' => $data['heading_1'] ?? null,
            'heading_2' => $data['heading_2'] ?? null,
            'description' => $data['description'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_url' => $data['button_url'] ?? null,
        ]);

        if (! empty($data['image_media_id'])) {
            $slider->image_id = $data['image_media_id'];
        }

        $slider->save();

        return redirect()->route('sliders.home')->with('success', 'Home banner saved.');
    }
}
