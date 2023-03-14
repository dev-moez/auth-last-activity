<p align="center">
<img style="margin-left: auto; margin-right: auto" height="120" src="https://raw.githubusercontent.com/dev-moez/auth-last-activity/main/assets/icons/icon.png">

<h1 align="center">Auth Last Activity</h1>
<h4 align="center">  
 👱 🕑 Multi-auth last activity laravel package </h4>
</p>

## Table of contents
- <a href="#introduction">:book: Introduction</a>
- <a href="#features">:sparkles: Features</a>
- <a href="#installation">:toolbox: Installation</a>
- <a href="#configuration">:gear: Configuration</a>
- <a href="#usage">:rocket: Usage</a>
	- <a href="#getting-a-user-last-activity">Getting user's last activity</a>
	- <a href="#getting-a-user-status">Getting a user status</a>
	- <a href="#getting-online-users">Getting online users</a>
	- <a href="#getting-offline-users">Getting offline users</a>
	- <a href="#getting-online-users-with-period">Getting online users with period</a>
	- <a href="#getting-user-online-status-using-cache">Getting user online status using `cache`</a>

## :book: Introduction
AuthLastActivity is a Laravel package that supports multiple authentication guards to record the last activity for your application users.


## :sparkles: Features
- Supports multiple authentication guards
- Adjustable database connection
- Ability to set seconds for the status of online users

## :toolbox: Installation

You can install the package via composer:

```
composer require dev-moez/auth-last-activity
```

The package will automatically register itself.

And to publish the config and the migration files needed for the package, run the following artisan command:
```
php artisan auth-last-activity:install
```

Or Manually:


You can publish the config file using the following command:
```
php artisan vendor:publish --tag="auth-last-activity-config"
```
This is the contents of the published config file:

```php
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

```

You can publish the migration file using the following command:

```
php artisan vendor:publish --tag="auth-last-activity-migrations"
```

After publishing the migration file, you can create the `user_last_activities` table by running the migrations:

```
php artisan migrate
```


## :gear: Configuration

### Step 1

To start using the package you have to add the middleware `AuthLastActivityMiddleware` to your `Kernel.php` which is located in your project's `app\Http` directory.

And to make sure that all requests via web or api are considered, you should add that middleware in both arrays `web` & `api`, like so:
```
protected $middlewareGroups = [
        'web' => [
            ...
            AuthLastActivityMiddleware::class,
        ],

        'api' => [
            ...
            AuthLastActivityMiddleware::class,
        ],
    ];
```
### Step 2
Add `HasLastActivity` trait to each authenticatable model you have in your laravel project, like `User` and any other model you would use for a different guard for authentication, example on `User` model:
```
<?php

namespace App\Models;

use DevMoez\AuthLastActivity\Models\Concerns\HasLastActivity;

class User extends Authenticatable
{
	..
	use HasLastActivity;
}
```

After doing this, you are ready to go :rocket:!

## :rocket: Usage

### Getting User Last Activity
Once you have installed and configured the package, it will automatically track the last activity time of authenticated users through your application. You can access the last activity time using the `lastActivity` one-to-one relation on the `User` model - or any other authenticatable models e.g: `Admin` ...etc, like so:

```
$user = User::findOrFail(100);
$user->lastActivity;
```

This will return a collection of the last activity data:
- `authenticatable_type` is the authenticatable model used for the record
- `authenticatable_id` is the authenticatable id used for the record
- `last_activity_url` is the last URL the user has visited
- `last_activity_time` is the datetime for the last activity for the user
- `user_agent` is user agent data from request
- `ip_address` is the IP Address that user has used for his last visited url
- `headers` is the request headers, in case of you needed it
- `is_mobile` is a boolean attribute to let you know if the last activity has been done via a mobile device or not.
- `request_source` is an enum value of `api` or `web`
- `previous_url` is the URL that the user visited before his last activity's URL.

### Getting User Status
To get the online status of a user you can use a method called `isOnline` to do so:
```
$user = User::findOrFail(100);
$user->isOnline(); // Check ->isOffline() too
```

### Getting Online Users
To get all online users you can use a static method called `getOnline`, like:
```
$onlineUsers = User::getOnline(); // returns Illuminate\Database\Eloquent\Builder
$onlineAdmins = Admin::getOnline(); // returns Illuminate\Database\Eloquent\Builder
```

### Getting Offline Users
To get all offline users you can use a static method called `getOffline`, like:
```
$offlineUsers = User::getOffline(); // returns Illuminate\Database\Eloquent\Builder
$offlineAdmins = Admin::getOffline(); // returns Illuminate\Database\Eloquent\Builder
```

Please note that getting online and offline users are based on the <i>`seconds`</i> value of `online-period` which you can find in the `auth-last-activity.php` config file.


### Getting Online Users Within Period
To get online users within a custom period of seconds you can use the static method of `activeWithin(int $seconds)` giving the seconds that you want to use.
```
$users = User::activeWithin(120);
```

Again, this will return the eloquent builder not the collection for the `User` model within the last two minutes. If you want the collection then follow the previous code with `->get()`:
```
$users = $users->get();
```

### Getting User Online Status Using `cache`
To get the online status of a user using `cache`, you can do by passing `true` to the argument of `viaCahce` in the `isOnline` method:
```
$user = User::findOrFail(100);
$user->isOnline(viaCache: true)
```

Or in your code you can use the following piece of code to achieve that:
```
use Illuminate\Support\Facades\Cache;

$isOnline = Cache::has('online-auth-{$authenticatable_name}-{$authenticatable_id}');
```

<b>What is the authenticatable name?</b><br><br>
It's the kebab/lowercase of your model's base name.
<br>
Let's say your project has two authenticatable models: 
1. `User` the default authenticatable model for users in any laravel project
2. `Admin` which you created for another guard just for administrators

Then for the first case the `$authenticatable_name` should be 'user' and for the second case the `$authenticatable_name` should be 'admin'.
<br><br>
And the `$authenticatable_id` is the primary key for this record in the database for sure.
Examples: 
* 'online-auth-user-10' 
* 'online-auth-admin-1000'

You can get the authenticatable name for any authenticatable model using a static method called `getAuthenticatableName()`.
```
$userAuthenticatableName = User::getAuthenticatableName();
$adminAuthenticatableName = Admin::getAuthenticatableName();
```

<b> :question: Why this package returns the Eloquent Builder not a Collection?</b>
<br>
This approach is suitable for large datasets as it retrieves only the necessary data from the database, making it faster and more efficient.
So if you would like to make any further operations such as filtering, mapping ...etc, it would be better to perform on a builder not a collection, after finishing you can call `get()` method to return the collection.



## Updating
Want to update to the latest version?
```
composer update dev-moez/auth-last-activity
```

## Uninstallation
```
composer remove dev-moez/auth-last-activity
```

## License

The MIT License (MIT).


## Contributing

Contributions are welcome! If you would like to contribute to the package with a new feature or any other enhancement, please fork the repository and submit a pull request.

1. Fork the Project
2. Create your feature branch (git checkout -b feature/new-feature-name)
3. Commit your changes (git commit -m 'Add and extra feature')
4. Push to the branch (git push origin feature/new-feature-name)
5. Open a pull request

And be sure that any contributions or comments you make are highly appreciated.


## Contact
Abdelrahman Moez (aka Moez) - abdelrahman.moez@icloud.com