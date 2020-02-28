<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Category;
class DashboardController extends Controller
{
    public function count_books(){
        $data['total'] = Book::count();
        return response()->json($data);
    }

    public function count_barrowed(){
        $data['total'] = Book::where('status',1)->count();
        return response()->json($data);
    }

    public function count_available(){
        $data['total'] = Book::where('status',0)->count();
        return response()->json($data);
    }

    public function count_overdue(){
        $today = date("Y/m/d");
        $data['total'] = Book::select('books.id')
        ->join('barrowed_books', 'barrowed_books.book_id', '=', 'books.id')
        ->where('barrowed_books.date_returned', NULL)
        ->where('barrowed_books.expected_date_return','<', $today)

        ->count();
        return response()->json($data);
    }

    public function category_chart(){
        $data = Category::select('categories.id', 'categories.name', 'categories.color')
        ->get();
        $results = [];
        foreach($data as $row){
            $results[] = array(
                'label'=>$row['name'],
                'color'=>$row['color'],
                'count'=>$this->getCount($row['id']),
            );
        }
        return response()->json($results);
    }

    private function getCount($id){
        $data['total'] = Book::where('category',$id)->count();
        return $data['total'];
    }
}
