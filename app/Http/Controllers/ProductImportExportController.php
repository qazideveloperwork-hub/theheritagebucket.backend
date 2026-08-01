<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Services\SkuGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductImportExportController extends Controller
{
    public function __construct(private readonly SkuGenerator $skuGenerator)
    {
    }

    public function importForm(): View
    {
        return view('product.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $import = new ProductsImport($this->skuGenerator);
        Excel::import($import, $request->file('file'));

        $failures = $import->failures();

        if ($failures->isEmpty()) {
            return redirect()->route('products.list')
                ->with('success', "Imported {$import->imported} product(s) successfully.");
        }

        $errorLines = $failures->map(fn ($failure) => "Row {$failure->row()}: ".implode(' ', $failure->errors()))->all();

        return redirect()->route('products.import')
            ->with('import_errors', $errorLines)
            ->with('import_success_count', $import->imported);
    }

    public function template(): BinaryFileResponse
    {
        return Excel::download(new ProductsExport(templateOnly: true), 'product-import-template.xlsx');
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new ProductsExport(), 'products-'.now()->format('Y-m-d').'.xlsx');
    }
}
