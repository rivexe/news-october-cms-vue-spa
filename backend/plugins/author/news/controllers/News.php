<?php namespace Author\News\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Author\News\Models\News as NewsModel;
use Cache;

/**
 * Контроллер управления новостями в административной панели
 */
class News extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'author.news.access_news'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Author.News', 'main-menu-item');
    }

    /**
     * API метод для получения списка опубликованных новостей
     */
    public function apiIndex()
    {
        $news = NewsModel::published()->get();
        
        return response()->json([
            'data' => $news->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'content' => \Str::limit($item->content, 200),
                    'published_at' => $item->published_at->format('Y-m-d H:i:s'),
                    'is_published' => $item->is_published
                ];
            })
        ]);
    }

    /**
     * API метод для получения детальной информации о новости
     */
    public function apiShow($slug)
    {
        $news = NewsModel::findBySlug($slug);
        
        if (!$news) {
            return response()->json(['error' => 'Новость не найдена'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $news->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'content' => $news->content,
                'published_at' => $news->published_at->format('Y-m-d H:i:s')
            ]
        ]);
    }

/**
 * API метод для создания новости
 */
public function apiStore()
{
    $this->checkApiPermissions();
    
    $data = request()->validate([
        'title' => 'required|max:255',
        'slug' => 'nullable|unique:author_news_news|max:255',
        'content' => 'required',
        'published_at' => 'nullable|date',
        'is_published' => 'boolean'
    ]);

    if (empty($data['published_at']) || $data['published_at'] === '') {
        $data['published_at'] = null;
    }

    $news = NewsModel::create($data);

    return response()->json([
        'data' => $news,
        'message' => 'Новость успешно создана'
    ], 201);
}

/**
 * API метод для обновления новости
 */
public function apiUpdate($id)
{
    $this->checkApiPermissions();
    
    $news = NewsModel::findOrFail($id);
    
    $data = request()->validate([
        'title' => 'required|max:255',
        'slug' => 'nullable|unique:author_news_news,slug,' . $id . '|max:255',
        'content' => 'required',
        'published_at' => 'nullable|date',
        'is_published' => 'boolean'
    ]);

    if (empty($data['published_at']) || $data['published_at'] === '') {
        $data['published_at'] = null;
    }

    $news->update($data);

    return response()->json([
        'data' => $news,
        'message' => 'Новость успешно обновлена'
    ]);
}

    /**
     * API метод для удаления новости
     */
    public function apiDestroy($id)
    {
        $this->checkApiPermissions();
        
        $news = NewsModel::findOrFail($id);
        $news->delete();

        return response()->json([
            'message' => 'Новость успешно удалена'
        ]);
    }

    /**
     * Проверка разрешений для API методов
     */
    private function checkApiPermissions(): void
    {
        $token = request()->bearerToken();
        
        if (!$token) {
            abort(401, 'Токен не предоставлен');
        }
        
        $userId = \Illuminate\Support\Facades\Cache::get('api_token_' . $token);
        
        if (!$userId) {
            abort(401, 'Недействительный токен');
        }
        
        $user = \Backend\Models\User::find($userId);
        
        if (!$user) {
            abort(401, 'Пользователь не найден');
        }
        
        if (!$user->isSuperUser() && !$user->hasPermission('author.news.access_news')) {
            abort(403, 'Недостаточно прав доступа');
        }
        
    }

    /**
     * API метод для получения ВСЕХ новостей (для админки)
     */
    public function apiIndexAdmin()
    {
        $this->checkApiPermissions();
        
        $news = NewsModel::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'data' => $news->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'content' => $item->content,
                    'published_at' => $item->published_at ? $item->published_at->format('Y-m-d H:i:s') : null,
                    'is_published' => $item->is_published
                ];
            })
        ]);
    }
}