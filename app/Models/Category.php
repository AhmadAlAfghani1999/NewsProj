<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table ='categories';

//    public function news_categories(){
//        return $this->hasMany(News::class,'category_id');
//    }
}
