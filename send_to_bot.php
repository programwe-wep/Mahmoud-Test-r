<?php
header('Content-Type: application/json');

// التحقق من أن الطلب POST وأنه يحتوي على ملف
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['photo'])) {
    echo json_encode(['success' => false, 'error' => 'طلب غير صالح']);
    exit;
}

// معلومات بوت التليجرام
$botToken = 'YOUR_BOT_TOKEN'; // استبدل هذا ب token البوت الخاص بك
$chatId = 'YOUR_CHAT_ID'; // استبدل هذا ب chat id البوت أو المجموعة

// معلومات الملف المرفوع
$photo = $_FILES['photo'];
$filePath = $photo['tmp_name'];
$fileName = basename($photo['name']);

// إنشاء بيانات الطلب لإرسال الصورة إلى بوت التليجرام
$url = "https://api.telegram.org/bot{$botToken}/sendPhoto";

$postFields = [
    'chat_id' => $chatId,
    'photo' => new CURLFile($filePath, mime_content_type($filePath), $fileName),
    'caption' => 'تم استلام صورة جديدة من الموقع'
];

// إعداد وإرسال الطلب باستخدام cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // فقط للتطوير، قم بتعطيله في الإنتاج

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// تحقق من الاستجابة
if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if ($responseData && $responseData['ok']) {
        echo json_encode(['success' => true]);
        exit;
    }
}

// في حالة الفشل
echo json_encode([
    'success' => false,
    'error' => 'فشل في إرسال الصورة إلى البوت',
    'telegram_response' => $response
]);
?>