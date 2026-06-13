```php
<?php
/**
 * DunbarDigital Consultation Request Form Mailer
 * Target Email Address: dunbar.mitch@icloud.com
 */

header('Content-Type: application/json; charset=utf-8');

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid Request Method. This script only processes POST data.'
    ]);
    exit;
}

// Target email address
$toEmail = 'dunbar.mitch@icloud.com';

// Sanitize inputs
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
$subscribeChecked = isset($_POST['subscribe']) && $_POST['subscribe'] === 'yes' ? 'Yes, please subscribe me' : 'No subscription requested';

// Validation
if (empty($name) || empty($phone) || empty($email)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please fill out all required fields: Name, Phone Number, and Email.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a valid email address.'
    ]);
    exit;
}

// Meta Data
$timestamp = date('F j, Y, g:i a');
$ipAddress = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Email Subject
$subject = "⚡ New Consultation: $name (DunbarDigital)";

// Elegant HTML Design using Candy Palette style tokens
$message = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Consultation Request</title>
</head>
<body style="margin: 0; padding: 0; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; background-color: #05060A; color: #DEDFE3;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #05060A; padding: 40px 20px;">
    <tr>
      <td align="center">
        <!-- Main Panel Container -->
        <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #131418; border: 1px solid #282C34; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
          
          <!-- Header Banner -->
          <tr>
            <td style="background-color: #282C34; padding: 30px; text-align: center; border-bottom: 2px solid #ED264D;">
              <span style="font-size: 24px; font-weight: bold; letter-spacing: -0.5px; color: #FFFFFF;">DunbarDigital</span>
              <p style="margin: 5px 0 0 0; font-size: 11px; color: #C4C8D2; text-transform: uppercase; letter-spacing: 2px;">New Consultation Request</p>
            </td>
          </tr>
          
          <!-- Body Content Area -->
          <tr>
            <td style="padding: 40px 30px;">
              <p style="font-size: 16px; line-height: 1.6; color: #DEDFE3; margin-top: 0;">
                You received a new incoming lead from the <strong>DunbarDigital</strong> contact form. Here are the client profile specifications:
              </p>
              
              <table width="100%" border="0" cellspacing="0" cellpadding="10" style="margin-top: 25px; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #282C34;">
                  <td width="30%" style="font-weight: bold; font-size: 13px; color: #72F7A0; text-transform: uppercase;">Name:</td>
                  <td style="font-size: 15px; color: #FFFFFF;">' . htmlspecialchars($name) . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #282C34;">
                  <td style="font-weight: bold; font-size: 13px; color: #72F7A0; text-transform: uppercase;">Phone:</td>
                  <td style="font-size: 15px; color: #FFFFFF;"><a href="tel:' . urlencode($phone) . '" style="color: #7CB8FF; text-decoration: none;">' . htmlspecialchars($phone) . '</a></td>
                </tr>
                <tr style="border-bottom: 1px solid #282C34;">
                  <td style="font-weight: bold; font-size: 13px; color: #72F7A0; text-transform: uppercase;">Email:</td>
                  <td style="font-size: 15px; color: #FFFFFF;"><a href="mailto:' . urlencode($email) . '" style="color: #7CB8FF; text-decoration: none;">' . htmlspecialchars($email) . '</a></td>
                </tr>
                <tr style="border-bottom: 1px solid #282C34;">
                  <td style="font-weight: bold; font-size: 13px; color: #C64DEC; text-transform: uppercase;">Subscribed:</td>
                  <td style="font-size: 14px; color: #DEDFE3; font-style: italic;">' . htmlspecialchars($subscribeChecked) . '</td>
                </tr>
              </table>
              
              <div style="margin-top: 35px; padding: 15px; background-color: #282C34; border-radius: 8px; border-left: 4px solid #C64DEC;">
                <p style="margin: 0; font-size: 11px; color: #C4C8D2; line-height: 1.5;">
                  <strong>Metadata Specifications:</strong><br>
                  Submitted on: ' . $timestamp . '<br>
                  Client IP: ' . $ipAddress . '<br>
                  Agent: ' . htmlspecialchars($userAgent) . '
                </p>
              </div>
            </td>
          </tr>
          
          <!-- Footer -->
          <tr>
            <td style="background-color: #0d0e12; padding: 20px; text-align: center; border-top: 1px solid #282C34;">
              <p style="margin: 0; font-size: 11px; color: #C4C8D2;">
                This message was auto-routed directly by DunbarDigital. Please do not reply directly to this automated email.
              </p>
            </td>
          </tr>
          
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
';

// Setup Email Headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
// Set From Header using a domain matching pattern to secure deliverability
$headers .= "From: DunbarDigital Mailer <mailer@dunbardigital.com>" . "\r\n";
$headers .= "Reply-To: $name <$email>" . "\r\n";

// Execute native PHP mail transport
if (mail($toEmail, $subject, $message, $headers)) {
    echo json_encode([
        'success' => true,
        'message' => 'Your request was processed successfully.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'The server was unable to route the mail payload. Please verify PHP mail settings.'
    ]);
}
exit;

```
