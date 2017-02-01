<?php
    
    /**
     * Database class
     * @package triturn
     * @author Veljko Ilic
     * @since october 2014
     */
    class Db {
        
        /**
         * @var string
         */
        const HOST = '127.0.0.1';
        
        /**
         * @var string
         */
        const USER = 'root';
        
        /**
         * @var string
         */
        const PASSWORD = 'veljko';
        
        /**
         * @var string
         */
        const DBNAME = 'triturn';
        
        /**
         * The only one instance of the class (singleton)
         * @var Db
         */
        private static $instance;
        
        /**
         * The database handler - instance of the PDO class
         * Used inside class for preparing statements, i.e. for creating a PDO statement object
         * @var PDO
         */
        public $dbh;
        
        /**
         * An error message
         * A PDO instantiation error message
         * @var string
         */
        private $error;
        
        /**
         * Creates an instance of the PDO class
         */
        private function __construct() {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DBNAME;
            $options = array(
                PDO::ATTR_PERSISTENT => TRUE,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            try {
                $this->dbh = new PDO( $dsn, self::USER, self::PASSWORD, $options );
            } catch ( PDOException $e ) {
                $this->error = $e->getMessage();
            }
        }
        
        /**
         * Creates, if necessary, and returns the only instance of the class (singleton).
         * @return Db
         */
        public static function getInstance() {
            if( empty( self::$instance ) ) {
                self::$instance = new Db();
            }
            return self::$instance->dbh;
        }
        
    }
    
?>