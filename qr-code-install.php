<?php
/**
 * Script to download and install the Endroid QR Code library locally
 * Run this script once: php qrcode-lib/qr-code-install.php
 */

// Create directory if it doesn't exist
$libDir = __DIR__ . '/qr-code-4.6.1';
if (!file_exists($libDir)) {
    mkdir($libDir, 0755, true);
}

// Download the library from GitHub (version 4.6.1)
$zipUrl = 'https://github.com/endroid/qr-code/archive/refs/tags/4.6.1.zip';
$zipFile = __DIR__ . '/qr-code-4.6.1.zip';

echo "Downloading QR Code library...\n";

// Download the ZIP file
$zipContent = file_get_contents($zipUrl);
if ($zipContent === false) {
    die("Failed to download library. Check your internet connection.\n");
}

file_put_contents($zipFile, $zipContent);

echo "Extracting library...\n";

// Extract the ZIP file
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo(__DIR__);
    $zip->close();
    
    // Move contents to proper directory
    $extractedDir = __DIR__ . '/qr-code-4.6.1';
    $tempDir = __DIR__ . '/qr-code-4.6.1-gh';
    
    if (file_exists($tempDir)) {
        // Move all files from temp directory
        $files = scandir($tempDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                rename($tempDir . '/' . $file, $extractedDir . '/' . $file);
            }
        }
        rmdir($tempDir);
    }
    
    unlink($zipFile);
    
    // Download required dependencies
    $dependencies = [
        'bacon/bacon-qr-code' => 'https://github.com/Bacon/BaconQrCode/archive/refs/tags/2.0.8.zip',
        'dasprid/enum' => 'https://github.com/DASPRiD/Enum/archive/refs/tags/1.0.5.zip'
    ];
    
    foreach ($dependencies as $depName => $depUrl) {
        echo "Downloading dependency: $depName\n";
        $depZip = __DIR__ . '/' . basename($depUrl);
        $depContent = file_get_contents($depUrl);
        
        if ($depContent !== false) {
            file_put_contents($depZip, $depContent);
            
            $zip = new ZipArchive;
            if ($zip->open($depZip) === TRUE) {
                $zip->extractTo($libDir . '/vendor/');
                $zip->close();
            }
            unlink($depZip);
        }
    }
    
    echo "QR Code library installed successfully!\n";
} else {
    die("Failed to extract library.\n");
}