<?php

class Florist_Service
{
	const ENDPOINT = '/florist-service/api.php/';
	
	private $_resource;
	private $_method;
	private $_params;
	private $_handler;
	
	// Database connection
	private $_db;
	
	public function __construct()
	{
		$this->_resource = str_replace(self::ENDPOINT, '', $_SERVER['REQUEST_URI']);
		
		if(strpos($this->_resource, '/') !== false)
		{
			$explode = explode('/', $this->_resource);
			
			$this->_resource = $explode[0];
			
			$this->_params = array_slice($explode, 1);
		}
		
		$this->_method   = $_SERVER['REQUEST_METHOD'];
		
		$this->_handler = strtolower($this->_method . '_' . $this->_resource);
		
		$this->_initDb();
	}
	
	private function _initDb()
	{
		$this->_db = new mysqli('localhost', 'root', '', 'florist');
		// Check connection
		if ($this->_db->connect_error)
		    die("Connection failed: " . $conn->connect_error);
	}
	
	private function _readFromDb($sql)
	{
		$query = $this->_db->query($sql);
		
		$results = array();
		
		while($row = $query->fetch_assoc())
			$results[] = $row;
		
		return $results;
	}
	
	private function _writeToDb($sql)
	{
		if($this->_db->query($sql) === true)
			return true;
		
		return false;
	}
	
	public function serve()
	{
		$func = $this->_handler;
		
		$this->$func($this->_params);
	}
	
	public static function main()
	{
		$service = new Florist_Service();
		
		$service->serve();
	}
	
	private function get_products($params)
	{
		$products = $this->_readFromDb("SELECT * FROM products");
		
		echo json_encode($products);
	}

	private function get_users($params)
	{
		$users = $this->_readFromDb("SELECT * FROM users");
		
		echo json_encode($users);
	}
	
	private function get_basket($params)
	{
		$basket = $this->_readFromDb("SELECT * FROM basket");
		
		echo json_encode($basket);
	}

	private function get_basketItem($params)
	{
		$basketItem = $this->_readFromDb("SELECT * FROM basket where id_user=$id_user");
		
		echo json_encode($basketItem);
	}

	private function post_products($params)
	{
		// Harus pakai ini jika content-type-nya Application/Json
		$rawContent = trim(file_get_contents("php://input"));
		
		// Parse jadi array
		$data = json_decode($rawContent, true);
			
		$id          = $data['id'];
		$name        = $data['name'];
		$price		 = $data['price'];
		$category	 = $data['category'];
		$img_url 	 = $data['img_url'];
		
		$inserted = $this->_writeToDb("INSERT INTO products (name, price, category, img_url) VALUES ('$name', $price, '$category', '$img_url')");
		
		if($inserted)
		{
			$insertedProducts = array(
				'id' => $id,
				'name' => $name,
				'price' => $price,
				'category' => $category,
				'img_url' => $img_url
			);
		
			echo json_encode($insertedProducts);
		}
	}

	private function post_users($params)
	{
		// Harus pakai ini jika content-type-nya Application/Json
		$rawContent = trim(file_get_contents("php://input"));
		
		// Parse jadi array
		$data = json_decode($rawContent, true);
			
		$id = $data['id'];
		$name = $data['name'];
		$username = $data['username'];
		$password = $data['password'];
		$email = $data['email'];
		$phone_number = $data['phone_number'];
		
		$inserted = $this->_writeToDb("INSERT INTO users (name, username, password, email, phone_number) VALUES ('$name', $username, '$password', '$email', '$phone_number')");
		
		if($inserted)
		{
			$insertedUsers = array(
				'id' => $id,
				'name' => $name,
				'username' => $username,
				'password' => $password,
				'email' => $email,
				'phone_number' => $phone_number
			);
		
			echo json_encode($insertedUsers);
		}
	}

	private function post_basket($params)
	{
		// Harus pakai ini jika content-type-nya Application/Json
		$rawContent = trim(file_get_contents("php://input"));
		
		// Parse jadi array
		$data = json_decode($rawContent, true);
			
		$id = $data['id'];
		$id_user = $data['id_user'];
		$id_product = $data['id_product'];
		$quantity = $data['quantity'];
		$total_price = $data['total_price'];
		
		$inserted = $this->_writeToDb("INSERT INTO basket (id_user, id_product, quantity, total_price) VALUES ('$id_user', $id_product, '$quantity', '$total_price')");
		
		if($inserted)
		{
			$insertedBasket = array(
				'id' => $id,
				'id_user' => $id_user,
				'id_product' => $id_product,
				'quantity' => $quantity,
				'total_price' => $total_price
			);
		
			echo json_encode($insertedBasket);
		}
	}
	
}

Florist_Service::main();
	
?>