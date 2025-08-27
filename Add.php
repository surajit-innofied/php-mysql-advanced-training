<?php
require "Db_Connect.php";
require "validation.php"; // external validation file

$errors = [];
$success = "";

// Fetch categories
$cats = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$validCatIds = array_map('strval', array_column($cats, 'id'));

// Preserve posted values
$email = $_POST["email"] ?? "";
$name  = $_POST["name"]  ?? "";
$price = $_POST["price"] ?? "";
$category_id = $_POST["category"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $errors = validateForm($_POST, $validCatIds);

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO new_products (email, name, price, category_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$email, $name, $price, $category_id]);

        $success = "✅ Product added successfully! Redirecting to list...";
        $email = $name = $price = $category_id = "";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <?php if ($success): ?>
        <meta http-equiv="refresh" content="2;url=index.php">
    <?php endif; ?>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 20px; }
        h2 { text-align: center; color: #333; }
        form {
            width: 400px; margin: auto; background: white; padding: 20px;
            border-radius: 8px; box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, select {
            width: 100%; padding: 10px; margin-top: 5px;
            border: 1px solid #ccc; border-radius: 5px;
        }
        button {
            margin-top: 15px; padding: 10px; width: 100%;
            background: #28a745; color: white; border: none; border-radius: 5px;
        }
        button:hover { background: #218838; cursor: pointer; }
        .back-link { display: block; margin-top: 15px; text-align: center; }
        .back-link a { color: #007bff; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
        .error { color: #d9534f; font-size: 14px; margin-top: 4px; display:block; }
        .success { background: #d4edda; padding: 10px; margin-bottom: 10px; color: #155724; border-radius: 5px; text-align: center; }
    </style>
</head>
<body>
    <h2>Add Product</h2>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        <?php if (!empty($errors['email'])): ?>
            <span class="error"><?= $errors['email'] ?></span>
        <?php endif; ?>

        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
        <?php if (!empty($errors['name'])): ?>
            <span class="error"><?= $errors['name'] ?></span>
        <?php endif; ?>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>" required>
        <?php if (!empty($errors['price'])): ?>
            <span class="error"><?= $errors['price'] ?></span>
        <?php endif; ?>

        <label>Category:</label>
        <select name="category" required>
            <option value="">---select---</option>
            <?php foreach ($cats as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($category_id !== "" && (string)$category_id === (string)$c['id']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['category'])): ?>
            <span class="error"><?= $errors['category'] ?></span>
        <?php endif; ?>

        <button type="submit">Save</button>
    </form>

    <div class="back-link">
        <a href="index.php">⬅ Back</a>
    </div>
</body>
</html>
