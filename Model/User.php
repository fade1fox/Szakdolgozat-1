<?php

namespace App\Model;

use App\Lib\Database;



use PDO;

class User
{
	public $id;
	public $name;
	public $email;
	public $admin;

	public function __construct($data)
	{
		$this->id = $data->id;
		$this->name = $data->nev;
		$this->email = $data->email;
		$this->admin = $data->admin;
	}

	public static function getUser()
	{
		if (array_key_exists("user", $_SESSION))
			return $_SESSION["user"];
		else
			return false;
	}

	public static function isAdmin()
	{
		$user = User::getUser();
		if ($user && $user->admin) {
			return 1;
		} else {
			return 0;
		}
	}


	public static function isLoggedIn()
	{
		return (array_key_exists("user", $_SESSION));
	}

	public static function logout()
	{
		unset($_SESSION["user"]);
		unset($_SESSION["cart"]);
	}

	public static function login($email, $password)
	{
		$dbObj = new Database();
		$conn = $dbObj->getConnection();

		$sql = "SELECT id, nev, email, jelszo, admin FROM boroplug.vasarlo WHERE email = :email;";
		$statement = $conn->prepare($sql);
		$statement->setFetchMode(\PDO::FETCH_OBJ);
		$statement->execute([
			'email' => $email
		]);
		$userData = $statement->fetch();

		$result = $statement->fetch();
		if ($result) {
			return "Hibás email vagy jelszó"; // email already exists in the database
		} else {
			if ($userData && $userData->jelszo == $password && $userData->torolt != 1) {
				$user = new User($userData);
				$_SESSION["user"] = $user;
				return true;
			} else
				return false;

			$error = "Hibás email-cím vagy jelszó!";
			echo "<div class='alert alert-danger'>$error</div>";
			return "USER_CREATED";
		}
	}
	

	public static function signup($name, $email, $password, $varos, $megjegyzes, $cim, $torolt, $telefonszam)
	{
		$dbObj = new Database();
		$conn = $dbObj->getConnection();

		$sql = "SELECT id FROM boroplug.vasarlo WHERE email = :email";
		$statement = $conn->prepare($sql);
		$statement->execute([
			"email" => $email
		]);
		$result = $statement->fetch();
		if ($result) {
			return "EMAIL_ALREADY_IN_USE"; // email already exists in the database
		} else {
			$sql = "INSERT INTO `vasarlo` (`nev`,`email`,`jelszo`,`varos_id`,`cim`,`megjegyzes`,`torolt`,`telefonszam`,`admin`)
				VALUES (:nev, :email, :jelszo, :varos, :cim, :megjegyzes, :torolt, :telefonszam, :admin)";
			$statement = $conn->prepare($sql);
			$statement->setFetchMode(\PDO::FETCH_OBJ);

			$params = [
				'nev' => $name,
				'email' => $email,
				'jelszo' => $password,
				'varos' => $varos, // fixed parameter name
				'cim' => $cim,
				'megjegyzes' => $megjegyzes,
				'telefonszam' => $telefonszam,
				'torolt' => $torolt,
				'admin' => 0
			];

			$statement->execute($params); // pass the $params array to execute() instead
			$error = "Sikeres regisztáció";
	
			return "USER_CREATED";
		}
	}
	public static function adminsignup($name, $email, $password, $varos, $megjegyzes, $cim, $torolt, $telefonszam, $admin)
	{
		$dbObj = new Database();
		$conn = $dbObj->getConnection();

		$sql = "SELECT id FROM boroplug.vasarlo WHERE email = :email";
		$statement = $conn->prepare($sql);
		$statement->execute([
			"email" => $email
		]);
		$result = $statement->fetch();
		if ($result) {
			return "EMAIL_ALREADY_IN_USE"; // email already exists in the database
		} else {
			$sql = "INSERT INTO `vasarlo` (`nev`,`email`,`jelszo`,`varos_id`,`cim`,`megjegyzes`,`torolt`,`telefonszam`,`admin`)
				VALUES (:nev, :email, :jelszo, :varos, :cim, :megjegyzes, :torolt, :telefonszam , :admin)";
			$statement = $conn->prepare($sql);
			$statement->setFetchMode(\PDO::FETCH_OBJ);

			$params = [
				'nev' => $name,
				'email' => $email,
				'jelszo' => $password,
				'varos' => $varos, // fixed parameter name
				'cim' => $cim,
				'megjegyzes' => $megjegyzes,
				'telefonszam' => $telefonszam,
				'torolt' => $torolt,
				'admin' => $admin
			];

			$statement->execute($params); // pass the $params array to execute() instead
			$error = "Sikeres regisztáció";
			return "USER_CREATED";
		}
	}
}
