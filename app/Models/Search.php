<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    protected $fillable = ['word', 'user_id'];
    protected $table = "searches";

    public function user()
    {
      return $this->belongsTo("User");
    }
}
