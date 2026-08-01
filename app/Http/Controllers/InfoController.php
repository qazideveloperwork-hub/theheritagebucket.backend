<?php

namespace App\Http\Controllers;

use App\Models\Info;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InfoController extends Controller
{
    public function index(): View
    {
        $infos = Info::orderBy('title')->paginate(20);

        return view('info.list', compact('infos'));
    }

    public function create(): View
    {
        return view('info.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        Info::create($data);

        return redirect()->route('infos.list')->with('success', "Info \"{$data['title']}\" was created.");
    }

    public function edit(Info $info): View
    {
        return view('info.edit', ['info' => $info]);
    }

    public function update(Request $request, Info $info): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $info->update($data);

        return redirect()->route('infos.list')->with('success', "Info \"{$info->title}\" was updated.");
    }

    public function destroy(Info $info): RedirectResponse
    {
        $title = $info->title;
        $info->delete();

        return redirect()->route('infos.list')->with('success', "Info \"{$title}\" was deleted.");
    }
}
