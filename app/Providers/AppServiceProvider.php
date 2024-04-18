<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

//User Repo
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\ConcreteClasses\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
