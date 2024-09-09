<?php

  namespace Framework;

  use Framework\Session; // Import the Session class from the Framework namespace. This allows you to use the Session class in this file without needing to specify the full namespace each time.

  class Authorization{

    /**
     * Check if the currently logged-in user owns a specific resource.
     * 
     * This method compares the ID of the currently logged-in user (retrieved from the session) 
     * with the ID of the resource to determine ownership. It returns true if the user owns the 
     * resource (i.e., the IDs match) and false otherwise.
     * 
     * @param int $resourceId The ID of the resource to check for ownership.
     * @return bool Returns true if the user owns the resource, otherwise false.
     */
    public static function isOwner($resourceId){
      
      // Retrieve the currently logged-in user from the session.
      $sessionUser = Session::get('user');

      // Check if there is a user in the session and if the user has an ID.
      if ($sessionUser !== null && isset($sessionUser['id'])) {
        // Cast the user's ID to an integer for comparison.
        $sessionUserId = (int) $sessionUser['id'];
        // Compare the user's ID with the resource ID to determine ownership.
        return $sessionUserId === $resourceId;
      }

      // Return false if the user is not logged in or if the IDs do not match.
      return false;
    }
  }

?>