<?php 

  /**
   * Define the namespace for this file as 'App\Controllers'. This groups the 'ListingController' class
   * under the 'App\Controllers' namespace, which helps in organizing code and avoiding name conflicts
   * by logically separating different parts of the application. It also allows for easier autoloading
   * of classes using PSR-4 standards.
   */
  namespace App\Controllers;

  // Import the Database class from the Framework namespace. This allows you to use the Database class in this file without needing to specify the full namespace each time.
  use Framework\Database;

  
  class ListingController{

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
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();

        // Load the 'listings/index' view and pass the retrieved listings data to it as an associative array. This will render the 'listings/index' view template and make the $listings data available within the view.
        loadView('listings/index', [
            'listings' => $listings
        ]);

    }


    public function create(){
      loadView('listings/create');
    }


    public function show($params){

      // Get the 'id' parameter from the URL query string, or set it to null if it doesn't exist. This 'id' will be used to query a specific listing from the database.
      $id = $params['id'] ?? null;

      // Prepare the parameters for the SQL query. Here, 'id' is the key, and its value is the ID retrieved from the query string.
      $params = [
          'id' => $id
      ];

      // Execute a SQL query to retrieve a specific listing based on the provided ID.
      // The query uses a named placeholder ':id', and the $params array binds the actual ID value to this placeholder.
      // The result is then fetched as an associative array or object, depending on the fetch mode.
      $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

      // Check if listing exists
      if(!$listing){
        ErrorController::notFound('Listing not found');
        return;
      }

      // Load the 'show' view for the listing, passing the fetched listing data to the view.
      // The view will use this data to display the details of the listing.
      loadView('listings/show', [
        'listing' => $listing
      ]);

    }
  }

?>