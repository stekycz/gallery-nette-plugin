<?php

require LIBS_DIR . '/Nette/loader.php';

Debugger::enable(Debugger::DETECT, APP_DIR . '/../log', 'martin.stekl@gmail.com');
Debugger::$strictMode = true;

// Load configuration from config.neon file
Environment::loadConfig(dirname(__FILE__) . '/config.neon');

// Configure application
$application = Environment::getApplication();
$application->errorPresenter = 'Error';

// Connect to database
dibi::connect(Environment::getConfig('database'));

// Setup router
{
	$router = $application->getRouter();

	$router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);

	$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
};


// Run the application!
$application->run();
