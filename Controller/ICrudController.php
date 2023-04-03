<?php namespace App\Controller;

    interface ICrudController
    {
        public function mainproductslist();
        public function sizelist();
        public function adidasproductslist(); 
        public function nikeproductslist();
        public function login();
        public function userordersList();
        public function profil();
        public function forgotten();
        public function aboutus();
        public function contact();
        public function faq();
        public function openByIdAll(int $id);
        public function productlistadmin();
        public function delete();
        public function deleteById(int $id);
        public function editById(int $id);
        public function DeleteUserById(int $id);
        public function update();
        public function updateAdress();
        public function profilDelete();
        public function openDelete();
        public function add();
        public function save();
        public function adminalluserlist();
        public function userDelete();
        public function ordergetbyid(int $id);
        public function diagrams();
    }
