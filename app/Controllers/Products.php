<?php

namespace App\Controllers;

use App\Models\productsModel;
use App\Models\unitsModel;
use App\Models\CategoriesModel;

class Products extends BaseController
{
    protected $products;
    protected $units;
    protected $categories;
    public function __construct()
    {
        $this->products = new productsModel();
        $this->units = new unitsModel();
        $this->categories = new CategoriesModel();
        $this->db = db_connect();
    }
    public function index()
    {
        $searchBtn = $this->request->getPost('searchProductBtn');
        if (isset($searchBtn)) {
            $search = $this->request->getPost('searchProduct');
            session()->set('searchProduct', $search);
            redirect()->to('/products');
        } else {
            $search = session()->get('searchProduct');
        }

        $data_products = $search ? $this->products->searchData($search) : $this->products->select(' products.*, categories.name AS category_name, units.name AS unit_name')->join('units', 'units.id=products.unit_id')->join('categories', 'categories.id=products.category_id');

        $pagenumber = $this->request->getVar('page_products') ? $this->request->getVar('page_products') : 1;
        $data = [
            'query' => $data_products->paginate(10, 'page_products'),
            'pager_products' => $data_products->pager,
            'pagenumber' => $pagenumber,
            'search' => $search
        ];
        return view('products/data', $data);
    }

    public function add()
    {
        return view('products/addForm');
    }

    public function fetchDataUnits()
    {
        if ($this->request->isAJAX()) {
            $Data = $this->units->findAll();

            $Value = "<option value='' selected> --- Choose --- </option>";

            foreach ($Data as $row) :
                $Value .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
            endforeach;

            $msg = [
                'data' => $Value
            ];
            echo json_encode($msg);
        }
    }

    public function fetchDataCategories()
    {
        if ($this->request->isAJAX()) {
            $Data = $this->categories->findAll();

            $Value = "<option value='' selected> --- Choose --- </option>";

            foreach ($Data as $row) :
                $Value .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
            endforeach;

            $msg = [
                'data' => $Value
            ];
            echo json_encode($msg);
        }
    }

    public function saveData()
    {
        if ($this->request->isAJAX()) {
            $barcode           = $this->request->getVar('barcode');
            $name              = $this->request->getVar('name');
            $unit              = $this->request->getVar('unit');
            $category          = $this->request->getVar('category');
            $stocks            = str_replace(',', '', $this->request->getVar('stocks'));
            $purchase_price    = str_replace(',', '', $this->request->getVar('purchase_price'));
            $sell_price        = str_replace(',', '', $this->request->getVar('sell_price'));

            $validation = \Config\Services::validation();

            $doValid = $this->validate([
                'barcode' => [
                    'label'  => 'Barcode',
                    'rules'  => 'required|is_unique[products.barcode]',
                    'errors' => [
                        'required' => '{field} Can\'t be Empty',
                        'is_unique' => '{field} Already Existed'
                    ]
                ],
                'name' => [
                    'label'  => 'Name',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Product {field} Can\'t be Empty'
                    ]
                ],
                'unit' => [
                    'label'  => 'Unit',
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => 'Product {field} Can\'t be empty',
                    ]
                ],
                'category' => [
                    'label'  => 'Category',
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => 'Product {field} Can\'t be empty',
                    ]
                ],
                'stocks' => [
                    'label'  => 'Stocks',
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                    ]
                ],
                'purchase_price' => [
                    'label' => 'Purchase Price',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'The {field} field is required.',
                    ],
                ],
                'sell_price' => [
                    'label' => 'Sell Price',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Product {field} Can\'t be Empty',
                    ],
                ],
                'upload_image' => [
                    'label' => 'Image',
                    'rules' => 'mime_in[image,image/png,image/jpeg]|ext_in[image,png,jpg]|is_image[image]',
                ]
            ]);

            if (!$doValid) {
                $msg = [
                    'error' => [
                        'errorBarcode' => $validation->getError('barcode'),
                        'errorName' => $validation->getError('name'),
                        'errorUnit' => $validation->getError('unit'),
                        'errorCategory' => $validation->getError('category'),
                        'errorStocks' => $validation->getError('stocks'),
                        'errorPurchasePrice' => $validation->getError('purchase_price'),
                        'errorSellPrice' => $validation->getError('sell_price'),
                        'errorUploadImage' => $validation->getError('upload_image')
                    ]
                ];
            } else {
                $file_upload = $_FILES['image']['name'];

                if ($file_upload != NULL) {
                    $image_name = "$barcode-$name";
                    $image_file = $this->request->getFile('image');
                    $image_file->move('assets/upload/products/', $image_name . '.' . $image_file->getExtension());

                    $path_image = '/assets/upload/products/' . $image_file->getName();
                } else {
                    $path_image = '';
                }

                $this->products->insert([
                    'barcode' => $barcode,
                    'name' => $name,
                    'unit_id' => $unit,
                    'category_id' => $category,
                    'stocks' => $stocks,
                    'purchase_price' => $purchase_price,
                    'sell_price' => $sell_price,
                    'image' => $path_image
                ]);

                $msg = ['success' => 'Product Successfully Added'];
            }

            echo json_encode($msg);
        }
    }

    public function delete()
    {
        if ($this->request->isAJAX()) {
            $barcode = $this->request->getVar('barcode');
            $rowDataProducts = $this->products->find($barcode);

            if ($rowDataProducts && !empty($rowDataProducts['image'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . $rowDataProducts['image'];

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $this->products->delete($barcode);

            $msg = [
                'success' => 'Product Deleted Successfully'
            ];

            echo json_encode($msg);
        }
    }

    public function edit($barcode)
    {
        $row = $this->products->find($barcode);

        if($row) {
            $data = [
                'barcode'           => $row['barcode'],
                'name'              => $row['name'],
                'product_unit'      => $row['unit_id'],
                'data_unit'         => $this->units->findAll(),
                'product_category'  => $row['category_id'],
                'data_category'     => $this->categories->findAll(),
                'stocks'            => $row['stocks'],
                'purchase_price'    => $row['purchase_price'],
                'sell_price'        => $row['sell_price'],
                'image'             => $row['image']
            ];
            return view('products/editForm', $data);
        } else {
            exit('Data not Found');
        }
    }

    public function updateData()
    {
        if ($this->request->isAJAX()) {
            $barcode           = $this->request->getVar('barcode');
            $name              = $this->request->getVar('name'); 
            $unit              = $this->request->getVar('unit');
            $category          = $this->request->getVar('category');
            $stocks            = str_replace(',', '', $this->request->getVar('stocks'));
            $purchase_price    = str_replace(',', '', $this->request->getVar('purchase_price'));
            $sell_price        = str_replace(',', '', $this->request->getVar('sell_price'));

            $validation = \Config\Services::validation();

            $doValid = $this->validate([
                'name' => [
                    'label'  => 'Name',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Product {field} Can\'t be Empty'
                    ]
                ],
                'stocks' => [
                    'label'  => 'Stocks',
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                    ]
                ],
                'purchase_price' => [
                    'label' => 'Purchase Price',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'The {field} field is required.',
                    ],
                ],
                'sell_price' => [
                    'label' => 'Sell Price',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Product {field} Can\'t be Empty',
                    ],
                ],
                'upload_image' => [
                    'label' => 'Image',
                    'rules' => 'mime_in[image,image/png,image/jpeg]|ext_in[image,png,jpg]|is_image[image]',
                ]
            ]);

            if (!$doValid) {
                $msg = [
                    'error' => [
                        'errorName' => $validation->getError('name'),
                        'errorStocks' => $validation->getError('stocks'),
                        'errorPurchasePrice' => $validation->getError('purchase_price'),
                        'errorSellPrice' => $validation->getError('sell_price'),
                        'errorUploadImage' => $validation->getError('upload_image')
                    ]
                ];
            } else {
                $file_upload = $_FILES['image']['name'];

                $rowDataProducts = $this->products->find($barcode);

                if ($file_upload != NULL) {
                    if ($rowDataProducts && !empty($rowDataProducts['image'])) {
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . $rowDataProducts['image'];

                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                    $image_name = "$barcode-$name";
                    $image_file = $this->request->getFile('image');
                    $image_file->move('assets/upload/products/', $image_name . '.' . $image_file->getExtension());

                    $path_image = '/assets/upload/products/' . $image_file->getName();
                } else {
                    $path_image = $rowDataProducts['image'];
                }

                $this->products->update($barcode,[
                    'barcode' => $barcode,
                    'name' => $name,
                    'unit_id' => $unit,
                    'category_id' => $category,
                    'stocks' => $stocks,
                    'purchase_price' => $purchase_price,
                    'sell_price' => $sell_price,
                    'image' => $path_image
                ]);

                $msg = ['success' => 'Product Successfully Updated'];
            }

            echo json_encode($msg);
        }
    }
}
