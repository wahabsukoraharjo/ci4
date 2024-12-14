<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\Controller;

class Products extends BaseController
{
    
  

    public function index()
    {
        $productModel = new ProductModel();
        $data['products'] = $productModel->findAll(); // Mengambil semua data produk
        
        return view('products/index', $data); // Mengirimkan data ke view
    }

    public function create()
    {
        return view('products/create'); // View untuk form tambah produk
    }

    public function store()
    {
        $productModel = new ProductModel();       
        $validationRules = [
            'product_name' => 'required|alpha_numeric_space|min_length[3]|max_length[100]',
            'price' => 'required|numeric|greater_than[0]',
            'description' => 'required|min_length[5]|max_length[500]',
            'category' => 'required|alpha_space|min_length[3]|max_length[50]',
            'product_image' => [
                'rules' => 'uploaded[product_image]|is_image[product_image]|mime_in[product_image,image/jpg,image/jpeg,image/png]|max_size[product_image,2048]',
                'errors' => [
                    'uploaded' => 'Gambar produk harus diunggah.',
                    'is_image' => 'File harus berupa gambar.',
                    'mime_in' => 'Gambar harus dalam format jpg atau png.',
                    'max_size' => 'Ukuran gambar maksimal 2MB.'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            // Jika validasi gagal, kembali ke form dengan error
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Proses upload gambar jika validasi berhasil
        $imagefile = $this->request->getFile('product_image');
        $newName = $imagefile->getRandomName();
        $imagefile->move(WRITEPATH . '../public/uploads', $newName);

        // Simpan data produk ke database
        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'price' => $this->request->getPost('price'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'image' => $newName
        ];

        $productModel->insert($data);
        return redirect()->to('/products')->with('message', 'File berhasil diupload');
       
    }

    
    
    public function edit($id)
    {
        $productModel = new ProductModel();
        $data['product'] = $productModel->find($id); 

        return view('products/edit', $data); // View untuk form edit produk
    }

    public function update($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        $validationRules = [
            'product_name' => 'required|alpha_numeric_space|min_length[3]|max_length[100]',
            'price' => 'required|numeric|greater_than[0]',
            'description' => 'required|min_length[5]|max_length[500]',
            'category' => 'required|alpha_space|min_length[3]|max_length[50]',
            'product_image' => [
                'rules' => 'uploaded[product_image]|is_image[product_image]|mime_in[product_image,image/jpg,image/jpeg,image/png]|max_size[product_image,2048]',
                'errors' => [
                    'uploaded' => 'Gambar produk harus diunggah.',
                    'is_image' => 'File harus berupa gambar.',
                    'mime_in' => 'Gambar harus dalam format jpg atau png.',
                    'max_size' => 'Ukuran gambar maksimal 2MB.'
                ]
            ]
        ];
        if (!$this->validate($validationRules)) {
            // Jika validasi gagal, kembali ke form dengan error
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $imagefile = $this->request->getFile('product_image');
        if ($imagefile && $imagefile->isValid() && !$imagefile->hasMoved()) {
            $newName = $imagefile->getRandomName();
            $imagefile->move(WRITEPATH . '../public/uploads', $newName);
            // Delete old image
            if ($product['image'] && file_exists(WRITEPATH . '../public/uploads' . $product['image'])) {
                unlink(WRITEPATH . '../public/uploads' . $product['image']);
            }
        } else {
            $newName = $product['image']; // Keep old image if no new image uploaded
        }
        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'price' => $this->request->getPost('price'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'image' => $newName
        ];
        $productModel->update($id, $data);
        return redirect()->to('/products');
    }

    public function delete($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);

        // Delete image
        if ($product['image'] && file_exists(WRITEPATH . '../public/uploads' . $product['image'])) {
            unlink(WRITEPATH . '../public/uploads' . $product['image']);
        }

        $productModel->delete($id);
        return redirect()->to('/products');
    }

    public function getProducts()
    {
        $productModel = new ProductModel();
    
        // Get the DataTables parameters
        $request = \Config\Services::request();
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $orderColumn = $request->getPost('order')[0]['column'];
        $orderDirection = $request->getPost('order')[0]['dir'];
    
        // Search and ordering
        $query = $productModel;
        if (!empty($searchValue)) {
            $query->like('product_name', $searchValue)
                  ->orLike('description', $searchValue);
        }
        if ($orderColumn !== null) {
            $columns = ['id', 'product_name', 'price', 'description', 'category', 'image'];
            $query->orderBy($columns[$orderColumn], $orderDirection);
        }
    
        // Total records before filtering
        $totalRecords = $productModel->countAll();
    
        // Total records after filtering
        $totalFilteredRecords = $query->countAllResults(false);
    
        // Pagination
        $products = $query->findAll($length, $start);
    
        // Prepare data for DataTables
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product['id'],
                'product_name' => $product['product_name'],
                'price' => $product['price'],
                'description' => $product['description'],
                'category' => $product['category'],
                'image' => '<img src="/uploads/' . $product['image'] . '" alt="Image" width="50">',
                'action' => '<button class="btn btn-primary btn-sm">Edit</button> 
                             <button class="btn btn-danger btn-sm">Delete</button>'
            ];
        }
    
        return $this->response->setJSON([
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFilteredRecords,
            'data' => $data
        ]);
    }
    
}
