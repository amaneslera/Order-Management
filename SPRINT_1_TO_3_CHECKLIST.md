# Sprint 1 to 3 Testing Checklist

Use this checklist while manually testing the system end-to-end.

## Tester Info

- [ ] Tester name:
- [ ] Test date:
- [ ] Environment: Local / Staging / Production
- [ ] Base URL verified
- [ ] Browser(s) used documented

## Pre-Test Setup

- [ ] Database imported successfully (schema)
- [ ] Demo data imported (optional)
- [ ] .env database config is correct
- [ ] App loads without fatal errors
- [ ] Login page opens
- [ ] Admin account can login
- [ ] Cashier account can login

---

## Sprint 1 Checklist

### 1) Owner Login

- [ ] Open login page
- [ ] Login as Admin with valid credentials
- [ ] Redirect goes to admin dashboard
- [ ] Invalid password shows error message
- [ ] Logout works and redirects to login page

Notes:
- Result:
- Issue found:

### 2) Search Products / Orders (Cashier Side)

- [ ] Login as Cashier
- [ ] Open POS search page
- [ ] Search using an existing order number
- [ ] Search result opens the correct order details
- [ ] Search using non-existing order number shows friendly error

Notes:
- Result:
- Issue found:

### 3) Modify Order Items

- [ ] Open a pending order in POS
- [ ] Add item to order
- [ ] Update item quantity
- [ ] Remove item from order
- [ ] Order total updates correctly after each action
- [ ] Cannot set quantity below 1
- [ ] Stock validation blocks impossible quantity

Notes:
- Result:
- Issue found:

---

## Sprint 2 Checklist

### 4) View Real-Time Inventory

- [ ] Login as Admin
- [ ] Open inventory page
- [ ] Stock table loads item quantities
- [ ] Leave page open and change stock in another session
- [ ] Inventory values auto-refresh without manual page reload
- [ ] Summary cards (In Stock / Low Stock / Out of Stock) update correctly

Notes:
- Result:
- Issue found:

### 5) Receive Low-Stock Alerts

- [ ] Open alerts page as Admin
- [ ] Reduce item stock below threshold
- [ ] Trigger stock check (manual button or command)
- [ ] Low-stock alert appears in alerts view
- [ ] Out-of-stock alert appears for zero stock
- [ ] Duplicate alert is not repeatedly created in short interval
- [ ] SMS status changes when SMS sending succeeds (if SMS config is enabled)

Notes:
- Result:
- Issue found:

### 6) Cashier Login

- [ ] Login as Cashier with valid credentials
- [ ] Redirect goes to cashier/POS page
- [ ] Cashier cannot access admin-only pages directly
- [ ] Cashier logout works

Notes:
- Result:
- Issue found:

---

## Sprint 3 Checklist

### 7) Manage Inventory

- [ ] Admin can open inventory management page
- [ ] Admin can add stock
- [ ] Admin can subtract stock
- [ ] Admin cannot make stock negative
- [ ] Admin can update low-stock threshold
- [ ] Recent stock logs are recorded with user and reason

Notes:
- Result:
- Issue found:

### 8) Manage Cashier Accounts

- [ ] Admin can open user management page
- [ ] Admin can add cashier account
- [ ] Admin can edit cashier username/role/password
- [ ] Admin can delete cashier account
- [ ] Admin cannot delete own currently logged-in account

Notes:
- Result:
- Issue found:

### 9) View Cashier Logs

- [ ] Admin can open activity logs page
- [ ] Cashier actions appear in logs
- [ ] Filter by action works
- [ ] Filter by role works
- [ ] Filter by date range works
- [ ] Login and logout events are logged

Notes:
- Result:
- Issue found:

---

## End-to-End Flow Validation

- [ ] Customer order is created
- [ ] Cashier finds order and edits items
- [ ] Cashier processes payment
- [ ] Inventory is deducted after payment
- [ ] Inventory log entry is created
- [ ] Low-stock alert appears when threshold is reached
- [ ] Admin can review related activity logs

Notes:
- Result:
- Issue found:

---

## Final Sign-Off

- [ ] Sprint 1 fully passed
- [ ] Sprint 2 fully passed
- [ ] Sprint 3 fully passed
- [ ] All critical bugs fixed or documented

Summary:
- Passed items count:
- Failed items count:
- Blocked items count:
- Ready for demo/release: Yes / No
