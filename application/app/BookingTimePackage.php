<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingTimesPackage extends Model
{
    protected $fillable = [
        'id' ,
        'day', 
        'opening_time', 
        'closing_time', 
        'is_off_day',
        'package_id',
    ];

    public function package()
    {
        return $this->hasOne('App\Package', 'id', 'package_id');
    }
}
