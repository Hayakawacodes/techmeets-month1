<?php
// =============================================
// お問い合わせフォーム（contact.php）
// 入力画面 → (エラーがあれば入力画面に戻る) → 確認画面
// =============================================

// ① XSS対策として、htmlspecialcharsを使う関数
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ② 4つの入力値（name:名前、email:メールアドレス、subject:件名、message:メッセージ）
$name    = '';
$email   = '';
$subject = '';
$message = '';

// errors：エラーメッセージが入る場所
$errors = [];

//  isConfirm：確認画面を表示するかどうか
$isConfirm = false;

// ③ フォームが送られたかどうか
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ④ 入力値を受け取る（trimで前後の空白を取り除く）
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // ---  ⑤ 4つの値の入力チェック（空ならエラーを入れる） ---

    // 名前
    if ($name === '') {
        $errors['name'] = '名前を入力してください。';
    }

    // メールアドレス（空チェック → 形式チェック）
    if ($email === '') {
        $errors['email'] = 'メールアドレスを入力してください。';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'メールアドレスの形式が正しくありません。';
    }

    // 件名
    if ($subject === '') {
        $errors['subject'] = '件名を入力してください。';
    }

    // メッセージ
    if ($message === '') {
        $errors['message'] = 'メッセージを入力してください。';
    }

    // ⑥ エラーがなく、戻るボタンで表示されたのでもない
    if (count($errors) === 0 && !isset($_POST['back'])) {
        $isConfirm = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 0 16px;
        }
        label {
            display: block;
            margin-top: 16px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        textarea {
            height: 150px;
        }
        .error {
            color: red;
            font-size: 14px;
            margin: 4px 0 0;
        }
        button {
            margin-top: 20px;
            padding: 10px 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            width: 30%;
            background: #f5f5f5;
        }
    </style>
</head>
<body>

<?php if ($isConfirm): ?>

    <!-- ========== 確認画面 ========== -->
    <h1>入力内容の確認</h1>
    <p>以下の内容でよろしいですか？</p>

    <table>
        <tr>
            <th>名前</th>
            <td><?php echo h($name); ?></td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td><?php echo h($email); ?></td>
        </tr>
        <tr>
            <th>件名</th>
            <td><?php echo h($subject); ?></td>
        </tr>
        <tr>
            <th>メッセージ</th>
            <!-- nl2br で改行を <br> に変換して表示（h() の後にかけるのがポイント） -->
            <td><?php echo nl2br(h($message)); ?></td>
        </tr>
    </table>

    <!-- 「戻る」を押すと入力した値を持ったまま入力画面に戻る -->
    <form action="contact.php" method="post">
        <input type="hidden" name="name" value="<?php echo h($name); ?>">
        <input type="hidden" name="email" value="<?php echo h($email); ?>">
        <input type="hidden" name="subject" value="<?php echo h($subject); ?>">
        <input type="hidden" name="message" value="<?php echo h($message); ?>">
        <input type="hidden" name="back" value="1">
        <button type="submit">戻って修正する</button>
    </form>

<?php else: ?>

    <!-- ========== 入力画面 ========== -->
    <h1>お問い合わせ</h1>

    <form action="contact.php" method="post">

        <label for="name">名前</label>
        <input type="text" id="name" name="name" value="<?php echo h($name); ?>">
        <?php if (isset($errors['name'])): ?>
            <p class="error"><?php echo h($errors['name']); ?></p>
        <?php endif; ?>

        <label for="email">メールアドレス</label>
        <input type="text" id="email" name="email" value="<?php echo h($email); ?>">
        <?php if (isset($errors['email'])): ?>
            <p class="error"><?php echo h($errors['email']); ?></p>
        <?php endif; ?>

        <label for="subject">件名</label>
        <input type="text" id="subject" name="subject" value="<?php echo h($subject); ?>">
        <?php if (isset($errors['subject'])): ?>
            <p class="error"><?php echo h($errors['subject']); ?></p>
        <?php endif; ?>

        <label for="message">メッセージ</label>
        <textarea id="message" name="message"><?php echo h($message); ?></textarea>
        <?php if (isset($errors['message'])): ?>
            <p class="error"><?php echo h($errors['message']); ?></p>
        <?php endif; ?>

        <button type="submit">確認する</button>
    </form>

<?php endif; ?>

</body>
</html>
