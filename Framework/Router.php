<?php 

  // Declare the namespace for the Router class. This places the Router class within the 'Framework' namespace, helping to organize the code and avoid class name conflicts.
  namespace Framework;

  /**
   * Router class for handling HTTP requests and routing them to appropriate controllers.
   * 
   * This class manages routes for different HTTP methods (GET, POST, PUT, DELETE).
   * It allows registering routes, and then routing incoming requests to the correct
   * controller based on the request's URI and method.
   */
  class Router {

      // Stores all registered routes
      protected $routes = [];

      /**
       * Register a new route.
       * 
       * This function registers a route by storing the HTTP method, URI, and the 
       * associated controller in the routes array. It's a helper function used by 
       * specific methods like get(), post(), put(), and delete() to register routes.
       * 
       * @param string $method The HTTP method (GET, POST, PUT, DELETE).
       * @param string $uri The URI path associated with the route.
       * @param string $controller The controller file that handles the request.
       * @return void
       */
      public function registerRoute($method, $uri, $controller){
        /** 
         * Adds a new route to the routes array
         * 
         * The $this->routes[] array stores each route as an associative array containing
         * the HTTP method, the URI, and the corresponding controller. This allows the
         * Router class to track all registered routes and later match incoming requests
         * to the appropriate route based on the method and URI.
         */ 
        $this->routes[] = [
          'method' => $method,
          'uri' => $uri,
          'controller' => $controller
        ];
      }

      /**
       * Register a GET route.
       * 
       * This function is a shorthand for registering a route that responds to GET requests.
       * It internally calls registerRoute with 'GET' as the method.
       * 
       * @param string $uri The URI path associated with the GET route.
       * @param string $controller The controller file that handles the GET request.
       * @return void
       */
      public function get($uri, $controller){
        $this->registerRoute('GET', $uri, $controller);
      }

      /**
       * Register a POST route.
       * 
       * This function is a shorthand for registering a route that responds to POST requests.
       * It internally calls registerRoute with 'POST' as the method.
       * 
       * @param string $uri The URI path associated with the POST route.
       * @param string $controller The controller file that handles the POST request.
       * @return void
       */
      public function post($uri, $controller){
        $this->registerRoute('POST', $uri, $controller);
      }

      /**
       * Register a PUT route.
       * 
       * This function is a shorthand for registering a route that responds to PUT requests.
       * It internally calls registerRoute with 'PUT' as the method.
       * 
       * @param string $uri The URI path associated with the PUT route.
       * @param string $controller The controller file that handles the PUT request.
       * @return void
       */
      public function put($uri, $controller){
        $this->registerRoute('PUT', $uri, $controller);
      }

      /**
       * Register a DELETE route.
       * 
       * This function is a shorthand for registering a route that responds to DELETE requests.
       * It internally calls registerRoute with 'DELETE' as the method.
       * 
       * @param string $uri The URI path associated with the DELETE route.
       * @param string $controller The controller file that handles the DELETE request.
       * @return void
       */
      public function delete($uri, $controller){
        $this->registerRoute('DELETE', $uri, $controller);
      }

      /**
       * Load an error page.
       * 
       * This function sets the HTTP response code (default is 404) and loads 
       * the corresponding error view. It's called when no matching route is found.
       * 
       * @param int $httpCode The HTTP response code (e.g., 404 for not found).
       * @return void
       */
      public function error($httpCode = 404){
        http_response_code($httpCode);
        loadView("error/{$httpCode}");
        exit;
      }

      /**
       * Route the incoming request to the appropriate controller.
       * 
       * This function compares the incoming request's URI and method against the 
       * registered routes. If a match is found, it loads the corresponding controller.
       * If no match is found, it calls the error() function to display an error page.
       * 
       * @param string $uri The URI of the incoming request.
       * @param string $method The HTTP method of the incoming request (GET, POST, etc.).
       * @return void
       */
      public function route($uri, $method){

        foreach($this->routes as $route){
          if($route['uri'] === $uri && $route['method'] === $method){
            require basePath('App/' . $route['controller']);
            return;
          }
        }

        // If no matching route is found, display an error page.
        $this->error();
      }

  }

?>