<?php 
namespace GCollection\GForm;

use GCollection\GForm\GFormBuilder;
use Illuminate\Support\ServiceProvider;
/**
 * ServiceProvider
 *
 * The service provider for the modules. After being registered
 * it will make sure that each of the modules are properly loaded
 * i.e. with their routes, views etc.
 *
 * @author Hoangdv <hoangdv1112@gmail.com>
 * @package GCollection\GForm
 */
class GFormServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $pkg_path = dirname(__DIR__);
        $views_path = $pkg_path . '/resources/views';

        $this->loadViewsFrom($views_path, 'GForm');
        $this->publishes([
            $views_path => base_path('resources/views/vendor/GForm')
        ]);
        
    }
    
    public function register() {
        $this->registerGFormBuilder();

        $this->app->alias('GForm', 'GCollection\GForm\Facades\GFormFacade');
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerGFormBuilder()
    {
        $this->app->singleton('GForm', function ($app) {

            $form = new GFormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }
}