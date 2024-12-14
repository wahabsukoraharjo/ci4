<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
         // Cek apakah user sudah login
         if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        return view('welcome_message');
    }
	
}
