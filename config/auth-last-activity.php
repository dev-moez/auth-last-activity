<?php

return [
	# True/false to enable the package or disable it
	'enabled' => true,

	# Define database connection which the package would use to store last activity for users
	# 'mysql' is set as the default connection, you can change it to another connection as you desire
	'connection' => 'mysql',

	# Define the period in seconds, by which a user status would be defined as 'Online'
	'online-period' => 5,

	# The timezone that the package would use to store the date and time for user's last activity
	# By default the env APP_TIMEZONE will be used otherwise the timezone in the app.php config file would be used
	'timezone' => env('APP_TIMEZONE', config('app.timezone')),

	# Used middleware for api routes
	'middleware' => ['auth'],
];
