<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/ResponseFormatter.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    private function input()
    {
        $json = json_decode(file_get_contents("php://input"), true);
        return $json ?? $_POST;
    }

    public function list()
    {
        $users = $this->userModel->getAllUsers();

        return ResponseFormatter::success(
            "Users retrieved successfully",
            $users
        );
    }

    public function get($id)
    {
        $user = $this->userModel->getUser($id);

        if (!$user) {
            return ResponseFormatter::error(
                "User not found",
                404
            );
        }

        return ResponseFormatter::success(
            "User found",
            $user
        );
    }

    public function update($id)
    {
        $data = $this->input();

        if (empty($data['name']) || empty($data['email'])) {
            return ResponseFormatter::error(
                "Missing fields",
                400
            );
        }

        $ok = $this->userModel->updateUser($id, $data['name'], $data['email']);

        if (!$ok) {
            return ResponseFormatter::error(
                "Failed to update user",
                500
            );
        }

        return ResponseFormatter::success(
            "User updated successfully",
            [
                "id" => $id,
                "name" => $data['name'],
                "email" => $data['email']
            ]
        );
    }

    public function delete($id)
    {
        $ok = $this->userModel->deleteUser($id);

        if (!$ok) {
            return ResponseFormatter::error(
                "Failed to delete user or already removed",
                400
            );
        }

        return ResponseFormatter::success(
            "User deleted successfully",
            ["id" => $id]
        );
    }
}
