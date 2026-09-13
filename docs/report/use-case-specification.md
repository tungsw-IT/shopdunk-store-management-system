
# Use Case Specification

This document describes representative use cases of the ShopDunk Store Management System.

The specifications include actors, objectives, preconditions, main flows, alternative flows, exceptions, and special requirements.

---

## 1. Login

### Use Case Name
Login

### Actor
- Employees
- Customers

### Objective
Allow users to access the system website.

### Preconditions
- The user already has a valid account.

### Description
Managers, employees, or customers must log in before accessing functions that require authentication.

The login form requires authentication information such as email and password. For administrator accounts, username may be used instead of email.

### Main Flow

1. The user selects the Login function from the home page.
2. The system displays the login form.
3. The user enters login information:
   - Email / Username
   - Password
4. The user submits the login information.
5. The system validates the entered credentials.
6. If the credentials are correct, the system allows the user to access the system.
7. The use case ends.

### Alternative / Exception Flow

#### Empty Fields
- The user leaves one or more required fields empty.
- The system displays a validation message indicating that the field cannot be empty.

#### Invalid Credentials
- The entered login information is incorrect.
- The system displays a login failure message.
- The user is required to enter the credentials again.

### Special Requirements

- After a successful login, the system may store authentication information using cookies so the user does not need to log in again on subsequent visits.
- If the user logs out or has not authenticated, the user must log in again.

---

## 2. Sales Activity Monitoring

### Use Case Name
Sales Activity Monitoring

### Actor
- Manager

### Objective
Allow managers to continuously monitor sales performance, progress, and key performance indicators (KPIs) to support timely management decisions.

### Preconditions

1. The Manager has successfully logged into the system.
2. The Manager has accessed the Operations Management function.
3. The system contains transaction, order, and sales target data.

### Description
The Manager accesses reports and dashboards that visualize sales information such as revenue, number of orders, employee performance, and sales performance by area.

### Main Flow

1. The Manager accesses the Operations Management function.
2. The Manager selects **Sales Activity Monitoring**.
3. The system displays an overview Dashboard.
4. The Manager selects monitoring criteria, such as:
   - Time period
   - Area
   - Employee
   - Product
5. The system retrieves and displays detailed reports based on the selected criteria.
6. Reports may include:
   - Daily sales
   - Conversion rate
   - Best-selling products
7. The Manager analyzes the displayed information.
8. The Manager may export reports to formats such as Excel or PDF.
9. The use case ends.

### Alternative / Exception Flow

#### Complex Report Request
- The Manager creates a report request that requires a large or complex query.
- The system informs the Manager about the expected processing time or requests a narrower data range.

### Special Requirements

- Dashboard data should be as close to real-time as possible.
- Reports should use charts and visualizations to make information easier to understand.
- The system should support alerts when KPIs reach critical thresholds, such as sales falling below 50% of the target.

---

## 3. Invoice Creation

### Use Case Name
Invoice Creation

### Actor
- Cashier / Customer Service Staff

### Objective
Create a detailed invoice containing products or services purchased by the customer, including tax, discounts, and the total amount payable.

### Preconditions

1. The Cashier / Customer Service Staff has successfully logged into the system.
2. The actor has accessed the Invoice Payment function.
3. A list of products/services selected by the customer or completed repair services is available.

### Description
The Cashier / Customer Service Staff enters or loads product and service information, applies discounts or promotions, and confirms the information to create an official invoice ready for payment.

### Main Flow

1. The actor accesses the Invoice Payment function.
2. The actor selects **Create Invoice**.
3. The system displays the invoice creation interface.
4. The actor enters or scans:
   - Product codes
   - Services
   - Completed repair requests
5. The system automatically calculates:
   - Unit price
   - Line-item amount
   - Subtotal
   - VAT
   - Discounts, if applicable
   - Total amount payable
6. The actor verifies that the invoice information is correct.
7. The system saves the invoice with the status **Pending Payment**.
8. The use case ends.

### Alternative / Exception Flow

#### Invalid Product Information
- The actor enters a product code that does not exist in inventory.
- The system displays an error message.
- The system does not allow the invalid item to be added to the invoice.

### Special Requirements

- The system should synchronize with inventory data to verify available stock.
- The system should support saving temporary invoices as Drafts for later editing.
- Created invoices should be directly associated with payment and invoice printing/export functions.

---

## 4. Order Confirmation

### Use Case Name
Order Confirmation

### Actor
- Customer

### Objective
Allow the customer to review products, delivery information, payment information, and submit the purchase request to the system.

### Relationship
`<<extend>>` Order Placement

### Preconditions

1. The customer has successfully logged into the system or is using a guest session.
2. At least one product exists in the shopping cart.
3. The customer has entered all required delivery information.
4. The customer has selected a payment method.

### Description
The customer reviews the order summary, including products, quantities, delivery details, payment method, shipping cost, and final amount before submitting the order.

### Main Flow

1. The customer accesses the Shopping Cart / Checkout page.
2. The customer selects **Confirm Order**.
3. The system displays an order summary including:
   - Product list
   - Product quantities
   - Delivery address
   - Payment method
   - Total cost including tax and shipping fees
4. The customer reviews the information.
5. The customer selects **Place Order** or **Complete Payment**.
6. The system:
   - Assigns the order status **Pending Processing**.
   - Sends an order confirmation notification through email/SMS.
   - Deducts the corresponding product quantities from inventory.
7. The use case ends.

### Alternative / Exception Flow

#### Missing Required Information
- The customer has not entered a delivery address or selected a payment method.
- The system prevents order confirmation.
- The system asks the customer to provide the missing information.

### Special Requirements

- Refund and return policies should be clearly displayed during order confirmation.
- Payment information must be protected during processing.
- Confirmed order details, including products, prices, and applied promotions, should be stored consistently after confirmation.

---

## 5. Shopping Cart Management

### Use Case Name
Shopping Cart Management

### Actor
- Customer

### Objective
Allow customers to modify product quantities or remove selected products from the shopping cart before payment.

### Preconditions

1. The customer has accessed the shopping cart page.
2. The shopping cart contains at least one product.

### Main Flow

1. The customer views the list of products in the shopping cart.
2. The customer changes the quantity of a selected product.
3. The system checks inventory availability.
4. The system recalculates the amount for the selected product.
5. The system recalculates the total order amount.
6. The updated total is displayed immediately.
7. The use case ends.

### Alternative Flow

#### Remove Product
1. The customer selects **Delete** for a product.
2. The system removes the product from the shopping cart.
3. The system recalculates the total order amount.

### Exception Flow

#### Quantity Exceeds Available Stock
- The selected quantity exceeds available inventory.
- The system displays the message **"Insufficient stock"**.
- The system adjusts the quantity to the maximum quantity currently available.

---

## Use Case Traceability

The selected use cases are supported by related system modeling and testing artifacts.

| Use Case | Activity Diagram | Sequence Diagram | Related Documentation |
|---|---|---|---|
| Login | `activity-login.png` | `sequence-login.png` | Login testing |
| Sales Activity Monitoring | `activity-sales-monitoring.png` | `sequence-sales-monitoring.png` | Requirements / Reporting |
| Invoice Creation | `activity-invoice-creation.png` | `sequence-invoice-creation.png` | Database / Payment |
| Order Confirmation | `activity-order-confirmation.png` | `sequence-order-confirmation.png` | Order process |
| Shopping Cart Management | `activity-cart-management.png` | `sequence-cart-management.png` | Cart / Order process |

---

## Related Documentation

- [System Requirements](requirements.md)
- [Requirement Elicitation](requirement-elicitation.md)
- [Database Design](database-design.md)
- [System Diagrams](../../diagrams/)
- [Testing Documentation](../../testing/README.md)
- [Full Project Report](ShopDunk_Project_Report.pdf)
