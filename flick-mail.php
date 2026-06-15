<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

// Get POST data (JSON or form)
$input = file_get_contents("php://input");
$data  = json_decode($input, true);
if (!$data) $data = $_POST;

$name     = htmlspecialchars(trim($data["name"]     ?? ""));
$email    = htmlspecialchars(trim($data["email"]    ?? ""));
$company  = htmlspecialchars(trim($data["company"]  ?? ""));
$comments = htmlspecialchars(trim($data["comments"] ?? ""));

// Basic validation
if (!$name || !$email || !$comments) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid email address"]);
    exit();
}

// Email config
$to      = "stephenkington@googlemail.com";
$subject = "Flick Suite — Sponsorship Enquiry";

$body  = "New sponsorship enquiry from Flick Suite\n";
$body .= "==========================================\n\n";
$body .= "Name:     $name\n";
$body .= "Email:    $email\n";
$body .= "Company:  " . ($company ?: "—") . "\n\n";
$body .= "Campaign details:\n$comments\n\n";
$body .= "==========================================\n";
$body .= "Sent from flicksuite via flick-mail.php\n";

$headers  = "From: Flick Suite <noreply@weareasset.co.uk>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(["success" => true, "message" => "Message sent"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Mail server error"]);
}
?>
