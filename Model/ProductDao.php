<?php

namespace App\Model;

use App\Lib\Database;
use App\Model\ICrudDao;
use PDO;

//error_reporting(0);
class ProductDao implements ICrudDao
{

    public static function mainproductsall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT cipo.megnevezes, COUNT(*) AS darab, SUM(cipo.elerheto) 
        AS osszes_elerheto, cipo.id, cipo.torolt, markak.markanev, cipo.marka_id, ar, allapotok.megnevezes
         AS 'allapot', cipo.fnev, GROUP_CONCAT(DISTINCT meret SEPARATOR ',') AS meretek
        FROM cipo 
        INNER JOIN allapotok ON allapot_id = allapotok.id 
        INNER JOIN markak ON marka_id = markak.id 
        GROUP BY cipo.megnevezes ";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        $sorok = $statement->fetchAll();

        $n = count($sorok);
        for ($i = 0; $i < $n; $i++) {
            $m = $sorok[$i]->meretek;
            $m = explode(",", $m);
            sort($m);
            $m = implode(",", $m);
            $sorok[$i]->meretek = $m;
        }

        return $sorok;
    }
    public static function sizeall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT cipo.id, cipo.torolt, cipo.megnevezes, cipo.meret, markak.markanev, cipo.marka_id, ar, allapotok.megnevezes AS 'allapot', cipo.fnev
        FROM cipo 
        INNER JOIN allapotok ON allapot_id = allapotok.id 
        INNER JOIN markak ON marka_id = markak.id 
        
        ";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }




    public static function loginpageall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $user = User::getUser();


        if (!empty($user->id)) {
            $sql = "SELECT * FROM vasarlo 
                    INNER JOIN varosok 
                    ON varos_id = varosok.id 
                    INNER JOIN megyek 
                    ON megye_id = megyek.id WHERE vasarlo.id = ?";
            $params = [$user->id];
        } else {
            $sql = "SELECT * FROM vasarlo 
                    INNER JOIN varosok 
                    ON varos_id = varosok.id 
                    INNER JOIN megyek 
                    ON megye_id = megyek.id";
            $params = [];
        }

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    //----------------------------User Dashbord----------------------------------------------------------------------
    public static function profilpageall()
    {

        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $user = User::getUser();


        if (!empty($user->id)) {
            $sql = "SELECT * FROM vasarlo 
                    INNER JOIN varosok 
                    ON varos_id = varosok.id 
                    INNER JOIN megyek 
                    ON megye_id = megyek.id WHERE vasarlo.id = ?";
            $params = [$user->id];
        } else {
            $sql = "SELECT * FROM vasarlo 
                    INNER JOIN varosok 
                    ON varos_id = varosok.id 
                    INNER JOIN megyek 
                    ON megye_id = megyek.id WHERE megyek.id = 0";
            $params = [];
        }

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute($params);
        return $statement->fetchAll();
    }
    public static function userOrders()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $user = User::getUser();


        if (!empty($user->id)) {
            $sql = "SELECT `vasarlasok`.id, `vasarlasok`.mikor, `fizetesimodok`.megnevezes, `vasarlasok`.megjegyzes, `vasarlasok`.cim, `vasarlasok`.allapot, `vasarlasok`.vasarlok_id, 
            SUM(vasarlas_tetelek.ar) 
            AS osszar,
             vasarlas_tetelek.cipo_id, GROUP_CONCAT(DISTINCT cipo.megnevezes SEPARATOR '\n') AS termekek FROM `vasarlasok` INNER JOIN `vasarlo` ON `vasarlasok`.`vasarlok_id` = `vasarlo`.`id` INNER JOIN `fizetesimodok` ON `vasarlasok`.`fizetesimod_id` = `fizetesimodok`.`id` INNER JOIN vasarlas_tetelek on vasarlasok_id = vasarlasok.id INNER JOIN cipo on cipo.id = vasarlas_tetelek.cipo_id WHERE vasarlasok.vasarlok_id = ? GROUP BY vasarlas_tetelek.vasarlasok_id ";

            $params = [$user->id];
        } else {
            $sql = "SELECT `vasarlasok`.id, `vasarlasok`.mikor, `fizetesimodok`.megnevezes, `vasarlasok`.megjegyzes, `vasarlasok`.cim, `vasarlasok`.allapot, `vasarlasok`.vasarlok_id, 
        SUM(vasarlas_tetelek.ar) 
        AS osszar,
         vasarlas_tetelek.cipo_id, GROUP_CONCAT(DISTINCT cipo.megnevezes SEPARATOR '\n') AS termekek FROM `vasarlasok` INNER JOIN `vasarlo` ON `vasarlasok`.`vasarlok_id` = `vasarlo`.`id` INNER JOIN `fizetesimodok` ON `vasarlasok`.`fizetesimod_id` = `fizetesimodok`.`id` INNER JOIN vasarlas_tetelek on vasarlasok_id = vasarlasok.id INNER JOIN cipo on cipo.id = vasarlas_tetelek.cipo_id WHERE vasarlasok.vasarlok_id = 0 GROUP BY vasarlas_tetelek.vasarlasok_id ";


            $params = [];
        }



        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public static function adress()
    {
        $user = User::getUser();
        $dbObj = new Database();
        $conn = $dbObj->getConnection();




        if (!empty($user->id)) {
            $sql = "SELECT * FROM vasarlo INNER JOIN varosok ON vasarlo.varos_id = varosok.id WHERE vasarlo.id = ?";
            $params = [$user->id];
        } else {
            $sql = "SELECT * FROM vasarlo INNER JOIN varosok ON vasarlo.varos_id = varosok.id WHERE vasarlo.id = 0";
            $params = [];
        }


        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute($params);
        $adatok = $statement->fetchAll();

        return $adatok;
    }

    //------------------edit------------------------


    public static function updateAddress()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $user = User::getUser();

        $id = $user->id;
        $telefonszam = $_POST['telefonszam'];
        $cim = $_POST['cim'];
        $varos_id = $_POST['varos'];


        $sql = "UPDATE vasarlo SET telefonszam=:telefonszam, cim=:cim, varos_id=:varos_id WHERE id= :id ";

        try {
            $statement = $conn->prepare($sql);
            $statement->execute([
                'telefonszam' => $telefonszam,
                'cim' => $cim,
                'varos_id' => $varos_id,
                'id' => $id
            ]);
        } catch (\PDOException $ex) {
            var_dump($ex);
        }
    }
    //----------------delete profil-------------------------
    public static function opendelete()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $user = User::getUser();
        $sql = " SELECT * FROM cipo WHERE 1 = 0";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }

    public static function profildelete()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $user = User::getUser();

        $id = $user->id;
        $f = fopen("asd.txt", "w");
        fwrite($f, $id);
        fclose($f);

        $sql = "UPDATE vasarlo SET vasarlo.torolt=1, vasarlo.email = '' WHERE `id`=:id;";


        $statement = $conn->prepare($sql);
        $statement->execute([
            'id' => $id,
        ]);
    }


    //-------------------------------------------------------------------------------------------------------------------
    public static function openById(int $id)
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $statement = $conn->prepare("SELECT cipo.megnevezes, cipo.id, markak.markanev, cipo.meret, ar, GROUP_CONCAT(DISTINCT meret SEPARATOR ',') AS meretek, cipo.leiras, cipo.elerheto,  allapotok.megnevezes
        AS 'allapot',cipo.fnev ,cipo.fnev2 ,cipo.fnev3 ,cipo.fnev4 ,cipo.fnev5
        FROM cipo 
        INNER JOIN allapotok 
        ON allapot_id = allapotok.id 
        INNER JOIN markak ON marka_id = markak.id 
        WHERE cipo.id =:id ");
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute([
            'id' => $id,

        ]);
        return $statement->fetch();
    }
    public static function nikeserachoptionsall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT cipo.megnevezes,cipo.ar,cipo.meret,cipo.marka_id, allapotok.megnevezes
         AS 'allapot' 
         
         FROM cipo 
         INNER JOIN allapotok 
         ON allapot_id = allapotok.id 
         INNER JOIN markak 
         ON marka_id = markak.id 
         WHERE cipo.marka_id = 1 
         GROUP BY meret ";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function adidasserachoptionsall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT cipo.megnevezes,cipo.ar,cipo.meret,cipo.marka_id, allapotok.megnevezes 
        AS 'allapot' 
        
        FROM cipo 
        INNER JOIN allapotok 
        ON allapot_id = allapotok.id 
        INNER JOIN markak 
        ON marka_id = markak.id  
        WHERE cipo.marka_id = 2 
        GROUP BY meret ";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function aboutusall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = " SELECT * FROM cipo WHERE 1 = 0";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function forgottenall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = " SELECT * FROM cipo WHERE 0 = 1";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function contactall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = " SELECT * FROM cipo WHERE 2 = 1";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function faqall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = " SELECT * FROM cipo WHERE 1 = 2";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }






    //----Szabó Dávid--------------------------------------------------------------------------------------------------------------------------------------------------------------->
    public static function getProductsByIds($ids)
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $sql = "SELECT * FROM boroplug.cipo WHERE id IN " . "(" . implode(", ", $ids) . ")";
        $statement = $conn->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }
    //----------------------Misnyovszk //


    public static function productlistadmin()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT  cipo.id, cipo.fnev, cipo.megnevezes, markak.markanev,cipo.meret ,cipo.ar, cipo.elerheto ,cipo.torolt,
        cipo.fnev2, cipo.fnev3, cipo.fnev4, cipo.fnev5
        FROM cipo INNER JOIN allapotok ON allapot_id = allapotok.id INNER JOIN markak ON marka_id = markak.id  ORDER BY cipo.id;  ";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }

    public static function getById(int $id)
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $statement = $conn->prepare("SELECT * FROM cipo WHERE id =:id;");
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute([
            'id' => $id,
        ]);
        return $statement->fetch();
    }

    public static function delete()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $id = $_POST['id'];
        $sql = "UPDATE `cipo` SET `torolt` = 1 WHERE `id` =:id;";
        try {
            $statement = $conn->prepare($sql);
            $statement->execute([
                'id' => $id,
            ]);
        } catch (\PDOException $ex) {
            var_dump($ex);
        }
    }



    public static function update()
    {



        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $id = $_POST['id'];

        $sql = "SELECT fnev, fnev2, fnev3, fnev4, fnev5 FROM cipo WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $fElso = $result['fnev'];
        $fMasodik = $result['fnev2'];
        $fHarmadik = $result['fnev3'];
        $fNegyedik = $result['fnev4'];
        $fOtodik = $result['fnev5'];




        $megnevezes = $_POST['megnevezes'];
        $ar = $_POST['ar'];
        $cipoallapot = $_POST['cipoallapot'];
        $marka = $_POST['marka'];
        $meret = $_POST['meret'];
        $elerheto = $_POST['elerheto'];

        $fnev = $_POST['fnev'];
        $fnev2 = $_POST['fnev2'];
        $fnev3 = $_POST['fnev3'];
        $fnev4 = $_POST['fnev4'];
        $fnev5 = $_POST['fnev5'];
        if ($fnev == "") {
            $fnev = $fElso;
        }
        echo "<h2>" . $fnev . "</h2>";
        if ($fnev2 == "") {
            $fnev2 = $fMasodik;
        }
        if ($fnev3 == "") {
            $fnev3 = $fHarmadik;
        }
        if ($fnev4 == "") {
            $fnev4 = $fNegyedik;
        }
        if ($fnev5 == "") {
            $fnev5 = $fOtodik;
        }




        $torolt = isset($_POST['torolt']) ? 1 : 0;
        $sql =  $sql = "UPDATE cipo SET 
                `megnevezes`=:megnevezes, 
                `ar`=:ar,
                `allapot_id`=:cipoallapot,
                `marka_id`=:marka,
                `meret`=:meret,
                `elerheto`=:elerheto,
                `fnev`=:fnev,
                `fnev2`=:fnev2,
                `fnev3`=:fnev3,
                `fnev4`=:fnev4,
                `fnev5`=:fnev5,
                `torolt`=:torolt
                WHERE `id` =:id;";
        try {
            $statement = $conn->prepare($sql);
            $statement->execute([
                'megnevezes' => $megnevezes,
                'ar' => $ar,
                'cipoallapot' => $cipoallapot,
                'marka' => $marka,
                'meret' => $meret,
                'elerheto' => $elerheto,
                'fnev' => $fnev,
                'fnev2' => $fnev2,
                'fnev3' => $fnev3,
                'fnev4' => $fnev4,
                'fnev5' => $fnev5,

                'torolt' => $torolt,
                'id' => $id
            ]);
        } catch (\PDOException $ex) {
            var_dump($ex);
        }
    }
    public function resize($kep, $target_name)
    {
        $image_name = $_FILES[$kep]['name'];
        $image_tmp = $_FILES[$kep]['tmp_name'];
        $image_size = $_FILES[$kep]['size'];

        // Check if file is an image
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif');
        $img_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        if (in_array($img_extension, $valid_extensions)) {

            // Resize image
            $target_width = 1536;
            $target_height = 2048;
            $image = imagecreatefromstring(file_get_contents($image_tmp));
            $image_resized = imagescale($image, $target_width, $target_height);

            // Save image to directory
            $target_dir = "App/Public/template/front-end/assets/images/shoes/product/";
            $target_file = $target_dir . $target_name;
            imagejpeg($image_resized, $target_file);

            // Free up memory
            imagedestroy($image);
            imagedestroy($image_resized);
        }
    }


    public static function save()
    {

        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $megnevezes = $_POST['megnevezes'];
        $ar = $_POST['ar'];
        $cipoallapot = $_POST['cipoallapot'];
        $marka = $_POST['marka'];
        $meret = $_POST['meret'];
        $elerheto = $_POST['elerheto'];
        $fnev = "";
        $fnev2  = "";
        $fnev3 = "";
        $fnev4 = "";
        $fnev5 = "";
       

        // Resize images and save them
        $target_name = $megnevezes . ".jpg";
        ProductDao::resize("fnev", $target_name);
        $target_name = $megnevezes . "1.jpg";
        ProductDao::resize("fnev2", $target_name);
        $target_name = $megnevezes . "2.jpg";
        ProductDao::resize("fnev3", $target_name);
        $target_name = $megnevezes . "3.jpg";
        ProductDao::resize("fnev4", $target_name);
        $target_name = $megnevezes . "4.jpg";
        ProductDao::resize("fnev5", $target_name);



        $fnev = $_FILES['fnev']['name'];
        $leiras = $_POST['leiras'];
        $fnev2 = $_FILES['fnev2']['name'];
        $fnev3 = $_FILES['fnev3']['name'];
        $fnev4 = $_FILES['fnev4']['name'];
        $fnev5 = $_FILES['fnev5']['name'];
        $torolt = isset($_POST['torolt']) ? 0 : 1;






        $sql = "INSERT INTO `cipo` (`megnevezes`,`ar`,`allapot_id`,`marka_id`,`meret`
        ,`elerheto`,`fnev`,`leiras`,`fnev2`,`fnev3`,`fnev4`,`fnev5`,`torolt`)
                VALUES (:megnevezes, :ar, :cipoallapot,
                 :marka, :meret,
                  :elerheto, :fnev, :leiras,
                  :fnev2, :fnev3, :fnev4, :fnev5, :torolt
                  );";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);

        $statement->execute([
            'megnevezes' => $megnevezes,
            'ar' => $ar,
            'cipoallapot' => $cipoallapot,
            'marka' => $marka,
            'meret' => $meret,
            'elerheto' => $elerheto,
            'fnev' => $fnev,
            'leiras' => $leiras,
            'fnev2' => $fnev2,
            'fnev3' => $fnev3,
            'fnev4' => $fnev4,
            'fnev5' => $fnev5,
            'torolt' => $torolt
        ]);
    }


    public static function adminalluserlist()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT `vasarlo`.id,`vasarlo`.nev,`vasarlo`.email,`vasarlo`.varos_id,`vasarlo`.cim , `varosok`.varosneve, `vasarlo`. torolt
        FROM `vasarlo`  INNER JOIN `varosok`
        ON `vasarlo`.`varos_id` = `varosok`.`id` ;";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function userDelete()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $id = $_POST['id'];
        $email = $_POST['email'];
        $sql = "UPDATE `vasarlo` SET `torolt` = 1 WHERE `id` =:id;";





        $subject = "torolt felhasználó";
        $txt = "A felhasználó törölve lett a boroplug rendszeréből mert túl nagy kurva\n Igen te!";
        $headers = "From: webmaster@example.com" . "\r\n" .
            "CC: somebodyelse@example.com";

        mail($email, $subject, $txt, $headers);






        try {
            $statement = $conn->prepare($sql);
            $statement->execute([
                'id' => $id,
            ]);
        } catch (\PDOException $ex) {
            var_dump($ex);
        }
    }
    public static function UsergetById(int $id)
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $statement = $conn->prepare("SELECT * FROM vasarlo WHERE id =:id;");
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute([
            'id' => $id,
        ]);
        return $statement->fetch();
    }




    public static function adminorderlist()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT `vasarlasok`.id,`vasarlo`.nev,`vasarlo`.email,`vasarlasok`.mikor,`vasarlo`.cim ,
            `fizetesimodok`.megnevezes, `vasarlasok`. megjegyzes,`vasarlasok`.cim, `vasarlasok`.allapot, `vasarlasok`.vasarlok_id
           FROM `vasarlasok`  INNER JOIN `vasarlo`
           ON `vasarlasok`.`vasarlok_id` = `vasarlo`.`id` 
           INNER JOIN `fizetesimodok`
           ON `vasarlasok`.`fizetesimod_id` = `fizetesimodok`.`id`
           ORDER BY `vasarlasok` . id     ";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function ordergetbyid(int $id)
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $statement = $conn->prepare("SELECT `vasarlasok`.id,`vasarlo`.nev,`vasarlo`.email,`vasarlasok`.mikor,`vasarlo`.cim ,
        `fizetesimodok`.megnevezes, `vasarlasok`. megjegyzes,`vasarlasok`.cim, `vasarlasok`.allapot
       FROM `vasarlasok`  INNER JOIN `vasarlo`
       ON `vasarlasok`.`vasarlok_id` = `vasarlo`.`id` 
       INNER JOIN `fizetesimodok`
       ON `vasarlasok`.`fizetesimod_id` = `fizetesimodok`.`id` WHERE `vasarlasok`.id =:id;");
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute([
            'id' => $id,
        ]);
        return $statement->fetch();
    }



    public static function orderupdate()
    {

        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $id = $_POST['id'];

        $nev = $_POST['nev'];
        $rendelesallapot = $_POST['rendelesallapot'];
        $email = $_POST['email'];
        $fizetesimod = $_POST['fizetesimod'];
        $mikor = $_POST['mikor'];
        $megjegyzes = $_POST['megjegyzes'];


        $sql = "UPDATE vasarlasok
            INNER JOIN `vasarlo`
                ON `vasarlasok`.`vasarlok_id` = `vasarlo`.`id` 
                INNER JOIN `fizetesimodok`
                ON `vasarlasok`.`fizetesimod_id` = `fizetesimodok`.`id`

            SET 
            `nev`=:nev, 
            `allapot`=:rendelesallapot,
            `email`=:email,
            `fizetesimod_id`=:fizetesimod,
            `mikor`=:mikor,
            `vasarlasok`.megjegyzes =:megjegyzes
            WHERE `vasarlasok`.id=:id";
        try {
            $statement = $conn->prepare($sql);
            $statement->execute([
                'nev' => $nev,
                'rendelesallapot' => $rendelesallapot,
                'email' => $email,
                'fizetesimod' => $fizetesimod,
                'mikor' => $mikor,
                'megjegyzes' => $megjegyzes,
                'id' => $id
            ]);
        } catch (\PDOException $ex) {
            var_dump($ex);
        }
    }




    public static function fizetesimodokall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $sql = "SELECT `fizetesimodok`.id,`fizetesimodok`.megnevezes FROM fizetesimodok ORDER BY id;";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }




    public static function felhasznalok()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $felhasznalok = "";

        $sql = "SELECT COUNT(id) as felhasznalok FROM vasarlo;        ";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }

    public static function rendelesek()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $rendelesek = "";

        $sql = "SELECT COUNT(id) as rendelesek FROM vasarlasok;        ";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }


    public static function havibevetel()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $maidatum = new \DateTime();
        $elmulthonap = new \DateTime('-30 days');


        $maidatum = $maidatum->format('Y-m-d H:i:s');
        $elmulthonap = $elmulthonap->format('Y-m-d H:i:s');

        $sql = "SELECT `vasarlasok`.mikor, SUM(vasarlas_tetelek.ar * vasarlas_tetelek.mennyiseg) AS osszesen
            FROM `vasarlasok` 
            INNER JOIN `vasarlas_tetelek` ON `vasarlasok`.`id` = `vasarlas_tetelek`.`vasarlasok_id` 
            WHERE `vasarlasok`.mikor BETWEEN '$elmulthonap' and '$maidatum' ";



        $statement = $conn->prepare($sql);

        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }

    public static function piechart()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();


        //utanvetelek szama;
        $sql = "SELECT * FROM vasarlasok where fizetesimod_id = '1';";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $utanvetelekszam = $statement->rowcount();

        //banki atutalas szama;
        $sql = "SELECT * FROM vasarlasok where fizetesimod_id = '2';";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $bankiatutalasszama = $statement->rowcount();

        //onlinefizetes szama;
        $sql = "SELECT * FROM vasarlasok where fizetesimod_id = '3';";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $onlinefizetesszama = $statement->rowcount();

        $fizetesimodok = [];
        $fizetesimodok["utanvetel"] = $utanvetelekszam;
        $fizetesimodok["bankiatutalas"] = $bankiatutalasszama;
        $fizetesimodok["onlinefizetes"] = $onlinefizetesszama;

        return $fizetesimodok;
    }




    public static function barchart()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();


        // megyenkenti rendelesek sql

        $sql = "Select m.megye, COUNT(a.id) as darab FROM megyek as m INNER join varosok as v on v.megye_id = m.id left join vasarlasok as a on a.varos_id = v.id GROUP BY m.id;";
        $statement = $conn->prepare($sql);
        $statement->execute();
        return  json_encode($statement->fetchAll());
    }
    public static function registerpage()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $user = User::getUser();


        if (!empty($user->id)) {
            $sql = "SELECT * FROM vasarlo 
                    INNER JOIN varosok 
                    ON varos_id = varosok.id 
                    INNER JOIN megyek 
                    ON megye_id = megyek.id WHERE vasarlo.id = ?";
            $params = [$user->id];
        } else {
            $sql = "SELECT * FROM vasarlo 
                    INNER JOIN varosok 
                    ON varos_id = varosok.id 
                    INNER JOIN megyek 
                    ON megye_id = megyek.id";
            $params = [];
        }

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute($params);
        return $statement->fetchAll();
    }
    public static function varosok()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $sql = "SELECT `varosok`.id,`varosok`.varosneve FROM varosok GROUP BY varosneve ORDER BY varosneve;";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }




    public function updateStock($productId, $maradek, $elerheto)
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $user = User::getUser();
        $cart = Cart::getCart();
        $data = $cart->getContents();
        $megrendeloid = $user->id;

        $sql = "INSERT INTO vasarlasok (vasarlok_id, mikor, varos_id, fizetesimod_id, megjegyzes, cim, allapot)
                VALUES (:vasarlok_id, :mikor, :varos_id, :fizetesimod_id, :megjegyzes, :cim, :allapot)";
        $statement = $conn->prepare($sql);
        $statement->execute([
            "vasarlok_id" => $megrendeloid,
            "mikor" => date('Y-m-d H:i:s'),
            "varos_id" => $_POST['varos'],
            "fizetesimod_id" => $_POST['fizetesimod_id'],
            "megjegyzes" => $_POST['megjegyzes'],
            "cim" => $_POST['cim'],
            "allapot" => 'Megrendelt'
        ]);

        $lastInsertId = $conn->lastInsertId();
        foreach ($data as $item) {
            $rendelendo = $item['kosarMennyiseg'];
            $ar = $rendelendo * $item['ar'];
            if ($rendelendo > $elerheto) {
                $checkresult = "A termék/termékek már nem elérhető(ek)";
            } elseif ($rendelendo <= 0) {
                $error = "A kosaradban nincs egy termék se.";
                echo "<div class='alert alert-danger'>$error</div>";
            } else {
                $sql = "INSERT INTO vasarlas_tetelek (vasarlasok_id, cipo_id, ar, mennyiseg)
                        VALUES (:vasarlasok_id, :cipo_id, :ar, :mennyiseg)";

                $statement = $conn->prepare($sql);
                $statement->execute([
                    "vasarlasok_id" => $lastInsertId,
                    "cipo_id" => $item['id'],
                    "ar" => $ar,
                    "mennyiseg" => $rendelendo
                ]);
            }

            $sql = "UPDATE cipo SET elerheto = :elerheto WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':elerheto', $maradek, PDO::PARAM_INT);
            $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
        }
    }  
    public static function lastFiveOrder()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT `vasarlasok`.id,`vasarlo`.nev,`vasarlo`.email , `vasarlo`.cim ,
            `fizetesimodok`.megnevezes, `vasarlasok`. megjegyzes,`vasarlasok`.cim, `vasarlasok`.allapot, `vasarlasok`.vasarlok_id
           FROM `vasarlasok`  INNER JOIN `vasarlo`
           ON `vasarlasok`.`vasarlok_id` = `vasarlo`.`id` 
           INNER JOIN `fizetesimodok`
           ON `vasarlasok`.`fizetesimod_id` = `fizetesimodok`.`id`
           ORDER BY `vasarlasok`.mikor DESC
           LIMIT 5;  ";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
}
