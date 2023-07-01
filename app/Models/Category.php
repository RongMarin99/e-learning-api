<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    const TABLE_NAME = 'category';
    const NAME = 'name';
    const CREATED_BY = 'created_by';
    const PARENT_ID = 'parent_id';

    protected $table = SELF::TABLE_NAME;
    protected $fillable = [
        self::NAME,
        self::CREATED_BY,
        self::PARENT_ID
    ];

    public function setData($request)
    {
        $this->{self::NAME} = $request[self::NAME];
        $this->{self::CREATED_BY} = auth()->user()->id;
        $this->{self::PARENT_ID} = $request[self::PARENT_ID];
    }

    public static function lists($filter)
    {
        return self::whereNull('parent_id')
            ->with('sub_category');
    }

    public function sub_category()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
