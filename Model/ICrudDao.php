<?php namespace App\Model;

    interface ICrudDao
    { 
        
        public static function mainproductsall();
        public static function sizeall();
        public static function loginpageall();
        public static function profilpageall();
        public static function aboutusall();
        public static function forgottenall();
        public static function contactall();
        public static function faqall();
        public static function nikeserachoptionsall();
        public static function adidasserachoptionsall();
        public static function openById(int $id);
        public static function getById(int $id);
        public static function delete();
        public static function update();
        public static function adress();
        public static function save();
        public static function productlistadmin();
        public static function userOrders();
        
       //---------------

      public static function updateAddress();
      public static function profildelete();
      public static function opendelete();

       
    }
?>