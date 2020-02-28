<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BarrowedBook extends Model
{

    public function book(){
        return $this->belongsTo(Book::class,'book_id');
    }
    public function student(){
        return $this->belongsTo(Student::class,'student_id');
    }
}
