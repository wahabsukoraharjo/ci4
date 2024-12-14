<?php
namespace App\Controllers;
use App\Models\UserModel;
class Logins extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }
    public function auth()
    {
        $session = session();
        $model = new UserModel();
        
        // Ambil input dari form
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        
        // Cari pengguna berdasarkan email
        $user = $model->where('email', $email)->first();
    
        // Validasi apakah pengguna ditemukan
        if ($user && password_verify($password, $user['password'])) {
            // Data untuk session
            $sessionData = [
                'username'   => $user['username'],
                'email'      => $user['email'],
                'isLoggedIn' => true
            ];
            $session->set($sessionData);
    
            // Set Cookie (opsional)
            setcookie('user_email', $user['email'], time() + (86400 * 30), "/"); // 1 hari
            return redirect()->to('/home');
        } else {
            // Redirect kembali jika gagal
            return redirect()->back()->with('error', 'Email atau Password salah');
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