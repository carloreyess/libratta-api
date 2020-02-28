<?php

namespace App\Http\Controllers;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(){
        $data = Student::all();
        return response()->json($data);
    }

    public function store(Request $request){
        $data = $request->all();
        $student = new Student();
        $student->student_id = $data['student_id'];
        $student->name = $data['name'];
        $student->address = $data['address'];
        $student->grade_level = $data['grade_level'];

        if($data['photo']!="null"){
            if($file = $request->photo){
                $path = Storage::disk('s3')->put('students', request()->file('photo'), 'public');
                $student->image =  env('AWS_URL').'/'.$path;
            }
        }

        $student->save();
        return response()->json($data);
    }

    public function update(Request $request){
        $input = $request->all();
        $data = Student::find($input['id']);
        $data->student_id = $input['student_id'];
        $data->name = $input['name'];
        $data->grade_level = $input['grade_level'];
        $data->address = $input['address'];
        if($input['photo']!="null"){
            if($file = $request->photo){
                $path = Storage::disk('s3')->put('students', request()->file('photo'), 'public');
                $data->image =  env('AWS_URL').'/'.$path;
            }
        }
        $data->update();

    }
}
