# WPMVC
A Wordpress plugin boilerplate in MVC pattern.

The purpose of this plugin is mainly to offer another way of creating wordpress plugins. Wordpress plugins are traditionally an amalgamation of classes, function, and actions that often don't follow a standard pattern. In my experience of hacking and modifying plugins, most Wordpress plugins have no standard pattern making the code very difficult to navigate through to track down bugs or extend plugin features. 

> **Code _can be an artform_, but even art has rules.**

## Getting Started

### Installation
To install WPMVC, download the zip file and drop it into your Wordpress plugins directory, or simply install from the wp-admin.

Upon activating WPMVC, a new `rewrite_rule` will be added to the wp_options table with a default `route` endpoint. You can change the default endpoint in the `config.php`.

### The Config File
The config file is meant to define constant variables that will be used throughout the WPMVC framework. You may add more constants like API keys to merchant accounts like Stripe, Authorize.net, Google, etc.

The default config file should look like this :

```php
<?php

	/**
 	 * The VERSION is the folder which your 
 	 * Controllers, Models, and Views live
 	 * in the source folder
	 */
	define('VERSION', 'v1');

	/**
 	 * The ROUTE is the query variable
 	 * Wordpress is set to match in the rewrite rule
	 */
	define('ROUTE', 'api_route');

	/**
 	 * The PATHNAME is the subpath of the domain 
 	 * and base path of our endpoints such as route/index/test
	 */
	define('PATHNAME', 'route');

	/**
 	 * The DEBUG will cause php to exit
 	 * if a method cannot be found in a controller
 	 * DEBUG set to FALSE will render Wordpress 404 page
	 */
	define('DEBUG', TRUE);
```

### The Source Folder
The source folder is where you're the MVC structure lives. 

```
./source
  |
  base
  |__ Controller.php
  |__ Model.php
  |
  v1
  |__ controllers
  |	|
  |	|__ IndexController.php
  |
  |__ models
  |	|
  |	|__ TestModel.php
  |
  |__ views
  	|
  	|__ test.php
```

> Notice the `v1` folder. This is where all your application logic lives. You can create a new version to your application by creating a new version folder in the `source` directory and change the `VERSION` constant in the `config.php` file.

### The Controller
The purpose of the controller is to build methods that handle one aspect of an application. Setters and getters for user data should all be kept in a controller named `UserController` since the purpose of the controller is to handle logic related to users. 

> This is a good coding habit to have that will pay dividends down the road when you want to fix a bug or extend your application.

The controller classname should be in Pascal Case, such as `UserController` and should extend the base controller.

An example controller included in the source should look like: 

```php
<?php

	namespace GYBC;

	class IndexController extends Controller{

		public function __construct(){
			
			parent::__construct();

			/**
		 	 * We call the parent construct in
		 	 * order to use properties such as
		 	 * $this->request
		 	 * $this->response()
			 */
		}
			
		/**
	 	 * Example using the request property
		 */

		public function testGET(){

			$this->response->json( $this->request['get'] );
		}

		/**
		 * Example RESTful method to return JSON
		 */

		public function return_jsonGET(){

			$UserModel = new UserModel;

			$users = $UserModel->getUsers();

			$this->response->json($users);

		}

		/**
		 * Example RESTful method to return a template
		 * Templates are located in the views directory
		 */

		public function return_viewGET(){

			$UserModel = new UserModel;

			$users = $UserModel->getUsers();

			$this->response->view('test', $users);

		}
	}
```

> Notice how each method has a `REQUEST_METHOD` at the end of each method name. This is to restrict a method that requires a `POST` method from being accessed with a `GET` method type, and visa versa. This is called a RESTful controller method.

Controllers and many classes in WPMVC are namespaced in order to avoid conflict with other classes from other plugins.

> Note that using a class not in a given namespace must be escaped. Such as `new DateTime;` must be instantiated as `new \DateTime;`.

> Also notice that we instantiated a Model in a couple of the example controller methods. Anytime we want to grab information from the database we use a Model method. More about Models in **"The Model"** section below.

To illustrate how to send a request to a RESTful controller method, here are two examples

```html
<form action="<?php echo secure_route_action('index/test'); ?>" method="GET">
	<input type="text" name="firstname" value="">
	<input type="text" name="lastname" value="">
	<input type="submit" value="submit">
</form>

<div id="ajax_container">
	<input type="button" value="AJAX Button">	
</div>
```

You can the `action` and `method` attributes of an HTML form to set your endpoint and `REQUEST_METHOD` respectively. In the example above, the `action` attribute points to `IndexController->testGET()` and the `method` attribute tells our framework to look for the `testGET()` method. We could even have `testGET()` and `testPOST` in the same controller.

> Notice that the the form action attribute uses the `route_action()` function, which can be found in the `includes/helpers.php` file. This is just a quicker way to write the whole URL which would be http://domain.com/route/index/test. 

> Also Note, that the `route_action()` function knows the `PATHNAME` constant set in the `config.php` which makes for a heck of a lot less maintenance.

```javascript
jQuery(document).ready(function($){
	$.ajax({
		url : '<?php echo route_action("index/test");?>',
		data : $('form').find('input'),
		type: 'GET',
		dataType: 'json',
		success: function(d){
			alert('firstname: ' + firstname + ', lastname: '+lastname);
		}
	})
});
```
If you prefer AJAX to send a RESTful request to your controller, you could use the script example above and set the `REQUEST_METHOD` in the `$.ajax` options property - `type`.

### The Model
The model puts the M in MVC. The purpose of the Model is to store all your database queries the same way we store logic in our Controllers, organized and labeled. We keep logic related to users in the `UserController` and queries related to users in the `UserModel`.

```php
<?php

	namespace GYBC;

	class UserModel extends Model
	{
		public function getUsers(){

			global $wpdb;

			$results = return $wpdb->get_results("
				SELECT 
					u.*, 
					um1.meta_value, 
					um2.meta_value 
				FROM {$wpdb->prefix}users as u
				JOIN {$wpdb->prefix}usermeta as um1
					ON um1.user_id = u.ID
				JOIN {$wpdb->prefix}usermeta as um2
					ON um1.user_id = u.ID
				WHERE um1.meta_key = 'firstname'
				WHERE um2.meta_key = 'lastname'
			");

			return $results;
		}

		
	}
```

> Notice that we are using Wordpress' default WPDB global variable to access the database. You can use whatever functions that are available in Wordpress to get information from the database. 
> 
> Technically, functions like `get_users($args);` is a sort of Model method and can be used in Controllers, but be careful because remembering where you used Wordpress functions vs Model methods can create confusion as your application grows.


### The Views
The purpose of the view is to display the data you called from the Model, then formatted in the Controller. The view templates are normally kept in the views directory located in the current version of your source directory (i.e: `source/v1/views`).

```html
<?php get_header(); ?>

<h1>This is our Test View</h1>

<h3>We can show our data in it's raw form</h3>

<?php echo '<pre>'. print_r($data, 1). '</pre>'; ?>

<?php get_footer(); ?>
```
> Notice that in our view, the `$data` variable has all the user data we passed to the `$this->response->view('test', $users)` method in our `IndexController`. That is because we could easily have multiple indexes for our data array, for example in our Controller method:

```php
public function return_viewGET(){

	$UserModel = new UserModel;

	$users = $UserModel->getUsers();

	$this->response->view('test', array(
		'users'=> $users,
		'foo' => 'bar'
	);

}
```

> The example above is the more appropriate way to pass data to our view. Now in our view we can get the data like so:

```html
<?php get_header(); ?>

<h1>This is our Test View</h1>

<h3>We can show our data in it's raw form</h3>

<?php echo '<pre>'. print_r( $data['users'], 1). '</pre>'; ?>

<?php get_footer(); ?>
```

> The above example will make life a whole lot easier when you have a view that needs to bring in more than one set of data.

Views are almost exactly like templates in Wordpress. You can use template tags like `<?php get_header(); ?>` and access any other wordpres function within the view.

## Food For Thought
Imagine you want to create an Account area for users to reset their passwords, edit their contact information or even check on past orders. There is a lot of logic, database queries and data entry that takes place for a basic user account. An MVC pattern is great way to built a modular, object oriented plugin that is organized neatly so any part of your code that handles one aspect of your application is easy to find.

If you want to bring in another developer, an MVC pattern has been around for a while and most PHP or ASPX developers could pick it up fairly quickly. You could work on the Product and Order logic while your another developer works on User Accounts and Affiliates because an MVC pattern separates these different aspects of your software. You can even disable features of your application without necessary breaking your website or app.

## Additional Classes

### Validation
Coming soon...

### Encryption
Coming soon...

## Extras
Coming soon...