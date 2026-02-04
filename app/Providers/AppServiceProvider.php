<?php

namespace App\Providers;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class,UserRepository::class);
        $this->app->bind(OrderRepositoryInterface::class,OrderRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class,PaymentRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
