<?php
// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get the CSRF token
$token = csrf_token();
?>
<!DOCTYPE html>
<html>
<head>
    <title>CSRF Token Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .token { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; margin: 10px 0; word-break: break-all; }
        button { padding: 5px 10px; background: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>CSRF Token Check</h1>
    <p>Current CSRF token:</p>
    <div class="token"><?php echo $token; ?></div>
    
    <button onclick="copyToClipboard('<?php echo $token; ?>')">Copy Token</button>
    
    <h2>Test Forms</h2>
    <p>Update your test forms with this token.</p>
    
    <h3>Simple Test Form</h3>
    <form action="index.php/test-post" method="post">
        <input type="hidden" name="_token" value="<?php echo $token; ?>">
        <input type="text" name="test_field" value="test value">
        <button type="submit">Submit Simple Test</button>
    </form>
    
    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Token copied to clipboard!');
        });
    }
    </script>
</body>
</html> 