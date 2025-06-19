<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'company_id',
        'token',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
