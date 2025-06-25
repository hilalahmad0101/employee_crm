<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $table='invitations';

    protected $guarded=[];

    public function company(){
        return $this->belongsTo(Company::class,'company_id');
    }
}
