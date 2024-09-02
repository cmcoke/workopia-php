<?php 

  /* Define routes for the web application.
   * 
   * The following routes are registered using the Router instance. Each route
   * corresponds to a specific URI and links to a controller file that handles 
   * the request. These routes specify what happens when a user visits certain 
   * pages on the website:
   *
   * - The root path ('/') loads the home page.
   * - The '/listings' path loads the listings index page, which likely displays a list of items.
   * - The '/listings/create' path loads a page for creating new listings.
  */ 
  
  $router->get('/', 'controllers/home.php');
  $router->get('/listings', 'controllers/listings/index.php');
  $router->get('/listings/create', 'controllers/listings/create.php');
  $router->get('/listing', 'controllers/listings/show.php');

?>