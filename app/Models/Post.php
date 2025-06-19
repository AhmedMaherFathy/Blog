<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    public $translatable = ['title', 'content'];

    protected $fillable = [
        'title',
        'content',
        'slug',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function addMultipleMediaFromRequest(array $images)
    {
        foreach ($images as $image) {
            $this->addMedia($image)
                ->sanitizingFileName(fn($name) => strtolower(preg_replace('/[^a-z0-9.-]+/i', '', $name)))
                ->toMediaCollection('post_images');
        }
    }

    public function updatePostMedia(array $images): void
    {
        $this->clearMediaCollection('post_images');

        foreach ($images as $image) {
            $this->addMedia($image)
                ->sanitizingFileName(fn($name) => strtolower(preg_replace('/[^a-z0-9.-]+/i', '', $name)))
                ->toMediaCollection('post_images');
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $englishTitle = $post->getTranslation('title', 'en');
            $post->slug = $post->generateUniqueSlug($englishTitle);
        });
    }

    public function generateUniqueSlug($title)
    {
        $baseTitle = is_array($title) ? ($title['en'] ?? (string)$title) : (string)$title;

        $slug = Str::slug($baseTitle);
        $count = Post::where('slug', 'LIKE', "{$slug}%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
