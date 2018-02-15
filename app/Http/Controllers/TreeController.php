<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Tree;
use App\Enums\TreeStatus;
use App\Enums\TreeType;
use File;

class TreeController extends Controller
{

    public function index(Request $request)
    {
        return redirect()->route('home');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email'  => 'required|email|max:191',
            'file'   => 'required|file|mimes:html, htm',
        ]);
        $tree = new Tree();
        $tree->status = TreeStatus::START;
        $tree->email = $request->email;
        $tree->name = "Yeni Soyağacı";
        $tree->type = TreeType::HTML;
        $tree->slug = str_random(5);
        $tree->parseHtml($request->file);
        cache(["tree-{$tree->slug}" => $tree->json()], 60);
        return redirect()->route('tree.show', $tree->slug);
    }

    public function show(Request $request, $slug)
    {
        $key = "tree-{$slug}";
        if (cache()->has($key)) {
            $tree = cache()->get($key);
            return view('front.tree')->with([
                'tree' => $tree
            ]);
        } else {
            return redirect()->route('home');
        }
    }

    public function pdfShow(Request $request)
    {
        return view('front.pdf');
    }

    public function pdfStore(Request $request)
    {
        $this->validate($request, [
            'email'  => 'required|email|max:191',
            'file'   => 'required|file|mimes:pdf',
        ]);
        $tree = new Tree();
        $tree->status = TreeStatus::START;
        $tree->email = $request->email;
        $tree->name = "Yeni Soyağacı";
        $tree->type = TreeType::PDF;
        $tree->slug = str_random(5);
        $tree->parsePdf($request->file);
        cache(["tree-{$tree->slug}" => $tree->json()], 60);
        return redirect()->route('tree.show', $tree->slug);
    }

    public function delete(Request $request, $slug)
    {
        cache()->forget("tree-{$slug}");
        $pdf_file = storage_path('app/inputs') . "/{$slug}.pdf";
        $html_file = storage_path('app/inputs') . "/{$slug}.html";
        $output_file = storage_path('app/outputs') . "/{$slug}.html";
        File::delete([$pdf_file, $html_file, $output_file]);
        return ['message' => 'Soyağacınız başarıyla silindi'];
    }
}
