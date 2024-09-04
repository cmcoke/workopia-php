<?php

  // Import the Database class from the Framework namespace. This allows you to use the Database class in this file without needing to specify the full namespace each time.
  use Framework\Database;

  // Load the database configuration settings from the specified file.
  $config = require basePath('config/db.php');

  // Create a new instance of the Database class, passing the configuration settings.
  $db = new Database($config);

  // Execute a SQL query to retrieve up to 6 listings from the 'listings' table and store the results.
  $listings = $db->query('SELECT * FROM listings LIMIT 6')->fetchAll();

  // Load the 'home' view and pass the retrieved listings data to it as an associative array.
  loadView('home', [
    'listings' => $listings
  ]);

?>