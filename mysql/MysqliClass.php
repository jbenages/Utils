<?php

	/**
	 *
	 * Class to secure and Raw queries to mysql with mysqli
	 * 
	 * To run need mysqlnd driver, intall -> apt-get install php5-mysqlnd
	 * 
	 *	@version 1.3b
	 * 
	 */

	class MysqliClass{
		const VERSION = "1.3b";
		/**
		 *	Global configuration of connection to Mysql
		 */
		private $db_config = array(
			"host"		=>	"",
			"user"		=>	"",
			"password"	=>	"",
			"bd"		=>	""
			);
		/**
		 *
		 * Set config for object to connect other server, user o data base in first time
		 * 
		 *
		 * @param array $config Configuration of database ("host" "user" "password" "bd")
		 *
		 */
		public function __construct( $config = array() ){
			$this->setConfig($config);
		}
		/**
		 *
		 * Set config for object to connect other server, user o data base
		 *
		 * @param array $config Configuration of database ("host" "user" "password" "bd")
		 *
		 */
		public function setConfig( $config = null ){
			if( !empty($config) ){
				$this->db_config = array_merge($this->db_config,$config);
				return true;
			}else{
				return false;
			}
		}
		/**
		 * Private function make connection to Mysql
		 * 
		 * @return object|string Mysqli object with connection or message error of connection fail
		 *
		 */
		private function connectDb(){
			$mysqli = new mysqli($this->db_config["host"],$this->db_config["user"],$this->db_config["password"],$this->db_config["bd"]);
			if ($mysqli->connect_errno) {
				$error = mysqli_connect_error();
    			$errno = mysqli_connect_errno();
   				return "$errno: $error";
			}
			return $mysqli;
		}
		/**
		 *
		 * This method execute all queries with prepare and bind params of mysqli
		 * 	
		 *
		 * SELECT
		 *
		 * $sql="SELECT name,email FROM user WHERE id = ?;"
		 *
		 * $values = array( array("type" => "i" , "content" => "1") );
		 *
		 * UPDATE
		 *
		 * $sql="UPDATE user SET name = ? WHERE id = ?;"
		 *  
		 * $values = array( array( "type" => "s" , "content" => "Pepe" ) , array( "type" => "i" , "content" => "1" ) );
		 *
		 * INSERT
		 *
		 * $sql="INSERT INTO user (name,email) VALUES (?,?)(?,?);"
		 *
		 * $values = array( array( "type" => "s" , "content" => "Pepe" ) , array( "type" => "s" , "content" => "pepe@pepe.com" ),array( "type" => "s" , "content" => "Paqui" ) , array( "type" => "s" , "content" => "paqui@paqui.com" ) );
		 *
		 * DELETE
		 * 
		 * $sql="DELETE FROM user WHERE name = ?;"
		 * 
		 *
		 * $values = array( array( "type" => "s" , "content" => "Pepe" ) );
		 * 
		 * Types of values "s" => string, "i" => integer, "d" => double, "b" => blob
		 * 
		 * @param string $sql Query raw format
		 *
		 * @param array $values [0]["type"],[0]["content"]
		 *
		 * @return array|string Return array of result query , num of rows affect or message error of query or connection to database
		 *
		 */
		public function query( $sql = null, $values = null ){
			if( empty($sql) ){
				return false;
			}
			$vector = array();
			$mysqli = $this->connectDb();
			if ( !is_object($mysqli) ){
				return $mysqli;
			}
			if ( $sql = $mysqli->prepare($sql) ) {
				if( !empty($values) ){
					$type = "";
					$value = array();
					for( $i = 0; $i < sizeof($values); $i++ ){
						$type .= $values[$i]["type"];
					}
					$a_params[] = & $type;
					for( $i = 0; $i < sizeof($values); $i++ ){
						$a_params[] = &	$values[$i]["content"];
					}
					call_user_func_array(array($sql, 'bind_param'), $a_params);
				}
			    $sql->execute();
			    $resu = array();
			    $result = $sql->get_result();
			    if( $result ){
			   		while ( $row = $result->fetch_assoc() ) {
				        $resu[] =  $row;
				    }
			    }else{
			    	$resu = $mysqli->affected_rows;
			    }
			    $sql->close();
			}else{
				$resu = $mysqli->error;
			}
			$mysqli->close();
			return $resu;
		}
	}