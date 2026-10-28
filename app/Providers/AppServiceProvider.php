<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('rupiah', function (string|int|float|null $value): string {
            return "<?php echo '<span class=\"rupiah\">' . e('Rp' . number_format((float) ({$value}), 0, ',', '.')) . '</span>'; ?>";
        });
    }
}
