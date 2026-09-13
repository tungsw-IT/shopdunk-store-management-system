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

The detailed database tables, attributes, primary keys, foreign keys, and relationships are documented in the following sections.
### Database Table List

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

### 1. Accounts

| Field | Data Type | Constraint | Description |
|---|---|---|---|
| account_id | INT | Primary Key, AI | Unique account identifier |
| accounts_name | VARCHAR(255) | NULL | User name |
| email | VARCHAR(255) | Unique, Not Null | Email address used for login |
| phone | VARCHAR(20) | Not Null | Contact phone number |
| password | VARCHAR(255) | Not Null | Hashed password |
| reset_code | VARCHAR(10) | NULL | Password recovery code |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Account creation time |
