<?php
// filepath: c:\xampp\htdocs\Order-Management\app\Controllers\ChatController.php


namespace App\Controllers;

use CodeIgniter\Controller;

class ChatController extends Controller
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        return view('chat/index');
    }
}