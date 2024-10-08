# Groce - Online Grocery Shop

This is an online grocery store built entirely using **PHP**, **HTML**, **CSS** and a **MySQL** database, without the use of any frameworks. The project was developed as part of a university assignment in the class **32516 Internet Programming** in the 2024 Autumn Session at the **University of Technology Sydney**, showcasing fundamental skills in web development.

## Features

- **Logo and Navigation**: The website features a consistent logo and category navigation bar across all pages, enhancing the user experience.

- **Search Functionality**: 
  - Users can search for products by name or description using the search bar.
  - The search results are displayed in a grid format, showing product images, names, unit prices, stock availability, and an "add to cart" button.

- **Category Browsing**: 
  - Users can browse products by category, with support for sub-categories.
  - Products are displayed in a grid, showing relevant information (name, price, stock status, etc.).
  - If a product is out of stock, the "add to cart" button is disabled.

- **Shopping Cart**: 
  - A fully functional shopping cart allows users to add items, view item details, adjust quantities, and remove products.
  - The cart displays the total price and allows users to clear all items with a single click.
  - Cart contents can be restored when revisiting the site within a short time period.
  - Users cannot proceed to the checkout if the cart is empty.

- **Delivery and Order Confirmation**:
  - Users fill out a form with their delivery details (name, address, email, and phone number). The address includes state and territory selection (Australia-specific).
  - Form validation ensures the inputs are correct (e.g., proper email format).
  - When placing an order, the system checks stock availability and updates the database accordingly.
  - Upon successful order placement, the shopping cart is cleared and the user is shown a confirmation page with their order details.

- **Interactive Design**: 
  - Visual feedback is provided on hover for product categories, items, and buttons (e.g., "add to cart" turns grey if out of stock).
  - Buttons are dynamically styled based on context (e.g., disabled when invalid or unavailable).

## Screenshots

Here are some screenshots demonstrating key features of the website:

### Home Page and Product List

![Product List](https://github.com/user-attachments/assets/883cf1ba-dff0-49eb-a264-b7ddcbdd99b0)


### Product Details

![Product Details](https://github.com/user-attachments/assets/c8b12c46-8cb1-4cd1-9017-f76d7b3b6fbe)


### Shopping Cart

![Shopping Cart](https://github.com/user-attachments/assets/d2cea6e4-15b4-4187-8162-08001d5d5464)



## Future Improvements
This project is a foundational example of a simple online shopping website. Potential areas for future improvement include:
- Implementing actual email notifications for order confirmations.
- Expanding product categories and improving the search functionality to support more complex queries.
- Integrating payment gateway functionality for real-world e-commerce use.
