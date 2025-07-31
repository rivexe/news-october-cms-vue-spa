<?php

use Author\News\Controllers\News;
use Backend\Facades\BackendAuth;
use Illuminate\Support\Facades\Cache;

Route::group(['prefix' => 'api'], function () {
    // Публичные маршруты (без авторизации)
    Route::get('news', [News::class, 'apiIndex']);
    Route::get('news/{slug}', [News::class, 'apiShow']);
    
    // Авторизация
    Route::post('auth/login', function() {
        $credentials = request()->only(['email', 'password']);
        
        $user = \Backend\Models\User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return response()->json(['message' => 'Пользователь с таким email не найден'], 404);
        }
        
        $loginCredentials = [
            'login' => $user->login,
            'password' => $credentials['password']
        ];
        
        if (\Backend\Facades\BackendAuth::attempt($loginCredentials)) {
            $authenticatedUser = \Backend\Facades\BackendAuth::getUser();
            
            $token = hash('sha256', $authenticatedUser->id . time() . config('app.key'));
            \Cache::put('api_token_' . $token, $authenticatedUser->id, now()->addHours(24));
            
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $authenticatedUser->id,
                    'email' => $authenticatedUser->email,
                    'login' => $authenticatedUser->login,
                    'name' => $authenticatedUser->first_name . ' ' . $authenticatedUser->last_name
                ]
            ]);
        }
        
        return response()->json(['message' => 'Неправильный пароль'], 401);
    });
    
    // Защищенные маршруты (БЕЗ middleware auth)
    Route::get('admin/news', [News::class, 'apiIndexAdmin']);
    Route::post('news', [News::class, 'apiStore']);
    Route::put('news/{id}', [News::class, 'apiUpdate']);
    Route::delete('news/{id}', [News::class, 'apiDestroy']);
});