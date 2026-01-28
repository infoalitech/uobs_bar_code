<?php
/**
 * QR Code Generator with proper line break handling
 */

session_start();

// Get form data
$qrText = $_POST['qrText'] ?? '';
$size = (int)($_POST['size'] ?? 300);
$filenameInput = $_POST['filename'] ?? 'qr-code';

if (empty($qrText)) {
    $_SESSION['error'] = 'Please enter text for QR code';
    header('Location: index.php');
    exit;
}

// Create directory
$outputDir = __DIR__ . '/qrcodes';
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Generate filename
$filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $filenameInput);
if (empty($filename)) $filename = 'qr-code';
$outputFilename = $filename . '-' . uniqid() . '.png';
$outputPath = $outputDir . '/' . $outputFilename;

// IMPORTANT: Handle line breaks properly
// Method 1: Replace newlines with URL-encoded newline (%0A)
$textForQR = $qrText;
$encodedText = rawurlencode($textForQR);

// Alternative: Use Google's QR code API which handles newlines well
$qrUrl = "https://chart.googleapis.com/chart?cht=qr&chs={$size}x{$size}&chl=" . $encodedText . "&choe=UTF-8&chld=H|0";

// Debug: Save what we're encoding
file_put_contents($outputDir . '/last_encoded.txt', 
    "Original: \n" . $qrText . 
    "\n\nEncoded: \n" . $encodedText .
    "\n\nURL: \n" . $qrUrl);

// Generate QR code
$qrImage = @file_get_contents($qrUrl);

if ($qrImage && strlen($qrImage) > 1000) {
    file_put_contents($outputPath, $qrImage);
    
    $_SESSION['success'] = '1';
    $_SESSION['image_url'] = 'qrcodes/' . $outputFilename;
    $_SESSION['qr_text'] = $qrText;
    $_SESSION['filename'] = $filename;
} else {
    // Try QuickChart API as backup
    $quickChartUrl = "https://quickchart.io/qr?text=" . urlencode($qrText) . "&size={$size}&margin=0";
    $qrImage = @file_get_contents($quickChartUrl);
    
    if ($qrImage && strlen($qrImage) > 1000) {
        file_put_contents($outputPath, $qrImage);
        
        $_SESSION['success'] = '1';
        $_SESSION['image_url'] = 'qrcodes/' . $outputFilename;
        $_SESSION['qr_text'] = $qrText;
        $_SESSION['filename'] = $filename;
    } else {
        $_SESSION['error'] = 'Failed to generate QR code. Please try different text.';
    }
}

header('Location: index.php');
exit;
?>