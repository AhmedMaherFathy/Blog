<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Comment;
use App\Policies\PostPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 
    }
}
