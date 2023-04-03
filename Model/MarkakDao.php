<?php namespace App\Model;

use App\Lib\Database;
use App\Model\ICrudDao;
  
class MarkakDao implements AllapotokCrud
{



public static function all()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $sql = "SELECT `markak`.id,`markak`.markanev FROM markak ORDER BY id;";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }


    public static function getById(int $id)
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $statement = $conn->prepare("SELECT  `markak`.id,`markak`.markanev FROM markak WHERE id =:id;");
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute([
            'id'=>$id,
        ]);
        return $statement->fetch();
    }

        
    public static function save()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
    
        $markanev = $_POST['markanev'];  
    
        $sql = "INSERT INTO allapotok (`markanev`) VALUES (:markanev);";
        $statement = $conn->prepare($sql);
        $statement->execute([
            'markanev'=>$markanev,
       
                
        ]);   
       
          
    }


    
    public static function update()
    { 
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $id = $_POST['id'];
        $markanev = $_POST['markanev'];       
        $sql = "UPDATE allapotok SET `markanev`=:markanev WHERE `id` =:id;";
        try {
            $statement = $conn->prepare($sql);
            $statement->execute([
                'markanev'=>$markanev,     
                'id'=>$id,
            ]);
        } catch (\PDOException $ex) {
            var_dump($ex);
        }
      
    }

  
   
    public static function delete()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $id = $_POST['id'];
        $sql = "DELETE FROM  allapot  WHERE  `id`=:id;";
        
            $statement = $conn->prepare($sql);
            $statement->execute([
                'id'=>$id,
            ]);
        
    }

        public static function mainproductsall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT cipo.markanev, cipo.id, markak.markanev, ar, allapotok.markanev AS 'allapot' ,cipo.fnev FROM cipo INNER JOIN allapotok ON allapot_id = allapotok.id INNER JOIN markak ON marka_id = markak.id  LIMIT 9  ";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }

    
    public static function adidasproductslistall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT cipo.markanev, cipo.id, markak.markanev, ar, allapotok.markanev AS 'allapot' ,cipo.fnev FROM cipo INNER JOIN allapotok ON allapot_id = allapotok.id INNER JOIN markak ON marka_id = markak.id WHERE markak.id = 2 LIMIT 4  ";
        
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function nikeproductslistall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT cipo.markanev, cipo.id, markak.markanev, ar, 
        allapotok.markanev AS 'allapot' ,cipo.fnev FROM cipo INNER JOIN allapotok 
        ON allapot_id = allapotok.id INNER JOIN markak ON marka_id = markak.id 
        WHERE markak.id = 1 LIMIT 4  ";
        
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
    public static function loginpageall()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT * FROM vasarlo INNER JOIN varosok ON varos_id = varosok.id INNER JOIN megyek ON megye_id = megyek.id";
        
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }

    public static function productlistadmin()
    {
        $dbObj = new Database();
        $conn = $dbObj->getConnection();

        $sql = "SELECT  cipo.id, cipo.fnev, cipo.markanev, markak.markanev,cipo.meret ,cipo.ar, cipo.mennyiseg ,cipo.deleted FROM cipo INNER JOIN allapotok ON allapot_id = allapotok.id INNER JOIN markak ON marka_id = markak.id   ";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute();
        return $statement->fetchAll();
    }
}
