<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'created_at', 'updated_at'];
    protected $returnType = 'array'; // This ensures we get arrays back, not objects
    protected $useTimestamps = true; // Automatically manage created_at and updated_at
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}