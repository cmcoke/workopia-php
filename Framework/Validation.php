<?php

// Declare the namespace for the Validation class. This places the Validation class within the 'Framework' namespace, helping to organize the code and avoid class name conflicts.
namespace Framework;

class Validation{

    /**
     * Validate a string.
     * 
     * This method checks if the provided value is a string, then trims any whitespace 
     * from both ends of the string and verifies if its length is within the specified 
     * minimum and maximum range. Returns true if the string meets the criteria, otherwise false.
     * 
     * @param string $value The string value to be validated.
     * @param int $min The minimum length of the string (default is 1).
     * @param int $max The maximum length of the string (default is INF, meaning no upper limit).
     * @return bool True if the string is valid, false otherwise.
     */
    public static function string($value, $min = 1, $max = INF){
      
      // Check if the provided value is a string.
      if (is_string($value)) {
        // Trim whitespace from the beginning and end of the string.
        $value = trim($value);
        // Get the length of the string.
        $length = strlen($value);
        // Check if the string's length is within the allowed range.
        return $length >= $min && $length <= $max;
      }

      // Return false if the value is not a string or does not meet length criteria.
      return false;
    }


    /**
     * Validate an email address.
     * 
     * This method trims any whitespace from the provided email address and then checks 
     * if it is a valid email format using PHP's built-in filter_var function with the 
     * FILTER_VALIDATE_EMAIL filter.
     * 
     * @param string $value The email address to be validated.
     * @return mixed The validated email address if valid, otherwise false.
     */
    public static function email($value){

      // Trim whitespace from the beginning and end of the email address.
      $value = trim($value);

      // Validate the email using PHP's filter_var function and return the result.
      return filter_var($value, FILTER_VALIDATE_EMAIL);
    }


    /**
     * Match a value against another.
     * 
     * This method compares two string values to see if they are identical. Both values 
     * are trimmed of whitespace before comparison. Returns true if both values match, 
     * otherwise false.
     * 
     * @param string $value1 The first value to compare.
     * @param string $value2 The second value to compare.
     * @return bool True if both values match, false otherwise.
     */
    public static function match($value1, $value2){

      // Trim whitespace from both values.
      $value1 = trim($value1);
      $value2 = trim($value2);

      // Return true if both values are identical, otherwise return false.
      return $value1 === $value2;
    }
    
  }

  

?>