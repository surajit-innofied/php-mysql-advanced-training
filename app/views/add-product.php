<?php
require_once __DIR__ . '/../../app/controllers/ProductController.php';
require_once __DIR__ . '/../../config/Db_Connect.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__.'/../middleware/auth.php';

session_start();

// Check if logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../public/login.php");
    exit;
}

// Allow only admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$errors = [];
$controller = new ProductController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $controller->add($_POST, $_FILES);
    if (empty($errors)) {
        header("Location: ../../public/index.php?success=1");
        exit;
    }
}

// Fetch categories grouped by type
$catStmt = $pdo->query("SELECT id, name, type FROM categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$categoriesByType = [
    'physical' => [],
    'digital' => []
];
foreach ($categories as $cat) {
    $categoriesByType[strtolower($cat['type'])][] = $cat;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #04AA6D;
            margin-top: 30px;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: 0.3s;
        }

        input:focus,
        select:focus {
            border-color: #04AA6D;
            box-shadow: 0 0 5px rgba(4,170,109,0.3);
        }

        button {
            padding: 10px 20px;
            background: #04AA6D;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #028a56;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 14px;
            background: gray;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .back-btn:hover {
            background: #555;
        }

        .error-box {
            color: red;
            background: #ffecec;
            padding: 10px;
            border: 1px solid red;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        ul {
            margin: 0;
            padding-left: 18px;
        }
    </style>
</head>
<body>
    <h1>Add New Product</h1>
    <div class="container">
        <!-- Back button -->
        <a href="../../public/index.php" class="back-btn">← Back to Products</a>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="name"> Name:</label>
            <input type="text" name="name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="category_type">Category Type:</label>
            <select id="category_type" name="category_type" onchange="updateCategoryDropdown()" required>
                <option value="">-- Select Type --</option>
                <option value="physical">Physical</option>
                <option value="digital">Digital</option>
            </select>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">-- Select Category --</option>
            </select>

            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" required>

            <div id="weightField" style="display:none;">
                <label for="weight">Weight:</label>
                <input type="text" name="weight">
            </div>

            <div id="fileField" style="display:none;">
                <label for="file_link">File:</label>
                <input type="file" name="file_link">
            </div>

            <button type="submit">Add Product</button>
        </form>
    </div>

    <script>
        const categoriesByType = <?php echo json_encode($categoriesByType); ?>;

        function updateCategoryDropdown() {
            const typeSelect = document.getElementById('category_type');
            const categorySelect = document.getElementById('category');
            const type = typeSelect.value.toLowerCase();

            categorySelect.innerHTML = '<option value="">-- Select Category --</option>';
            if (categoriesByType[type]) {
                categoriesByType[type].forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    categorySelect.appendChild(opt);
                });
            }
        }

        document.getElementById('category_type').addEventListener('change', function () {
            const weightField = document.getElementById('weightField');
            const fileField = document.getElementById('fileField');
            
            if (this.value === 'physical') {
                weightField.style.display = 'block';
                fileField.style.display = 'none';
            } else if (this.value === 'digital') {
                fileField.style.display = 'block';
                weightField.style.display = 'none';
            } else {
                weightField.style.display = 'none';
                fileField.style.display = 'none';
            }
        });
    </script>
</body>
</html>
