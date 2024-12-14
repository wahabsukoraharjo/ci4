namespace App\Controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use App\Models\ProductModel;

class Export extends BaseController {
    public function exportExcel() {
        $productModel = new ProductModel();
        $products = $productModel->findAll();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Product Name');
        $sheet->setCellValue('C1', 'Price');
        $sheet->setCellValue('D1', 'Description');
        $sheet->setCellValue('E1', 'Category');

        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product['id']);
            $sheet->setCellValue('B' . $row, $product['product_name']);
            $sheet->setCellValue('C' . $row, $product['price']);
            $sheet->setCellValue('D' . $row, $product['description']);
            $sheet->setCellValue('E' . $row, $product['category']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'products.xlsx';
        $writer->save($fileName);
        return $this->response->download($fileName, null)->setFileName($fileName);
    }

    public function exportPDF() {
        $productModel = new ProductModel();
        $products = $productModel->findAll();

        $dompdf = new Dompdf();
        $html = view('export/pdf', ['products' => $products]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("products.pdf", ["Attachment" => 0]);
    }
}
