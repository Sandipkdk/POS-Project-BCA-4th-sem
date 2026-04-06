##Project Overview

This Point of Sale (POS) System is designed to streamline retail management by combining inventory management and billing system functionalities. The system supports multiple user roles (Admin and Cashier) with role-specific permissions to ensure smooth operations in a retail environment.

##Features
#Cashier Features:

View available products and stock levels

Get low-stock alerts for inventory

Manage customer information

Create invoices and bills

View and print bills

Process refunds

#Admin Features:

Add and manage users

Manage product categories

Add, update, and remove products

Process refunds

Generate reports (sales, inventory, etc.)

Perform all cashier functions



##User Roles
Role	Permissions
Cashier	View products, low-stock alerts, manage customers, create/view/print bills, process refunds
Admin	All cashier permissions + add/manage users, manage categories/products, generate reports



##Use Case Diagram

The use case diagram represents interactions between the system and its users.

        +--------------------+
        |      Admin         |
        +--------------------+
                |
                |------------------------+
                |                        |
       Manage Users                    Generate Reports
                |                        |
          Manage Products               Process Refunds
                |
          Manage Categories
                |
            All Cashier Functions







        +--------------------+
        |     Cashier        |
        +--------------------+
                |
                |------------------------+
                |                        |
          View Products            Create Invoice/Bill
                |                        |
        Low Stock Alert               View/Print Bill
                |
          Manage Customers
                |
           Process Refunds






##Data Flow Diagram (DFD)
Level 0 (Context Diagram)


      +------------+
      |   Admin    |
      +------------+
            |
            v
      +----------------+
      | POS System     |
      +----------------+
            ^
            |
      +------------+
      |  Cashier   |
      +------------+
Level 1 DFD (Processes)

Process 1: Inventory Management
- Input: Product Details, Stock Updates
- Output: Stock Alerts, Product List

Process 2: Billing
- Input: Customer Details, Product Selection
- Output: Invoice, Payment Confirmation

Process 3: User Management (Admin Only)
- Input: User Details
- Output: User Accounts, Permissions

Process 4: Reporting (Admin Only)
- Input: Sales Data, Inventory Data
- Output: Sales Reports, Inventory Reports

Process 5: Refund Processing
- Input: Invoice Details, Customer Request
- Output: Updated Inventory, Refund Confirmation





##Usage

Login as Admin or Cashier

Admin can manage users, products, categories, and generate reports

Cashier can view products, create bills, manage customers, and process refunds

Use the system interface to perform daily operations efficiently






##Future Enhancements

Add multi-store support

Integrate barcode scanning

Support for multiple payment methods (card, UPI, wallet)

Real-time inventory sync

Advanced analytics for sales trends