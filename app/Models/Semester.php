<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function gpas()
    {
        return $this->hasMany(GPA::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
}
