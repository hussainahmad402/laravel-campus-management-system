<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GPA extends Model
{
    protected $fillable = ['student_id', 'semester_id', 'gpa_value'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}

