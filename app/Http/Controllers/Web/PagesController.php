<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index($link)
    {
        $firstCharacter = substr($link, 0, 1);
        if ($firstCharacter !== '/') {
            $link = '/' . $link;
        }

        $page = Page::where('link', $link)
            ->where('status', 'publish')
            ->first();

        if (!empty($page)) {
            $data = [
                
            ];

            return view('web.default.pages.other_pages');
        }

        abort(404);
    }
}
