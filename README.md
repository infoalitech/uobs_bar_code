# PHP QR Code Generator

A standalone QR code generator using local PHP libraries (no CDN required).

## Features

- Generate QR codes from text, URLs, or any data
- Customizable size and margin
- Download QR codes as PNG images
- Clean, responsive interface
- No external dependencies (all libraries included locally)
- Automatic cleanup of old QR codes

## Installation

1. Upload all files to your web server
2. Ensure PHP 7.4+ is installed with GD/Imagick extension
3. Run the installation script once:

```bash
php qrcode-lib/qr-code-install.php