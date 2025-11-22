<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = ['user_id', 'assignment_alert', 'fee_alert', 'attendance_alert'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

