<?php

namespace App\Http\Controllers;
use App\BarrowedBook;
use App\Book;
use App\Student;
use Illuminate\Http\Request;

class BarrowedBookController extends Controller
{
    public function history($id){
        $data = BarrowedBook::with('book')
            ->with('student')
            ->where('barrowed_books.book_id', $id)
            ->get();
        $barrowed = [];
        foreach($data as $row){
            $barrowed[] = array(
                'id'=>$row['id'],
                'book'=>$row->book->name,
                'image'=>$row->book->image,
                'serial'=>$row->book->serial,
                'student'=>$row->student->name,
                'date_barrowed'=>$row['date_barrowed'],
                'expected_date_return'=>$row['expected_date_return'],
                'date_returned'=>$row['date_returned'],
                'left'=>$this->getDaysLeft($row['expected_date_return'], $row['date_returned']),
                'created_at'=>$row['created_at'],
                'status'=>$row['status'],
            );
        }
        return response()->json($barrowed);
    }

    public function index(){
        $data = Book::all();
        $books = [];
        foreach($data as $row){
            $books[] = array(
                'id'=>$row['id'],
                'serial'=>$row['serial'],
                'name'=>$row['name'],
                'author'=>$row['author'],
                'publication'=>$row['publication'],
                'date_published'=>$row['date_published'],
                'tags'=>$row['tags'],
                'created_at'=>$row['created_at'],
                'image'=>$row['image'],
                'status'=>$row['status'],
                'overdue'=>$this->getOverdue($row['id'], $row['status'])
            );
        }
        return response()->json($books);
    }

    private function getOverdue($book_id, $status){
        $data = BarrowedBook::where('book_id', $book_id)
        ->get()
        ->last();
        $today = date("Y-m-d");
        $date_returned = $data['date_returned'];
        $expected_date_return = $data['expected_date_return'];
        $start_date = strtotime($today);
        $end_date = strtotime($expected_date_return);
        $diff = ($end_date - $start_date)/60/60/24;
        if($status!='1'){
            return 0;
        }else{
            if($diff<0){
                return 1;
            }
        }
    }

    private function getDaysLeft($expected_return, $date_returned){
        $today = date("Y-m-d");
        $start_date = strtotime($today);
        $end_date = strtotime($expected_return);
        $diff = ($end_date - $start_date)/60/60/24;
        if($date_returned){
            return 'Returned';
        }else{
            if($diff>0){
                return $diff." day(s) left";
            }elseif($diff===0){
                return "Due date today";
            }else{
                return abs($diff)." day(s) exceed";
            }
        }
    }

    public function store(Request $request){
        $input = $request->all();
        $books = $input['books'];
        foreach($books as $row){
            $barrow = new BarrowedBook();
            $barrow->book_id = $row['value'];
            $barrow->student_id = $input['student_id']['value'];
            $barrow->date_barrowed = $input['date_barrowed'];
            $barrow->expected_date_return = $input['expected_date_return'];
            $barrow->save();

            $book = Book::find($row['value']);
            $book->status = 1;
            $book->save();
        }
    }

    public function return_books(Request $request){
        $input = $request->all();
        $books = $input['books'];

        foreach($books as $row){
          $data = BarrowedBook::where('book_id', $row['value'])
          ->where('date_returned', NULL)
          ->where('student_id', $input['student_id'])
          ->first();
          $data->date_returned = $input['date_returned'];
          $data->update();

          $bookss = Book::find($row['value']);
          $bookss->status = 0;
          $bookss->update();
        }
    }

    public function barrowed($id){
        $data = BarrowedBook::with('book')
            ->with('student')
            ->where('student_id', $id)
            ->where('date_returned', NULL)
            ->get();
            $barrowed = [];
        foreach($data as $row){
            $barrowed[] = array(
                'label'=>$row->book->name,
                'value'=>$row->book->id,
                'book'=>$row->book->name,
                'student'=>$row->student->name,
                'date_barrowed'=>$row['date_barrowed'],
                'expected_date_return'=>$row['expected_date_return'],
                'date_returned'=>$row['date_returned'],
                'created_at'=>$row['created_at'],
            );
        }
        return response()->json($barrowed);
    }

    public function select_students(){
        $data = Student::all();
        $students = [];
        foreach($data as $row){
            $students[] = array(
                'value'=>$row['id'],
                'label'=>$row['name'],
            );
        }
        return response()->json($students);
    }

    public function select_books(){
        $data = Book::select('books.id','books.name')
            ->where('books.status', 0)
            ->get();
        $books = [];
        foreach($data as $row){
            $books[] = array(
                'value'=>$row['id'],
                'label'=>$row['name'],
            );
        }
        return response()->json($books);
    }

    public function extend_barrow(Request $request){
        $input = $request->all();
        $data = BarrowedBook::find($input['id']);
        $data->expected_date_return  = $input['extend_date'];
        $data->update();
    }
}
