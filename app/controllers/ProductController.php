<?php
require_once __DIR__ . '/../models/PhysicalProduct.php';
require_once __DIR__ . '/../models/DigitalProduct.php';
require_once __DIR__ . '/../validation/ProductValidation.php';
require_once __DIR__ . '/../../config/Db_Connect.php';

class ProductController
{
    public function add($post, $files)
    {
        if ($_SESSION['role'] !== 'admin') {
            die("Access denied! Only admins can add products.");
        }
        if (!isset($_SESSION['user'])) {
            throw new Exception("Unauthorized access");
        }

        $errors = ProductValidation::validate($post, $files);
        if (!empty($errors)) {
            return $errors;
        }

        // normalize
        $categoryType = strtolower(trim($post['category_type'] ?? ''));
        $categoryId = $post['category'] ?? null;
        $price = is_numeric($post['price'] ?? null) ? (float)$post['price'] : 0.0;

        // prepare variables
        $weight = null;
        $fileLink = null;

        // PHYSICAL
        if ($categoryType === 'physical') {
            $weight = trim($post['weight'] ?? '');
            // create model
            $product = new PhysicalProduct(
                $post['name'],
                $post['email'],
                $categoryType,
                (int)$categoryId,
                $price,
                $weight
            );

            // DIGITAL
        } elseif ($categoryType === 'digital') {
            // handle upload safely
            if (!isset($files['file_link']) || $files['file_link']['error'] === UPLOAD_ERR_NO_FILE) {
                // validation should have caught this, but be safe
                return ['File is required for digital products.'];
            }

            $f = $files['file_link'];

            if ($f['error'] !== UPLOAD_ERR_OK) {
                return ['File upload error code: ' . $f['error']];
            }

            // limit file size (optional) - e.g. 5MB
            if ($f['size'] > 5 * 1024 * 1024) {
                return ['File too large (max 5MB).'];
            }

            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8)) . ($ext ? '.' . $ext : '');
            $target = $uploadDir . DIRECTORY_SEPARATOR . $basename;

            if (!move_uploaded_file($f['tmp_name'], $target)) {
                return ['Failed to move uploaded file.'];
            }

            // store the basename (or store 'uploads/'.$basename if you prefer)
            $fileLink = $basename;

            $product = new DigitalProduct(
                $post['name'],
                $post['email'],
                $categoryType,
                (int)$categoryId,
                $price,
                $fileLink
            );
        } else {
            return ['Invalid category type.'];
        }


        // Insert into DB
        global $pdo;
        // Inside your add() function before DB insert
        $stock = isset($post['stock']) && is_numeric($post['stock']) ? (int)$post['stock'] : 0;
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->execute([$product->getCategory()]);
        if ($stmt->rowCount() === 0) {
            return ["Invalid category selected"];
        }

        try {
            $stmt = $pdo->prepare("
    INSERT INTO new_products (name, email, price, category_id, weight, file_link, stock ,created_by)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
            $stmt->execute([
                $product->getName(),
                $product->getEmail(),
                $product->getPrice(),
                $product->getCategory(),   // must exist in categories.id
                $weight ?: null,
                $fileLink ?: null,
                $stock,
                $_SESSION['user']['id']    // current admin ID
            ]);
        } catch (PDOException $e) {
            return ['Database error: ' . $e->getMessage()];
        }

        return []; // no errors
    }

    public function list()
    {
        global $pdo;
        $stmt = $pdo->query("
            SELECT p.id, p.name, p.email, p.price,p.stock, p.weight, p.file_link,
                   c.name AS category_name, c.type AS category_type
            FROM new_products p
            JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Edit controller 

    public function edit($id, $post, $files)
    {
        if ($_SESSION['role'] !== 'admin') {
            die("Access denied! Only admins can edit products.");
        }

        if (!isset($_SESSION['user'])) {
            throw new Exception("Unauthorized access");
        }

        $errors = ProductValidation::validate($post, $files);
        if (!empty($errors)) {
            return $errors; // return validation errors
        }

        global $pdo;

        // new field: stock
        $stock = isset($post['stock']) ? (int)$post['stock'] : 0;

        if ($post['category_type'] === 'physical') {
            $product = new PhysicalProduct(
                $post['name'],
                $post['email'],
                $post['category_type'],
                $post['category'],
                $post['price'],
                $post['weight']
            );
            $weight = $product->getWeight();
            $fileLink = null;
        } else {
            // If new file uploaded, replace; otherwise keep old one
            if (!empty($files['file_link']['name'])) {
                $fileName = time() . "_" . basename($files['file_link']['name']);
                $targetPath = __DIR__ . '/../../public/uploads/' . $fileName;
                move_uploaded_file($files['file_link']['tmp_name'], $targetPath);
                $fileLink = $fileName;
            } else {
                // Keep old file link
                $stmtOld = $pdo->prepare("SELECT file_link FROM new_products WHERE id=?");
                $stmtOld->execute([$id]);
                $fileLink = $stmtOld->fetchColumn();
            }

            $product = new DigitalProduct(
                $post['name'],
                $post['email'],
                $post['category_type'],
                $post['category'],
                $post['price'],
                $fileLink
            );
            $weight = null;
        }

        // ✅ Update query with stock
        $stmt = $pdo->prepare("UPDATE new_products 
                       SET name=?, email=?, category_id=?, price=?, weight=?, file_link=?, stock=? 
                       WHERE id=?");
        $stmt->execute([
            $product->getName(),
            $product->getEmail(),
            $product->getCategory(),
            $product->getPrice(),
            $weight,
            $fileLink,
            $stock,
            $id
        ]);

        return [];
    }


    // Delete Product
    public function delete($id)
    {
        if ($_SESSION['role'] !== 'admin') {
            die("Access denied! Only admins can delete products.");
        }

        require __DIR__ . '/../../config/Db_Connect.php';
        require __DIR__ . '/../middleware/auth.php';

        if (!isset($_SESSION['user'])) {
            throw new Exception("Unauthorized access");
        }

        $stmt = $pdo->prepare("DELETE FROM new_products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
