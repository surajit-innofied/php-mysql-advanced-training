<?php
function validateForm($data, $validCatIds) {
    $errors = [];

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $price = trim($data['price'] ?? '');
    $category = trim($data['category'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors['name'] = "Name must contain only letters and spaces.";
    }

    if ($price === "" || !is_numeric($price) || $price <= 0) {
        $errors['price'] = "Price must be a positive number.";
    }

    if ($category === "" || !in_array((string)$category, $validCatIds, true)) {
        $errors['category'] = "Please select a valid category.";
    }

    return $errors;
}
?>
