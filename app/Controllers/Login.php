<?php
namespace App\Controllers;
class Login extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function auth()
    {
        $session = session();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Validasi username dan password statis
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Username atau Password salah'); 
          
        } else {
           
            // Set session untuk login
            session()->set('logged_in', true);
            session()->set('id', $user['id']);
            session()->set('username', $user['email']);

            // Simpan username ke cookies (kedaluwarsa 1 hari)
            setcookie('email', $email, time() + (120), "/"); // 1 hari
            return redirect()->to('/home');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();

        // Hapus cookies
        setcookie('email', '', time() - 120, "/");

        return redirect()->to('/');
    }
}