<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
class SearchController extends Controller
{
    public function search($name){
        $data = Book::where('name', 'LIKE', '%' . $name . '%')
        ->get();
        return response()->json($data);
    }
}
