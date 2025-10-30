<?php

namespace App\Controllers;

use App\Models\MenuItemModel;
use App\Models\ActivityLogModel;

class MenuController extends BaseController
{
    protected $menuModel;
    protected $activityLog;

    public function __construct()
    {
        $this->menuModel = new MenuItemModel();
        $this->activityLog = new ActivityLogModel();
    }

    // Check if user is admin
    private function checkAuth()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        return null;
    }

    // List all menu items
    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $data['menu_items'] = $this->menuModel->findAll();
        $data['categories'] = $this->menuModel->getCategories();

        return view('admin/menu/list', $data);
    }

    // Add menu item
    public function add()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();

            $rules = [
                'name'        => 'required|min_length[3]|max_length[255]',
                'category'    => 'required',
                'price'       => 'required|decimal',
                'status'      => 'required|in_list[available,unavailable]',
                'image'       => 'uploaded[image]|max_size[image,2048]|is_image[image]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $imageFile = $this->request->getFile('image');
            $imageName = null;

            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                $imageName = $imageFile->getRandomName();
                $imageFile->move(ROOTPATH . 'public/uploads/menu', $imageName);
            }

            $menuData = [
                'name'        => $this->request->getPost('name'),
                'category'    => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'price'       => $this->request->getPost('price'),
                'image'       => $imageName,
                'status'      => $this->request->getPost('status'),
            ];

            if ($this->menuModel->insert($menuData)) {
                $this->activityLog->logActivity(
                    session()->get('user_id'),
                    'add_menu_item',
                    "Added menu item: {$menuData['name']}"
                );

                return redirect()->to('/admin/menu')->with('success', 'Menu item added successfully');
            }

            return redirect()->back()->with('error', 'Failed to add menu item');
        }

        return view('admin/menu/add');
    }

    // Edit menu item
    public function edit($itemId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $menuItem = $this->menuModel->find($itemId);

        if (!$menuItem) {
            return redirect()->to('/admin/menu')->with('error', 'Menu item not found');
        }

        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();

            $rules = [
                'name'        => 'required|min_length[3]|max_length[255]',
                'category'    => 'required',
                'price'       => 'required|decimal',
                'status'      => 'required|in_list[available,unavailable]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $menuData = [
                'name'        => $this->request->getPost('name'),
                'category'    => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'price'       => $this->request->getPost('price'),
                'status'      => $this->request->getPost('status'),
            ];

            // Handle image upload
            $imageFile = $this->request->getFile('image');
            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                // Delete old image
                if ($menuItem['image']) {
                    $oldImagePath = ROOTPATH . 'public/uploads/menu/' . $menuItem['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $imageName = $imageFile->getRandomName();
                $imageFile->move(ROOTPATH . 'public/uploads/menu', $imageName);
                $menuData['image'] = $imageName;
            }

            if ($this->menuModel->update($itemId, $menuData)) {
                $this->activityLog->logActivity(
                    session()->get('user_id'),
                    'edit_menu_item',
                    "Updated menu item: {$menuData['name']}"
                );

                return redirect()->to('/admin/menu')->with('success', 'Menu item updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update menu item');
        }

        $data['menu_item'] = $menuItem;
        return view('admin/menu/edit', $data);
    }

    // Delete menu item
    public function delete($itemId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $menuItem = $this->menuModel->find($itemId);

        if (!$menuItem) {
            return redirect()->to('/admin/menu')->with('error', 'Menu item not found');
        }

        // Delete image file
        if ($menuItem['image']) {
            $imagePath = ROOTPATH . 'public/uploads/menu/' . $menuItem['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->menuModel->delete($itemId)) {
            $this->activityLog->logActivity(
                session()->get('user_id'),
                'delete_menu_item',
                "Deleted menu item: {$menuItem['name']}"
            );

            return redirect()->to('/admin/menu')->with('success', 'Menu item deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete menu item');
    }

    // Toggle status
    public function toggleStatus($itemId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $menuItem = $this->menuModel->find($itemId);

        if ($menuItem) {
            $newStatus = $menuItem['status'] === 'available' ? 'unavailable' : 'available';
            $this->menuModel->update($itemId, ['status' => $newStatus]);

            return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
        }

        return $this->response->setJSON(['success' => false]);
    }
}
