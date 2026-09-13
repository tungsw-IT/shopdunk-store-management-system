
# Business Rules

This document summarizes key business rules identified from the requirements and use cases of the ShopDunk Store Management System.

---

## 1. Authentication Rules

### BR-01 — Account Requirement
Users must have a valid account before accessing functions that require authentication.

### BR-02 — Required Login Information
Login fields such as email/username and password are required.

If a required field is empty, the system must display a validation message.

### BR-03 — Invalid Credentials
If the entered credentials are incorrect, the system must reject the login attempt and request valid information.

### BR-04 — Login Session
After successful authentication, the system may maintain the login state using cookies/session information.

After logout, users must authenticate again before accessing protected functions.

---

## 2. Customer Rules

### BR-05 — Customer Information Validation
Customer information must be validated before being stored.

Validation includes:

- Email format
- Phone number format
- Required information

### BR-06 — Duplicate Customer Detection
The system should check whether customer information already exists before creating a new customer profile.

Duplicate detection is primarily based on:

- Phone number
- Email address

### BR-07 — Customer Identification
A newly created customer must receive a unique customer identifier.

---

## 3. Shopping Cart Rules

### BR-08 — Cart Product Requirement
A customer must have at least one product in the shopping cart before proceeding with order confirmation.

### BR-09 — Inventory Validation
When a customer changes product quantity, the system must verify available inventory.

### BR-10 — Quantity Exceeds Stock
If the requested quantity is greater than available stock:

- The system must display an insufficient stock warning.
- The quantity must not exceed the available stock.

### BR-11 — Cart Total Recalculation
Whenever a product quantity changes or a product is removed, the system must recalculate the cart total.

---

## 4. Order Rules

### BR-12 — Order Confirmation Information
Before an order can be confirmed, the customer must provide:

- Required delivery information
- A payment method
- At least one product in the shopping cart

### BR-13 — Missing Information
If required order information is missing, the system must prevent order confirmation and request the missing information.

### BR-14 — Order Status
After successful confirmation, the order is assigned a processing status such as:

`Pending Processing`

### BR-15 — Inventory Update
After an order is successfully confirmed, the corresponding product inventory must be updated.

### BR-16 — Order Confirmation Notification
The system should send an order confirmation notification to the customer through supported channels such as email or SMS.

---

## 5. Invoice Rules

### BR-17 — Authorized Invoice Creation
Invoice creation is performed by authorized staff such as Cashier / Customer Service Staff.

### BR-18 — Product Validation
Products or services added to an invoice must exist in the system.

If a product code is invalid, the system must reject the item.

### BR-19 — Invoice Calculation
The system automatically calculates:

- Unit price
- Item amount
- Subtotal
- VAT
- Discount, if applicable
- Total payment amount

### BR-20 — Invoice Status
A newly created invoice can be stored with the status:

`Pending Payment`

### BR-21 — Draft Invoice
The system may support storing an unfinished invoice as a Draft for later editing.

### BR-22 — Inventory Synchronization
Invoice creation should be synchronized with inventory data to verify stock availability.

---

## 6. Payment Rules

### BR-23 — Payment Preconditions
Payment can be processed only when:

- An invoice/order has been created.
- The customer has selected a supported payment method.

### BR-24 — Successful Payment
After successful payment:

- Transaction information must be recorded.
- The payment/order status must be updated.

### BR-25 — Failed Payment
If an electronic payment transaction fails, the system must not mark the invoice/order as successfully paid.

---

## 7. Promotion Rules

### BR-26 — Promotion Validity
A promotion can be applied only when it satisfies its configured conditions.

Conditions may include:

- Valid period
- Order value
- Product quantity
- Customer eligibility

### BR-27 — Invalid Promotion
If a promotion is expired, invalid, or its conditions are not satisfied:

- The system must reject the promotion.
- The original invoice total must remain unchanged.

### BR-28 — Total Recalculation
When a valid promotion is applied, the system must recalculate and display the updated total amount.

---

## 8. Product and Inventory Rules

### BR-29 — Inventory Availability
Products should be sold only when sufficient stock is available.

### BR-30 — Inventory Monitoring
The system should support inventory monitoring to help identify products that are running low.

### BR-31 — Product Identifier
Mobile products are identified using IMEI information in the system database.

---

## 9. Sales Monitoring Rules

### BR-32 — Sales Data Availability
Sales monitoring requires existing transaction, order, and sales target data.

### BR-33 — KPI Monitoring
Managers should be able to monitor sales indicators such as:

- Revenue
- Number of orders
- Employee performance
- Conversion rate
- Best-selling products

### BR-34 — Monitoring Filters
Sales reports may be filtered by criteria such as:

- Time period
- Employee
- Product
- Area

### BR-35 — KPI Alerts
The system may provide alerts when KPIs reach configured warning thresholds.

---

## 10. Data Rules

### BR-36 — Unique Account Email
Account email addresses must be unique where defined by the database design.

### BR-37 — Password Storage
Passwords must be stored in hashed/encrypted form rather than plain text.

### BR-38 — Record Identification
Major database records should use primary keys to ensure unique identification.

### BR-39 — Data Relationships
Related system data should use appropriate relationships and foreign keys where defined in the database design.

---

## Related Documentation

- [System Requirements](requirements.md)
- [Requirement Elicitation](requirement-elicitation.md)
- [Use Case Specification](use-case-specification.md)
- [Requirements Traceability Matrix](traceability-matrix.md)
- [Database Design](database-design.md)
- [Testing Documentation](../../testing/README.md)
