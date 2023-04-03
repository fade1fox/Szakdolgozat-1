<?php

namespace App\Model;

use App\Model\ProductDao;
use App\Lib\Database;
use PDO;

class Cart
{


	public $ids = array();

	public static function getCart()
	{
		if (array_key_exists("cart", $_SESSION))
			return unserialize($_SESSION["cart"]);
		else {
			$cart = new Cart();
			$_SESSION["cart"] = serialize($cart);

			return $cart;
		}
	}

	private function save()
	{
		$_SESSION["cart"] = serialize($this);
	}


	public function addProductById($id, $qt = 1)
	{
		if (array_key_exists($id, $this->ids))
			$this->ids[$id] += $qt;
		else
			$this->ids[$id] = $qt;
		$this->save();
	}

	public function setProductQuantityById($id, $qt)
	{
		if (array_key_exists($id, $this->ids))
			$this->ids[$id] = $qt;
		$this->save();
	}

	/*public function removeProductById($id){
		if(array_key_exists($id, $this->ids))
			unset($this->ids[$id]);
		$this->save();
	}*/
	public function removeProductById($id)
	{
		if (array_key_exists($id, $this->ids)) {
			unset($this->ids[$id]);
			$this->save();
		}
	}

	public function getContents()
	{
		$ids = $this->ids;
		if (sizeof($ids) == 0)
			return array();
		$products = ProductDao::getProductsByIds(array_keys($ids));
		for ($i = 0; $i < count($products); $i++) {
			$products[$i]["kosarMennyiseg"] = $ids[$products[$i]["id"]];
		}

		return $products;
	}

	public function clear()
	{
		$this->ids = array();
		$this->save();
	}

	public function getItemNumber()
	{
		$sum = 0;

		foreach ($this->ids as $qty) {
			$sum += $qty;
		}
	}

	public static function OrderCheck()
{
    $dbObj = new Database();
    $conn = $dbObj->getConnection();

    $cart = Cart::getCart();
    $data = $cart->getContents();

    foreach ($data as $item) {
        $sql = "SELECT elerheto FROM boroplug.cipo WHERE id = :id";
        $statement = $conn->prepare($sql);
        $statement->execute([
            "id" => $item['id']
        ]);
        $product = $statement->fetch(PDO::FETCH_ASSOC);
        $elerheto = $product['elerheto'];
        $rendelendo = $item['kosarMennyiseg'];
        $maradek = $elerheto - $rendelendo;
        if ($maradek < 0) {
            return false;
        }
    }

    foreach ($data as $item) {
        $productDao = new ProductDao();
        $productDao->updateStock($item['id'], $maradek, $elerheto);
    }

    return true;
}

}
