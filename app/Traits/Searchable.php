<?php

namespace App\Traits;

use Illuminate\Database\Query\Builder;

trait Searchable
{
    public function scopeSearchable($query , $value ,array $keys = ['name'] )
    {
        return $query->whereAny($keys, "like" , "%$value%");
    }    
}
