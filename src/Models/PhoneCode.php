<?php

namespace aliwebto\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneCode extends Model
{
    protected $fillable = [
        "code",
        "phone",
        "status"
    ];
    protected $hidden = [
        'code'
    ];


}
