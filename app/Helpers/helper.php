<?php

use App\Models\Category;

function getCategories(){
   return  $category = Category::orderBy('name','ASC')
                    ->with('sub_category')
                    ->orderBy('id','DESC')
                    ->where('status',1)
                    ->where('show_home','Yes')
                    ->take(4)
                    ->get();
}
