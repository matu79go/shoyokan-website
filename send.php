<?php
/**
 * お問い合わせフォーム送信処理
 * PHPMailer + SMTP認証版（さくらレンタルサーバー対応）
 */

// PHPMailer読み込み
require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 文字コード設定
mb_language('Japanese');
mb_internal_encoding('UTF-8');

// 設定
$config = [
    'to_email' => 'takachika3.5@gmail.com',       // 道場のメールアドレス
    'to_name' => '藤枝将陽館',
    'from_email' => 'noreply@shoyokan.jp',        // 送信元メールアドレス
    'from_name' => '藤枝将陽館 お問い合わせフォーム',
    'subject_prefix' => '【藤枝将陽館】',
    // SMTP設定（さくらレンタルサーバー）
    'smtp_host' => 'shoyokan.sakura.ne.jp',
    'smtp_port' => 587,
    'smtp_user' => 'noreply@shoyokan.jp',
    'smtp_pass' => 'shoyokan123456789',
];

// POSTリクエスト以外は拒否
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// 入力値の取得とサニタイズ
$subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// バリデーション
$errors = [];

if (empty($name)) {
    $errors[] = 'お名前を入力してください。';
}

if (empty($email)) {
    $errors[] = 'メールアドレスを入力してください。';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = '有効なメールアドレスを入力してください。';
}

if (empty($message)) {
    $errors[] = 'お問い合わせ内容を入力してください。';
}

// エラーがある場合
if (!empty($errors)) {
    showErrorPage($errors);
    exit;
}

// お問い合わせ項目が空の場合のデフォルト
if (empty($subject)) {
    $subject = 'お問い合わせ';
}

// 送信日時
$sendDate = date('Y年m月d日 H:i');

// 電話番号が空の場合
$phoneDisplay = empty($phone) ? '未入力' : $phone;
$messageDisplay = empty($message) ? '未入力' : $message;

// 道場宛メール本文
$adminMailBody = <<<EOT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　ホームページからお問い合わせがありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

【お問い合わせ項目】
{$subject}

【お名前】
{$name} 様

【メールアドレス】
{$email}

【電話番号】
{$phoneDisplay}

【お問い合わせ内容】
{$messageDisplay}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
送信日時: {$sendDate}
IPアドレス: {$_SERVER['REMOTE_ADDR']}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
EOT;

// 自動返信メール本文
$autoReplyBody = <<<EOT
{$name} 様

この度は、藤枝将陽館へお問い合わせいただき、
誠にありがとうございます。

以下の内容でお問い合わせを受け付けいたしました。
2営業日以内に担当者よりご連絡いたしますので、
今しばらくお待ちくださいませ。

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　お問い合わせ内容
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

【お問い合わせ項目】
{$subject}

【お名前】
{$name} 様

【メールアドレス】
{$email}

【電話番号】
{$phoneDisplay}

【お問い合わせ内容】
{$messageDisplay}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

※このメールは自動送信されています。
※このメールに心当たりのない場合は、お手数ですが
　破棄していただきますようお願いいたします。

──────────────────────────────
NPO法人 日本空手松濤連盟 藤枝将陽館
〒426-0067 静岡県藤枝市前島3-10-43
TEL: 090-6098-8133
URL: https://www.shoyokan.jp/
──────────────────────────────
EOT;

// 件名
$adminSubject = $config['subject_prefix'] . $subject . '（' . $name . '様）';
$autoReplySubject = $config['subject_prefix'] . 'お問い合わせありがとうございます';

/**
 * PHPMailerでメール送信
 */
function sendMailWithSMTP($config, $to, $toName, $subject, $body, $replyTo = null) {
    $mail = new PHPMailer(true);

    try {
        // SMTP設定
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['smtp_port'];

        // 文字コード
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // 送信元・送信先
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to, $toName);

        // 返信先
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        // 本文
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// 道場宛メール送信
$adminResult = sendMailWithSMTP(
    $config,
    $config['to_email'],
    $config['to_name'],
    $adminSubject,
    $adminMailBody,
    $email  // 返信先をお客様のメールアドレスに
);

// 自動返信メール送信
$autoReplyResult = sendMailWithSMTP(
    $config,
    $email,
    $name,
    $autoReplySubject,
    $autoReplyBody
);

// 結果に応じてリダイレクト
if ($adminResult) {
    header('Location: thanks.html');
} else {
    showErrorPage(['メールの送信に失敗しました。お手数ですが、お電話でお問い合わせください。']);
}

/**
 * エラーページを表示
 */
function showErrorPage($errors) {
    $errorList = implode('<br>', array_map('htmlspecialchars', $errors));
    echo <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>エラー - 藤枝将陽館</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;700&family=Zen+Kaku+Gothic+New:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full text-center">
        <div class="text-red-500 text-6xl mb-4">&#9888;</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">送信エラー</h1>
        <p class="text-gray-600 mb-6">{$errorList}</p>
        <a href="javascript:history.back()" class="inline-block bg-blue-900 hover:bg-blue-800 text-white font-bold px-8 py-3 rounded transition-colors">
            戻る
        </a>
    </div>
</body>
</html>
HTML;
}
