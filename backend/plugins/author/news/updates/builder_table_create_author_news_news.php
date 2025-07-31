<?php namespace Author\News\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * Миграция для создания таблицы новостей
 */
class BuilderTableCreateAuthorNewsNews extends Migration
{
    public function up()
    {
        Schema::create('author_news_news', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            
            $table->index(['is_published', 'published_at']);
            $table->index('slug');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('author_news_news');
    }
}