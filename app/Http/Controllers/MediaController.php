<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\ImageManager;

class MediaController extends Controller
{
    // Every upload is resized down into these named variants (width in pixels,
    // aspect ratio preserved, never upscaled) so the storefront can request an
    // appropriately sized image instead of always shipping the full original.
    private const VARIANT_WIDTHS = [
        'thumbnail' => 400,
        'medium' => 800,
        'large' => 1600,
    ];

    public function index(Request $request): View
    {
        $media = Media::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', "%{$term}%")
                        ->orWhere('alt_text', 'like', "%{$term}%")
                        ->orWhere('original_name', 'like', "%{$term}%");
                });
            })
            ->with('uploader')
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('media.index', compact('media'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'],
        ]);

        $created = collect($request->file('files'))->map(fn (UploadedFile $file) => $this->storeUploadedFile($file));

        if ($request->expectsJson()) {
            return response()->json([
                'media' => $created->map(fn (Media $media) => [
                    'id' => $media->id,
                    'url' => $media->url(),
                    'thumb' => $media->thumbnailUrl(),
                    'title' => $media->title,
                    'alt_text' => $media->alt_text,
                    'file_name' => $media->file_name,
                    'dimensions' => $media->dimensions,
                    'size' => $media->formatted_size,
                    'uploaded_by' => Auth::user()?->name,
                    'destroy_url' => route('media.destroy', $media),
                ])->values(),
            ]);
        }

        return redirect()->route('media.index')->with('success', 'Media uploaded.');
    }

    private function storeUploadedFile(UploadedFile $file): Media
    {
        $manager = ImageManager::gd();
        $directory = 'media/'.now()->format('Y/m');

        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileName = $baseName.'-'.Str::random(8).'.'.$extension;
        $path = "{$directory}/{$fileName}";

        $originalImage = $manager->read($file->getRealPath());
        [$width, $height] = [$originalImage->width(), $originalImage->height()];

        Storage::disk('public')->put($path, (string) $originalImage->encode());

        $variants = [];
        foreach (self::VARIANT_WIDTHS as $label => $variantWidth) {
            $variantPath = "{$directory}/{$baseName}-".Str::random(8)."-{$label}.{$extension}";
            $variantImage = $manager->read($file->getRealPath())->scaleDown(width: $variantWidth);
            Storage::disk('public')->put($variantPath, (string) $variantImage->encode());
            $variants[$label] = $variantPath;
        }

        return Media::create([
            'disk' => 'public',
            'path' => $path,
            'variants' => $variants,
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'alt_text' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'uploaded_by' => Auth::id(),
        ]);
    }

    public function update(Request $request, Media $media): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $media->update($data);

        return redirect()->route('media.index')->with('success', 'Media details saved.');
    }

    public function destroy(Media $media): RedirectResponse
    {
        Storage::disk($media->disk)->delete($media->allPaths());
        $media->delete();

        return redirect()->route('media.index')->with('success', 'Media deleted.');
    }
}
