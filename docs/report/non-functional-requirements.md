# Non-functional Requirements

This document summarizes the non-functional requirements of the ShopDunk Store Management System.

The requirements are grouped into seven quality areas:

- Performance
- Security
- Reliability
- Usability
- Scalability
- Data Integrity
- Maintainability

---

## 1. Performance

### NFR-01 — Response Time

The system should respond within **3 seconds or less** when performing operations such as:

- Product search
- Order creation

### NFR-02 — Concurrent Users

The system should support at least:

**200 concurrent users**

This includes:

- Sales employees
- Customers accessing the system

### NFR-03 — Product List Loading

The phone/product list should load within:

**2 seconds or less**

### NFR-04 — Order Processing Capacity

The system should be able to process at least:

**500 orders per day**

---

## 2. Security

### NFR-05 — Authentication

Users must authenticate using:

- Account
- Password

### NFR-06 — Password Protection

Passwords must be stored in encrypted/hashed form.

### NFR-07 — Role-based Access

The system must support access control for different user roles.

Roles defined in the project include:

- Admin
- Sales Staff
- Customer

### NFR-08 — Customer Data Protection

Customer information must be protected from unauthorized access.

### NFR-09 — Automatic Backup

The system should automatically back up data every day.

### NFR-10 — Session Timeout

The system should automatically log users out after:

**10 minutes of inactivity**

---

## 3. Reliability

### NFR-11 — System Availability

The system should operate:

**24/7**

### NFR-12 — Data Loss Prevention

The system should prevent data loss in situations such as:

- Power failure
- System failure

### NFR-13 — Backup and Recovery

The system must provide:

- Database Backup
- Data Restore

### NFR-14 — System Uptime

The expected system uptime is:

**≥ 99%**

---

## 4. Usability

### NFR-15 — User Interface

The user interface should be:

- Simple
- Easy to operate
- User-friendly

### NFR-16 — Language

The system should support:

- Vietnamese

### NFR-17 — Ease of Use

The system should be easy to use for operational staff such as:

- Sales Staff
- Warehouse Staff

### NFR-18 — Clear Main Functions

Core functions should be clearly displayed, including:

- Product Management
- Sales
- Customer Management
- Statistics

---

## 5. Scalability

### NFR-19 — Multi-store Support

The system should support:

- Multiple stores
- Multiple branches

### NFR-20 — Platform Expansion

The system should be easy to extend to additional platforms such as:

- Online shopping website
- Mobile application

### NFR-21 — External Integration

The system should support future integration with:

- MoMo
- VNPay
- Banking systems
- Delivery systems

### NFR-22 — Database Scalability

The database design should allow additional tables to be added when the system is expanded.

---

## 6. Data Integrity

### NFR-23 — Unique Identifiers

The system must prevent duplication of identifiers such as:

- Phone/product code
- Invoice code
- Customer code

### NFR-24 — Inventory Validation

The system must not allow a product to be sold when:

**Inventory quantity = 0**

### NFR-25 — Database Constraints

The database should use:

- Primary Keys
- Foreign Keys

to maintain relationships and data integrity.

### NFR-26 — Data Validation

The system should validate business data such as:

- Selling price > 0
- Quantity ≥ 0

### NFR-27 — Data Consistency

The system must maintain consistency between:

- Orders
- Products
- Inventory

---

## 7. Maintainability

### NFR-28 — Code Readability

Source code should contain:

- Clear comments
- Understandable variable names

### NFR-29 — Software Design

The system should apply:

- Object-Oriented Programming (OOP)
- MVC architecture

### NFR-30 — Functional Extensibility

The system should be designed so that new functions can be added easily.

Potential extensions include:

- Promotion Management
- Warranty Management
- Installment Payment Management

### NFR-31 — Modular Design

Major system functions should be separated into modules such as:

- Product Module
- Sales Module
- Customer Module
- Reporting Module

---

## Non-functional Requirement Summary

| Category | Requirement IDs | Main Focus |
|---|---|---|
| Performance | NFR-01 – NFR-04 | Speed, concurrency and processing capacity |
| Security | NFR-05 – NFR-10 | Authentication, authorization and data protection |
| Reliability | NFR-11 – NFR-14 | Availability, backup and recovery |
| Usability | NFR-15 – NFR-18 | User-friendly interface and ease of use |
| Scalability | NFR-19 – NFR-22 | Multi-branch support and future integrations |
| Data Integrity | NFR-23 – NFR-27 | Constraints, validation and data consistency |
| Maintainability | NFR-28 – NFR-31 | Code quality, architecture and modular design |

---

## Quality Targets

| Quality Attribute | Target |
|---|---|
| Search / order response time | ≤ 3 seconds |
| Product list loading time | ≤ 2 seconds |
| Concurrent users | ≥ 200 users |
| Daily order capacity | ≥ 500 orders |
| System uptime | ≥ 99% |
| Operation time | 24/7 |
| Inactivity timeout | 10 minutes |
| Backup frequency | Daily |

---

## Related Documentation

- [System Requirements](requirements.md)
- [Functional Requirements](functional-requirements.md)
- [Requirement Elicitation](requirement-elicitation.md)
- [Stakeholder Analysis](stakeholder-analysis.md)
- [Business Rules](business-rules.md)
- [Use Case Specification](use-case-specification.md)
- [Requirements Traceability Matrix](traceability-matrix.md)
- [Database Design](database-design.md)
- [Testing Documentation](../../testing/README.md)
