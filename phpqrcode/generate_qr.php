<?php
/**
 * QR Code Generator Script
 * Uses local PHP GD library - no external APIs
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include QR code library
require_once 'phpqrcode/QRCode.php';

// Get form data
$qrText = $_POST['qrText'] ?? '';
$size = $_POST['size'] ?? 300;
$margin = $_POST['margin'] ?? 10;
$filenameInput = $_POST['filename'] ?? 'qr-code';

// Validate input
if (empty($qrText)) {
    $_SESSION['error'] = 'Please enter some text or URL to generate QR code.';
    header('Location: index.php');
    exit;
}

// Sanitize filename
$filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $filenameInput);
if (empty($filename)) {
    $filename = 'qr-code';
}

// Create output directory
$outputDir = __DIR__ . '/qrcodes';
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Generate unique filename
$uniqueId = uniqid();
$outputFilename = $filename . '-' . $uniqueId . '.png';
$outputPath = $outputDir . '/' . $outputFilename;

// Clean old QR codes (older than 1 hour)
cleanOldQRCodes($outputDir);

try {
    // Check if GD library is available
    if (!function_exists('imagecreatetruecolor')) {
        throw new Exception('GD library is not enabled. Please enable GD extension in PHP.');
    }
    
    // Generate QR code
    $qrCode = new SimpleQRCode($size, $margin);
    $success = $qrCode->generate($qrText, $outputPath);
    
    if ($success && file_exists($outputPath)) {
        // Check if file was created successfully
        if (filesize($outputPath) > 0) {
            $_SESSION['success'] = '1';
            $_SESSION['image_url'] = 'qrcodes/' . $outputFilename;
            $_SESSION['qr_text'] = $qrText;
            $_SESSION['filename'] = $filename;
        } else {
            throw new Exception('QR code file was created but is empty.');
        }
    } else {
        throw new Exception('Failed to generate QR code image.');
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

// Redirect back to index
header('Location: index.php');
exit;

/**
 * Clean old QR code files
 */
function cleanOldQRCodes($directory) {
    $files = glob($directory . '/*.png');
    $now = time();
    $oneHour = 3600;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) > $oneHour) {
                @unlink($file);
            }
        }
    }
}
?>