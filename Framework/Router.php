<?php 

  // Declare the namespace for the Router class. This places the Router class within the 'Framework' namespace, helping to organize the code and avoid class name conflicts.
  namespace Framework;

  /**
   * Import the ErrorController class from the App\Controllers namespace. This allows you to use the ErrorController class
   * in the Router class without needing to specify the full namespace each time. The ErrorController class handles error
   * responses and displays appropriate error views, such as 404 Not Found or 403 Forbidden, which can be utilized
   * within the Router class to manage error handling for unmatched routes.
   */
  use App\Controllers\ErrorController;

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
       * This method registers a route by storing the HTTP method, URI, and the associated
       * controller and method in the routes array. It is used by methods like get(), post(), 
       * put(), and delete() to add routes to the Router class.
       * 
       * @param string $method The HTTP method (GET, POST, PUT, DELETE) for the route.
       * @param string $uri The URI path associated with the route.
       * @param string $action The action to be performed, specified as 'Controller@method'.
       * @return void
       */
      public function registerRoute($method, $uri, $action){

        /**
         * Parse the action parameter into controller and method.
         * 
         * The action parameter is expected to be in the format 'Controller@method'. 
         * This line splits the action into the controller and method parts.
         */
        list($controller, $controllerMethod) = explode('@', $action);

        /**
         * Add a new route to the routes array.
         * 
         * The $this->routes[] array stores each route as an associative array containing
         * the HTTP method, the URI, the corresponding controller, and the method to be 
         * called on that controller. This structure allows the Router class to track all 
         * registered routes and later match incoming requests to the appropriate route 
         * based on the method and URI.
         */
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod
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
       * Route the incoming request to the appropriate controller.
       * 
       * This method processes the incoming request by comparing the URI and method 
       * of the request against the registered routes. If a matching route is found, 
       * it constructs the full class name for the controller, creates an instance 
       * of the controller, and invokes the specified method on that controller. 
       * If no matching route is found, it calls the ErrorController to display 
       * an error page.
       * 
       * @param string $uri The URI of the incoming request.
       * @return void
       */
      public function route($uri){

        // Get the current HTTP request method from the server's REQUEST_METHOD variable. This retrieves the method (e.g., GET, POST) used in the current HTTP request.
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        /**
         * Iterate through the registered routes to find a match.
         * 
         * For each route in the routes array, the method checks if the URI and 
         * HTTP method match the incoming request's URI and method.
         */
        foreach($this->routes as $route){

            // Split the current URI into segments. This breaks the URI into its individual parts for comparison.
            $uriSegments = explode('/', trim($uri, '/'));

            // Split the route URI into segments. This breaks the route's URI into individual parts for comparison.
            $routeSegments = explode('/', trim($route['uri'], '/'));

            // Initialize match as true, assuming the URIs and methods will match.
            $match = true;

            // Check if the number of segments matches and if the request method matches the route's method.
            if(count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod){

                // Initialize an empty array to store any route parameters.
                $params = [];

                // Iterate through the segments to compare them and extract any parameters.
                for($i = 0; $i < count($uriSegments); $i++){
                    
                    // If the URIs do not match and there is no parameter placeholder, set match to false.
                    if($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])){
                        $match = false;
                        break;
                    }

                    // Check if the current segment is a parameter and add it to the $params array.
                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                        $params[$matches[1]] = $uriSegments[$i];
                    }

                }

                // If a match is found after the loop, proceed with routing.
                if($match){

                    /**
                     * Construct the fully qualified controller class name and extract the method.
                     * 
                     * The controller class name is constructed by prefixing the controller name 
                     * with the namespace 'App\\Controllers\\'. The method to be called on the 
                     * controller is extracted from the route.
                     */
                    $controller = 'App\\Controllers\\' . $route['controller'];
                    $controllerMethod = $route['controllerMethod'];

                    /**
                     * Instantiate the controller and call the method.
                     * 
                     * An instance of the controller is created, and the specified method is 
                     * called on the instance with the parameters passed. This executes the 
                     * logic defined in the controller for the matched route.
                     */
                    $controllerInstance = new $controller();
                    $controllerInstance->$controllerMethod($params);

                    return;

                }

            }

        }

        // If no matching route is found, display a 404 error page using the ErrorController.
        ErrorController::notFound();
      }



  }

?>