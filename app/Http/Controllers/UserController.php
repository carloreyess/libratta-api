<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\User;
class UserController extends Controller
{
    public function index(){
        $data = User::all();
        return response()->json($data);
    }

    public function store(Request $request){
        $data = $request->all();
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        if($data['photo']!="null"){
            if($file = $request->photo){
                $path = Storage::disk('s3')->put('users', request()->file('photo'), 'public');
                $user->image =  env('AWS_URL').'/'.$path;
            }
        }
        $user->save();
    }

    public function update(Request $request){
        $input = $request->all();
        $data = User::find($input['id']);
        $data->name = $input['name'];
        $data->email = $input['email'];
        if($input['password']!="null"){
            $data->password = bcrypt($input['password']);
        }
        if($input['photo']!="null"){
            if($file = $request->photo){
                $path = Storage::disk('s3')->put('users', request()->file('photo'), 'public');
                $data->image =  env('AWS_URL').'/'.$path;
            }
        }
        $data->update();

    }
}
