<?php
class MyDB
{
    protected static $instance;
    protected $pdo;
	
	
    
     protected $user_name="";
     protected $password= "";
    

	
     public function __construct()
     {
		$config=parse_ini_file("setup.ini");
		$this->user_name=$config["db_user"];
		$this->password=$config["db_password"];
        $this->pdo=new PDO("pgsql:dbname=".$config["db_name"].";host=".$config["db_server"].";port=".$config["db_port"], $this->user_name, $this->password );
     }
     
   // a classical static method to make it universally available
    public static function instance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public  function getPDO()
    {
        if (self::$instance === null)
        {
            self::$instance = new self;
        }
        $var=self::$instance;
        return $var->pdo;
        
    }
    
    // a proxy to native PDO methods
    /*public function __call($method, $args)
    {
        return call_user_func_array(array($this->pdo, $method), $args);
    }*/
        
}

?>