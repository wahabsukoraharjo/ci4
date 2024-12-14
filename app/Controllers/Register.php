<?php
namespace App\Controllers;

use App\Models\UserModel;

class Register extends BaseController
{
    public function index()
    {
        return view('auth/register');
    }

    public function create()
    {
        $userModel = new UserModel();
        

        // Validasi input
        $validationRules = [
            'username' => 'required|alpha_numeric|min_length[3]|max_length[50]xss_clean',
            'password' => 'required|min_length[5]|max_length[255]xss_clean'
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);

        $userModel->insert([
        
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ]);
        
        return redirect()->to('/')->with('success', 'Pendaftaran berhasil!');
        echo('test');die;
    }
}
