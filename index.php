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
    <title>UOBS QR Code Generator - University of Baltistan Skardu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Poppins', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0c2c54 0%, #1a4b8c 50%, #2a6cb8 100%);
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
            background: linear-gradient(90deg, #0c2c54 0%, #1a4b8c 50%, #2a6cb8 100%);
            color: white;
            padding: 25px 40px 30px;
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .logo-img {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background: white;
            padding: 8px;
        }
        
        .logo-img img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 10px;
        }
        
        .university-name {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }
        
        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        .header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #bbdefb;
        }
        
        .header p {
            opacity: 0.95;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-weight: 300;
            color: #e3f2fd;
        }
        
        .document-reference {
            background: rgba(0, 0, 0, 0.2);
            padding: 8px 15px;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-top: 10px;
            display: inline-block;
            border-left: 3px solid #4fc3f7;
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
            padding: 40px;
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
            background: linear-gradient(to bottom, transparent, #2a6cb8, transparent);
        }
        
        .result-section {
            flex: 1;
            min-width: 350px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #0c2c54;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        label i {
            color: #2a6cb8;
        }
        
        textarea, input[type="text"] {
            width: 100%;
            padding: 16px;
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
            border-color: #2a6cb8;
            outline: none;
            box-shadow: 0 6px 12px rgba(42, 108, 184, 0.2);
            transform: translateY(-2px);
        }
        
        .char-count {
            text-align: right;
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
        
        .options {
            background: linear-gradient(135deg, #e8eaf6 0%, #e3f2fd 100%);
            padding: 25px;
            border-radius: 15px;
            margin-top: 15px;
            border: 1px solid #bbdefb;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .options h3 {
            margin-bottom: 20px;
            color: #0c2c54;
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
            color: #1a4b8c;
        }
        
        select {
            width: 100%;
            padding: 14px;
            border: 2px solid #bbdefb;
            border-radius: 10px;
            background: white;
            font-size: 1rem;
            color: #333;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        select:focus {
            border-color: #2a6cb8;
            outline: none;
            box-shadow: 0 4px 8px rgba(42, 108, 184, 0.2);
        }
        
        .btn {
            background: linear-gradient(90deg, #0c2c54 0%, #2a6cb8 100%);
            color: white;
            border: none;
            padding: 18px 35px;
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
            box-shadow: 0 8px 20px rgba(12, 44, 84, 0.3);
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
            box-shadow: 0 12px 25px rgba(12, 44, 84, 0.4);
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
            border: 2px dashed #bbdefb;
            box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .qr-placeholder {
            color: #1a4b8c;
            font-size: 1.3rem;
            padding: 40px 20px;
            text-align: center;
        }
        
        .qr-placeholder i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #bbdefb;
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
            background: #e3f2fd;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            text-align: left;
            width: 100%;
        }
        
        .qr-info-box h4 {
            color: #0c2c54;
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
            background: linear-gradient(90deg, #0c7c42 0%, #2ecc71 100%);
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
            box-shadow: 0 6px 15px rgba(12, 124, 66, 0.3);
        }
        
        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 9px 20px rgba(12, 124, 66, 0.4);
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
            color: #0c7c42;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 6px solid #0c7c42;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 5px 15px rgba(12, 124, 66, 0.1);
        }
        
        .examples {
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #e8eaf6;
        }
        
        .examples h3 {
            margin-bottom: 20px;
            color: #2a6cb8;
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
            border: 2px solid #bbdefb;
            padding: 10px 18px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1a4b8c;
        }
        
        .example-btn:hover {
            background: #2a6cb8;
            color: white;
            border-color: #2a6cb8;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(42, 108, 184, 0.2);
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
                font-size: 1.8rem;
            }
            
            .header h2 {
                font-size: 1.4rem;
            }
            
            .logo-img {
                width: 60px;
                height: 60px;
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
            background: linear-gradient(90deg, transparent, #2ecc71, transparent);
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
        
        .official-examples {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 20px;
            border-radius: 15px;
            margin-top: 25px;
            border: 1px solid #90caf9;
        }
        
        .official-examples h3 {
            color: #0c2c54;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .official-examples p {
            margin-bottom: 10px;
            color: #1a4b8c;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .official-example-btn {
            background: #0c2c54;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .official-example-btn:hover {
            background: #2a6cb8;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(42, 108, 184, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <div class="logo-img">
                    <img src="https://uobs.edu.pk/images/logo/logo.png" alt="University of Baltistan Skardu Logo">
                </div>
                <div class="university-name">
                    <h1>University of Baltistan Skardu</h1>
                    <h2>QR Code Generator</h2>
                    <p>Professional QR Code Generation System for University Official Documents</p>
                </div>
            </div>
            <div class="document-reference">
                <i class="fas fa-file-alt"></i> Ref: UOBS-Estt-1(3)/2018/1234 | Date: <?php echo date("F d, Y"); ?>
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
                            placeholder="uobs-document-qr (default: qr-code)"
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
                
                <div class="official-examples">
                    <h3>
                        <i class="fas fa-university"></i>
                        Official University Examples:
                    </h3>
                    <p>Generate QR codes for official university documents and resources:</p>
                    <button type="button" class="official-example-btn" onclick="setOfficialExample('Notification of Selection Committee', 'UOBS-Estt-1(3)/2018/1234\\nDate: January 28, 2026\\nSub: Notification of Selection Committee\\nRef: Appointment of Teaching Staff\\nThe selection committee meeting is scheduled for...')">
                        <i class="fas fa-users"></i>
                        Selection Committee
                    </button>
                    <button type="button" class="official-example-btn" onclick="setOfficialExample('Student ID Card', 'UNIVERSITY OF BALTISTAN SKARDU\\nStudent ID: UOBS2024-00123\\nName: John Smith\\nProgram: BS Computer Science\\nValidity: 2024-2028\\nContact: registrar@uobs.edu.pk')">
                        <i class="fas fa-id-card"></i>
                        Student ID
                    </button>
                    <button type="button" class="official-example-btn" onclick="setOfficialExample('Admission Portal', 'https://admissions.uobs.edu.pk\\nUniversity of Baltistan Skardu\\nAdmission Portal\\nApply online for Fall 2024\\nDeadline: June 30, 2024')">
                        <i class="fas fa-graduation-cap"></i>
                        Admissions
                    </button>
                    <button type="button" class="official-example-btn" onclick="setOfficialExample('Campus WiFi', 'WIFI:S:UOBS-Campus;T:WPA2;P:Student@2024;;\\nUniversity of Baltistan Skardu\\nConnect to campus WiFi\\nFor assistance: ithelp@uobs.edu.pk')">
                        <i class="fas fa-wifi"></i>
                        Campus WiFi
                    </button>
                    <button type="button" class="official-example-btn" onclick="setOfficialExample('Library Access', 'https://library.uobs.edu.pk\\nUOBS Digital Library\\nAccess Code: UOBS-LIB-2024\\n24/7 Online Resources\\nContact: librarian@uobs.edu.pk')">
                        <i class="fas fa-book"></i>
                        Library
                    </button>
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
                    <p><i class="fas fa-shield-alt"></i> Secure • Official • University Approved</p>
                    <p>QR codes can store up to 4,296 alphanumeric characters</p>
                    <p style="color: #1a4b8c; font-weight: 600; margin-top: 10px;">
                        <i class="fas fa-map-marker-alt"></i> University of Baltistan Skardu, Gilgit-Baltistan, Pakistan
                    </p>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2024 University of Baltistan Skardu - Official QR Code Generator | Version 2.0</p>
            <p>For technical support, contact: <strong>it-support@uobs.edu.pk</strong> | Phone: +92-XXX-XXXXXXX</p>
        </div>
    </div>
    
    <script>
        function setExample(text) {
            // Replace escaped newlines with actual newlines
            var actualText = text.replace(/\\n/g, '\n');
            document.getElementById('qrText').value = actualText;
            document.getElementById('filename').value = 'uobs-qr-' + Date.now().toString().slice(-6);
            updateCharCount();
        }
        
        function setOfficialExample(title, text) {
            // Replace escaped newlines with actual newlines
            var actualText = text.replace(/\\n/g, '\n');
            document.getElementById('qrText').value = actualText;
            document.getElementById('filename').value = 'uobs-' + title.toLowerCase().replace(/\s+/g, '-');
            updateCharCount();
            
            // Show a small notification
            showNotification('Example loaded: ' + title);
        }
        
        function showNotification(message) {
            // Create notification element
            var notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #0c2c54;
                color: white;
                padding: 15px 20px;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                z-index: 1000;
                animation: slideIn 0.3s ease-out;
                max-width: 300px;
            `;
            
            notification.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(function() {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(function() {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
        
        // Add CSS for animations
        var style = document.createElement('style');
        style.innerHTML = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
        
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
                    showNotification('Please enter some text to generate QR code.');
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