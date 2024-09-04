<?php 

  /**
   * Define the namespace for this file as 'App\Controllers'. This groups the 'HomeController' class
   * under the 'App\Controllers' namespace, which helps in organizing code and avoiding name conflicts
   * by logically separating different parts of the application. It also allows for easier autoloading
   * of classes using PSR-4 standards.
   */
  namespace App\Controllers;

  // Import the Database class from the Framework namespace. This allows you to use the Database class in this file without needing to specify the full namespace each time.
  use Framework\Database;

  
  class HomeController{

    // Property to hold the instance of the Database class
    protected $db;

    public function __construct(){

        // Load the database configuration settings from the specified file. The configuration settings are read from the 'config/db.php' file.
        $config = require basePath('config/db.php');

        // Create a new instance of the Database class, passing the configuration settings. This sets up the database connection using the provided configuration.
        $this->db = new Database($config);
    }

    public function index(){
      
        // Execute a SQL query to retrieve up to 6 listings from the 'listings' table and store the results. The query is executed using the Database class instance, and the results are fetched as an array.
        $listings = $this->db->query('SELECT * FROM listings LIMIT 6')->fetchAll();

        // Load the 'home' view and pass the retrieved listings data to it as an associative array. This will render the 'home' view template and make the $listings data available within the view.
        loadView('home', [
            'listings' => $listings
        ]);

    }

  }

?>