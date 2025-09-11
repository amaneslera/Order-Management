<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Test extends Controller
{
    public function index()
    {
        echo "<h1>Test Controller</h1>";
        
        // Modify the form to use the correct URL format
        echo "<h2>Login Test Form</h2>";
        echo "<form method='post' action='" . site_url('test/login') . "'>";
        echo "Username: <input type='text' name='username' required><br><br>";
        echo "Password: <input type='password' name='password' required><br><br>";
        echo "<button type='submit'>Test Login</button>";
        echo "</form><hr>";
        
        echo "<h2>Request Information</h2>";
        echo "Request Method: " . $this->request->getMethod() . "<br>";
        echo "POST data: <pre>" . print_r($this->request->getPost(), true) . "</pre>";
        echo "<h2>Session Information</h2>";
        echo "Session data: <pre>" . print_r(session()->get(), true) . "</pre>";
        
        // Test database connection
        try {
            $db = \Config\Database::connect();
            echo "<h2>Database Connection</h2>";
            echo "Database connected successfully<br>";
            
            // Test query
            $query = $db->query("SELECT * FROM users");
            $results = $query->getResult();
            echo "Users found: " . count($results) . "<br>";
            if (count($results) > 0) {
                echo "First user: " . $results[0]->username . "<br>";
            }
        } catch (\Exception $e) {
            echo "<h2>Database Error</h2>";
            echo "Error: " . $e->getMessage() . "<br>";
        }
        
        echo "<h2>Environment</h2>";
        echo "CI_ENVIRONMENT: " . ENVIRONMENT . "<br>";
    }
    
    public function login()
    {
        echo "<h1>Login Test Results</h1>";
        
        echo "Request method: " . $this->request->getMethod() . "<br>";
        echo "POST data: <pre>" . print_r($this->request->getPost(), true) . "</pre>";
        
        if (strtolower($this->request->getMethod()) !== 'post' || empty($this->request->getPost())) {
            echo "<p>No login data submitted or not using POST method.</p>";
            echo "<a href='" . site_url('test') . "'>Back to test page</a>";
            return;
        }
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        echo "<h2>Login Attempt Details</h2>";
        echo "Username: " . $username . "<br>";
        echo "Password received: Yes<br>";
        
        try {
            $userModel = new UserModel();
            $user = $userModel->where('username', $username)->first();
            
            echo "<h3>Database Check</h3>";
            echo "User found in database: " . ($user ? 'Yes' : 'No') . "<br>";
            
            if ($user) {
                echo "User ID: " . $user['id'] . "<br>";
                echo "Username in DB: " . $user['username'] . "<br>";
                echo "Password hash in DB: " . substr($user['password'], 0, 15) . "...<br>";
                
                echo "<h3>Password Verification</h3>";
                $verification = password_verify($password, $user['password']);
                echo "Password verify result: " . ($verification ? 
                    "<span style='color:green;font-weight:bold;'>SUCCESS</span>" : 
                    "<span style='color:red;font-weight:bold;'>FAILED</span>") . "<br>";
                
                if ($verification) {
                    echo "<h3>Login Success!</h3>";
                    echo "User is " . ($username === 'admin' ? 'an admin' : 'a regular user') . "<br>";
                    
                    // Set session data for demonstration
                    session()->set([
                        'user_id'   => $user['id'],
                        'username'  => $user['username'],
                        'logged_in' => true,
                        'is_admin'  => ($username === 'admin')
                    ]);
                    
                    echo "<h3>Session Data (after login)</h3>";
                    echo "<pre>" . print_r(session()->get(), true) . "</pre>";
                    
                    echo "<p>You can now <a href='" . site_url($username === 'admin' ? 'admin' : 'dashboard') . "'>
                        go to the dashboard</a> or <a href='" . site_url('test') . "'>return to test page</a>.</p>";
                }
            }
        } catch (\Exception $e) {
            echo "<h3>Error During Login Test</h3>";
            echo "<p style='color:red'>" . $e->getMessage() . "</p>";
        }
        
        echo "<br><a href='" . site_url('test') . "'>Back to test page</a>";
    }
}