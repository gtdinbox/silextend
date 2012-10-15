<?php

namespace Silextend\Repository;

use Silex\ServiceProviderInterface;
use Silex\Application;

class RepositoryServiceProvider implements ServiceProviderInterface
{
	protected $booted = false;
	
	public function register(Application $app)
	{
		if ( ! $this->booted ) {
			$self = $this;
			$app->before(function() use ($app,$self) {
				$self->assignRepos($app);
			});
		}
	}

	public function boot(Application $app)
	{
		$this->assignRepos($app);
	}
	
	public function assignRepos($app)
	{
		foreach ($app['repository.repositories'] as $label => $class) {
			$ident = 'db.' . $class::$dbName . '.' . $label;
			$app[$ident] = $app->share(function($app) use ($class) {
				return new $class($app['dbs'][$class::$dbName]);
			});
			if ( $class::$dbName === 'default' ) {
				$app['db.' . $label] = $app[$ident];
			}
		}
		$this->booted = true;
	}
}