<?php 

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;

class UserController{

  // Declare a protected property to hold the database instance.
  protected $db;

  /**
   * Constructor to initialize the database connection.
   * 
   * @return void
   */
  public function __construct()
  {
    // Load the database configuration from the 'db.php' file and initialize the Database instance.
    $config = require basePath('config/db.php');
    $this->db = new Database($config);
  }

  /**
   * Display the login page.
   * 
   * @return void
   */
  public function login(){
    // Load the login view located in the 'users' folder.
    loadView('users/login');
  }

  /**
   * Display the registration page.
   * 
   * @return void
   */
  public function create(){
    // Load the create user (registration) view located in the 'users' folder.
    loadView('users/create');
  }

  /**
   * Handle user registration and store the new user in the database.
   * 
   * @return void
   */
  public function store(){
    
    // Retrieve user input from the POST request.
    $name = $_POST['name'];
    $email = $_POST['email'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['password_confirmation'];

    // Initialize an empty array to hold validation errors.
    $errors = [];

    // Validate the email format.
    if (!Validation::email($email)) {
      $errors['email'] = 'Please enter a valid email address';
    }

    // Validate the name length (must be between 2 and 50 characters).
    if (!Validation::string($name, 2, 50)) {
      $errors['name'] = 'Name must be between 2 and 50 characters';
    }

    // Validate the password length (must be at least 6 characters).
    if (!Validation::string($password, 6, 50)) {
      $errors['password'] = 'Password must be at least 6 characters';
    }

    // Check if the password and confirmation password match.
    if (!Validation::match($password, $passwordConfirmation)) {
      $errors['password_confirmation'] = 'Passwords do not match';
    }

    // If there are any validation errors, reload the registration page with the errors and the user input.
    if (!empty($errors)) {
      loadView('users/create', [
        'errors' => $errors,
        'user' => [
          'name' => $name,
          'email' => $email,
          'city' => $city,
          'state' => $state,
        ]
      ]);
      exit;
    }

    // Check if the email already exists in the database.
    $params = [
      'email' => $email
    ];

    // Execute a query to find a user with the provided email address.
    $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

    // If the user already exists, display an error and reload the registration page.
    if ($user) {
      $errors['email'] = 'That email already exists';
      loadView('users/create', [
        'errors' => $errors
      ]);
      exit;
    }

    // Prepare the parameters to create a new user account, including hashing the password.
    $params = [
      'name' => $name,
      'email' => $email,
      'city' => $city,
      'state' => $state,
      'password' => password_hash($password, PASSWORD_DEFAULT) // Hash the password for security.
    ];

    // Insert the new user into the 'users' table in the database.
    $this->db->query('INSERT INTO users (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password)', $params);

    // Get the ID of the newly inserted user from the database.
    $userId = $this->db->conn->lastInsertId();

    // Set a session for the newly registered user with their ID, name, email, city, and state.
    Session::set('user', [
      'id' => $userId,
      'name' => $name,
      'email' => $email,
      'city' => $city,
      'state' => $state
    ]);

    // Redirect the user to the homepage after successful registration.
    redirect('/');
  }


  /**
   * Logout a user and destroy the session
   * 
   * This function clears all session data, removes the session cookie, and redirects the user to the homepage.
   * 
   * @return void
   */
  public function logout(){
    
    // Clear all session data by unsetting session variables and destroying the session
    Session::clearAll();

    // Get the session cookie parameters, including path and domain
    $params = session_get_cookie_params();

    // Expire the 'PHPSESSID' session cookie by setting its expiration time in the past
    setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);

    // Redirect the user to the homepage (or another destination after logout)
    redirect('/');
  }



  /**
   * Authenticate a user using email and password
   * 
   * This function handles user login by validating the input, checking if the user exists, and verifying the password. 
   * If successful, it sets the user session and redirects to the homepage.
   * 
   * @return void
   */
  public function authenticate(){
    
    // Retrieve the email and password from the POST request (user-submitted form data)
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Initialize an empty array to store validation errors
    $errors = [];

    // Validate the email field to ensure it contains a valid email address
    if (!Validation::email($email)) {
      $errors['email'] = 'Please enter a valid email';
    }

    // Validate the password to ensure it has at least 6 characters
    if (!Validation::string($password, 6, 50)) {
      $errors['password'] = 'Password must be at least 6 characters';
    }

    // If there are validation errors, load the login view and display the errors to the user
    if (!empty($errors)) {
      loadView('users/login', [
        'errors' => $errors
      ]);
      exit; // Stop further execution if validation fails
    }

    // Prepare the query parameters to find a user by their email address
    $params = [
      'email' => $email
    ];

    // Execute a SQL query to check if the user exists in the 'users' table using the provided email
    $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

    // If no user is found, show an error message and reload the login page
    if (!$user) {
      $errors['email'] = 'Incorrect credentials';
      loadView('users/login', [
        'errors' => $errors
      ]);
      exit; // Stop execution if the email doesn't match any user
    }

    // Verify the password entered by the user against the hashed password stored in the database
    if (!password_verify($password, $user->password)) {
      $errors['email'] = 'Incorrect credentials';
      loadView('users/login', [
        'errors' => $errors
      ]);
      exit; // Stop execution if the password is incorrect
    }

    // If authentication is successful, store the user's data in a session
    Session::set('user', [
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'city' => $user->city,
      'state' => $user->state
    ]);

    // Redirect the user to the homepage after successful login
    redirect('/');
  }


  
}

?>