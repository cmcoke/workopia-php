<?php

  // -------------------------------------
  // Routes for handling various sections
  // -------------------------------------

  // Home Page
  // GET request to '/' - calls the index method in HomeController.
  // Displays the homepage.
  $router->get('/', 'HomeController@index');

  // Listings Page
  // GET request to '/listings' - calls the index method in ListingController.
  // Displays all job listings.
  $router->get('/listings', 'ListingController@index');

  // Create Listing Page
  // GET request to '/listings/create' - calls the create method in ListingController.
  // Displays the form to create a new job listing.
  // Requires the user to be authenticated ('auth' middleware).
  $router->get('/listings/create', 'ListingController@create', ['auth']);

  // Edit Listing Page
  // GET request to '/listings/edit/{id}' - calls the edit method in ListingController.
  // Displays the form to edit a specific job listing identified by {id}.
  // Requires the user to be authenticated ('auth' middleware).
  $router->get('/listings/edit/{id}', 'ListingController@edit', ['auth']);

  // Search Listings Page
  // GET request to '/listings/search' - calls the search method in ListingController.
  // Displays search results for job listings.
  $router->get('/listings/search', 'ListingController@search');

  // Show Listing Details
  // GET request to '/listings/{id}' - calls the show method in ListingController.
  // Displays the details for a specific job listing identified by {id}.
  $router->get('/listings/{id}', 'ListingController@show');


  
  // -----------------------------------
  // Routes for CRUD operations on jobs
  // -----------------------------------

  // Store a New Job Listing
  // POST request to '/listings' - calls the store method in ListingController.
  // Handles the form submission to store a new job listing in the database.
  // Requires the user to be authenticated ('auth' middleware).
  $router->post('/listings', 'ListingController@store', ['auth']);

  // Update an Existing Job Listing
  // PUT request to '/listings/{id}' - calls the update method in ListingController.
  // Handles the form submission to update a job listing identified by {id}.
  // Requires the user to be authenticated ('auth' middleware).
  $router->put('/listings/{id}', 'ListingController@update', ['auth']);

  // Delete a Job Listing
  // DELETE request to '/listings/{id}' - calls the destroy method in ListingController.
  // Deletes the job listing identified by {id} from the database.
  // Requires the user to be authenticated ('auth' middleware).
  $router->delete('/listings/{id}', 'ListingController@destroy', ['auth']);


  
  // -----------------------------------
  // Routes for Authentication handling
  // -----------------------------------

  // Register Page
  // GET request to '/auth/register' - calls the create method in UserController.
  // Displays the form for user registration.
  // Only accessible by guests (non-authenticated users) via 'guest' middleware.
  $router->get('/auth/register', 'UserController@create', ['guest']);

  // Login Page
  // GET request to '/auth/login' - calls the login method in UserController.
  // Displays the login form.
  // Only accessible by guests (non-authenticated users) via 'guest' middleware.
  $router->get('/auth/login', 'UserController@login', ['guest']);

  // Register User
  // POST request to '/auth/register' - calls the store method in UserController.
  // Handles the form submission to register a new user.
  // Only accessible by guests (non-authenticated users) via 'guest' middleware.
  $router->post('/auth/register', 'UserController@store', ['guest']);

  // Logout User
  // POST request to '/auth/logout' - calls the logout method in UserController.
  // Logs the user out by clearing the session.
  // Requires the user to be authenticated ('auth' middleware).
  $router->post('/auth/logout', 'UserController@logout', ['auth']);

  // Authenticate User
  // POST request to '/auth/login' - calls the authenticate method in UserController.
  // Handles the form submission to log the user in.
  // Only accessible by guests (non-authenticated users) via 'guest' middleware.
  $router->post('/auth/login', 'UserController@authenticate', ['guest']);

?>