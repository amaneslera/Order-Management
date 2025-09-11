<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // Regular user dashboard
        return view('dashboard/index');
    }
    
    public function admin()
    {
        // Check if user is logged in AND is admin
        if (!session()->get('logged_in') || session()->get('role') !== 'Admin') {
            return redirect()->to('/login');
        }
        
        // Admin dashboard
        return view('dashboard/admin');
    }
    
    public function cashier()
    {
        // Check if user is logged in AND is cashier
        if (!session()->get('logged_in') || session()->get('role') !== 'cashier') {
            return redirect()->to('/login');
        }
        
        // Cashier dashboard
        return view('cashier/dashboardcashier');
    }
}