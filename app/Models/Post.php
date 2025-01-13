<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $fillable = [
        'title',
        'slug',
        'color',
        'category_id',
        'content',
        'thumbnail',
        'tags',
        'published',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function authors(){
        return $this->belongsToMany(User::class,'post_user')->withPivot(['order'])->withTimestamps();
    }

    public function comments() {
        return $this->morphMany(Comment::class,'commentable');
    }
}
