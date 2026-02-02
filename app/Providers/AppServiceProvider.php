<?php

namespace App\Providers;

use App\Application\Shared\Contracts\TransactionManager;
use App\Application\Transfer\Contracts\AuthorizationGateway;
use App\Application\Transfer\Contracts\TransferRepository;
use App\Application\Wallet\Contracts\WalletRepository;
use App\Infrastructure\Database\Eloquent\EloquentTransferRepository;
use App\Infrastructure\Database\Eloquent\EloquentWalletRepository;
use App\Infrastructure\Database\LaravelTransactionManager;
use App\Infrastructure\Http\HttpAuthorizationGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TransferRepository::class, EloquentTransferRepository::class);
        $this->app->bind(WalletRepository::class, EloquentWalletRepository::class);
        $this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
        $this->app->bind(AuthorizationGateway::class, HttpAuthorizationGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
