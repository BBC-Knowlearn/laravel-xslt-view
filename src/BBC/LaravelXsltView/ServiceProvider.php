<?php

namespace BBC\LaravelXsltView;

use Illuminate\View\ViewServiceProvider;

class ServiceProvider extends ViewServiceProvider
{
    public function register() {
        $app = $this->app;
        $app['view']->addExtension(
            'xsl', 'xsl', function() use ($app) {
                return new ViewEngine(new \XSLTProcessor());
            }
        );
    }
}