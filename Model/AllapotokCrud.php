<?php namespace App\Model;

    interface AllapotokCrud
    { 
        
       
        public static function getById(int $id);
        public static function delete();
        public static function update();
        public static function save();
        public static function productlistadmin();
       
       
    }
?>