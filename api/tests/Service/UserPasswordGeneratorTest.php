<?php

namespace App\Tests\Service;

use App\Service\UserPasswordGenerator;
use PHPUnit\Framework\TestCase;

class UserPasswordGeneratorTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testGeneratePassword(): void
    {
        $passwordGenerator = new UserPasswordGenerator();

        // Test que le mot de passe généré a la longueur attendue
        $length = 10;
        $password = $passwordGenerator->generatePassword($length);
        $this->assertEquals($length, strlen($password));

        // Test que le mot de passe généré ne contient que des caractères valides
        $validCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+';
        $this->assertMatchesRegularExpression('/^[' . preg_quote($validCharacters, '/') . ']+$/', $password);
    }
}
