<?php
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = $GLOBALS['pdo'];
        $_SESSION = [];
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        $_SESSION = [];
        parent::tearDown();
    }
}
