<?php

namespace App\Http\Controllers;
use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class BookController extends Controller
{

    public function index(){
        $data = Book::all();
        $books = [];
        foreach($data as $row){
            $books[] = array(
                'serial'=>$row['serial'],
                'name'=>$row['name'],
                'author'=>$row['author'],
                'publication'=>$row['publication'],
                'date_published'=>$row['date_published'],
                'tags'=>$row['tags'],
                'created_at'=>$row['created_at'],
            );
        }
        return response()->json($data);
    }

    public function store(Request $request){
        $data = $request->all();
        $tags = $data['tags'];
        $book = new Book();
        $book->serial = $data['serial'];
        $book->name = $data['name'];
        $book->category = $data['category'];
        $book->author = $data['author'];
        $book->publication = $data['publication'];
        $book->published_date = $data['published_date'];
        $book->tags = $data['tags'];

        if($data['photo']!="null"){
            if($file = $request->photo){
                $path = Storage::disk('s3')->put('books', request()->file('photo'), 'public');
                $book->image =  env('AWS_URL').'/'.$path;
            }
        }
        $book->save();
        return response()->json($data);
    }

    public function update(Request $request){
        $input = $request->all();
        $data = Book::find($input['id']);
        $data->name = $input['name'];
        $data->serial = $input['serial'];
        $data->author = $input['author'];

        $data->published_date = $input['published_date'];
        $data->publication = $input['publication'];
        $data->tags = $input['tags'];

        if($input['category']!="null"){
            $data->category = $input['category'];
        }

        if($input['photo']!="null"){
            if($file = $request->photo){
                $path = Storage::disk('s3')->put('books', request()->file('photo'), 'public');
                $data->image =  env('AWS_URL').'/'.$path;
            }
        }
        $data->update();
    }
}
