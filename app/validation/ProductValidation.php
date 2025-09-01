<?php
class ProductValidation {
    public static function validate($data, $files) {
        $errors = [];

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $categoryType = strtolower(trim($data['category_type'] ?? ''));
        $category = $data['category'] ?? null;
        $price = $data['price'] ?? null;

        // --- Name Validation ---
        if ($name === '' || strlen($name) < 3) {
            $errors[] = "Product name must be at least 3 characters long.";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            $errors[] = "Product name can only contain letters and spaces.";
        }

        // --- Email Validation ---
        if ($email === '') {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address format.";
        }

        // --- Category Type ---
        if (!in_array($categoryType, ['physical', 'digital'], true)) {
            $errors[] = "Category type is required (physical or digital).";
        }

        // --- Category ---
        if (empty($category) || !ctype_digit((string)$category)) {
            $errors[] = "Category is required.";
        }

        // --- Price ---
        if (!is_numeric($price) || (float)$price < 0) {
            $errors[] = "Price must be a non-negative number.";
        }

        // --- Physical product checks ---
        if ($categoryType === 'physical') {
            $weight = trim($data['weight'] ?? '');
            if ($weight === '') {
                $errors[] = "Weight is required for physical products.";
            } elseif (!is_numeric($weight) || (float)$weight <= 0) {
                $errors[] = "Weight must be a positive number.";
            }
        }

        // --- Digital product checks ---
        if ($categoryType === 'digital') {
            if (!isset($files['file_link']) || $files['file_link']['error'] === UPLOAD_ERR_NO_FILE) {
                $errors[] = "A file upload is required for digital products.";
            } else {
                if ($files['file_link']['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "File upload error (code: {$files['file_link']['error']}).";
                } elseif ($files['file_link']['size'] > 5 * 1024 * 1024) {
                    $errors[] = "File too large (max 5MB).";
                }
            }
        }

        return $errors;
    }
}
?>
