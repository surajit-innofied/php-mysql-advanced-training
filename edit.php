<?php
require "Db_Connect.php";
require "validation.php";

// Fetch categories for dropdown
$cats = $pdo->query("SELECT id, name, type FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$validCatIds = array_map('strval', array_column($cats, 'id'));

// Get product ID
$id = $_GET['id'] ?? null;
if (!$id) {
    die("Product ID missing");
}

// Fetch product by ID
$stmt = $pdo->prepare("SELECT * FROM new_products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found");
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateForm($_POST, $validCatIds);

    if (!$errors) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $price = trim($_POST['price']);
        $category = trim($_POST['category']);
        $category_type = trim($_POST['category_type']);

        // Handle weight/file depending on type
        if ($category_type === "Physical") {
            $weight = $_POST['weight'] !== "" ? $_POST['weight'] : null;
            $file_link = null;
        } elseif ($category_type === "Digital") {
            $file_link = $_POST['file_link'] !== "" ? $_POST['file_link'] : null;
            $weight = null;
        } else {
            $weight = null;
            $file_link = null;
        }

        // Update product
        $stmt = $pdo->prepare("
            UPDATE new_products 
            SET name=?, email=?, price=?, category_id=?, weight=?, file_link=? 
            WHERE id=?
        ");
        $stmt->execute([$name, $email, $price, $category, $weight, $file_link, $id]);

        header("Location: index.php");
        exit;
    }
}
?>

<html>

<head>
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        form {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #f9f9f9;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .btn {
            margin-top: 15px;
            padding: 10px 15px;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-back {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background: #6c757d;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #5a6268;
        }
    </style>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const productType = document.getElementById('productType');
            const category = document.getElementById('category');
            const extraField = document.getElementById('extraField');
            const hiddenCatType = document.getElementById('category_type_input');

            function applyCategoryFilter(selectedType) {
                for (let i = 0; i < category.options.length; i++) {
                    const opt = category.options[i];
                    if (!opt.value) continue;
                    opt.style.display = (selectedType === "" || opt.getAttribute('data-type') === selectedType) ? 'block' : 'none';
                }
            }

            function renderExtraField(type, prefValues = {}) {
                extraField.innerHTML = "";
                if (type === 'Physical') {
                    const weightVal = prefValues.weight ?? "<?= htmlspecialchars($product['weight'] ?? '') ?>";
                    extraField.innerHTML = `
                <div class="form-group">
                    <label for="weight">Weight</label>
                    <input id="weight" name="weight" type="number" step="0.01" value="${weightVal}" required>
                </div>
            `;
                } else if (type === 'Digital') {
                    const linkVal = prefValues.file_link ?? "<?= htmlspecialchars($product['file_link'] ?? '') ?>";
                    extraField.innerHTML = `
                <div class="form-group">
                    <label for="file_link">File Link</label>
                    <input id="file_link" name="file_link" type="url" value="${linkVal}" required>
                </div>
            `;
                }
            }

            // event listeners
            productType.addEventListener('change', function() {
                const val = this.value;
                hiddenCatType.value = val;
                applyCategoryFilter(val);
                category.value = "";
                extraField.innerHTML = "";
            });

            category.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const catType = selectedOption ? selectedOption.getAttribute('data-type') : "";
                if (!productType.value) hiddenCatType.value = catType || "";
                if (productType.value) hiddenCatType.value = productType.value;
                renderExtraField(hiddenCatType.value);
            });

            // initialize on load
            applyCategoryFilter(productType.value || "");
            if (category.value) {
                category.dispatchEvent(new Event('change'));
            }
        });
    </script>

</head>

<body>
    <h2>Edit Product</h2>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $product['name']) ?>">
        <?php if (!empty($errors['name'])): ?><div class="error"><?= $errors['name'] ?></div><?php endif; ?>

        <label>Email:</label>
        <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $product['email']) ?>">
        <?php if (!empty($errors['email'])): ?><div class="error"><?= $errors['email'] ?></div><?php endif; ?>

        <label>Price:</label>
        <input type="text" name="price" value="<?= htmlspecialchars($_POST['price'] ?? $product['price']) ?>">
        <?php if (!empty($errors['price'])): ?><div class="error"><?= $errors['price'] ?></div><?php endif; ?>

        <div class="form-group">
            <label for="productType">Product Type</label>
            <select id="productType">
                <option value="">Select types</option>
                <option value="Physical" <?= ($product['category_type'] === 'Physical') ? 'selected' : '' ?>>Physical</option>
                <option value="Digital" <?= ($product['category_type'] === 'Digital') ? 'selected' : '' ?>>Digital</option>
            </select>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <?php foreach ($cats as $c):
                    $capType = ucfirst(strtolower($c['type'])); // "Physical" or "Digital"
                    $sel = ($product['category'] == $c['id']) ? 'selected' : '';
                ?>
                    <option value="<?= htmlspecialchars($c['id']) ?>" data-type="<?= $capType ?>" <?= $sel ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- JS keeps this hidden input in sync -->
        <input type="hidden" name="category_type" id="category_type_input" value="<?= htmlspecialchars($product['category_type']) ?>">

        <div id="extraField"></div>

        <div class="btn-row">
            <button type="submit" class="btn">Update Product</button>
        </div>

        <div style="margin-top:10px;">
            <a href="index.php" class="btn btn-secondary" style="display:inline-block; text-align:center; width:100%; padding:10px; text-decoration:none; color:#fff; border-radius:6px;">Back</a>
        </div>
    </form>
    </div>
    <a href="index.php" class="btn-back">⬅ Back</a>
</body>

</html>