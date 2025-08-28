<?php
function validateForm($data, $validCatIds) {
    $errors = [];

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $price = trim($data['price'] ?? '');
    $category = trim($data['category'] ?? '');
    $category_type = trim($data['category_type'] ?? '');

    // validate email type
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    //  validate name type
    if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors['name'] = "Name must contain only letters and spaces.";
    }

    // validate price type
    if ($price === "" || !is_numeric($price) || $price <= 0) {
        $errors['price'] = "Price must be a positive number.";
    }

    //  validate category type
    if ($category_type === "" || !in_array($category_type, ["Physical", "Digital"], true)) {
        $errors['category_type'] = "Please select a valid category type.";
    }

    // validate category id
    if ($category === "" || !in_array((string)$category, $validCatIds, true)) {
        $errors['category'] = "Please select a valid category.";
    }

    return $errors;
}
?>
