<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Media;
use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategorySliderController extends Controller
{
    public function index(): View
    {
        $slides = Slider::ofType(Slider::TYPE_HOME_CATEGORY)->with(['category', 'image'])->orderBy('sort_order')->get();

        $availableCategories = Category::where('status', '!=', 'deleted')
            ->whereNotIn('id', $slides->pluck('category_id'))
            ->orderBy('name')
            ->get();

        return view('slider.category', [
            'slides' => $slides,
            'availableCategories' => $availableCategories,
            'mediaLibrary' => Media::with('uploader')->latest()->limit(500)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'image_media_id' => ['required', 'exists:media,id'],
        ]);

        $nextOrder = Slider::ofType(Slider::TYPE_HOME_CATEGORY)->max('sort_order') + 1;

        Slider::create([
            'type' => Slider::TYPE_HOME_CATEGORY,
            'category_id' => $data['category_id'],
            'image_id' => $data['image_media_id'],
            'sort_order' => $nextOrder,
        ]);

        return redirect()->route('sliders.category')->with('success', 'Category added.');
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        abort_unless($slider->type === Slider::TYPE_HOME_CATEGORY, 404);

        $slider->delete();

        return redirect()->route('sliders.category')->with('success', 'Category removed.');
    }
}
