<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorize {

  /**
   * Check if a user is authenticated
   * 
   * This function checks whether a user is logged in by verifying if there is 
   * an active session with user data. It returns `true` if a user session exists, 
   * otherwise, it returns `false`.
   * 
   * @return bool True if the user is authenticated, false otherwise
   */
  public function isAuthenticated(){
    // Check if the session has a 'user' key indicating a logged-in user
    return Session::has('user');
  }


  /**
   * Handle the user's request based on their authentication status
   * 
   * This function controls access based on the user's role (e.g., 'guest', 'auth').
   * It redirects the user based on whether they are authenticated or not.
   * 
   * @param string $role The role of the user ('guest' or 'auth')
   * @return bool
   */
  public function handle($role){
    // If the role is 'guest' and the user is authenticated, redirect to the homepage
    if ($role === 'guest' && $this->isAuthenticated()) {
      return redirect('/'); // Authenticated users should not access guest pages
    } 
    // If the role is 'auth' and the user is not authenticated, redirect to the login page
    elseif ($role === 'auth' && !$this->isAuthenticated()) {
      return redirect('/auth/login'); // Unauthenticated users should be redirected to login
    }
  }
}

?>