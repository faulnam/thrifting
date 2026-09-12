<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Coupon;
use App\Models\HeroSlide;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Shipment;
use App\Models\SiteSetting;
use App\Models\StoreLocation;
use App\Observers\DemoActivityObserver;
use Illuminate\Support\Facades\URL;
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
        if (
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
            || request()->header('x-forwarded-proto') === 'https'
            || str_contains(request()->header('host') ?? '', 'ngrok')
            || str_contains(request()->getHttpHost(), 'ngrok')
        ) {
            URL::forceScheme('https');
        }

        // Register DemoActivityObserver for automatic 10-minute demo rollback
        $observer = DemoActivityObserver::class;
        $trackedModels = [
            Product::class,
            ProductVariant::class,
            ProductImage::class,
            Category::class,
            Collection::class,
            BlogPost::class,
            HeroSlide::class,
            Page::class,
            StoreLocation::class,
            Coupon::class,
            Review::class,
            NewsletterSubscriber::class,
            SiteSetting::class,
            Order::class,
            OrderItem::class,
            Address::class,
            Shipment::class,
        ];

        foreach ($trackedModels as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe($observer);
            }
        }
    }
}
