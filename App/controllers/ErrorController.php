<?php 

  /**
   * Define the namespace for this file as 'App\Controllers'. This groups the 'ErrorController' class
   * under the 'App\Controllers' namespace, which helps in organizing code and avoiding name conflicts
   * by logically separating different parts of the application. It also allows for easier autoloading
   * of classes using PSR-4 standards.
   */
  namespace App\Controllers;

  /**
   * The ErrorController class is responsible for handling error responses and displaying error views.
   * It provides static methods to handle different types of HTTP errors, such as 404 Not Found and 403
   * Forbidden. These methods set the appropriate HTTP response code and load an error view with a message.
   */
  class ErrorController{

    /**
     * Display a 404 Not Found error page.
     * 
     * This method sets the HTTP response code to 404 and loads the error view for a resource not found.
     * It accepts an optional message parameter to display a custom error message.
     * 
     * @param string $message The error message to be displayed (default is 'Resource not found').
     * @return void
     */
    public static function notFound($message = 'Resource not found'){
      
      // Set the HTTP response code to 404, indicating that the requested resource was not found.
      http_response_code(404);
  
      // Load the 'error' view and pass the status code and message as data for display.
      loadView('error', [
        'status' => '404',
        'message' => $message
      ]);

    }

    /**
     * Display a 403 Forbidden error page.
     * 
     * This method sets the HTTP response code to 403 and loads the error view for an unauthorized access.
     * It accepts an optional message parameter to display a custom error message.
     * 
     * @param string $message The error message to be displayed (default is 'You are not authorized to view this resource').
     * @return void
     */
    public static function unauthorized($message = 'You are not authorized to view this resource'){

      // Set the HTTP response code to 403, indicating that access to the resource is forbidden.
      http_response_code(403);
  
      // Load the 'error' view and pass the status code and message as data for display.
      loadView('error', [
        'status' => '403',
        'message' => $message
      ]);
      
    }

  }

?>