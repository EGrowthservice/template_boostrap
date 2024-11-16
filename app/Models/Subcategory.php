<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'sub_categories';
    protected $primaryKey = 'sub_category_id';

    protected $fillable = ['name_sub_category', 'category_id', 'created_at'];

    // Mối quan hệ với Category
    public function category()
    {
        return $this->hasOne(Category::class, 'category_id');
    }

    // Mối quan hệ với Product
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
