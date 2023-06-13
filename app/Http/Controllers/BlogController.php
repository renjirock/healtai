<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\ingredient;
use App\Models\Meta;

class BlogController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $url){
        $blog = Blog::where('url', $url)->first();
        $ingredients = ingredient::where('id', $blog->ingredients_id)->first();
        $ingredientsList = isset($ingredients->ingredients) ? json_decode($ingredients->ingredients) : null;
        $meta = Meta::where('id', $blog->meta_id)->first();
        return view('blog', compact('blog', 'ingredientsList', 'meta'));
    }
}
