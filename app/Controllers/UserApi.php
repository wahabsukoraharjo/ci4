<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class UserApi extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';
    
    public function index() // GET /users
    {
        $users = $this->model->findAll();
        return $this->respond($users, 200);
    }

    public function create() // POST /users
    {
        $data = $this->request->getPost();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]'
        ]);

        if (!$validation->run($data)) { return $this->fail($validation->getErrors(), 400); }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT); // Hash password
        
        $this->model->save($data); // Simpan data
        return $this->respondCreated([
            'status' => 'success',
            'message' => 'User created successfully.'
        ]);
    }
   
    public function delete($id = null)  // DELETE /users/{id}
    {
        $user = $this->model->find($id);

        if (!$user) {
            return $this->failNotFound('User not found.');
        }
        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'User deleted successfully.']);
    }
}
