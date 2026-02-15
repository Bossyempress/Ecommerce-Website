## System Architecture

Architecture Pattern: Procedural PHP with modular file separation
Client-Server Model
Session-based Cart Management
Relational Database with Foreign Key Constraints
___________________________________________________________________________
## Technology Stack

# Backend
PHP 
MySQL 
phpMyAdmin 

# Frontend

HTML5
CSS
JavaScript
AJAX (Add-to-cart without page reload)
XAMPP (Apache + MySQL)
Git & GitHub
___________________________________________________________________________
## Core Functional Modules

# Authentication System
Admin login system
Session-based authentication
Password validation
Password reset logging system

# Product Management (Admin)
Create product
Edit product
Delete product
Upload product images
Define available colors per product
Store products 

# Product Display (Customer View)
Dynamic product fetching from database
Grid-based layout rendering
Color selection dropdown
AJAX add-to-cart request handling
Success popup notifications

# Cart System
Session-based cart storage
Quantity increment/update functionality
Preferred color persistence
Item removal functionality
Grand total calculation
Horizontal card layout rendering
No forced redirection after adding item

# Checkout System
Order insertion into orders table
Order item breakdown into order_items
Preferred color stored per order item
Grand total storage
WhatsApp integration for order confirmation
___________________________________________________________________
## Database Schema Overview
# users
Column	Type	Description
id	INT UNSIGNED	Primary Key
username	VARCHAR	Login username
email	VARCHAR	Used for password reset
password	VARCHAR	Hashed password
# products
Column	Type	Description
id	INT UNSIGNED	Primary Key
name	VARCHAR	Product name
price	DECIMAL	Product price
image	VARCHAR	Image path
available_colors	TEXT	Comma-separated color list
# orders
Column	Type	Description
id	INT UNSIGNED	Primary Key
customer_name	VARCHAR	Customer name
total_amount	DECIMAL	Grand total
created_at	TIMESTAMP	Order date
# order_items
Column	Type	Description
id	INT UNSIGNED	Primary Key
order_id	INT UNSIGNED	Foreign Key
product_id	INT UNSIGNED	Foreign Key
quantity	INT	Quantity ordered
preferred_color	VARCHAR	Selected color
price	DECIMAL	Price at time of purchase
_______________________________________________________________________
## password_reset_logs
Logs reset attempts
Stores timestamp
References users.id via foreign key
_______________________________________________________________________
## Data Flow Example (Add to Cart)
User selects product & color
AJAX request sent to add_to_cart.php
PHP validates product ID
Product data fetched from database
Session array updated:
JSON response returned
Frontend displays success message

__________________________________________________________________________
## Security Considerations
Input validation on form submissions
Session-based authentication
Foreign key constraints for referential integrity
Server-side price validation
Controlled product ID verification
Password strength validation
__________________________________________________________________________
## Scalability Improvements (Future Roadmap)
Payment gateway integration (Paystack / Stripe)
MVC refactor
OOP migration
REST API layer
Customer authentication system
CSRF protection
Prepared statements (PDO/MySQLi)
Inventory management system
Email notification system
__________________________________________________________________________
## Key Learning Outcomes
Session-based state management
AJAX implementation in PHP
Relational database modeling
Foreign key constraints handling
E-commerce order structuring
Admin/customer interface separation
Debugging cart state persistence isues
___________________________________________________________________________
## Author
Chidera Carol Omeribe
Junior Web Developer | PHP & MySQL
