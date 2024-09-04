<?php 

  // Declare the namespace for the Database class. This places the Database class within the 'Framework' namespace, helping to organize the code and avoid class name conflicts.
  namespace Framework;

  // Import the PDO class from the global namespace. This allows the Database class to use PDO for database interactions without needing to specify the global namespace each time.
  use PDO;

  class Database {

    public $conn;

    /**
     * Constructor for Database class
     * 
     * This constructor method initializes a database connection using the provided
     * configuration array. It constructs a DSN (Data Source Name) string based on the 
     * host, port, and database name, and then attempts to create a PDO connection 
     * with the specified username and password. The connection options include enabling 
     * exception mode for errors and setting the default fetch mode to return results 
     * as objects. If the connection fails, an exception is thrown with an error message.
     * 
     * @param array $config Configuration array containing database connection details
     *                      (host, port, dbname, username, and password).
     */
      public function __construct($config){
        
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

        $options = [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
        
        try {
          $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
          throw new Exception("Database connection failed: {$e->getMessage()}");
        }
      }


      /**
       * Query the database
       * 
       * This method prepares and executes a SQL query using the PDO connection established
       * in the constructor. It accepts a SQL query string and an optional array of parameters.
       * The query is first prepared using the PDO `prepare` method, and if parameters are provided,
       * they are bound to the query using the `bindValue` method. The query is then executed.
       * If the query executes successfully, it returns the resulting PDOStatement object, 
       * which can be used to fetch the results. If the query fails, an exception is thrown with an error message.
       * 
       * @param string $query The SQL query to be executed.
       * @param array $params An optional associative array of parameters to bind to the query.
       *                      The array keys should correspond to the parameter placeholders in the query.
       *                      For example, if the query has a placeholder `:id`, the array should contain
       *                      an entry like `['id' => 123]`.
       * 
       * @return PDOStatement The result of the executed query.
       * @throws PDOException If the query fails to execute.
       */
      public function query($query, $params = []){

        try {
            // Prepare the SQL query.
            $sth = $this->conn->prepare($query);

            // Bind each parameter to its corresponding placeholder in the query.
            foreach($params as $param => $value){
                $sth->bindValue(':' . $param, $value);
            }

            // Execute the query.
            $sth->execute();

            // Return the PDOStatement object resulting from the query.
            return $sth;

        } catch (PDOException $e) {
            // If the query fails, throw an exception with an error message.
            throw new Exception("Query failed to execute: {$e->getMessage()} ");
        }

      }


  }

?>