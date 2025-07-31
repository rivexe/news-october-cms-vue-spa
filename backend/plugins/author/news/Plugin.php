<?php namespace Author\News;

use System\Classes\PluginBase;

/**
 * Плагин для управления новостями
 */
class Plugin extends PluginBase
{
    /**
     * Возвращает информацию о плагине
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'author.news::lang.plugin.name',
            'description' => 'author.news::lang.plugin.description',
            'author'      => 'Author',
            'icon'        => 'icon-newspaper-o'
        ];
    }

    /**
     * Регистрация навигации в административной панели
     */
    public function registerNavigation(): array
    {
        return [
            'news' => [
                'label'       => 'author.news::lang.plugin.name',
                'url'         => \Backend::url('author/news/news'),
                'icon'        => 'icon-newspaper-o',
                'permissions' => ['author.news.*'],
                'order'       => 500,
            ]
        ];
    }

    /**
     * Регистрация разрешений
     */
    public function registerPermissions(): array
    {
        return [
            'author.news.access_news' => [
                'tab' => 'author.news::lang.plugin.name',
                'label' => 'author.news::lang.permissions.access_news'
            ]
        ];
    }
}