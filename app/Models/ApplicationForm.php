<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationForm extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birthdate',
        'email',
        'phone',
        'status',
        'remarks',
        'income_source',
        'occupation',
        'salary_range_from',
        'salary_range_to',
        'address'
    ];

    public function registrations()
    {
        return $this->hasMany(ApplicationForm::class, 'user_id');
    }
}
