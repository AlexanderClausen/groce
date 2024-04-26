<?php
include 'functions.php';

// Set title and hide stuff
$title = 'Order details';
$show_breadcrumbs = 0;
$show_categories = 0;
$show_searchbar = 0;

// Initialize content
$content = '';

// Start session
session_start();

// Check session
if (!isset($_SESSION['cart']) or !is_array($_SESSION['cart']) or empty($_SESSION['cart']) or array_sum($_SESSION['cart']) == 0) {
    $content .= '
        <form id="no-items-form" action="cart.php" method="post">
            <input type="hidden" name="action" value="noitems">
        </form>
        <script>
            document.getElementById("no-items-form").submit();
        </script>
    ';
}
else {
    // Check if all items in the cart are sufficiently available and list ids of unavailable items
    $unavailable_items = [];
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $product = json_decode(getProduct($id));
        if ($product->stock_quantity < $quantity) {
            $unavailable_items[] = $id;
        }
    }
    // For testing: print unavailable items in HTML
    // $content .= '<pre>'.print_r($unavailable_items, true).'</pre>';

    // If any items are unavailable, forward to cart.php posting the unavailable items
    if (!empty($unavailable_items)) {
        $content .= '
            <form id="unavailable-items-form" action="cart.php" method="post">
                <input type="hidden" name="action" value="unavailable">
                <input type="hidden" name="unavailable_items" value="'.json_encode($unavailable_items).'">
            </form>
            <script>
                document.getElementById("unavailable-items-form").submit();
            </script>
        ';
    }
    else {
        // Display cart: quantities, product names, total price (per item) and total price (all items)
        $content .= '
            <div id="min-cart">
                <h2>Your cart</h2>
                <p>Review your order before placing it.</p>
                <table>
                    <tr>
                        <th>Amount</th>
                        <th>Product</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
        ';

        $total_cart = 0;

        foreach ($_SESSION['cart'] as $id => $quantity) {
            $product = json_decode(getProduct($id));
            $total_product = $product->unit_price * $quantity;
            $total_cart += $total_product;

            $content .= '
                <tr>
                    <td>'.$quantity.'</td>    
                    <td>'.$product->product_name.'</td>
                    <td style="text-align: right;">$'.number_format($total_product, 2).'</td>
                </tr>
            ';
        }

        $content .= '
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;"><strong>$'.number_format($total_cart, 2).'</strong></td>
                </tr>
                </table>
            </div>
        </div>
        ';
        
        // Recipient details
        $content .= '
        <div class="checkout-container">
            <div id="order-recipient">
                <h2>Recipient details</h2>
                <p>Please fill in your details below. The red asterisk indicates a required field.</p>
                <form action="order.php" method="post">
                    <div class="input-row">
                        <label class="required" for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="John Doe" required>
                    </div>
                    <div class="input-row">
                        <label class="required" for="email">Email</label>
                        <input type="email" id="email" name="email" pattern="^(.+)(@)([a-zA-Z0-9]+)(\.[a-zA-Z0-9]+)+$" placeholder="e.g. john.doe@uts.edu.au" required>
                    </div>
                    <div class="input-row">
                        <label class="required" for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" pattern="^(0|\+61)[ -]?([0-9]{3}[ -]?){2}[0-9]{3}$" placeholder="e.g. 0123 456 789" required>
                    </div>
                    <div class="input-row">
                        <label class="required" for="street">Address</label>
                        <input type="text" id="street" name="address" pattern="^(.*\/)?([0-9]*-?[0-9]+)( )([a-zA-Z]* ?)*$" placeholder="e.g. 15 Broadway" required>
                    </div>
                    <div class="input-row">
                        <label class="required" for="suburb">Suburb</label>
                        <input type="text" id="suburb" name="suburb" placeholder="e.g. Ultimo" required>
                    </div>
                    <div class="input-row">
                        <label class="required" for="state">State</label>
                        <select id="state" name="state" required>
                            <option value="ACT">ACT (Australian Capital Territory)</option>
                            <option value="NSW">NSW (New South Wales)</option>
                            <option value="NT">NT (Northern Territory)</option>
                            <option value="QLD">QLD (Queensland)</option>
                            <option value="SA">SA (South Australia)</option>
                            <option value="TAS">TAS (Tasmania)</option>
                            <option value="VIC">VIC (Victoria)</option>
                            <option value="WA">WA (Western Australia)</option>
                        </select>
                    </div>
                    <div class="input-row">
                        <label class="required" for="postcode">Postcode</label>
                        <input type="text" id="postcode" name="postcode" pattern="^[0-9]{4}$" placeholder="e.g. 2007" required>
                    </div>
                    <div class="input-row">
                        <input class="button" type="submit" value="Place order">
                    </div>
                </form>
            </div>
        </div>
        ';
    }
}

include 'layout.php';