<?php 

// Load the database configuration settings from the specified file. This configuration array typically contains database connection details such as host, port, dbname, username, and password.
$config = require basePath('config/db.php');

// Create a new instance of the Database class, passing the configuration settings. This initializes the database connection using the provided configuration.
$db = new Database($config);

// Get the 'id' parameter from the URL query string, or set it to null if it doesn't exist. This 'id' will be used to query a specific listing from the database.
$id = $_GET['id'] ?? null;

// Prepare the parameters for the SQL query. Here, 'id' is the key, and its value is the ID retrieved from the query string.
$params = [
    'id' => $id
];

// Execute a SQL query to retrieve a specific listing based on the provided ID.
// The query uses a named placeholder ':id', and the $params array binds the actual ID value to this placeholder.
// The result is then fetched as an associative array or object, depending on the fetch mode.
$listing = $db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

// Load the 'show' view for the listing, passing the fetched listing data to the view.
// The view will use this data to display the details of the listing.
loadView('listings/show', [
  'listing' => $listing
]);

?>