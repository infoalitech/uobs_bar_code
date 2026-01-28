<?php
echo "<h2>QR Code Generator Test</h2>";

// Test 1: Check PHP version
echo "<h3>1. PHP Version:</h3>";
echo "PHP " . phpversion() . "<br>";

// Test 2: Check GD library
echo "<h3>2. GD Library:</h3>";
if (function_exists('gd_info')) {
    $gdInfo = gd_info();
    echo "GD Library: Enabled<br>";
    echo "Version: " . ($gdInfo['GD Version'] ?? 'Unknown') . "<br>";
    echo "PNG Support: " . ($gdInfo['PNG Support'] ? 'Yes' : 'No') . "<br>";
} else {
    echo "GD Library: NOT ENABLED<br>";
    echo "Please enable GD extension in php.ini<br>";
}

// Test 3: Check directory permissions
echo "<h3>3. Directory Permissions:</h3>";
$dirs = ['qrcodes', 'phpqrcode'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        echo "$dir: Exists (Permission: " . substr(sprintf('%o', fileperms($dir)), -4) . ")<br>";
        if (is_writable($dir)) {
            echo "$dir: Writable ✓<br>";
        } else {
            echo "$dir: NOT Writable ✗<br>";
        }
    } else {
        echo "$dir: Does not exist<br>";
    }
}

// Test 4: Create a simple QR code
echo "<h3>4. Test QR Code Generation:</h3>";
if (function_exists('imagecreatetruecolor')) {
    // Create simple QR code
    $size = 200;
    $image = imagecreatetruecolor($size, $size);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    
    imagefill($image, 0, 0, $white);
    
    // Draw a simple pattern
    for ($i = 0; $i < 10; $i++) {
        for ($j = 0; $j < 10; $j++) {
            if (($i + $j) % 2 == 0) {
                $x = $i * 20;
                $y = $j * 20;
                imagefilledrectangle($image, $x, $y, $x + 20, $y + 20, $black);
            }
        }
    }
    
    $testFile = 'qrcodes/test_qr.png';
    if (imagepng($image, $testFile)) {
        echo "QR code created successfully!<br>";
        echo "<img src='$testFile' alt='Test QR'><br>";
        echo "File size: " . filesize($testFile) . " bytes<br>";
    } else {
        echo "Failed to create QR code<br>";
    }
    imagedestroy($image);
} else {
    echo "Cannot test QR generation - GD library not available<br>";
}

echo "<h3>5. Quick Test Form:</h3>";
?>
<form method="POST" action="generate_qr.php" style="border: 1px solid #ccc; padding: 20px;">
    <label>Test Text: <input type="text" name="qrText" value="Test QR Code"></label><br>
    <label>Size: 
        <select name="size">
            <option value="200">200x200</option>
            <option value="300" selected>300x300</option>
            <option value="400">400x400</option>
        </select>
    </label><br>
    <input type="submit" value="Test Generate QR">
</form>

<a href="index.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none;">Go to Main QR Generator</a>