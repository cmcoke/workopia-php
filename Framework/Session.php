<?php

namespace Framework;

class Session
{
  /**
   * Start the session if it hasn't been started yet.
   * 
   * @return void
   */
  public static function start()
  {
    /* 
    * Check if a session is not already active.
    *
    * PHP_SESSION_DISABLED - (0 = sessions disabled)
    * 
    * PHP_SESSION_NONE - (1 = no session)
    *
    * PHP_SESSION_ACTIVE - (2 = active session)
    */
    if (session_status() == PHP_SESSION_NONE) {
      // Start a new session if no session exists.
      session_start();
    }
  }

  
  /**
   * Set a session key/value pair.
   * 
   * @param string $key The session key to store the value.
   * @param mixed $value The value to be stored in the session.
   * @return void
   */
  public static function set($key, $value)
  {
    // Store the value in the session under the specified key.
    $_SESSION[$key] = $value;
  }


  /**
   * Get a session value by the key.
   * 
   * @param string $key The session key to retrieve the value for.
   * @param mixed $default The default value to return if the key is not found in the session.
   * @return mixed The session value associated with the key, or the default value if not found.
   */
  public static function get($key, $default = null)
  {
    // Return the value associated with the key if it exists, otherwise return the default value.
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }


  /**
   * Check if a session key exists.
   * 
   * @param string $key The session key to check.
   * @return bool True if the session key exists, otherwise false.
   */
  public static function has($key)
  {
    // Return true if the session key is set, otherwise false.
    return isset($_SESSION[$key]);
  }


  /** 
   * Clear a specific session key and its value.
   * 
   * @param string $key The session key to be removed.
   * @return void
   */
  public static function clear($key)
  {
    // Check if the session key exists, and if so, unset it to remove the key and its value.
    if (isset($_SESSION[$key])) {
      unset($_SESSION[$key]);
    }
  }


  /**
   * Clear all session data and destroy the session.
   * 
   * @return void
   */
  public static function clearAll()
  {
    // Unset all session variables.
    session_unset();
    // Destroy the current session.
    session_destroy();
  }


  /**
   * Set a flash message to be displayed once.
   * 
   * Flash messages are temporary session messages typically used for notifications.
   * 
   * @param string $key The flash message key.
   * @param string $message The flash message content.
   * @return void
   */
  public static function setFlashMessage($key, $message)
  {
    // Store the flash message in the session with the key prefixed by 'flash_'.
    self::set('flash_' . $key, $message);
  }


  /**
   * Get a flash message and remove it from the session after retrieval.
   * 
   * Flash messages are only meant to be displayed once, so after retrieving the message, it is cleared.
   * 
   * @param string $key The flash message key.
   * @param mixed $default The default value to return if the flash message doesn't exist.
   * @return string The flash message content, or the default value if not found.
   */
  public static function getFlashMessage($key, $default = null)
  {
    // Retrieve the flash message using the key (with 'flash_' prefix).
    $message = self::get('flash_' . $key, $default);
    // Clear the flash message after it has been retrieved so it won't be displayed again.
    self::clear('flash_' . $key);
    // Return the retrieved flash message.
    return $message;
  }

}