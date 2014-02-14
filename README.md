konfig
======

Simple configuration class for PHP with support for cascading file system.

Installation
============

Either copy the Konfig.php class or add this to `composer.json`:

     require {
     	"laurent22/konfig": "dev-master"
     }

Usage
=====

Each config files is a simple PHP file that returns an associative array. For example:

	return array(
		'host' => '127.0.0.1',
		'port' => '6543',
		'user' => 'dbadmin',
		'password' => '123456',
	);

Assuming this kind of file structure:

	config/
		dev/
			database.php
			default.php
		live/
			database.php
			default.php
			
The following can be used to load the config files:

	// Always load the live environment
	Konfig::addLookupFolder('config/live');
	
	// But allow overriding the values if we are in development environment:
	if (ENV == 'dev') Konfig::addLookupFolder('config/dev');
	
Then to access the values:

	$dbHost = Konfig::get('database', 'host');
	
	$fullDbConfig = Konfig::getGroup('database');
	
The relevant files are loaded only as needed (if they are not used, nothing gets loaded).
