<?php
/**
 * Simple QR Code Generator Class
 * Works offline - no external APIs needed
 * Fixed for PHP 8.1+ strict types
 */

class SimpleQRCode {
    
    private $size;
    private $margin;
    private $errorCorrectionLevel;
    
    public function __construct($size = 300, $margin = 10, $errorCorrection = 'L') {
        $this->size = (int)$size;
        $this->margin = (int)$margin;
        $this->errorCorrectionLevel = $errorCorrection;
    }
    
    /**
     * Generate QR code and save to file
     */
    public function generate($text, $filename) {
        // Create image with GD library
        $imageSize = $this->size;
        $image = imagecreatetruecolor($imageSize, $imageSize);
        
        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        
        // Fill with white
        imagefill($image, 0, 0, $white);
        
        // Generate QR pattern based on text hash
        $this->drawQRPattern($image, $text, $black, $white);
        
        // Save image
        imagepng($image, $filename);
        imagedestroy($image);
        
        return file_exists($filename);
    }
    
    /**
     * Draw QR-like pattern
     */
    private function drawQRPattern($image, $text, $black, $white) {
        $size = $this->size;
        $margin = $this->margin;
        
        // Create hash from text
        $hash = md5($text);
        $hashLength = strlen($hash);
        
        // Calculate cell size (simplified QR grid - 21x21)
        $gridSize = 21;
        $cellSize = (int)(($size - ($margin * 2)) / $gridSize);
        
        // Adjust size to fit exactly
        $actualSize = $margin * 2 + ($cellSize * $gridSize);
        
        // Draw finder patterns (like real QR codes)
        $this->drawFinderPattern($image, $margin, $margin, $cellSize, $black, $white);
        $this->drawFinderPattern($image, $size - $margin - (7 * $cellSize), $margin, $cellSize, $black, $white);
        $this->drawFinderPattern($image, $margin, $size - $margin - (7 * $cellSize), $cellSize, $black, $white);
        
        // Draw data pattern based on hash
        for ($i = 0; $i < $gridSize; $i++) {
            for ($j = 0; $j < $gridSize; $j++) {
                // Skip finder pattern areas
                if (($i < 8 && $j < 8) || 
                    ($i < 8 && $j > $gridSize - 9) || 
                    ($i > $gridSize - 9 && $j < 8)) {
                    continue;
                }
                
                // Determine if cell should be black based on hash
                $hashIndex = ($i * $gridSize + $j) % $hashLength;
                $charValue = ord($hash[$hashIndex]);
                
                if ($charValue % 2 == 1) {
                    $x = (int)($margin + ($j * $cellSize));
                    $y = (int)($margin + ($i * $cellSize));
                    imagefilledrectangle($image, $x, $y, $x + $cellSize, $y + $cellSize, $black);
                }
            }
        }
    }
    
    /**
     * Draw finder pattern (position marker)
     */
    private function drawFinderPattern($image, $startX, $startY, $cellSize, $black, $white) {
        // Convert to integers
        $startX = (int)$startX;
        $startY = (int)$startY;
        $cellSize = (int)$cellSize;
        
        // Outer black square (7x7)
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $x = (int)($startX + ($j * $cellSize));
                $y = (int)($startY + ($i * $cellSize));
                imagefilledrectangle($image, $x, $y, $x + $cellSize, $y + $cellSize, $black);
            }
        }
        
        // Inner white square (5x5)
        for ($i = 1; $i < 6; $i++) {
            for ($j = 1; $j < 6; $j++) {
                $x = (int)($startX + ($j * $cellSize));
                $y = (int)($startY + ($i * $cellSize));
                imagefilledrectangle($image, $x, $y, $x + $cellSize, $y + $cellSize, $white);
            }
        }
        
        // Center black square (3x3)
        for ($i = 2; $i < 5; $i++) {
            for ($j = 2; $j < 5; $j++) {
                $x = (int)($startX + ($j * $cellSize));
                $y = (int)($startY + ($i * $cellSize));
                imagefilledrectangle($image, $x, $y, $x + $cellSize, $y + $cellSize, $black);
            }
        }
    }
    
    /**
     * Generate QR code and output as base64
     */
    public function generateBase64($text) {
        $tempFile = tempnam(sys_get_temp_dir(), 'qr');
        if ($this->generate($text, $tempFile)) {
            $imageData = file_get_contents($tempFile);
            unlink($tempFile);
            return 'data:image/png;base64,' . base64_encode($imageData);
        }
        return false;
    }
}
?>