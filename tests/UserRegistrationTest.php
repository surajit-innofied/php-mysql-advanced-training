<?php
require_once __DIR__ . '/BaseTestCase.php';

class UserRegistrationTest extends BaseTestCase
{
    public function test_user_registration()
    {
        require_once __DIR__ . '/../app/controllers/UserController.php';
        $uc = new UserController();

        $email = 'test_' . time() . '@example.com';
        $data = [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'Secret123'
        ];

        $result = $uc->register($data);
        $this->assertTrue($result);

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        $this->assertNotEmpty($user);
        $this->assertTrue(password_verify($data['password'], $user['password']));
    }
}
