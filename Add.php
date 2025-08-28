<?php
require "Db_Connect.php";       // your PDO connection; provides $pdo
require "validation.php";      // your validation file (expects validateForm($data, $validCatIds))
require_once "product.php";
require_once "PhysicalProduct.php";
require_once "DigitalProduct.php";

// fetch categories from DB
$cats = $pdo->query("SELECT id, name, type FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// build valid cat ids as strings for validator
$validCatIds = array_map('strval', array_column($cats, 'id'));

// initialize
$errors = [];
$success = "";
$old = [
    'name' => '',
    'email' => '',
    'price' => '',
    'category' => '',
    'category_type' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // preserve old values for re-render
    $old['name'] = $_POST['name'] ?? '';
    $old['email'] = $_POST['email'] ?? '';
    $old['price'] = $_POST['price'] ?? '';
    $old['category'] = $_POST['category'] ?? '';
    $old['category_type'] = $_POST['category_type'] ?? '';

    // run your validation function (it returns an associative array of errors)
    $validationErrors = validateForm($_POST, $validCatIds);

    if (!empty($validationErrors)) {
        // map to $errors indexed list for display
        foreach ($validationErrors as $v) {
            $errors[] = $v;
        }
    } else {
        // validation passed — prepare to save
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $price = $_POST['price'];
        $category_id = $_POST['category'];           // note: validator expects 'category'
        $posted_cat_type = $_POST['category_type'];  // validator expects 'Physical'/'Digital'

        // double-check category type from DB to be safe
        $stmt = $pdo->prepare("SELECT type FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $dbcat = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dbcat) {
            $errors[] = "Selected category not found.";
        } else {
            // db type is probably 'physical' or 'digital' -> normalize
            $db_type = strtolower($dbcat['type']); // 'physical'|'digital'

            if ($db_type === 'physical') {
                $weight = $_POST['weight'] ?? null;
                if ($weight === null || $weight === '') {
                    $errors[] = "Weight is required for physical products.";
                } else {
                    // instantiate PhysicalProduct (constructor expects $pdo first)
                    $product = new PhysicalProduct($pdo, $name, $price, $weight);
                }
            } elseif ($db_type === 'digital') {
                $file_link = $_POST['file_link'] ?? null;
                if (empty($file_link)) {
                    $errors[] = "File link is required for digital products.";
                } else {
                    $product = new DigitalProduct($pdo, $name, $price, $file_link);
                }
            } else {
                // fallback to generic Product
                $product = new Product($pdo);
                $product->setName($name);
                $product->setPrice($price);
            }

            // if no errors, set email/category on product and save
            if (empty($errors)) {
                // set shared fields (your Product class uses setters/getters)
                if (method_exists($product, 'setEmail')) $product->setEmail($email);
                if (method_exists($product, 'setCategoryId')) $product->setCategoryId($category_id);

                // add product (children use $this->pdo internally)
                $product->addProduct();

                // redirect to index after successful save
                header("Location: index.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add Product</title>
    <style>
        /* ---------- Page Layout ---------- */
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* ---------- Container Box ---------- */
        .container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            width: 420px;
        }

        /* ---------- Container Heading ---------- */
        .container h1 {
            text-align: center;
            margin-bottom: 18px;
            color: #333;
            font-size: 20px;
        }

        /* ---------- Form Group Styling ---------- */
        .form-group { margin-bottom: 12px; }
        
        .form-group label {
            display:block;
            font-weight:600;
            margin-bottom:6px;
            color:#444;
            font-size:14px;
        }

        .form-group input, .form-group select {
            width:100%;
            padding:8px 10px;
            border-radius:6px;
            border:1px solid #d0d7de;
            font-size:14px;
        }

        .form-group input:focus, .form-group select:focus {
            outline:none;
            border-color:#007bff;
        }

        /* ---------- Primary Button ---------- */
        .btn {
            width:100%;
            padding:10px;
            background:#007bff;
            color:#fff;
            border:0;
            border-radius:6px;
            font-size:15px;
            cursor:pointer;
            margin-top:8px;
        }

        .btn:hover { background:#0062d1; }

        /* ---------- Button Row ---------- */
        .btn-row {
            display:flex;
            gap:10px;
        }

        .btn-secondary { background:#6c757d; }
        .btn-secondary:hover { background:#5a6268; }

        /* ---------- Messages (Alerts) ---------- */
        .message {
            padding:10px;
            border-radius:6px;
            margin-bottom:12px;
            font-size:14px;
        }

        .error {
            background:#ffecec;
            color:#b33a3a;
        }
</style>

</head>
<body>
    <div class="container">
        <h1>Add Product</h1>

        <?php if (!empty($errors)): ?>
            <div class="message error">
                <?php foreach ($errors as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" id="addProductForm" novalidate>
            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="<?= htmlspecialchars($old['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= htmlspecialchars($old['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input id="price" name="price" type="number" step="0.01" value="<?= htmlspecialchars($old['price']) ?>" required>
            </div>

            <!-- Product Type select is for UI only; actual submitted field is the hidden input 'category_type' -->
            <div class="form-group">
                <label for="productType">Product Type</label>
                <select id="productType">
                    <option value="">All types</option>
                    <option value="Physical" <?= ($old['category_type'] === 'Physical') ? 'selected' : '' ?>>Physical</option>
                    <option value="Digital" <?= ($old['category_type'] === 'Digital') ? 'selected' : '' ?>>Digital</option>
                </select>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($cats as $c): 
                        $capType = ucfirst(strtolower($c['type'])); // "Physical" or "Digital"
                        $sel = ($old['category'] == $c['id']) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($c['id']) ?>" data-type="<?= $capType ?>" <?= $sel ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- JS will keep this hidden input up-to-date; validator expects this field -->
            <input type="hidden" name="category_type" id="category_type_input" value="<?= htmlspecialchars($old['category_type']) ?>">

            <div id="extraField"></div>

            <div class="btn-row">
                <button type="submit" class="btn">Add Product</button>
            </div>

            <div style="margin-top:10px;">
                <a href="index.php" class="btn btn-secondary" style="display:inline-block; text-align:center; width:100%; padding:10px; text-decoration:none; color:#fff; border-radius:6px;">Back</a>
            </div>
        </form>
    </div>

    <script>
    (function(){
        const productType = document.getElementById('productType');  // UI-only select
        const category = document.getElementById('category');        // category select (name='category')
        const extraField = document.getElementById('extraField');
        const hiddenCatType = document.getElementById('category_type_input');

        // helper to show/hide category options based on selected type (UI only)
        function applyCategoryFilter(selectedType) {
            for (let i=0; i<category.options.length; i++) {
                const opt = category.options[i];
                if (!opt.value) continue; // skip placeholder
                // show if no type selected or types match
                opt.style.display = (selectedType === "" || opt.getAttribute('data-type') === selectedType) ? 'block' : 'none';
            }
        }

        // helper to render extra field based on type
        function renderExtraField(type, prefValues = {}) {
            extraField.innerHTML = "";
            if (type === 'Physical') {
                const weightVal = prefValues.weight ?? '';
                extraField.innerHTML = `
                    <div class="form-group">
                        <label for="weight">Weight</label>
                        <input id="weight" name="weight" type="number" step="0.01" value="${weightVal}" required>
                    </div>
                `;
            } else if (type === 'Digital') {
                const linkVal = prefValues.file_link ?? '';
                extraField.innerHTML = `
                    <div class="form-group">
                        <label for="file_link">File Link</label>
                        <input id="file_link" name="file_link" type="url" value="${linkVal}" required>
                    </div>
                `;
            }
        }

        // update hidden input whenever productType or category changes
        productType.addEventListener('change', function() {
            const val = this.value; // "Physical", "Digital" or ""
            // update hidden category_type (what validator expects)
            hiddenCatType.value = val;

            // filter categories shown
            applyCategoryFilter(val);

            // reset category selection and extra field
            category.value = "";
            extraField.innerHTML = "";
        });

        // when category changes, set the hidden category_type if empty
        category.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const catType = selectedOption ? selectedOption.getAttribute('data-type') : "";
            // if user hasn't chosen a productType explicitly, use category's type
            if (!productType.value) {
                hiddenCatType.value = catType || "";
            }
            // if productType exists, ensure hidden input matches it (keep consistency)
            if (productType.value) {
                hiddenCatType.value = productType.value;
            }

            // show extra field depending on type (use hiddenCatType value)
            const effectiveType = hiddenCatType.value;
            renderExtraField(effectiveType);
        });

        // On page load: apply filter and render extra field if old values exist
        window.addEventListener('DOMContentLoaded', function() {
            // initial apply filter (if productType had old selected)
            applyCategoryFilter(productType.value || '');

            // if category already selected (old POST), trigger change to render extra field and set hidden input
            if (category.value) {
                const ev = new Event('change');
                category.dispatchEvent(ev);
            }
        });

        // Before submit: if hiddenCatType empty but category selected, set it from option
        document.getElementById('addProductForm').addEventListener('submit', function(e){
            if (!hiddenCatType.value && category.value) {
                const opt = category.options[category.selectedIndex];
                hiddenCatType.value = opt ? opt.getAttribute('data-type') : '';
            }
            // let server-side validateForm handle the rest
        });

    })();
    </script>
</body>
</html>
