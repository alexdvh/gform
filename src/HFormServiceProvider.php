<?php 
namespace HCollection\HForm;

use HCollection\HForm\HFormBuilder;
use Illuminate\Support\ServiceProvider;
/**
 * ServiceProvider
 *
 * The service provider for the modules. After being registered
 * it will make sure that each of the modules are properly loaded
 * i.e. with their routes, views etc.
 *
 * @author Hoangdv <hoangdv1112@gmail.com>
 * @package HCollection\HForm
 */
class HFormServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $pkg_path = dirname(__DIR__);
        $views_path = $pkg_path . '/resources/views';

        $this->loadViewsFrom($views_path, 'hform');
        $this->publishes([
            $views_path => base_path('resources/views/vendor/hform')
        ]);
        
    }
    
    public function register() {
        $this->registerHFormBuilder();

        $this->app->alias('hform', 'HCollection\HForm\Facades\HFormFacade');
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHFormBuilder()
    {
        $this->app->singleton('hform', function ($app) {

            $form = new HFormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }
}