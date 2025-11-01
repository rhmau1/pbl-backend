<?php
namespace App\Requests;

final class AuthLoginRequest
{
    public static function validate(array $in): array
    {
        $email = trim((string) ($in['email'] ?? ''));
        $pass = (string) ($in['password'] ?? '');
        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Invalid email';
        if (strlen($pass) < 6)
            $errors['password'] = 'Min 6 chars';
        return [$errors === [], $errors, ['email' => $email, 'password' => $pass]];
    }
}
