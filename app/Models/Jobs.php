<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Jobs extends Model
{
    use HasFactory;

    protected $table = 'jobs';
    protected $fillable = ['title', 'description', 'location', 'type', 'salary', 'company'];
}
