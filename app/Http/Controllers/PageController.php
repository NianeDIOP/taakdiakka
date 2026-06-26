<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    /** Affiche une page de contenu (légal) public. */
    public function show(Page $page)
    {
        return view('pages.show', compact('page'));
    }
}
