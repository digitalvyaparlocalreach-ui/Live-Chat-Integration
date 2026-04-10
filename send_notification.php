<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get input data
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$message = $data['message'] ?? '';

// Admin details - CHANGE THESE
$admin_email = 'mafiyalegend786@gmail.com';  // Where you want notifications
$telegram_bot_token = 'YOUR_BOT_TOKEN';    // Optional
$telegram_chat_id = 'YOUR_CHAT_ID';         // Optional

if (empty($name) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Name and message are required']);
    exit;
}

// Current time
$timestamp = date('Y-m-d H:i:s');

// ==============================
// METHOD 1: EMAIL NOTIFICATION
// ==============================
$to = $admin_email;
$subject = "📱 New Message from Website - $name";
$email_message = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: #667eea; color: white; padding: 20px; }
        .content { padding: 20px; background: #f9f9f9; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #333; }
        .value { color: #666; margin-top: 5px; }
        .btn { display: inline-block; background: #25D366; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>📱 New Message Received!</h2>
            <p>Timestamp: $timestamp</p>
        </div>
        <div class='content'>
            <div class='field'>
                <div class='label'>👤 Name:</div>
                <div class='value'>$name</div>
            </div>
            <div class='field'>
                <div class='label'>📧 Email:</div>
                <div class='value'>$email</div>
            </div>
            <div class='field'>
                <div class='label'>📱 Phone:</div>
                <div class='value'>" . ($phone ? $phone : 'Not provided') . "</div>
            </div>
            <div class='field'>
                <div class='label'>💬 Message:</div>
                <div class='value'>$message</div>
            </div>
        </div>
    </div>
</body>
</html>
";

$headers = "From: website@yourdomain.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$email_sent = mail($to, $subject, $email_message, $headers);

// ==============================
// METHOD 2: TELEGRAM NOTIFICATION
// ==============================
if ($telegram_bot_token && $telegram_chat_id) {
    $telegram_message = "📱 *New Message Received!*\n\n" .
        "👤 *Name:* $name\n" .
        "📧 *Email:* $email\n" .
        "📱 *Phone:* " . ($phone ? $phone : 'Not provided') . "\n" .
        "💬 *Message:* $message\n\n" .
        "🕐 *Time:* $timestamp";
    
    $telegram_url = "https://api.telegram.org/bot$telegram_bot_token/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode($telegram_message) . "&parse_mode=Markdown";
    
    file_get_contents($telegram_url);
}

// ==============================
// METHOD 3: SAVE TO FILE
// ==============================
$log_file = 'messages.txt';
$log_data = "========================================\n";
$log_data .= "Time: $timestamp\n";
$log_data .= "Name: $name\n";
$log_data .= "Email: $email\n";
$log_data .= "Phone: $phone\n";
$log_data .= "Message: $message\n";
$log_data .= "========================================\n\n";

file_put_contents($log_file, $log_data, FILE_APPEND);

// Return response
if ($email_sent) {
    echo json_encode([
        'success' => true, 
        'message' => 'Message sent successfully!'
    ]);
} else {
    // Even if email fails, message is saved to file
    echo json_encode([
        'success' => true, 
        'message' => 'Message saved! We\'ll contact you soon.'
    ]);
}
?>
