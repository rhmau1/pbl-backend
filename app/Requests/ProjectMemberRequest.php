<?php

namespace App\Requests;

final class ProjectMemberRequest
{
    public static function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['project_id']) || !is_numeric($data['project_id'])) {
            $errors['project_id'] = 'Project ID is required and must be numeric';
        }

        if (empty($data['member_id']) || !is_numeric($data['member_id'])) {
            $errors['member_id'] = 'Member ID is required and must be numeric';
        }

        if (empty($data['role']) || !in_array($data['role'], ['member', 'dosen'])) {
            $errors['role'] = 'Role must be either "member" or "dosen"';
        }

        $valid = empty($errors);
        return [$valid, $errors, $data];
    }

    public static function validateUpdate(array $data): array
    {
        $errors = [];

        if (isset($data['project_id']) && (!is_numeric($data['project_id']))) {
            $errors['project_id'] = 'Project ID must be numeric';
        }

        if (isset($data['member_id']) && (!is_numeric($data['member_id']))) {
            $errors['member_id'] = 'Member ID must be numeric';
        }

        if (isset($data['role']) && !in_array($data['role'], ['member', 'dosen'])) {
            $errors['role'] = 'Role must be either "member" or "dosen"';
        }

        $valid = empty($errors);
        return [$valid, $errors, $data];
    }
}
