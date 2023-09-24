<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSort extends Model
{
    use HasFactory;

    protected $fillable = [
        'layer',
        'group',
        'serial_number',
    ];

    protected $appends = ['groupImage'];

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
        if (!$this->groupImageRelationship) {
            return '';
        }

        return url('/images/' . $this->groupImageRelationship->name . '.' . $this->groupImageRelationship->mime_type);
    }

    protected static function booted()
    {
        static::saving(function ($productSort) {
            $image = $productSort->groupImageRelationship?->name ?? null;

            if ($image !== null) {
                $productSort->groupImageRelationship?->save();
            }
        });
    }
}
