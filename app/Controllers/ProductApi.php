<?php
namespace App\Controllers;
use App\Models\ProductModel;
use CodeIgniter\RESTful\ResourceController;
class ProductApi extends ResourceController
{
    protected $modelName = 'App\Models\ProductModel';
    protected $format    = 'json';
    public function index() // GET /products
    {
        $products = $this->model->findAll();
        return $this->respond($products, 200);
    }
    public function create()   // POST /products
    {
        $data = $this->request->getPost();
        $validation = \Config\Services::validation();  // Validasi input
        $validation->setRules([
            'product_name' => 'required|min_length[3]|max_length[255]',
            'price'        => 'required|decimal',
            'description'  => 'required|min_length[10]',
            'category'     => 'required|min_length[3]|max_length[100]',
            'image'        => 'uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/png,image/jpeg]'
        ]);
        if (!$validation->run($data)) {
            return $this->fail($validation->getErrors(), 400);
        }
        $imageFile = $this->request->getFile('image');       // Upload gambar
        if ($imageFile->isValid() && !$imageFile->hasMoved()) {
            $imageName = $imageFile->getRandomName();
            $imageFile->move(WRITEPATH . 'uploads', $imageName);
            $data['image'] = $imageName;
        }
        $this->model->save($data);
        return $this->respondCreated(['message' => 'Product created successfully.']);
    }
    public function delete($id = null) // DELETE /products/{id}
    {
        $product = $this->model->find($id);
        if (!$product) {
            return $this->failNotFound('Product not found.');
        }
        if (file_exists(WRITEPATH . 'uploads/' . $product['image'])) { // Hapus gambar
            unlink(WRITEPATH . 'uploads/' . $product['image']);
        }
        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Product deleted successfully.']);
    }}
