<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        helper(['form']);
        
        // Check for form submission using case-insensitive comparison
        if (strtoupper($this->request->getMethod()) === 'POST') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            
            $userModel = new UserModel();
            $user = $userModel->where('username', $username)->first();
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session data with role
                session()->set([
                    'user_id'   => $user['id'],
                    'username'  => $user['username'],
                    'logged_in' => true,
                    'role'      => $user['role'],
                    'is_admin'  => ($user['role'] === 'Admin')
                ]);
                
                // Set success message
                session()->setFlashdata('success', 'Welcome back, ' . $username . '! You have successfully logged in.');
                
                // Redirect based on role
                if ($user['role'] === 'Admin') {
                    return redirect()->to(site_url('admin'));
                } else {
                    return redirect()->to(site_url('cashier'));
                }
            } else {
                return view('auth/login', ['error' => 'Invalid username or password']);
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}