<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
class CategoryController extends Controller
{
    public function index(){
        $data = Category::all();
        return response()->json($data);
    }

    public function store(Request $request){
        $input = $request->all();
        $data =  new Category();
        $data->name = $input['name'];
        $data->color = $input['color'];
        $data->save();
    }

    public function update(Request $request){
        $input = $request->all();
        $category = Category::find($input['id']);
        $category->name = $input['name'];
        $category->color = $input['color'];
        $category->update();
    }

    public function select_categories(){
        $data = Category::all();
        $categories = [];
        foreach($data as $row){
            $categories[] = array(
                'label'=>$row['name'],
                'value'=>$row['id'],
            );
        }
        return response()->json($categories);
    }
}
