<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSort extends Model
{
    use HasFactory;

    protected $appends = ['groupImage'];

    // This is so you don't end up also showing the relationship
    protected $hidden = ['groupImageRelationship'];

    public function products()
    {
        return $this->hasMany(Product::class, 'group', 'group');
    }

    public function groupImageRelationship()
    {
        return $this->hasOne(ProductSortImage::class, 'product_sort_id', 'id');
    }

    public function getgroupImageAttribute()
    {
        return url('/images/' . $this->groupImageRelationship->name . '.' . $this->groupImageRelationship->mime_type);
    }

    protected static function booted()
    {
        static::saving(function ($myModel) {
            $myModel->groupImageRelationship->save();
        });
    }
}
