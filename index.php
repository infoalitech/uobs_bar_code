<?php
session_start();

// Get messages
$error = $_SESSION['error'] ?? null;
$success = isset($_SESSION['success']);
$imageUrl = $_SESSION['image_url'] ?? null;
$qrText = $_SESSION['qr_text'] ?? '';
$filename = $_SESSION['filename'] ?? 'qr-code';

// Clear session
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['image_url'], $_SESSION['qr_text'], $_SESSION['filename']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOBS QR Code Generator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Poppins', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.05)"/></svg>');
            background-size: cover;
            z-index: 0;
        }
        
        .container {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 1000px;
            overflow: hidden;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header {
            background: linear-gradient(90deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
            color: white;
            padding: 35px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            right: -50%;
            bottom: -50%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.3;
            animation: moveGrid 20s linear infinite;
        }
        
        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .logo i {
            font-size: 32px;
            color: #1565c0;
        }
        
        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            background: linear-gradient(90deg, #ffffff, #bbdefb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header p {
            opacity: 0.95;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            font-weight: 300;
        }
        
        .content {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            min-height: 600px;
        }
        
        .form-section {
            flex: 1;
            min-width: 350px;
            padding: 45px;
            border-right: 1px solid #e8eaf6;
            background: #f8f9ff;
            position: relative;
        }
        
        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: -1px;
            width: 1px;
            height: 100%;
            background: linear-gradient(to bottom, transparent, #3949ab, transparent);
        }
        
        .result-section {
            flex: 1;
            min-width: 350px;
            padding: 45px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }
        
        .form-group {
            margin-bottom: 28px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #1a237e;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        label i {
            color: #3949ab;
        }
        
        textarea, input[type="text"] {
            width: 100%;
            padding: 18px;
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        textarea {
            min-height: 140px;
            resize: vertical;
            font-family: 'Courier New', monospace;
            line-height: 1.5;
        }
        
        textarea:focus, input[type="text"]:focus {
            border-color: #3949ab;
            outline: none;
            box-shadow: 0 6px 12px rgba(57, 73, 171, 0.2);
            transform: translateY(-2px);
        }
        
        .char-count {
            text-align: right;
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
        
        .options {
            background: linear-gradient(135deg, #e8eaf6 0%, #f3e5f5 100%);
            padding: 25px;
            border-radius: 15px;
            margin-top: 15px;
            border: 1px solid #c5cae9;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .options h3 {
            margin-bottom: 20px;
            color: #1a237e;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
        }
        
        .option-group {
            margin-bottom: 20px;
        }
        
        .option-group label {
            margin-bottom: 8px;
            font-size: 1rem;
            color: #5c6bc0;
        }
        
        select {
            width: 100%;
            padding: 14px;
            border: 2px solid #c5cae9;
            border-radius: 10px;
            background: white;
            font-size: 1rem;
            color: #333;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        select:focus {
            border-color: #3949ab;
            outline: none;
            box-shadow: 0 4px 8px rgba(57, 73, 171, 0.2);
        }
        
        .btn {
            background: linear-gradient(90deg, #1a237e 0%, #3949ab 100%);
            color: white;
            border: none;
            padding: 20px 35px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 15px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            box-shadow: 0 8px 20px rgba(26, 35, 126, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(26, 35, 126, 0.4);
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:active {
            transform: translateY(-2px);
        }
        
        .qr-container {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f8f9ff 0%, #e8eaf6 100%);
            border-radius: 20px;
            width: 100%;
            min-height: 350px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 2px dashed #c5cae9;
            box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .qr-placeholder {
            color: #7986cb;
            font-size: 1.3rem;
            padding: 40px 20px;
            text-align: center;
        }
        
        .qr-placeholder i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #c5cae9;
            display: block;
        }
        
        .qr-image {
            max-width: 280px;
            height: auto;
            margin: 25px 0;
            border: 1px solid #e1e1e1;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .qr-image:hover {
            transform: scale(1.03);
        }
        
        .qr-info-box {
            background: #e8eaf6;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            text-align: left;
            width: 100%;
        }
        
        .qr-info-box h4 {
            color: #1a237e;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .qr-info-box pre {
            background: white;
            padding: 12px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            max-height: 100px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .download-btn {
            background: linear-gradient(90deg, #2e7d32 0%, #4caf50 100%);
            color: white;
            border: none;
            padding: 16px 35px;
            font-size: 1.1rem;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            transition: all 0.3s;
            box-shadow: 0 6px 15px rgba(46, 125, 50, 0.3);
        }
        
        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 9px 20px rgba(46, 125, 50, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .error {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            color: #c62828;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 6px solid #c62828;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 5px 15px rgba(198, 40, 40, 0.1);
        }
        
        .success {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #2e7d32;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 6px solid #2e7d32;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.1);
        }
        
        .examples {
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #e8eaf6;
        }
        
        .examples h3 {
            margin-bottom: 20px;
            color: #3949ab;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .example-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .example-btn {
            background: white;
            border: 2px solid #c5cae9;
            padding: 10px 18px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #5c6bc0;
        }
        
        .example-btn:hover {
            background: #3949ab;
            color: white;
            border-color: #3949ab;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(57, 73, 171, 0.2);
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            
            .form-section {
                border-right: none;
                border-bottom: 1px solid #e8eaf6;
                padding: 30px;
            }
            
            .result-section {
                padding: 30px;
            }
            
            .header h1 {
                font-size: 2.2rem;
            }
            
            .logo {
                width: 50px;
                height: 50px;
            }
            
            .logo i {
                font-size: 24px;
            }
        }
        
        /* Animation for success */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .qr-container > * {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Scan line effect */
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, #00e676, transparent);
            animation: scan 2s linear infinite;
            border-radius: 2px;
        }
        
        @keyframes scan {
            0% { top: 0; }
            100% { top: 100%; }
        }
        
        .text-encoded {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
            max-width: 280px;
            word-break: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <div class="logo">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                    <h1>UOBS BarCode</h1>
                    <p>Professional QR Code Generation System for University of Business Studies</p>
                </div>
            </div>
        </div>
        
        <div class="content">
            <div class="form-section">
                <form method="POST" action="generate_qr.php" id="qrForm">
                    <?php if ($error): ?>
                    <div class="error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Success!</strong> QR code generated successfully!
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="qrText">
                            <i class="fas fa-font"></i>
                            Enter text or URL:
                        </label>
                        <textarea 
                            id="qrText" 
                            name="qrText" 
                            placeholder="Enter text, URL, contact info, or any data you want to encode..." 
                            required
                            oninput="updateCharCount()"
                        ><?php echo htmlspecialchars($qrText); ?></textarea>
                        <div class="char-count">
                            <span id="charCount">0</span> characters
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="filename">
                            <i class="fas fa-file-signature"></i>
                            Filename (optional):
                        </label>
                        <input 
                            type="text" 
                            id="filename" 
                            name="filename" 
                            placeholder="my-qr-code (default: qr-code)"
                            value="<?php echo htmlspecialchars($filename); ?>"
                        >
                    </div>
                    
                    <div class="options">
                        <h3>
                            <i class="fas fa-sliders-h"></i>
                            QR Code Options
                        </h3>
                        
                        <div class="option-group">
                            <label for="size">
                                <i class="fas fa-expand-alt"></i>
                                Size (pixels):
                            </label>
                            <select id="size" name="size">
                                <option value="300" selected>300x300</option>
                                <option value="400">400x400</option>
                                <option value="500">500x500</option>
                                <option value="600">600x600</option>
                                <option value="800">800x800</option>
                            </select>
                        </div>
                        
                        <div class="option-group">
                            <label for="margin">
                                <i class="fas fa-border-all"></i>
                                Margin:
                            </label>
                            <select id="margin" name="margin">
                                <option value="10" selected>10px</option>
                                <option value="15">15px</option>
                                <option value="20">20px</option>
                                <option value="25">25px</option>
                                <option value="0">No margin</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-bolt"></i>
                        Generate QR Code
                    </button>
                </form>
                
                <div class="examples">
                    <h3>
                        <i class="fas fa-lightbulb"></i>
                        Try these examples:
                    </h3>
                    <div class="example-list">
                        <button type="button" class="example-btn" onclick="setExample('https://github.com')">
                            <i class="fab fa-github"></i>
                            GitHub URL
                        </button>
                        <button type="button" class="example-btn" onclick="setExample('Hello World! This is a test QR code.')">
                            <i class="fas fa-text-height"></i>
                            Sample Text
                        </button>
                        <button type="button" class="example-btn" onclick="setExample('WIFI:S:UOBS-WiFi;T:WPA2;P:Campus2024;;')">
                            <i class="fas fa-wifi"></i>
                            WiFi Connection
                        </button>
                        <button type="button" class="example-btn" onclick="setExample('mailto:admissions@uobs.edu')">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </button>
                        <button type="button" class="example-btn" onclick="setExample('BEGIN:VCARD\\nVERSION:3.0\\nFN:Student Name\\nORG:UOBS\\nTEL:+1234567890\\nEMAIL:student@uobs.edu\\nEND:VCARD')">
                            <i class="fas fa-address-card"></i>
                            Contact Card
                        </button>
                        <button type="button" class="example-btn" onclick="setExample('UOBS-ID: STU2024-001\\nName: John Smith\\nCourse: BSc Computer Science\\nYear: 2024')">
                            <i class="fas fa-id-card"></i>
                            Student ID
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="result-section">
                <div class="qr-container">
                    <?php if ($imageUrl && file_exists($imageUrl)): ?>
                        <h3>
                            <i class="fas fa-qrcode"></i>
                            Your QR Code
                        </h3>
                        
                        <div style="position: relative; display: inline-block;">
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Generated QR Code" class="qr-image">
                            <div class="scan-line"></div>
                        </div>
                        
                        <p>Scan this QR code with your smartphone camera</p>
                        
                        <div class="qr-info-box">
                            <h4>
                                <i class="fas fa-info-circle"></i>
                                Encoded Text:
                            </h4>
                            <pre><?php echo htmlspecialchars($qrText); ?></pre>
                        </div>
                        
                        <a href="<?php echo $imageUrl; ?>" download="<?php echo $filename; ?>.png" class="download-btn">
                            <i class="fas fa-download"></i>
                            Download QR Code
                        </a>
                        
                        <div class="text-encoded">
                            File: <?php echo htmlspecialchars($filename); ?>.png
                        </div>
                        
                    <?php elseif ($imageUrl): ?>
                        <div class="error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>File Not Found</strong><br>
                                QR code file was not found. Please generate a new one.
                            </div>
                        </div>
                        <div class="qr-placeholder">
                            <i class="fas fa-qrcode"></i>
                            <p>Your generated QR code will appear here</p>
                            <p>↑ Enter text and click "Generate QR Code" ↑</p>
                        </div>
                    <?php else: ?>
                        <div class="qr-placeholder">
                            <i class="fas fa-qrcode"></i>
                            <p>Your generated QR code will appear here</p>
                            <p>↑ Enter text and click "Generate QR Code" ↑</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 25px; text-align: center; color: #666; font-size: 0.9rem;">
                    <p><i class="fas fa-shield-alt"></i> Secure • Fast • Reliable</p>
                    <p>QR codes can store up to 4,296 alphanumeric characters</p>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2024 University of Business Studies - QR Code Generator | Version 2.0</p>
            <p>For technical support, contact: <strong>it-support@uobs.edu</strong></p>
        </div>
    </div>
    
    <script>
        function setExample(text) {
            // Replace escaped newlines with actual newlines
            var actualText = text.replace(/\\n/g, '\n');
            document.getElementById('qrText').value = actualText;
            updateCharCount();
        }
        
        function updateCharCount() {
            var text = document.getElementById('qrText').value;
            document.getElementById('charCount').textContent = text.length;
        }
        
        // Initialize character count
        document.addEventListener('DOMContentLoaded', function() {
            updateCharCount();
            document.getElementById('qrText').focus();
            
            // Add animation to success message
            <?php if ($success): ?>
            setTimeout(function() {
                document.querySelector('.success').style.animation = 'fadeIn 0.5s ease-out';
            }, 100);
            <?php endif; ?>
            
            // Add form validation
            document.getElementById('qrForm').addEventListener('submit', function(e) {
                var text = document.getElementById('qrText').value.trim();
                if (text.length === 0) {
                    e.preventDefault();
                    alert('Please enter some text to generate QR code.');
                    return false;
                }
                
                // Show loading state
                var btn = document.querySelector('.btn');
                var originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating QR Code...';
                btn.disabled = true;
                
                // Re-enable after 3 seconds (in case submission fails)
                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 3000);
            });
        });
        
        // Add keyboard shortcut (Ctrl+Enter to submit)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                document.getElementById('qrForm').submit();
            }
        });
    </script>
</body>
</html>