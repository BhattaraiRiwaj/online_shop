<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
        'show_home',
    ];

    protected $hidden = [

    ];
    protected $cast =[

    ];

    public function sub_category() {
        return $this->hasMany(SubCategory::class);
    }
}
