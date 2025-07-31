<?php namespace Author\News\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Carbon\Carbon;

/**
 * Модель новости
 */
class News extends Model
{
    use Validation;

    public $table = 'author_news_news';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'published_at',
        'is_published'
    ];

    protected $dates = [
        'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime'
    ];

    public $rules = [
        'title' => 'required|max:255',
        'slug' => 'required|unique:author_news_news|max:255',
        'content' => 'required',
        'published_at' => 'nullable|date',
        'is_published' => 'boolean'
    ];

    /**
     * Получить список опубликованных новостей
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', Carbon::now())
                    ->orderBy('published_at', 'desc');
    }

    /**
     * Автоматическая генерация slug перед сохранением
     */
    public function beforeSave()
    {
        if (empty($this->slug) && !empty($this->title)) {
            $this->slug = \Str::slug($this->title);
        }
    }

    /**
     * Получить новость по slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->published()->first();
    }
}