<?php 

  /**
   * Get the base path of the project directory.
   * 
   * This function returns the absolute path to the base directory of the project.
   * Optionally, it can append a relative path passed as a parameter to the base path.
   * This is useful for constructing file paths relative to the project's root directory.
   * 
   * @param string $path Optional. A relative path to append to the base path.
   * @return string The absolute path to the specified file or directory.
   */
  function basePath($path = ''){
    return __DIR__ . '/' . $path;
  }


  /**
   * Load a view file from the views directory with optional data.
   * 
   * This function constructs the file path for a view file by appending the view name 
   * to the base path and then checks if the file exists. If the file exists, it extracts
   * the elements of the `$data` array into individual variables, making them accessible 
   * within the view file, and then includes the view file. If the file doesn't exist, 
   * it displays an error message.
   * 
   * @param string $name The name of the view file (without the .view.php extension).
   * @param array $data An associative array of data to be extracted into variables 
   *                    within the view file (default is an empty array).
   * @return void
  */
  function loadView($name, $data = []){

    $viewPath = basePath("App/views/{$name}.view.php");

    if(file_exists($viewPath)){
      extract($data);  // Extract the data array into individual variables.
      require $viewPath; // Include the view file.
    }else{
      echo " View '{$name}' not found! ";
    }
    
  }



  /**
   * Load a partial file from the partials directory.
   * 
   * This function works similarly to loadView but is used to load smaller, 
   * reusable parts of a view (partials), such as head, footer, navbar, etc.
   * It constructs the file path for a partial file and includes it if it exists. 
   * If the file doesn't exist, it displays an error message.
   * 
   * The function also accepts an optional `$data` array, which is extracted into individual variables
   * for use within the partial file. This allows dynamic data to be passed into partials.
   * 
   * @param string $name The name of the partial file (without the .php extension).
   * @param array $data (Optional) An associative array of data to be extracted and made available in the partial.
   * @return void
   */
  function loadPartial($name, $data = []){

    // Construct the full path to the partial file based on the provided name.
    $partialPath = basePath("App/views/partials/{$name}.php");

    // Check if the partial file exists at the constructed path.
    if (file_exists($partialPath)) {
        // Extract the $data array so that its keys become variables available within the partial file.
        extract($data);

        // Include the partial file so its content is rendered at this location.
        require $partialPath;
    } else {
        // Display an error message if the partial file does not exist.
        echo "Partial '{$name}' not found!";
    }
  }



  /**
   * Inspect and display the contents of a variable.
   * 
   * This function outputs detailed information about a variable using var_dump 
   * wrapped in <pre> tags for better readability. It is useful for debugging purposes 
   * when you want to inspect the structure and contents of a variable.
   * 
   * @param mixed $value The variable to inspect.
   * @return void
  */
  function inspect($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
  }


  /**
   * Inspect the contents of a variable and halt script execution.
   * 
   * This function works like inspect but also terminates the script after 
   * displaying the variable's contents. It is useful for debugging when you 
   * want to stop execution immediately after inspecting a variable.
   * 
   * @param mixed $value The variable to inspect.
   * @return void
  */
  function inspectAndDie($value){
    echo "<pre>";
    die(var_dump($value));
    echo "</pre>";
  }


  /**
   * Format salary to a currency format.
   * 
   * This function takes a salary value as a string, converts it to a floating-point number,
   * and then formats it with comma separators for thousands. The formatted salary is prefixed 
   * with a dollar sign ('$') to represent the currency. This is useful for displaying salaries 
   * in a user-friendly format.
   * 
   * @param string $salary The salary amount as a string.
   * @return string Formatted Salary in currency format (e.g., $50,000).
   */
  function formatSalary($salary){
    return '$' . number_format(floatval($salary));
  }


  /**
   * Sanitize Data
   * 
   * This function sanitizes a given string by removing unnecessary whitespace 
   * and escaping special characters that could be used in XSS attacks. It's useful 
   * for ensuring that user input is safe to display or store.
   * 
   * @param string $dirty The potentially unsafe input string to be sanitized.
   * @return string The sanitized string, safe for output or storage.
   */
  function sanitize($dirty) {

    // Trim any leading or trailing whitespace from the input string.
    $trimmed = trim($dirty);

    // Use filter_var to escape special characters like <, >, & to their HTML entity equivalents. This prevents Cross-Site Scripting (XSS) attacks by neutralizing harmful code.
    return filter_var($trimmed, FILTER_SANITIZE_SPECIAL_CHARS);
  }


  
  /**
   * Redirect to a given URL
   * 
   * This function redirects the user to the specified URL by sending a raw HTTP 
   * header to the browser. It stops further execution of the script to ensure 
   * that no additional code runs after the redirect.
   * 
   * @param string $url The URL to redirect the user to.
   * @return void
   */
  function redirect($url){

    // Send a Location header to the browser to initiate a redirect to the specified URL.
    header("Location: {$url}");

    // Terminate the script immediately after sending the redirect header to prevent further execution.
    exit;
  }


?>