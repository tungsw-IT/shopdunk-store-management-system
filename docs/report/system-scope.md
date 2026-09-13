# System Scope

This document defines the scope and boundaries of the ShopDunk Store Management System.

The system is designed to support the main management and business activities of a mobile phone retail store.

---

## 1. System Objective

The ShopDunk Store Management System aims to support and automate store operations, reduce errors in data processing, and improve the efficiency of employees and managers.

The system supports activities related to:

- Product Management
- Customer Management
- Employee Management
- Sales Management
- Inventory Management
- Invoice Management
- Payment Processing
- Reporting and Statistics

---

## 2. In Scope

The following functions are included within the system scope.

### 2.1 Product Management

The system supports:

- Adding products
- Updating product information
- Deleting products
- Viewing product lists
- Viewing product details
- Searching products
- Managing inventory

---

### 2.2 Customer Management

The system supports:

- Adding customers
- Updating customer information
- Searching customers
- Managing customer profiles
- Storing purchase history
- Supporting customer promotions and offers

---

### 2.3 Account Management

The system supports:

- Account registration
- Login
- Logout
- Forgot password
- Viewing account information
- Updating account information
- Account management

---

### 2.4 Sales Management

The Point-of-Sale (POS) functions include:

- Creating sales orders
- Editing orders
- Cancelling orders
- Applying promotions
- Checking product inventory
- Creating invoices
- Processing payments
- Exporting electronic invoices
- Suggesting related products

---

### 2.5 Shopping Cart and Ordering

Customers can:

- View products
- Search products
- Select products
- Add products to the shopping cart
- Change product quantities
- Remove products from the cart
- Confirm orders
- Place orders
- Make payments

---

### 2.6 Inventory Management

The system supports:

- Checking product inventory
- Updating inventory information
- Preventing sales when inventory is unavailable
- Monitoring stock levels
- Supporting inventory-related reporting

---

### 2.7 Repair and Warranty Management

The system supports:

- Receiving repair requests
- Scheduling repairs
- Updating repair status
- Recording product problems
- Recording repair solutions
- Viewing repair history
- Supporting warranty-related information

---

### 2.8 Employee Management

The system supports:

- Viewing employee lists
- Updating employee information
- Assigning permissions
- Locking / unlocking accounts
- Assigning work shifts
- Monitoring employee sales performance

---

### 2.9 System Administration

Management functions include:

- Managing system data
- Backing up data
- Restoring data
- Coordinating employees
- Handling operational/system incidents
- Managing access permissions

---

### 2.10 Reporting and Statistics

The system supports reports and statistics such as:

- Revenue reports
- Best-selling products
- Inventory statistics
- Order statistics
- Employee performance
- KPI monitoring
- Report export

---

## 3. System Actors

The system includes the following actors:

| Actor | Main Responsibility |
|---|---|
| Manager | Manage operations, employees, reports and system administration |
| Sales Team Leader | Monitor sales team performance and coordinate sales activities |
| Sales Staff | Support customers, create orders and manage sales |
| Cashier / Customer Service | Create invoices, process payments and support customers |
| Technician | Manage repair and warranty activities |
| General Staff | Use common employee functions |
| Customer | Purchase products and use customer services |
| Guest Visitor | Access public functions before authentication |

---

## 4. System Boundary

The system covers business processes related to store operations and customer purchasing activities.

The main process boundary can be represented as:

```text
Product / Inventory
        ↓
Customer → Sales → Order → Invoice → Payment
        ↓
Customer Service
        ↓
Repair / Warranty
        ↓
Reports / Management
