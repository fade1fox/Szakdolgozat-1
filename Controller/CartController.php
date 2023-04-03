<?php
namespace App\Controller;
use App\Lib\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Model\Cart;
use App\Model\ProductDao;
use App\Model\User;
class CartController 
{
    public function cartTest()
    {
        
		$cart = Cart::getCart();
        $data = $cart->getContents();
       $maindata = ProductDao::mainproductsall();
        $twig = (new CartController())->setTwigEnvironment(); 
       // echo var_dump($data);
        echo $twig->render('pages/mainpage.html.twig', ['allproduct'=>$maindata,'cart'=>$data]);
    }
    public function cartList()
    {

        
		$cart = Cart::getCart();
        $data = $cart->getContents();
        $userlogged = User::isLoggedin();
       
      
        $twig = (new CartController())->setTwigEnvironment(); 
       // echo var_dump($data);
    
        echo $twig->render('pages/cart.html.twig', ['cart'=>$data]);
    }
    public function cartClear()
    {
		$cart = Cart::getCart();
        $data = $cart->clear();
        
		header("Location: /cart");
    }
    public function RemoveProduct(Request $req)
    {
		$body = $req->getBody();
		$id = $body["id"];
        $cart = Cart::getCart();
        $cart->removeProductById($id);
		header("Location: /cart");
    }
    public function EditProduct(Request $req)
    {
		$body = $req->getBody();
		$id = $body["id"];
		$qty = $body["quantity"];
        $cart = Cart::getCart();
        $cart->setProductQuantityById($id, $qty);
		header("Location: /cart");
    }
    public function ChechkOutList()
    {
        $cart = Cart::getCart();
        $data = $cart->getContents();
        $maindata = ProductDao::mainproductsall();
        $userdata = ProductDao::loginpageall();
        $varosok = ProductDao::varosok();
        $userlogged = User::isLoggedIn();
        $twig = (new CartController())->setTwigEnvironment(); 
       // echo var_dump($data);
       if ($userlogged) {
        echo $twig->render('pages/checkout.html.twig', ['cart'=>$data, 'allproduct'=>$maindata, 'loginAll'=>$userdata, 'varosok'=>$varosok ]);
       }
       else{
        header("Location: /cart");
       }
    }
    public function checkoutemail()
    {
        $cart = Cart::getCart();
        $data = $cart->getContents();
        $maindata = ProductDao::mainproductsall();
        $userdata = ProductDao::loginpageall();
        $varosok = ProductDao::varosok();


        
        
        
        $twig = (new CartController())->setTwigEnvironment(); 
       // echo var_dump($data);
        echo $twig->render('orderemail.html.twig', ['cart'=>$data, 'allproduct'=>$maindata, 'loginAll'=>$userdata, 'varosok'=>$varosok ]);



    }
    public function setTwigEnvironment()
    {
        $loader = new FilesystemLoader(__DIR__ . '\..\View');
        $twig = new \Twig\Environment($loader, [
            'debug' => true,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension()); 
        return $twig;
    }
}
