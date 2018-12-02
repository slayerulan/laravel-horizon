<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\CmsPage;
use App\Language;
use App\SiteSetting;
use App\BannerImage;
/**
 * This will send all common data to view page.
 *
 *  @author	Anirban Saha
 */
class ViewVarriables extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        if($request->is('*site-admin*') == false) {
			//only for front end
			$cms_pages = Cache::remember('cms_pages', 60*24, function () {
				return CmsPage::select('title','slug_name')->where('status','active')->get();
			});

			View::share('cms_pages', $cms_pages);

            $banners = Cache::remember('banner_images', 60*24, function () {
                return BannerImage::where('status', 'active')->get();
            });
            View::share('banner_images', $banners);
		}

        $languages = Cache::remember('languages', 60*24, function () {
            return Language::select('slug','language')->where('status','active')->get();
        });
        View::share('languages', $languages);

        $site_settings = Cache::remember('site_settings', 60*24, function () {
			  return SiteSetting::select('image','admin_email')->first();
		});
        View::share('site_logo', $site_settings->image);
        View::share('admin_email', $site_settings->admin_email);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
