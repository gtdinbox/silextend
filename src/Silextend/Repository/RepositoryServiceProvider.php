<?php

namespace Silextend\Repository;

use Silex\ServiceProviderInterface;
use Silex\Application;

class RepositoryServiceProvider implements ServiceProviderInterface
{
	public function register(Application $app)
	{
		$app->before(function() use ($app) {
			foreach ($app['repository.repositories'] as $label => $class) {
				$ident = 'db.' . $class::$dbName . '.' . $label;
				$app[$ident] = $app->share(function($app) use ($class) {
					return new $class($app['dbs'][$class::$dbName]);
				});
				if ( $class::$dbName === 'default' ) {
					$app['db.' . $label] = $app[$ident];
				}
			}
		});
	}

	public function boot(Application $app)
	{
		
	}
}
