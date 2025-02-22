<?php
session_start();
session_regenerate_id(true); // 🔹 セッションの有効期限をリフレッシュ

// 必須項目チェック
$errors = [];
if (empty($_POST['name'])) $errors[] = "お名前を入力してください。";
if (empty($_POST['kana'])) $errors[] = "フリガナを入力してください。";
if (empty($_POST['tel']) || !preg_match("/^[0-9]+$/", $_POST['tel'])) $errors[] = "お電話番号は半角数字のみで入力してください。";
if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "正しいメールアドレスを入力してください。";
if (empty($_POST['prefecture'])) $errors[] = "都道府県を選択してください。";
if (empty($_POST['privacy'])) $errors[] = "個人情報保護方針に同意してください。";

// エラーがあれば前のページに戻る
if (!empty($errors)) {
  echo "<script>alert('" . implode("\\n", $errors) . "'); history.back();</script>";
  exit;
}

// 入力データをセッションに保存
$_SESSION['form_data'] = $_POST;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>確認画面</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="confirm-container">
    <h1>入力内容の確認</h1>
    <p>以下の内容で送信します。問題なければ「送信する」ボタンを押してください。</p>

    <table class="confirm-table">
      <tr>
        <th>お名前</th>
        <td><?= htmlspecialchars($_POST['name'], ENT_QUOTES) ?></td>
      </tr>
      <tr>
        <th>フリガナ</th>
        <td><?= htmlspecialchars($_POST['kana'], ENT_QUOTES) ?></td>
      </tr>
      <tr>
        <th>お電話番号</th>
        <td><?= htmlspecialchars($_POST['tel'], ENT_QUOTES) ?></td>
      </tr>
      <tr>
        <th>メールアドレス</th>
        <td><?= htmlspecialchars($_POST['email'], ENT_QUOTES) ?></td>
      </tr>
      <tr>
        <th>都道府県</th>
        <td><?= htmlspecialchars($_POST['prefecture'], ENT_QUOTES) ?></td>
      </tr>
      <tr>
        <th>ご計画内容</th>
        <td><?= htmlspecialchars($_POST['plan'] ?? "なし", ENT_QUOTES) ?></td>
      </tr>
      <tr>
        <th>建売住宅の購入予定</th>
        <td><?= htmlspecialchars(implode(", ", $_POST['purchase_plan'] ?? ["なし"]), ENT_QUOTES) ?></td>
      </tr>
      <tr>
        <th>その他（自由記入）</th>
        <td><?= nl2br(htmlspecialchars($_POST['message'] ?? "なし", ENT_QUOTES)) ?></td>
      </tr>
    </table>

    <!-- ★ ここにフォームを追加（これがないと送信されない） -->
    <form action="contact.php" method="post">
      <?php foreach ($_POST as $key => $value): ?>
        <?php if (is_array($value)): ?>
          <?php foreach ($value as $sub_value): ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>[]" value="<?= htmlspecialchars($sub_value, ENT_QUOTES) ?>">
          <?php endforeach; ?>
        <?php else: ?>
          <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value, ENT_QUOTES) ?>">
        <?php endif; ?>
      <?php endforeach; ?>

      <div class="confirm-buttons">
        <button type="button" class="back-btn" onclick="history.back();">修正する</button>
        <button type="submit" class="send-btn">送信する</button>
      </div>
    </form>
  </div>
</body>

</html>