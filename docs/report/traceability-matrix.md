
# Requirements Traceability Matrix

This document provides traceability between system requirements, use cases, UML design artifacts, and testing documentation of the ShopDunk Store Management System.

The purpose of the matrix is to ensure that analyzed requirements are represented in system design and, where available, validated through testing.

---

## Traceability Overview

| ID | Requirement / Business Need | Use Case | Activity Diagram | Sequence Diagram | Testing |
|---|---|---|---|---|---|
| RTM-01 | Users must be able to authenticate before accessing protected system functions | Login | `activity-login.png` | `sequence-login.png` | Login Decision Table & Test Cases |
| RTM-02 | Managers need to monitor sales performance and KPIs | Sales Activity Monitoring | `activity-sales-monitoring.png` | `sequence-sales-monitoring.png` | Not documented in Chapter 4 |
| RTM-03 | The system must support creation of invoices for products/services | Invoice Creation | `activity-invoice-creation.png` | `sequence-invoice-creation.png` | Not documented in Chapter 4 |
| RTM-04 | Customers must be able to review and confirm their orders | Order Confirmation | `activity-order-confirmation.png` | `sequence-order-confirmation.png` | Not documented in Chapter 4 |
| RTM-05 | Customers must be able to update quantities or remove products from their cart | Shopping Cart Management | `activity-cart-management.png` | `sequence-cart-management.png` | Not documented in Chapter 4 |
| RTM-06 | Users must be able to create an account | Registration | — | — | Registration Decision Table & Test Case |
| RTM-07 | Users must be able to search for information/products | Search | — | — | Search Decision Table & Test Case |

---

## RTM-01 — Login

### Requirement

Users with valid accounts must be able to log into the system before accessing functions that require authentication.

### Actor

- Employees
- Customers

### Design Artifacts

- [Activity Diagram](../../diagrams/activity-login.png)
- [Sequence Diagram](../../diagrams/sequence-login.png)

### Testing Artifacts

- Login Decision Table
- Valid Login Test Case
- Invalid Password Test Case

[View Testing Documentation](../../testing/README.md)

### Traceability Status

**Covered**

The requirement is represented in the Use Case specification, Activity Diagram, Sequence Diagram, and testing documentation.

---

## RTM-02 — Sales Activity Monitoring

### Requirement

Managers need to monitor sales activities, including sales performance, orders, employee performance, and KPIs.

### Actor

- Manager

### Design Artifacts

- [Activity Diagram](../../diagrams/activity-sales-monitoring.png)
- [Sequence Diagram](../../diagrams/sequence-sales-monitoring.png)

### Key Business Information

The monitoring function supports information such as:

- Revenue
- Number of orders
- Employee performance
- Sales performance
- KPI monitoring
- Time-based filtering
- Product filtering
- Report export

### Testing Status

No dedicated test case for this function is documented in Chapter 4 of the project report.

### Traceability Status

**Requirement + Design Covered**

---

## RTM-03 — Invoice Creation

### Requirement

The system must allow authorized staff to create invoices for products or services purchased by customers.

### Actor

- Cashier / Customer Service Staff

### Design Artifacts

- [Activity Diagram](../../diagrams/activity-invoice-creation.png)
- [Sequence Diagram](../../diagrams/sequence-invoice-creation.png)

### Related Data

Invoice creation includes:

- Product/service information
- Unit price
- Quantity
- VAT
- Discounts
- Total amount
- Payment status

### Business Rules

- Invalid products must not be added to an invoice.
- Inventory information should be checked during invoice creation.
- The invoice can be saved with a **Pending Payment** status.
- Draft invoices may be stored for later editing.

### Testing Status

No dedicated invoice test case is documented in Chapter 4.

### Traceability Status

**Requirement + Design Covered**

---

## RTM-04 — Order Confirmation

### Requirement

Customers must be able to review order information before officially submitting an order.

### Actor

- Customer

### Preconditions

- The cart contains at least one product.
- Delivery information has been entered.
- A payment method has been selected.

### Design Artifacts

- [Activity Diagram](../../diagrams/activity-order-confirmation.png)
- [Sequence Diagram](../../diagrams/sequence-order-confirmation.png)

### Business Rules

The system should display:

- Product list
- Product quantity
- Delivery address
- Payment method
- Total order value
- Shipping cost

After confirmation, the system:

- Creates/updates the order status.
- Sends an order confirmation.
- Updates inventory quantity.

### Testing Status

No dedicated order confirmation test case is documented in Chapter 4.

### Traceability Status

**Requirement + Design Covered**

---

## RTM-05 — Shopping Cart Management

### Requirement

Customers must be able to manage selected products before completing an order.

### Actor

- Customer

### Functions

- View products in the cart
- Update product quantity
- Remove products
- Recalculate product amount
- Recalculate total order value

### Design Artifacts

- [Activity Diagram](../../diagrams/activity-cart-management.png)
- [Sequence Diagram](../../diagrams/sequence-cart-management.png)

### Exception

If the requested quantity exceeds available inventory:

- The system displays an insufficient stock message.
- The quantity is adjusted according to available stock.

### Testing Status

No dedicated Shopping Cart test case is documented in Chapter 4.

### Traceability Status

**Requirement + Design Covered**

---

## RTM-06 — Registration

### Requirement

The system must allow new users to register an account using valid information.

### Testing Artifacts

The project includes:

- Registration Decision Table
- Registration Test Case
- Valid and invalid input conditions

[View Testing Documentation](../../testing/README.md)

### Traceability Status

**Requirement + Testing Covered**

---

## RTM-07 — Search

### Requirement

The system must allow users to search for relevant information or products.

### Testing Artifacts

The project includes:

- Search Decision Table
- Search Test Case
- Valid search input
- Empty search input
- Invalid / unmatched search conditions

[View Testing Documentation](../../testing/README.md)

### Traceability Status

**Requirement + Testing Covered**

---

## Coverage Summary

| Area | Status |
|---|---|
| Requirement Analysis | Covered |
| Use Case Specification | Covered |
| UML Activity Diagrams | Covered for selected use cases |
| UML Sequence Diagrams | Covered for selected use cases |
| Database Design | Covered |
| Login Testing | Covered |
| Registration Testing | Covered |
| Search Testing | Covered |
| Testing for remaining use cases | Not documented in Chapter 4 |

---

## Related Documentation

- [System Requirements](requirements.md)
- [Requirement Elicitation](requirement-elicitation.md)
- [Use Case Specification](use-case-specification.md)
- [Database Design](database-design.md)
- [System Diagrams](../../diagrams/)
- [Testing Documentation](../../testing/README.md)
- [Full Project Report](ShopDunk_Project_Report.pdf)
