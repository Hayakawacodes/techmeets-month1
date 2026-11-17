<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>コメント投稿</title>
</head>
<body>

<h1>コメント投稿フォーム</h1>

<form method="POST">
  <label>名前:</label>
  <input type="text" name="name"><br>
  <label>コメント:</label>
  <textarea name="comment"></textarea><br>
  <button type="submit">投稿する</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 入力値の受け取り
    $name    = trim($_POST["name"] ?? "");
    $comment = trim($_POST["comment"] ?? "");

    // ===にすることで入力値がからかどうか調べられる
    // コメントにもチェックがついた
    $errors = [];
    if ($name === "") {
        $errors[] = "名前を入力してください。";
    }
    if ($comment === "") {
        $errors[] = "コメントを入力してください。";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p>" . $error . "</p>";
        }
    } else {
        //　XSS対策として、htmlspecialcharsを使っている
        $safeName    = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
        $safeComment = htmlspecialchars($comment, ENT_QUOTES, "UTF-8");

        echo "<p>" . $safeName . "さんのコメント:</p>";
        echo "<p>" . nl2br($safeComment) . "</p>";
    }
}
?>

</body>
</html>
