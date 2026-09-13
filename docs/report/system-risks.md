# System Risks

This document summarizes the main system risks identified in the ShopDunk Store Management System project.

The original project report identifies four major risks:

1. Data Loss
2. Incorrect Payment Calculation
3. System Overload
4. Internet Connection Interruption

---

## 1. Risk Overview

| Risk ID | Risk | Potentially Affected Areas |
|---|---|---|
| RISK-01 | Data Loss | Database, Orders, Customers, Products, Payments |
| RISK-02 | Incorrect Payment Calculation | Sales, Invoice, Payment |
| RISK-03 | System Overload | Performance, Ordering, Product Search, Reporting |
| RISK-04 | Internet Connection Interruption | Web Access, Payment, Data Synchronization |

---

# 2. RISK-01 — Data Loss

## Description

System data may be lost because of unexpected failures such as:

- System errors
- Power interruption
- Database problems
- Failed data operations

## Potentially Affected Data

Data loss could affect information related to:

- Accounts
- Customers
- Products
- Orders
- Inventory
- Invoices
- Payments
- Repair records

## Related Requirements

The project includes several requirements that help reduce this risk:

- Automatic data backup
- Database backup
- Data restoration
- Data integrity constraints
- Primary Key / Foreign Key constraints

## Existing Controls

The system is expected to:

- Perform regular database backups.
- Support data restoration.
- Maintain data integrity.
- Prevent invalid database operations.
- Maintain consistency between related system data.

## Related Documentation

- [Database Design](database-design.md)
- [Non-functional Requirements](non-functional-requirements.md)

---

# 3. RISK-02 — Incorrect Payment Calculation

## Description

Errors in price or payment calculations may cause incorrect invoice totals.

This may affect:

- Product prices
- Discounts
- VAT
- Order totals
- Invoice totals
- Payment amounts

## Related Business Process

During invoice creation, the system calculates:

- Unit price
- Item amount
- Subtotal
- VAT
- Discount
- Total amount payable

## Potential Consequences

Incorrect calculations may result in:

- Incorrect customer payment
- Incorrect invoice information
- Incorrect transaction records
- Inconsistent sales information

## Related Controls

The system requirements support controls such as:

- Automatic calculation of invoice totals.
- Validation of selling prices.
- Validation of product quantity.
- Verification of invoice information before payment.
- Maintaining consistent transaction data.

## Related Documentation

- [Business Rules](business-rules.md)
- [Use Case Specification](use-case-specification.md)
- [Database Design](database-design.md)

---

# 4. RISK-03 — System Overload

## Description

A large number of users, transactions, searches, or reports may cause the system to respond slowly or become temporarily unavailable.

## Potentially Affected Functions

- Product Search
- Product List
- Order Creation
- Invoice Processing
- Sales Reports
- Dashboard
- Customer Operations

## Performance Requirements

The project defines several performance targets:

| Metric | Target |
|---|---:|
| Search / order response time | ≤ 3 seconds |
| Product list loading time | ≤ 2 seconds |
| Concurrent users | ≥ 200 users |
| Daily order processing | ≥ 500 orders |

## Related Controls

The system should be designed to:

- Support concurrent users.
- Maintain acceptable response times.
- Process expected daily order volumes.
- Avoid excessively large report queries where possible.

## Related Documentation

- [Non-functional Requirements](non-functional-requirements.md)
- [System Scope](system-scope.md)

---

# 5. RISK-04 — Internet Connection Interruption

## Description

Internet connection problems may interrupt communication between users and the web-based system.

## Potentially Affected Functions

- Login
- Product Search
- Order Processing
- Online Payment
- Customer Support
- Reporting
- Real-time synchronization

## Potential Consequences

Connection interruptions may cause:

- Failed requests
- Delayed system responses
- Interrupted transactions
- Failed data updates
- Delayed synchronization

## Related System Considerations

The project requires real-time or near real-time synchronization for several business activities, including:

- Reports
- Inventory information
- Multi-channel customer requests
- Payment status

Therefore, stable connectivity is important for system operation.

## Related Controls

Where supported by the system design, operations should:

- Display an error when a connection fails.
- Avoid storing incomplete data.
- Allow the user to retry the operation.
- Preserve data consistency when an operation cannot be completed.

---

# 6. Risk-to-Requirement Mapping

| Risk | Related Requirement Area | Existing Project Response |
|---|---|---|
| Data Loss | Reliability / Data Integrity | Backup, Restore, Database Constraints |
| Incorrect Payment Calculation | Sales / Data Integrity | Validation and automatic calculation |
| System Overload | Performance | Response-time and capacity requirements |
| Internet Connection Interruption | Reliability / Synchronization | Error handling and real-time synchronization requirements |

---

# 7. Risk Relationship to Non-functional Requirements

The identified risks are closely related to the project's non-functional requirements.

### Reliability

Supports mitigation of:

- Data Loss
- System Failure

### Performance

Supports mitigation of:

- System Overload
- Slow Response

### Data Integrity

Supports mitigation of:

- Incorrect Transaction Data
- Incorrect Inventory Data
- Invalid Database Operations

### Security

Supports protection of:

- Accounts
- Customer Data
- Payment-related information

---

# 8. Risk Management Note

The original report identifies the system risks but does not provide a formal risk matrix containing:

- Probability
- Impact score
- Risk owner
- Risk priority
- Residual risk

Therefore, these values are intentionally not invented in this documentation.

This document focuses on the risks explicitly identified in the report and connects them with existing system requirements and controls.

---

## Related Documentation

- [System Scope](system-scope.md)
- [System Requirements](requirements.md)
- [Functional Requirements](functional-requirements.md)
- [Non-functional Requirements](non-functional-requirements.md)
- [Business Rules](business-rules.md)
- [Use Case Specification](use-case-specification.md)
- [Requirements Traceability Matrix](traceability-matrix.md)
- [Database Design](database-design.md)
- [Testing Documentation](../../testing/README.md)
