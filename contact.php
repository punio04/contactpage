<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// 🔹 env.php を読み込む
$config = require __DIR__ . '/env.php';

// セッション開始
session_start();

// フォームデータ取得
$form_data = $_SESSION['form_data'] ?? null;
if (!$form_data) {
  echo "<script>alert('セッションが切れました。最初からやり直してください。'); window.location.href='index.html';</script>";
  exit;
}

// 🔹 送信先メールアドレス（管理者）
$recipients = [
  'gudeko.0417@gmail.com',
  'gude_0417@icloud.com'
];

// メール送信設定（管理者宛）
$mail = new PHPMailer(true);

try {
  // 🔹 SMTP 設定
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = $config['GMAIL_USERNAME'];
  $mail->Password   = $config['GMAIL_APP_PASSWORD'];
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;

  // 🔹 送信者情報
  if (filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
    $mail->setFrom($form_data['email'], "=?UTF-8?B?" . base64_encode("みなの家") . "?=");
  } else {
    echo "エラー: 無効なメールアドレスです。";
    exit();
  }

  // 🔹 受信者設定（管理者宛）
  foreach ($recipients as $recipient) {
    $mail->addAddress($recipient);
  }

  // 🔹 件名（管理者宛）
  $mail->Subject = "=?UTF-8?B?" . base64_encode("【みなの家】お問合わせがありました。") . "?=";

  // 🔹 メール内容（管理者宛）
  $mail->isHTML(true);
  $mail->CharSet = "UTF-8";
  $mail->Encoding = "base64";
  $mail->Body = "
        <p><strong>お名前:</strong> {$form_data['name']}</p>
        <p><strong>フリガナ:</strong> {$form_data['kana']}</p>
        <p><strong>お電話番号:</strong> {$form_data['tel']}</p>
        <p><strong>メールアドレス:</strong> {$form_data['email']}</p>
        <p><strong>都道府県:</strong> {$form_data['prefecture']}</p>
        <p><strong>ご計画内容:</strong><br> " . nl2br($form_data['plan']) . "</p>
        <p><strong>建売住宅の購入予定:</strong><br> " . implode(", ", $form_data['purchase_plan'] ?? ["なし"]) . "</p>
        <p><strong>その他（自由記入）:</strong><br> " . nl2br($form_data['message'] ?? "なし") . "</p>
    ";

  // メール送信（管理者宛）
  $mail->send();
} catch (Exception $e) {
  echo "<script>alert('メールの送信に失敗しました: {$mail->ErrorInfo}'); history.back();</script>";
  exit;
}

// 🔹 ユーザー宛の自動返信メール設定
$user_mail = new PHPMailer(true);

try {
  // 🔹 SMTP 設定
  $user_mail->isSMTP();
  $user_mail->Host       = 'smtp.gmail.com';
  $user_mail->SMTPAuth   = true;
  $user_mail->Username   = $config['GMAIL_USERNAME'];
  $user_mail->Password   = $config['GMAIL_APP_PASSWORD'];
  $user_mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $user_mail->Port       = 587;

  // 🔹 送信者情報
  $user_mail->setFrom($config['GMAIL_USERNAME'], "=?UTF-8?B?" . base64_encode("みなの家") . "?=");
  $user_mail->addAddress($form_data['email']);

  // 🔹 メール件名（ユーザー宛）
  $user_mail->Subject = "=?UTF-8?B?" . base64_encode("【みなの家】お問い合わせありがとうございます") . "?=";

  // 🔹 メール本文（ユーザー宛）
  $user_mail->isHTML(true);
  $user_mail->CharSet = "UTF-8";
  $user_mail->Encoding = "base64";
  $user_mail->Body = "
        <p>{$form_data['name']} 様</p><br>
        <p>この度はお問い合わせいただき、誠にありがとうございます。</p>
        <p>以下の内容で受け付けましたので、ご確認ください。</p><br>
        <hr><br>
        <p><strong>■ お名前:</strong> {$form_data['name']}</p>
        <p><strong>■ フリガナ:</strong> {$form_data['kana']}</p>
        <p><strong>■ お電話番号:</strong> {$form_data['tel']}</p>
        <p><strong>■ メールアドレス:</strong> {$form_data['email']}</p>
        <p><strong>■ 都道府県:</strong> {$form_data['prefecture']}</p>
        <p><strong>■ ご計画内容:</strong><br> " . nl2br($form_data['plan']) . "</p>
        <p><strong>■ 建売住宅の購入予定:</strong><br> " . implode(", ", $form_data['purchase_plan'] ?? ["なし"]) . "</p>
        <p><strong>■ その他（自由記入）:</strong><br> " . nl2br($form_data['message'] ?? "なし") . "</p>
        <br>
        <hr><br>
        <p>担当者より折り返しご連絡いたしますので、しばらくお待ちください。</p><br>
        <p>みなの家</p>
    ";

  // メール送信（ユーザー宛）
  $user_mail->send();
} catch (Exception $e) {
  echo "<script>alert('自動返信メールの送信に失敗しました: {$user_mail->ErrorInfo}'); history.back();</script>";
  exit;
}

// セッション削除
unset($_SESSION['form_data']);

// 🔹 送信完了メッセージを表示
echo "<script>alert('お問い合わせが送信されました。確認次第ご連絡いたします。'); window.location.href='index.html';</script>";

exit;
