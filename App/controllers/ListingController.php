<?php

namespace App\Controllers;

use Framework\Database; // Import the Database class from the Framework namespace. This allows you to use the Database class in this file without needing to specify the full namespace each time.
use Framework\Validation; // Import the Validation class from the Framework namespace. This allows you to use the Validation class in this file without needing to specify the full namespace each time.
use Framework\Session; // Import the Session class from the Framework namespace. This allows you to manage session data in this file.
use Framework\Authorization; // Import the Authorization class from the Framework namespace. This allows you to use authorization checks in this file.
use Framework\Middleware\Authorize; // Import the Authorize middleware class from the Framework namespace. This class is used to manage authorization middleware for routes.

class ListingController{
  protected $db;

  public function __construct()
  {
    // Load database configuration from the specified file and create a new Database instance.
    $config = require basePath('config/db.php');
    $this->db = new Database($config);
  }

  /**
   * Show all listings
   * 
   * @return void
   */
  public function index(){
    // Retrieve all listings from the database, ordered by creation date in descending order.
    $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();

    // Load the view to display the listings, passing the retrieved data to the view.
    loadView('listings/index', [
      'listings' => $listings
    ]);
  }

  /**
   * Show the create listing form
   * 
   * @return void
   */
  public function create(){
    // Load the view for creating a new listing.
    loadView('listings/create');
  }

  /**
   * Show a single listing
   * 
   * @param array $params The parameters containing the ID of the listing to be displayed.
   * @return void
   */
  public function show($params){
    // Get the listing ID from the provided parameters.
    $id = $params['id'] ?? '';

    $params = [
      'id' => $id
    ];

    // Query the database to fetch the listing with the provided ID.
    $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

    // Check if the listing exists. If not, display a 404 error page.
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    // Load the view to display the single listing, passing the listing data to the view.
    loadView('listings/show', [
      'listing' => $listing
    ]);
  }

  /**
   * Store data in database
   * 
   * @return void
   */
  public function store(){
    // Define the allowed fields for the new listing data.
    $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

    // Filter the submitted data to include only the allowed fields.
    $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

    // Add the user ID to the new listing data from the session.
    $newListingData['user_id'] = Session::get('user')['id'];

    // Sanitize the values of the new listing data.
    $newListingData = array_map('sanitize', $newListingData);

    // Define the required fields for the listing form.
    $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

    // Initialize an array to store validation errors.
    $errors = [];

    // Check if all required fields are provided and valid.
    foreach ($requiredFields as $field) {
      if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
        $errors[$field] = ucfirst($field) . ' is required';
      }
    }

    // If there are validation errors, reload the form view with errors and the submitted data.
    if (!empty($errors)) {
      loadView('listings/create', [
        'errors' => $errors,
        'listing' => $newListingData
      ]);
    } else {
      // Prepare the SQL query for inserting the new listing data into the database.
      $fields = [];

      foreach ($newListingData as $field => $value) {
        $fields[] = $field;
      }

      $fields = implode(', ', $fields);

      $values = [];

      foreach ($newListingData as $field => $value) {
        // Convert empty strings to null for proper database handling.
        if ($value === '') {
          $newListingData[$field] = null;
        }
        // Add placeholders for the SQL query values.
        $values[] = ':' . $field;
      }

      $values = implode(', ', $values);

      // Construct the SQL query for inserting the new listing data.
      $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

      // Execute the query to insert the data into the database.
      $this->db->query($query, $newListingData);

      // Set a flash message indicating successful creation of the listing.
      Session::setFlashMessage('success_message', 'Listing created successfully');

      // Redirect to the listings page.
      redirect('/listings');
    }
  }

  /**
   * Delete a listing
   * 
   * @param array $params The parameters containing the ID of the listing to be deleted.
   * @return void
   */
  public function destroy($params){
    // Get the listing ID from the provided parameters.
    $id = $params['id'];

    $params = [
      'id' => $id
    ];

    // Query the database to fetch the listing with the provided ID.
    $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

    // Check if the listing exists. If not, display a 404 error page.
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    // Check if the current user is authorized to delete the listing.
    if (!Authorization::isOwner($listing->user_id)) {
      // Set a flash message indicating the user is not authorized to delete the listing.
      Session::setFlashMessage('error_message', 'You are not authorized to delete this listing');
      // Redirect back to the listing page.
      return redirect('/listings/' . $listing->id);
    }

    // Execute the query to delete the listing from the database.
    $this->db->query('DELETE FROM listings WHERE id = :id', $params);

    // Set a flash message indicating successful deletion of the listing.
    Session::setFlashMessage('success_message', 'Listing deleted successfully');

    // Redirect to the listings page.
    redirect('/listings');
  }

  /**
   * Show the listing edit form
   * 
   * @param array $params The parameters containing the ID of the listing to be edited.
   * @return void
   */
  public function edit($params){
    // Get the listing ID from the provided parameters.
    $id = $params['id'] ?? '';

    $params = [
      'id' => $id
    ];

    // Query the database to fetch the listing with the provided ID.
    $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

    // Check if the listing exists. If not, display a 404 error page.
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    // Check if the current user is authorized to edit the listing.
    if (!Authorization::isOwner($listing->user_id)) {
      // Set a flash message indicating the user is not authorized to edit the listing.
      Session::setFlashMessage('error_message', 'You are not authorized to update this listing');
      // Redirect back to the listing page.
      return redirect('/listings/' . $listing->id);
    }

    // Load the view for editing the listing, passing the listing data to the view.
    loadView('listings/edit', [
      'listing' => $listing
    ]);
  }

  /**
   * Update a listing
   * 
   * @param array $params The parameters containing the ID of the listing to be updated.
   * @return void
   */
  public function update($params){
    // Get the listing ID from the provided parameters.
    $id = $params['id'] ?? '';

    $params = [
      'id' => $id
    ];

    // Query the database to fetch the listing with the provided ID.
    $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

    // Check if the listing exists. If not, display a 404 error page.
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    // Check if the current user is authorized to update the listing.
    if (!Authorization::isOwner($listing->user_id)) {
      // Set a flash message indicating the user is not authorized to update the listing.
      Session::setFlashMessage('error_message', 'You are not authorized to update this listing');
      // Redirect back to the listing page.
      return redirect('/listings/' . $listing->id);
    }

    // Define the allowed fields for the listing data.
    $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

    // Filter the submitted data to include only the allowed fields.
    $updateValues = [];

    $updateValues = array_intersect_key($_POST, array_flip($allowedFields));

    // Sanitize the values of the updated listing data.
    $updateValues = array_map('sanitize', $updateValues);

    // Define the required fields for the listing form.
    $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

    // Initialize an array to store validation errors.
    $errors = [];

    // Check if all required fields are provided and valid.
    foreach ($requiredFields as $field) {
      if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
        $errors[$field] = ucfirst($field) . ' is required';
      }
    }

    // If there are validation errors, reload the form view with errors and the submitted data.
    if (!empty($errors)) {
      loadView('listings/edit', [
        'listing' => $listing,
        'errors' => $errors
      ]);
      exit; // Exit the method to prevent further execution.
    } else {
      // Prepare the SQL query for updating the listing data in the database.
      $updateFields = [];

      foreach (array_keys($updateValues) as $field) {
        $updateFields[] = "{$field} = :{$field}";
      }

      $updateFields = implode(', ', $updateFields);

      $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

      // Add the listing ID to the update values and execute the query.
      $updateValues['id'] = $id;
      $this->db->query($updateQuery, $updateValues);

      // Set a flash message indicating successful update of the listing.
      Session::setFlashMessage('success_message', 'Listing updated');

      // Redirect to the updated listing page.
      redirect('/listings/' . $id);
    }
  }


    /**
     * Search listings by keywords/location
     * 
     * @return void
     */
    public function search(){
      
      $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
      
      $location = isset($_GET['location']) ? trim($_GET['location']) : '';

      $query = "SELECT * FROM listings WHERE (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords) AND (city LIKE :location OR state LIKE :location)";

      $params = [
        'keywords' => "%{$keywords}%",
        'location' => "%{$location}%"
      ];

      $listings = $this->db->query($query, $params)->fetchAll();

      loadView('/listings/index', [
        'listings' => $listings,
        'keywords' => $keywords,
        'location' => $location
      ]);
      
    }


}

?>