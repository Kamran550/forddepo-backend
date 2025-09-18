<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;

class CheckShopAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     \Log::info('middlewareye daxil oldu');
    //     $shopId = $request->route('uuid'); // route param (məs: /shop/{id})
    //     $shop = Shop::find($shopId);

    //     \Log::info('middlewarede shop:', ['shop' => $shop]);
    //     if (!$shop) {
    //         \Log::info("abort");
    //         abort(404, 'Mağaza tapılmadı.');
    //     }

    //     $user = auth('sanctum')->user(); // login olan user
    //     \Log::info("middlewarede user:", ['user:', $user]);

    //     if ($shop->type === Shop::WHOLESALE && $user === null) {
    //         \Log::info("abort 403: wholesale shop, user null");
    //         abort(403, 'Bu mağazaya giriş icazəniz yoxdur.');
    //     }

    //     // Məsələn: shop->type = 'topdan' | 'perakende'
    //     if ($shop->type === Shop::WHOLESALE && $user->role !== 'wholesale_customer') {
    //         \Log::info("'middleware if 1");

    //         abort(403, 'Bu mağazaya giriş icazəniz yoxdur.');
    //     }

    //     if ($shop->type === Shop::RETAIL && $user->role !== 'retail_customer') {
    //         \Log::info("'middleware if 2");
    //         abort(403, 'Bu mağazaya giriş icazəniz yoxdur.');
    //     }

    //     \Log::info('hersey ok');

    //     return $next($request);
    // }
    public function handle(Request $request, Closure $next)
    {
        \Log::info('middlewareye daxil oldu');
        $shopId = $request->route('uuid'); // route param (məs: /shop/{id})
        $shop = Shop::find($shopId);

        \Log::info('middlewarede shop:', ['shop' => $shop]);
        if (!$shop) {
            \Log::info("abort");
            abort(404, 'Mağaza tapılmadı.');
        }

        $user = auth('sanctum')->user(); // login olan user
        \Log::info("middlewarede user:", ['user' => $user]);

        // WHOLESALE mağaza üçün yoxlamalar
        if ($shop->type === Shop::WHOLESALE) {
            if ($user === null) {
                \Log::info("abort 403: wholesale shop, user null");
                abort(403, 'Bu mağazaya giriş icazəniz yoxdur.');
            }

            if ($user->role !== 'wholesale_customer') {
                \Log::info("middleware if 1: wholesale shop, wrong role");
                abort(403, 'Bu mağazaya giriş icazəniz yoxdur.');
            }
        }

        // RETAIL mağaza üçün yoxlamalar
        if ($shop->type === Shop::RETAIL) {
            // Əgər user null-dırsa və retail mağazaya giriş tələb olunursa
            if ($user === null) {
                \Log::info("retail shop accessed without login - allowing");
                // Retail mağazalara login olmadan da giriş ola bilər
                // Əgər login tələb edirsinizsə, bu sətri uncomment edin:
                // abort(403, 'Bu mağazaya giriş üçün login olmalısınız.');
            } elseif ($user->role !== 'retail_customer') {
                \Log::info("middleware if 2: retail shop, wrong role");
                abort(403, 'Bu mağazaya giriş icazəniz yoxdur.');
            }
        }

        \Log::info('hersey ok');
        return $next($request);
    }
}
