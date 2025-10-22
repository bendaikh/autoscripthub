<!DOCTYPE html>
<html>
<head>
    <title>Sales Popup Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #22c55e; }
        .error { color: #ef4444; }
        h1 { color: #333; }
        code { background: #f1f1f1; padding: 2px 6px; border-radius: 3px; }
        pre { background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 6px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Sales Popup Diagnostic Tool</h1>
    
    <div class="box">
        <h2>File Check</h2>
        <?php
        $jsFile = __DIR__ . '/js/sales-popup.js';
        $cssFile = __DIR__ . '/css/sales-popup.css';
        
        echo '<p><strong>JavaScript File:</strong> ';
        if (file_exists($jsFile)) {
            echo '<span class="success">✅ EXISTS</span>';
            echo ' (' . number_format(filesize($jsFile)) . ' bytes)';
        } else {
            echo '<span class="error">❌ NOT FOUND</span>';
        }
        echo '</p>';
        
        echo '<p><strong>CSS File:</strong> ';
        if (file_exists($cssFile)) {
            echo '<span class="success">✅ EXISTS</span>';
            echo ' (' . number_format(filesize($cssFile)) . ' bytes)';
        } else {
            echo '<span class="error">❌ NOT FOUND</span>';
        }
        echo '</p>';
        ?>
    </div>
    
    <div class="box">
        <h2>API Endpoint Check</h2>
        <?php
        $apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/api/sales-notifications";
        echo '<p><strong>API URL:</strong> <code>' . $apiUrl . '</code></p>';
        
        $response = @file_get_contents($apiUrl);
        if ($response) {
            echo '<p class="success">✅ API is responding</p>';
            $data = json_decode($response, true);
            if (isset($data['notifications'])) {
                echo '<p>Notifications found: <strong>' . count($data['notifications']) . '</strong></p>';
            }
            echo '<details><summary>View API Response</summary><pre>' . htmlspecialchars(json_encode(json_decode($response), JSON_PRETTY_PRINT)) . '</pre></details>';
        } else {
            echo '<p class="error">❌ API is not responding</p>';
        }
        ?>
    </div>
    
    <div class="box">
        <h2>Test Links</h2>
        <p><a href="/api/sales-notifications" target="_blank">→ View API Response</a></p>
        <p><a href="js/sales-popup.js" target="_blank">→ View JavaScript File</a></p>
        <p><a href="css/sales-popup.css" target="_blank">→ View CSS File</a></p>
        <p><a href="test-popup.html" target="_blank">→ Open Test Page</a></p>
    </div>
    
    <div class="box">
        <h2>Browser Console Test</h2>
        <p>The popup should appear on this page in 3 seconds...</p>
        <div id="popup-test-result"></div>
    </div>
    
    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Load Sales Popup -->
    <link rel="stylesheet" href="css/sales-popup.css">
    <script src="js/sales-popup.js"></script>
    
    <script>
        // Check after 10 seconds
        setTimeout(function() {
            var result = document.getElementById('popup-test-result');
            if ($('#sales-popup-container').length > 0) {
                result.innerHTML = '<p class="success">✅ Popup container was created!</p>';
                if ($('#sales-popup-container').hasClass('sales-popup-show')) {
                    result.innerHTML += '<p class="success">✅ Popup is visible!</p>';
                } else {
                    result.innerHTML += '<p style="color: #f59e0b;">⚠️ Popup container exists but not showing yet. Wait a bit longer...</p>';
                }
            } else {
                result.innerHTML = '<p class="error">❌ Popup container was NOT created. Check console for errors.</p>';
            }
        }, 10000);
    </script>
</body>
</html>

