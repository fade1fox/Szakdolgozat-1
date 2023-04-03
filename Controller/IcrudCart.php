<?php namespace App\Controller;

    interface IcrudCart
    {
        public function cartList();
        public function ChechkOutList();
        public function cartClear();
        public function checkoutemail();
        public function removeProductById($id);
    }
?>





