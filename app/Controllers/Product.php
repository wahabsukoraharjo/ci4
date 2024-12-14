<?php

namespace App\Controllers;
use App\Models\ProductModel;

class Product extends BaseController
{
    public function index() 
    {
        $productModel = new ProductModel;
        $data['products'] = $productModel->findAll();
        return view('v_product', $data);

    }
}
