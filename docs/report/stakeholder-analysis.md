# Stakeholder Analysis

This document identifies the main stakeholders and actors involved in the ShopDunk Store Management System.

The analysis is based on the project requirements, actor definitions, interviews, and system use cases.

---

## 1. Stakeholder Overview

The system involves both internal and external stakeholders.

### Internal Stakeholders

- Manager
- Sales Team Leader
- Sales Staff
- Cashier / Customer Service Staff
- Technician
- General Staff

### External Stakeholders

- Customer
- Guest Visitor

---

## 2. Stakeholder Summary

| Stakeholder | Type | Main Needs | Related System Functions |
|---|---|---|---|
| Manager | Internal | Monitor operations, employees, sales and system performance | HR Management, System Management, Operations Management |
| Sales Team Leader | Internal | Monitor sales team, assign shifts and handle sales issues | Sales Team Management, Sales Issue Support |
| Sales Staff | Internal | Sell products, manage customers and check inventory | Sales Management, Customer Management, Inventory |
| Cashier / Customer Service | Internal | Process invoices, payments and customer support requests | Invoice Payment, Customer Management, Customer Support |
| Technician | Internal | Handle repair requests and update repair status | Repair Management, Repair History |
| General Staff | Internal | Access common system functions and account information | Login, Account Management, Product Details |
| Customer | External | Purchase products, manage cart, confirm orders and request support | Account, Ordering, Cart, Product Review, Repair Request |
| Guest Visitor | External | View public product/system information before registration/login | Public browsing functions |

---

# 3. Manager

## Role

The Manager is responsible for supervising store operations and managing the system at a higher level.

## Main Needs

- Monitor store operations
- Monitor sales performance
- Manage employees
- Control user permissions
- Monitor repair and warranty activities
- Handle operational incidents
- Access reports and business information

## Related Functions

### Human Resource Management

- Lock / unlock accounts
- Edit employee information
- Assign account permissions
- View employee lists

### System Management

- Manage system data
- Backup data
- Restore data

### Operations Management

- Monitor sales activities
- Monitor repair and warranty activities
- Handle operational incidents
- Coordinate employees

## Business Value

The Manager needs accurate and timely information to support operational and management decisions.

---

# 4. Sales Team Leader

## Role

The Sales Team Leader supervises sales employees and supports sales-related operational activities.

## Main Needs

- Monitor sales performance
- Track team productivity
- Assign work shifts
- Handle sales problems
- Support employees when order issues occur

## Related Functions

### Sales Team Management

- Monitor sales performance
- Assign work shifts

### Sales Issue Support

- Handle order errors

## Key Information

The Sales Team Leader needs information such as:

- Employee performance
- Sales results
- Order status
- Sales targets
- Operational issues

---

# 5. Sales Staff

## Role

Sales Staff directly interact with customers during the product consultation and purchasing process.

## Main Needs

- Search and check product information
- Check available inventory
- Create sales orders
- Apply promotions
- Update customer information
- Support customers during the purchasing process

## Related Functions

### Sales Management

- Create sales orders
- Apply promotions
- Cancel / edit orders

### Customer Management

- Add customers
- Update customer information
- Store purchase history
- Search customers

### Local Inventory

- View inventory
- Report insufficient stock

## Business Context

The sales process includes understanding customer needs, introducing suitable products, checking inventory, supporting the customer before purchase, and transferring the transaction to the cashier when necessary.

---

# 6. Cashier / Customer Service Staff

## Role

This stakeholder performs both payment processing and customer support activities.

## Main Needs

- Process invoices
- Process customer payments
- Verify customer/order information
- Receive customer complaints
- Answer customer questions
- Track customer requests
- Support warranty and return inquiries

## Related Functions

### Customer Management

- View and manage customer information

### Invoice Payment

- Create invoice
- Process payment

### Repair History

- Access repair-related information

### Customer Support

- Receive complaints
- Answer questions
- Send responses to customers

## Customer Support Channels

Customer requests may be received through:

- Direct interaction
- Telephone
- Email
- Hotline
- Zalo
- Fanpage
- Website

## Key Challenges

Customer support staff may need to manage requests from multiple communication channels, which can create risks such as:

- Missing customer requests
- Duplicate responses
- Difficulty tracking request status

The system should help centralize and track customer support information.

---

# 7. Technician

## Role

Technicians are responsible for handling repair and technical service activities.

## Main Needs

- Receive repair requests
- View assigned repair work
- Update repair status
- Record product issues
- Record repair solutions
- View repair history

## Related Functions

### Repair Management

- Receive repair requests
- Schedule repairs
- Update repair status
- Record errors and solutions

### Repair History

- View repair history

## Information Needs

Technicians may need access to:

- Customer information
- Product information
- Repair request information
- Error descriptions
- Repair status
- Previous repair history

---

# 8. General Staff

## Role

General Staff represents common system functionality shared between different employee roles.

## Main Needs

- Authenticate into the system
- Manage personal account information
- View product details

## Related Functions

### Authentication

- Login
- Logout

### Account Management

- View account
- Update account information

### Product Information

- View product details

---

# 9. Customer

## Role

Customers use the system to search for products, manage purchases, and interact with store services.

## Main Needs

- Access account information
- Select products
- Add products to the shopping cart
- Manage shopping cart
- Confirm orders
- Make purchases
- Review products
- Request repair/support services

## Related Functions

### Authentication

- Login
- Logout

### Account Management

- Manage account information

### Ordering

- Select product
- Add product to cart
- Confirm order

### Shopping Cart

- Update quantity
- Remove products
- Review total amount

### Product Review

- Submit product reviews

### Repair Support

- Submit repair requests

## Business Value

The system should provide customers with a convenient purchasing experience and access to after-sales services.

---

# 10. Guest Visitor

## Role

A Guest Visitor represents a user who has not yet authenticated into the system.

## Main Needs

- View publicly available information
- Explore products
- Decide whether to register or log in before using protected functions

## Access Limitation

Guest visitors should only access functions that do not require authenticated accounts.

Protected operations require registration or login.

---

# 11. Stakeholder Interaction Summary

The main business flow connects multiple stakeholders:

```text
Customer
   ↓
Sales Staff
   ↓
Cashier / Customer Service
   ↓
Invoice / Payment
   ↓
Order Completion
```

For after-sales services:

```text
Customer
   ↓
Customer Service
   ↓
Technician
   ↓
Repair / Warranty Processing
```

For management activities:

```text
Sales Staff / Cashier / Technician
            ↓
     Sales Team Leader
            ↓
         Manager
            ↓
 Dashboard / Reports / KPI
```

---

# 12. Stakeholder-to-Function Matrix

| Function | Manager | Sales Team Leader | Sales Staff | Cashier / CSKH | Technician | Customer |
|---|---:|---:|---:|---:|---:|---:|
| Login | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Account Management | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Employee Management | ✓ |  |  |  |  |  |
| Sales Management |  | ✓ | ✓ |  |  |  |
| Customer Management |  |  | ✓ | ✓ |  |  |
| Inventory Checking | ✓ | ✓ | ✓ |  |  |  |
| Invoice Creation |  |  |  | ✓ |  |  |
| Payment |  |  |  | ✓ |  | ✓ |
| Shopping Cart |  |  |  |  |  | ✓ |
| Order Confirmation |  |  |  |  |  | ✓ |
| Repair Management | ✓ |  |  |  | ✓ |  |
| Repair Request |  |  |  | ✓ | ✓ | ✓ |
| Customer Support |  |  |  | ✓ |  | ✓ |
| Sales Monitoring | ✓ | ✓ |  |  |  |  |

---

# 13. Related Documentation

- [Requirement Elicitation](requirement-elicitation.md)
- [System Requirements](requirements.md)
- [Business Rules](business-rules.md)
- [Use Case Specification](use-case-specification.md)
- [Requirements Traceability Matrix](traceability-matrix.md)
- [System Diagrams](../../diagrams/)
- [Database Design](database-design.md)
- [Testing Documentation](../../testing/README.md)
