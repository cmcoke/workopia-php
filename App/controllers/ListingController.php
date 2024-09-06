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

  // Import the Validation class from the Framework namespace. This allows you to use the Validation class in this file without needing to specify the full namespace each time.
  use Framework\Validation;

  
  class ListingController{

    // Property to hold the instance of the Database class
    protected $db;


    public function __construct(){

        // Load the database configuration settings from the specified file. The configuration settings are read from the 'config/db.php' file.
        $config = require basePath('config/db.php');

        // Create a new instance of the Database class, passing the configuration settings. This sets up the database connection using the provided configuration.
        $this->db = new Database($config);
    }



    /**
     * Show all listings
     * 
     * This method retrieves a list of all listings from the database and displays them.
     * 
     * @return void
     */
    public function index(){

      // Execute a SQL query to retrieve all listings from the 'listings' table, 
      // ordering them by the 'created_at' timestamp in descending order. This ensures
      // the most recently created listings appear first. The results are fetched as an array.
      $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();

      // Load the 'listings/index' view and pass the retrieved listings data to it.
      // The $listings array will be available in the view template for rendering the listings.
      loadView('listings/index', [
          'listings' => $listings
      ]);

    }



    /**
    * Show the create listing form
    * 
    * This method displays a form that allows users to create a new listing.
    * It loads the view for creating a new listing, but doesn't handle any form submission logic.
    * 
    * @return void
    */
    public function create(){

      // Load the 'listings/create' view, which renders a form for the user to fill out 
      // to create a new listing. No additional data is passed to the view in this case.
      loadView('listings/create');
    }




    /**
     * Show a single listing
     * 
     * This method retrieves a specific listing from the database based on the 
     * 'id' parameter from the URL and displays it. If the listing is not found, 
     * an error page is shown.
     * 
     * @param array $params An associative array of parameters, typically from the URL.
     * @return void
     */
    public function show($params){

      // Get the 'id' parameter from the URL query string, or set it to null if it's not provided.
      // The 'id' will be used to query the database for the specific listing.
      $id = $params['id'] ?? null;

      // Prepare the SQL query parameters by creating an array where 'id' is the key and
      // the value is the 'id' retrieved from the query string.
      $params = [
          'id' => $id
      ];

      // Execute a SQL query to retrieve a specific listing from the 'listings' table where the 'id' matches the provided value.
      // The query uses a named placeholder ':id', and the actual ID value from $params is bound to this placeholder.
      // The result is fetched as an associative array or object, depending on the fetch mode.
      $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

      // Check if the listing was found in the database. If not, call the notFound() method from the ErrorController to display a 404 error page with the message 'Listing not found'.
      if (!$listing) {
        ErrorController::notFound('Listing not found');
        return; // Exit the method to prevent further execution if the listing doesn't exist.
      }

      // Load the 'show' view, passing the fetched listing data to the view. The 'listing' data will be used by the view to display the details of the listing.
      loadView('listings/show', [
        'listing' => $listing
      ]);

    }



    /**
     * Store data in the database
     * 
     * This method handles the storage of new listing data. It extracts the allowed fields 
     * from the submitted data, validates required fields, sanitizes inputs, and submits 
     * the sanitized data into the database. If validation fails, it reloads the form view 
     * with error messages.
     * 
     * @return void
     */
    public function store(){

      // Define the fields that are allowed to be processed from the form submission.
      $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

      // Filter the submitted data ($_POST) to include only the allowed fields.
      $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

      // Assign a default user ID (1) to the new listing. This simulates associating the listing with a specific user.
      $newListingData['user_id'] = 1;

      // Sanitize each field in the new listing data using the 'sanitize' function.
      $newListingData = array_map('sanitize', $newListingData);

      // Define the fields that are required for the form submission to be considered valid.
      $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

      // Initialize an empty array to store validation errors.
      $errors = [];

      // Loop through each required field and check if it is empty or invalid.
      foreach ($requiredFields as $field) {
        if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
          // Add an error message if the field is empty or fails validation.
          $errors[$field] = ucfirst($field) . ' is required';
        }
      }

      // If there are validation errors, reload the form view and pass the errors and the submitted data.
      if (!empty($errors)) {
        
        loadView('listings/create', [
          'errors' => $errors,        // Pass validation error messages to the view.
          'listing' => $newListingData // Pass the submitted data back to the view to pre-fill the form.
        ]);

      } else {
        // If no validation errors, proceed to submit the sanitized data to the database.

        // Initialize an array to hold the field names for the SQL query.
        $fields = [];

        // Populate the $fields array with the names of the fields in the new listing data.
        foreach ($newListingData as $field => $value) {
          $fields[] = $field;
        }

        // Convert the field names array into a comma-separated string for the SQL query.
        $fields = implode(', ', $fields);

        // Initialize an array to hold the placeholders for the SQL query values.
        $values = [];

        // Populate the $values array with placeholders (e.g., ':title') for the query.
        foreach ($newListingData as $field => $value) {
          // Convert empty strings to null for proper database handling.
          if ($value === '') {
            $newListingData[$field] = null;
          }
          // Add a placeholder for each field (e.g., ':title').
          $values[] = ':' . $field;
        }

        // Convert the placeholders array into a comma-separated string for the SQL query.
        $values = implode(', ', $values);

        // Construct the SQL query to insert the new listing data into the 'listings' table.
        $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

        // Execute the SQL query using the database connection and the sanitized data.
        $this->db->query($query, $newListingData);

        // Redirect the user to the '/listings' page after successfully inserting the data.
        redirect('/listings');

      }
    }


    
    
    /**
     * Delete a listing
     * 
     * This method deletes a listing from the database based on the provided ID.
     * If the listing doesn't exist, an error message is displayed.
     * 
     * @param array $params The parameters containing the ID of the listing to be deleted.
     * @return void
     */
    public function destroy($params){

      // Get the 'id' from the $params array which contains the URL parameters.
      $id = $params['id'];

      // Prepare the SQL query parameters by creating an array where 'id' is the key and the value is the ID retrieved from the URL.
      $params = [
          'id' => $id
      ];

      // Query the database to check if the listing with the provided ID exists. 
      // Fetch the result as an associative array or object, depending on the fetch mode.
      $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

      // Check if the listing exists. If not, call the notFound() method from ErrorController to display a 404 error page.
      if (!$listing) {
          ErrorController::notFound('Listing not found');
          return; // Exit the method to prevent further execution if the listing doesn't exist.
      }

      // Delete the listing from the database where the 'id' matches the provided value.
      $this->db->query('DELETE FROM listings WHERE id = :id', $params);

      // Set a success message in the session to inform the user that the listing has been deleted.
      $_SESSION['success_message'] = 'Listing deleted successfully';

      // Redirect the user to the listings page after the deletion.
      redirect('/listings');
      
    }


    
   /**
    * Show the listing edit form
    * 
    * This method displays the edit form for a specific listing, allowing the user to modify its details.
    * 
    * @param array $params The parameters containing the ID of the listing to be edited.
    * @return void
    */
    public function edit($params){

      // Get the 'id' from the URL parameters or set it to null if it's not provided. The 'id' will be used to query the database for the specific listing.
      $id = $params['id'] ?? null;

      // Prepare the SQL query parameters by creating an array where 'id' is the key and the value is the ID retrieved from the URL.
      $params = [
          'id' => $id
      ];

      // Query the database to retrieve the listing with the provided ID. 
      // Fetch the result as an associative array or object, depending on the fetch mode.
      $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

      // Check if the listing exists. If not, call the notFound() method from ErrorController to display a 404 error page.
      if (!$listing) {
          ErrorController::notFound('Listing not found');
          return; // Exit the method to prevent further execution if the listing doesn't exist.
      }

      // Load the 'edit' view, passing the retrieved listing data to the view. 
      // The view will render the edit form pre-filled with the listing details.
      loadView('listings/edit', [
          'listing' => $listing
      ]);

    }



  /**
   * Update a listing
   * 
   * This method updates an existing listing in the database based on the provided ID and form input.
   * If the listing doesn't exist or required fields are missing, appropriate actions are taken.
   * 
   * @param array $params The parameters containing the ID of the listing to be updated.
   * @return void
   */
  public function update($params){

    // Get the 'id' from the URL parameters or set it to an empty string if not provided.
    $id = $params['id'] ?? '';

    // Prepare the SQL query parameters by creating an array where 'id' is the key and the value is the ID retrieved from the URL.
    $params = [
        'id' => $id
    ];

    // Query the database to retrieve the listing with the provided ID.
    // The query uses a named placeholder ':id', and the actual ID value from $params is bound to this placeholder.
    // The result is fetched as an associative array or object, depending on the fetch mode.
    $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

    // Check if the listing exists. If not, call the notFound() method from ErrorController to display a 404 error page.
    if (!$listing) {
        ErrorController::notFound('Listing not found');
        return; // Exit the method to prevent further execution if the listing doesn't exist.
    }

    // Define the fields that are allowed to be updated.
    $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

    // Get the submitted data from the $_POST array, but only include fields that are allowed.
    $updateValues = array_intersect_key($_POST, array_flip($allowedFields));

    // Sanitize each of the allowed fields using the 'sanitize' function to ensure data is safe for storing.
    $updateValues = array_map('sanitize', $updateValues);

    // Define the fields that are required to be filled for a valid update.
    $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

    // Initialize an empty array to store validation errors.
    $errors = [];

    // Loop through the required fields and validate them.
    // If a required field is missing or doesn't pass validation, add an error message to the $errors array.
    foreach ($requiredFields as $field) {
        if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
          $errors[$field] = ucfirst($field) . ' is required';
        }
    }

    // If there are validation errors, reload the edit form view and pass the errors and current listing data back to the view.
    if (!empty($errors)) {
        loadView('listings/edit', [
            'listing' => $listing,
            'errors' => $errors
        ]);
        exit; // Stop further execution after reloading the form.
    } else {
        // Prepare the fields and values for the SQL UPDATE query.
        $updateFields = [];

        // For each field being updated, create a field assignment string in the format "field = :field".
        foreach (array_keys($updateValues) as $field) {
            $updateFields[] = "{$field} = :{$field}";
        }

        // Join all field assignments into a comma-separated string for use in the SQL query.
        $updateFields = implode(', ', $updateFields);

        // Construct the SQL UPDATE query, using named placeholders for each field.
        $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

        // Add the 'id' to the $updateValues array to bind it to the SQL query.
        $updateValues['id'] = $id;

        // Execute the SQL UPDATE query, passing the $updateValues array to bind the data to the query placeholders.
        $this->db->query($updateQuery, $updateValues);

        // Set a success message in the session to inform the user that the listing has been updated.
        $_SESSION['success_message'] = 'Listing Updated';

        // Redirect the user to the updated listing's page.
        redirect('/listings/' . $id);
    }
  }

    
 }

?>