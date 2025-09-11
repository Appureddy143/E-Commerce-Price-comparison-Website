<?php
session_start();
require 'vendor/autoload.php'; // Load the PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Security: Check if the user is logged in and is an admin.
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Check if the request is a POST request (i.e., the form was submitted).
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- Step 1: Sanitize and Validate Form Data ---
    $productName = trim($_POST['product_name']);
    $store = trim($_POST['store']);
    $price = trim($_POST['price']);
    $url = trim($_POST['url']);
    $imageUrl = trim($_POST['image_url']);
    $description = trim($_POST['description']);
    
    // Simple validation to ensure required fields are not empty.
    if (empty($productName) || empty($store) || empty($price) || empty($url) || empty($imageUrl) || empty($description)) {
        $_SESSION['message'] = "All fields are required. Please fill out the entire form.";
        $_SESSION['message_type'] = 'error';
        // **UPDATED:** Redirect back to the new form page.
        header('Location: add_product.php');
        exit;
    }
    
    // --- Step 2: Write the New Data to the Excel File ---
    $filePath = 'prices.xlsx';
    
    try {
        // Load the existing spreadsheet.
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Find the first empty row to append the new data.
        $newRow = $sheet->getHighestRow() + 1;
        
        // **CORRECTION:** Writing data to the correct columns (B, C, D, E, F, G).
        // We leave column A empty to match the existing format.
        $sheet->setCellValue('B' . $newRow, $productName);
        $sheet->setCellValue('C' . $newRow, $store);
        $sheet->setCellValue('D' . $newRow, $price);
        $sheet->setCellValue('E' . $newRow, $url);
        $sheet->setCellValue('F' . $newRow, $imageUrl);
        $sheet->setCellValue('G' . $newRow, $description);
        
        // Create a writer object to save the changes.
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        
        // --- Step 3: Set Success Message and Redirect ---
        $_SESSION['message'] = "Product price for '" . htmlspecialchars($productName) . "' has been added successfully!";
        $_SESSION['message_type'] = 'success';
        
    } catch (Exception $e) {
        // Handle potential errors, such as file permission issues.
        $_SESSION['message'] = "An error occurred while writing to the Excel file: " . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
    
    // **UPDATED:** Always redirect back to the new form page.
    header('Location: add_product.php');
    exit;
} else {
    // If someone tries to access this page directly, redirect them.
    header('Location: admin_panel.php');
    exit;
}
?>

    