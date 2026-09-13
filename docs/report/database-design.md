# Database Design

## Overview

The database of the ShopDunk Store Management System is designed to support the storage and management of data required by the system's business processes.

The project uses MySQL as the database management system.

## Database Design Objectives

- Organize system data in a structured manner.
- Define relationships between system entities.
- Maintain data integrity and consistency.
- Support the system's functional requirements.
- Provide data for business operations, management, and reporting.

## Database Tables

The system database consists of the following main tables:

1. Accounts
2. Admin
3. Blog
4. Brands
5. Cart
6. Customer
7. Mobile
8. Operations_tasks
9. Paymentbill
10. Reviews

---

### 1. Accounts

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| account_id | INT | Primary Key, AI | Unique account identifier |
| accounts_name | VARCHAR(255) | NULL | User name |
| email | VARCHAR(255) | Unique, Not Null | Email address used for login |
| phone | VARCHAR(20) | Not Null | Contact phone number |
| password | VARCHAR(255) | Not Null | Hashed password |
| reset_code | VARCHAR(10) | NULL | Password recovery code |
| created_at | TIMESTAMP | Default: CURRENT_TIMESTAMP | Account creation time |

### 2. Admin

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| admin_username | VARCHAR(100) | Primary Key | Admin login username |
| admin_full_name | VARCHAR(100) | NULL | Full name |
| admin_password | VARCHAR(255) | Not Null | Hashed password |
| admin_role | VARCHAR(50) | Default: 'quan_ly' | Role / permission |
| admin_status | VARCHAR(20) | Default: 'active' | Account status |
| created_at | TIMESTAMP | Default: CURRENT_TIMESTAMP | Account creation time |
| updated_at | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | Last update time |

### 3. Blog

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| id | INT(11) | Primary Key, AI | Unique article identifier |
| title | VARCHAR(255) | Not Null | Article title |
| summary | TEXT | NULL | Short content summary |
| image | VARCHAR(255) | NULL | Image path |
| content | LONGTEXT | NULL | Detailed article content |
| link | VARCHAR(255) | NULL | Article URL |
| created_at | TIMESTAMP | Default: CURRENT_TIMESTAMP | Article creation time |

### 4. Brands

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| id | INT(11) | Primary Key, AI | Unique brand identifier |
| brand_name | VARCHAR(120) | Not Null | Brand name |
| brand_code | VARCHAR(50) | NULL | Brand code |
| note | TEXT | NULL | Additional brand notes |
| created_at | TIMESTAMP | Default: CURRENT_TIMESTAMP | Record creation time |

### 5. Cart

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| account_id | INT(11) | Primary Key, Foreign Key | Customer account identifier |
| product_imei | VARCHAR(50) | Primary Key, Foreign Key | Product IMEI |
| quantity | INT(11) | Not Null, Default: 1 | Product quantity |
| created_at | TIMESTAMP | Default: CURRENT_TIMESTAMP | Time added to cart |

### 6. Customer

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| customer_id | INT(11) | Primary Key, AI | Unique customer identifier |
| customer_name | VARCHAR(120) | Not Null | Customer full name |
| customer_gender | VARCHAR(20) | NULL | Gender |
| customer_email | VARCHAR(191) | NULL | Email address |
| customer_contact_no | VARCHAR(30) | NULL | Contact phone number |
| customer_address | TEXT | NULL | Permanent address |
| created_at | TIMESTAMP | Default: CURRENT_TIMESTAMP | System registration time |

### 7. Mobile

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| company_name | VARCHAR(100) | Not Null | Manufacturer name |
| company_series | VARCHAR(100) | Not Null | Product series |
| model_no | VARCHAR(100) | Not Null | Model number |
| imei_number | VARCHAR(50) | Primary Key | IMEI number |
| ram(GB) | INT(11) | Not Null, Default: 0 | RAM capacity (GB) |
| rom(GB) | INT(11) | Not Null, Default: 0 | Internal storage (GB) |
| display_size(inch) | DECIMAL(4,2) | Not Null, Default: 0.00 | Screen size |
| display_quality | VARCHAR(100) | NULL | Display quality |
| processor | VARCHAR(150) | NULL | CPU / processor |
| battery_capacity(mah) | INT(11) | Not Null, Default: 0 | Battery capacity |
| color | VARCHAR(50) | NULL | Product color |
| price | DECIMAL(15,2) | Not Null, Default: 0.00 | Current selling price |
| old_price | DECIMAL(15,2) | Not Null, Default: 0.00 | Price before discount |
| stock_quantity | INT(11) | Not Null, Default: 0 | Inventory quantity |

### 8. Operations_tasks

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| id | INT(11) | Primary Key, AI | Unique task identifier |
| title | VARCHAR(150) | Not Null | Task title |
| description | TEXT | NULL | Detailed task description |
| status | VARCHAR(30) | Default: 'pending' | Task status |
| assigned_role | VARCHAR(50) | NULL | Assigned role |
| due_date | DATE | NULL | Completion deadline |
| updated_at | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | Last update time |

### 9. Paymentbill

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| pb_id | INT(11) | Primary Key, AI | Unique invoice identifier |
| pb_date | VARCHAR(20) | Not Null | Invoice creation date |
| customer_id | INT(11) | NULL | Customer identifier |
| customer_name | VARCHAR(120) | Not Null | Customer name at purchase time |
| customer_contact_no | VARCHAR(30) | NULL | Customer phone number |
| purchase_mobile_imei_no | VARCHAR(50) | Not Null | Purchased product IMEI |
| purchase_mobile_company | VARCHAR(100) | Not Null | Product manufacturer |
| purchase_mobile_series | VARCHAR(100) | Not Null | Product series |
| purchase_mobile_model | VARCHAR(100) | Not Null | Product model |
| purchase_mobile_price | DECIMAL(15,2) | Not Null, Default: 0.00 | Product price at sale time |
| total_paid_amount | DECIMAL(15,2) | Not Null, Default: 0.00 | Total amount paid |
| created_at | TIMESTAMP | Default: CURRENT_TIMESTAMP | Transaction creation time |

### 10. Reviews

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| review_id | INT(11) | Primary Key, AI | Unique review identifier |
| account_id | INT(11) | NULL | Reviewer account identifier |
| company_series | VARCHAR(100) | Not Null | Reviewed product series |
| model_no | VARCHAR(100) | Not Null | Product model number |
| rating | INT(11) | Not Null, Default: 5 | Rating score |
| review_text | TEXT | NULL | Review content |
| created_at | TIMESTAMP | Default: CURRENT_TIMESTAMP | Review submission time |

## Data Integrity

The database design applies several integrity rules, including:

- Primary keys for unique record identification.
- Foreign keys for relationships between related data.
- Unique constraints where required.
- Validation of inventory quantity.
- Validation of selling prices.
- Consistency between orders, products, and inventory.

## Database Technology

- Database Management System: MySQL
- Relational database model
- Primary Key / Foreign Key constraints
- Structured data management
