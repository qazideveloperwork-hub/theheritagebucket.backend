<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtisanStoryController extends Controller
{
    public function edit(): View
    {
        return view('slider.artisan_story', [
            'slider' => Slider::ofType(Slider::TYPE_ARTISAN_STORY)->first() ?? new Slider(['type' => Slider::TYPE_ARTISAN_STORY]),
            'mediaLibrary' => Media::with('uploader')->latest()->limit(500)->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'heading_1' => ['nullable', 'string', 'max:255'],
            'heading_2' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quote' => ['nullable', 'string'],
            'image_media_id' => ['nullable', 'exists:media,id'],
        ]);

        $slider = Slider::ofType(Slider::TYPE_ARTISAN_STORY)->first() ?? new Slider(['type' => Slider::TYPE_ARTISAN_STORY]);

        $slider->fill([
            'heading_1' => $data['heading_1'] ?? null,
            'heading_2' => $data['heading_2'] ?? null,
            'description' => $data['description'] ?? null,
            'quote' => $data['quote'] ?? null,
        ]);

        if (! empty($data['image_media_id'])) {
            $slider->image_id = $data['image_media_id'];
        }

        $slider->save();

        return redirect()->route('sliders.artisan-story')->with('success', 'Artisan story saved.');
    }
}
