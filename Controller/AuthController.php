<?php
namespace App\Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Model\ProductDao;
use App\Lib\Request;
use App\Model\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\model\Cart;
class AuthController
{
	public function setTwigEnvironment()
	{
		$loader = new FilesystemLoader(__DIR__ . '\..\View');
		$twig = new \Twig\Environment($loader, [
			'debug' => true,
		]);
		$twig->addExtension(new \Twig\Extension\DebugExtension());
		return $twig;
	}
	public function LoginPage(Request $req)
	{

		$error = "";
		if (User::isLoggedIn())
			return header("Location: /");
		if ($req->reqMethod == "POST") {
			$body = $req->getBody();
			$email = $body["email"];
			$password = hash('sha256', $body["password"]);

			if (User::login($email, $password)) {
				if (User::isAdmin()) {
					return header("Location: /diagrams");
				} else {
					return header("Location: /");
				}
			} else {
				$error = "Téves Email-cím és/vagy jelszó!"; // set error message
			}
		}
		$data = ProductDao::loginpageall();
		$twig = $this->setTwigEnvironment();
		echo $twig->render('pages/auth/login.html.twig', ['loginAll' => $data, 'error' => $error]);
	}
	public function LogoutPage()
	{
		User::logout();
		$data = ProductDao::loginpageall();
		$twig = $this->setTwigEnvironment();

		echo $twig->render('pages/auth/login.html.twig', ['loginAll' => $data]);
	}
	public function AdminSignUpPage(Request $req)
	{
		$isAdmin = User::isAdmin();
		$varosok = ProductDao::varosok();
		$error = "";

		$admin = User::isAdmin();



		$twig = (new ProductController())->setTwigEnvironment();


		if ($req->reqMethod == "POST") {
			$body = $req->getBody();
			$email = $body["email"];
			$password = $body["password"];
			$password = hash('sha256', $body["password"]);
			$name = $body["name"];
			$varos = $body["varos"];
			$telefonszam = $body["telefon"];
			$megjegyzes = "";
			$cim = $body["cim"];
			if ($body["admin"]) {
				$admin = 1;
			} else {
				$admin = 0;
			}

			$torolt = 0;

			$result = User::adminsignup($name, $email, $password, $varos, $megjegyzes, $cim, $torolt, $telefonszam, $admin);
			// check if there was an error
			if ($result == "EMAIL_ALREADY_IN_USE") {
				$error = "Az email már létezik"; // set error message
			} elseif ($result == "USER_CREATED") {
			}
		}

		if ($isAdmin == 1) {
			echo $twig->render('admin/adminregister.html.twig', ['varosok' => $varosok, 'error' => $error]);
		} else {
			echo $twig->render('404.html.twig');
		}
	}
	public function SignUpPage(Request $req)
	{

		require 'emailincludes/PHPMailer.php';
		require 'emailincludes/SMTP.php';
		require 'emailincludes/Exception.php';
		$varosok = ProductDao::varosok();
		$error = "";
		$succes = "";

		if (User::isLoggedIn()) {
			return header("Location: /");
		}
		if ($req->reqMethod == "POST") {
			$body = $req->getBody();
			$email = $body["email"];
			$password = $body["password"];
			$password = hash('sha256', $body["password"]);
			$name = $body["name"];
			$varos = $body["varos"];
			$telefonszam = $body["telefon"];
			$megjegyzes = "";
			$cim = $body["cim"];
			$torolt = 0;
			$mail = new PHPMailer();
			$result = User::signup($name, $email, $password, $varos, $megjegyzes, $cim, $torolt, $telefonszam);
			// check if there was an error
			if ($result == "EMAIL_ALREADY_IN_USE") {
				$error = "Az email már létezik"; // set error message
			} elseif ($result == "USER_CREATED") {
				$cart = Cart::getCart();
				$data = $cart->getContents();
				$maindata = ProductDao::mainproductsall();


				$twig = $this->setTwigEnvironment();
				$welcomecontent = $twig->render('welcome.html.twig', ['cart' => $data, 'allproduct' => $maindata, 'vasarlo' => $name, 'varosok' => $varosok]);
				$mail->CharSet = "UTF-8";
				$mail->isSMTP();
				$mail->Host = "smtp.gmail.com";
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = "tls";
				$mail->Port = "587";
				$mail->Username = "boroplug.info@gmail.com";
				$mail->Password = "sdyyhxbtgemcgirf    ";
				$mail->Subject = "Sikeres regisztráció";
				$mail->setFrom('boroplug.info@gmail.com');
				$mail->isHTML(true);

				$mail->addEmbeddedImage('App/Public/template/email-template/images/welcome.jpg', 'welcome', 'welcome.jpg');
				$mail->addEmbeddedImage('App/Public/template/email-template/images/insta.png', 'insta', 'insta.png');

				$mail->Body = $welcomecontent;
				$mail->addAddress($email);
				$mail->send();
				$mail->smtpClose();

				$succes = "Sikeres regisztráció, jelentkezzen be.";
			}
		}

		$twig = $this->setTwigEnvironment();
		$html = $twig->render('pages/auth/register.html.twig', ['varosok' => $varosok, 'error' => $error,'succes' => $succes]);
		echo $html;
	}
}
