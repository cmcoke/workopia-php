<?php 

  namespace Framework;

  use App\Controllers\ErrorController;
  use Framework\Middleware\Authorize;

  class Router{

    protected $routes = [];

    
    /**
     * Add a new route
     * 
     * This method registers a new route by defining the HTTP method, URI, controller action, 
     * and optional middleware for the route. The controller and its method are split from the 
     * action string, then stored in the `$routes` array.
     * 
     * @param string $method The HTTP method (GET, POST, etc.)
     * @param string $uri The URI path for the route
     * @param string $action The controller@method format (e.g., UserController@login)
     * @param array $middleware Middleware to handle the route (optional)
     * @return void
     */
    public function registerRoute($method, $uri, $action, $middleware = []){
      // Split the action into controller and controller method
      list($controller, $controllerMethod) = explode('@', $action);

      // Store route details including method, URI, controller, method, and middleware
      $this->routes[] = [
        'method' => $method,
        'uri' => $uri,
        'controller' => $controller,
        'controllerMethod' => $controllerMethod,
        'middleware' => $middleware
      ];
    }



    /**
     * Add a GET route
     * 
     * @param string $uri The URI for the GET route
     * @param string $controller The controller action
     * @param array $middleware Middleware (optional)
     * @return void
     */
    public function get($uri, $controller, $middleware = []){
      // Register the route as a GET method
      $this->registerRoute('GET', $uri, $controller, $middleware);
    }



    /**
     * Add a POST route
     * 
     * @param string $uri The URI for the POST route
     * @param string $controller The controller action
     * @param array $middleware Middleware (optional)
     * @return void
     */
    public function post($uri, $controller, $middleware = []){
      // Register the route as a POST method
      $this->registerRoute('POST', $uri, $controller, $middleware);
    }



    /**
     * Add a PUT route
     * 
     * @param string $uri The URI for the PUT route
     * @param string $controller The controller action
     * @param array $middleware Middleware (optional)
     * @return void
     */
    public function put($uri, $controller, $middleware = []){
      // Register the route as a PUT method
      $this->registerRoute('PUT', $uri, $controller, $middleware);
    }



    /**
     * Add a DELETE route
     * 
     * @param string $uri The URI for the DELETE route
     * @param string $controller The controller action
     * @param array $middleware Middleware (optional)
     * @return void
     */
    public function delete($uri, $controller, $middleware = []){
      // Register the route as a DELETE method
      $this->registerRoute('DELETE', $uri, $controller, $middleware);
    }



    /**
     * Route the request to the appropriate controller and method
     * 
     * This function matches the current request's URI and method against the registered routes.
     * If a match is found, it handles middleware, instantiates the controller, and calls the 
     * appropriate method. If no match is found, it calls the notFound method from ErrorController.
     * 
     * @param string $uri The current URI requested by the user
     * @return void
     */
    public function route($uri){
      // Get the request method (e.g., GET, POST)
      $requestMethod = $_SERVER['REQUEST_METHOD'];

      // If POST request includes _method (for method override), adjust the request method
      if ($requestMethod === 'POST' && isset($_POST['_method'])) {
        $requestMethod = strtoupper($_POST['_method']); // Override the request method with _method value
      }

      // Loop through the registered routes to find a match
      foreach ($this->routes as $route) {
        // Break down the requested URI and the route URI into segments
        $uriSegments = explode('/', trim($uri, '/'));
        $routeSegments = explode('/', trim($route['uri'], '/'));

        $match = true;

        // Check if the URI segment count and HTTP method match
        if (count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {
          $params = [];

          for ($i = 0; $i < count($uriSegments); $i++) {
            // Check if the segments match exactly or if there is a placeholder parameter (e.g., {id})
            if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
              $match = false;
              break;
            }

            // If there's a parameter in the route URI, extract it and add it to the $params array
            if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
              $params[$matches[1]] = $uriSegments[$i];
            }
          }

          if ($match) {
            // Handle middleware (e.g., authentication)
            foreach ($route['middleware'] as $middleware) {
              (new Authorize())->handle($middleware);
            }

            // Create the full controller class name
            $controller = 'App\\Controllers\\' . $route['controller'];
            $controllerMethod = $route['controllerMethod'];

            // Instantiate the controller and call its method, passing parameters if any
            $controllerInstance = new $controller();
            $controllerInstance->$controllerMethod($params);
            return;
          }
        }
      }

      // If no matching route is found, call the 404 error handler
      ErrorController::notFound();
    }
  }

?>