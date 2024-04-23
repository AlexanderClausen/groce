<?php
include 'functions.php';

// Set title and hide stuff
$title = 'Order confirmation';
$show_breadcrumbs = 0;
$show_categories = 0;
$show_searchbar = 0;

// Initialize content
$content = '';

// Start session
session_start();

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
    // Success message for submitting order
    $content .= '
                <div class="success">
                    <p>Your order has been successfully submitted. You will receive a confirmation email shortly. We will also notify you once your order has left our dispatch centre.</p>
                    <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                    <a href="./index.php">Continue shopping</a>
                </div>';

    // Check POST data
    if(isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['suburb'], $_POST['state'], $_POST['postcode'])) {
        $recipient = $_POST['name'].'<br />'.$_POST['address'].'<br />'.strtoupper($_POST['suburb']).' '.$_POST['state'].' '.$_POST['postcode'];
        $content .= '
            <div id="order-confirmation">
                <h2>Order confirmation</h2>
                <h3>Recipient details</h3>
                <p>'.$recipient.'</p>
                <p>'.$_POST['email'].'<br />'.$_POST['phone'].'</p>
            ';
    } else {
        $content .= 'Something went wrong. Please fill in all fields.';
    }


    // Display cart: quantities, product names, total price (per item) and total price (all items)
    $content .= '
    <div id="min-cart">
        <h2>Your order</h2>
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

    // Check if buyer exists in database, otherwise add buyer to database. Columns: buyer_id (auto-incremented), name, street, suburb, state, postcode, email, phone
    $buyer = json_decode(getBuyer($_POST['email']));
    if ($buyer == null) {
        addBuyer($_POST['name'], $_POST['address'], $_POST['suburb'], $_POST['state'], $_POST['postcode'], $_POST['email'], $_POST['phone']);
        $buyer = json_decode(getBuyer($_POST['email']));
    } else {
        // Check if all the details are the same, if not add as new buyer
        if ($buyer->name != $_POST['name'] || $buyer->street != $_POST['address'] || $buyer->suburb != $_POST['suburb'] || $buyer->state != $_POST['state'] || $buyer->postcode != $_POST['postcode'] || $buyer->phone != $_POST['phone']) {
            addBuyer($_POST['name'], $_POST['address'], $_POST['suburb'], $_POST['state'], $_POST['postcode'], $_POST['email'], $_POST['phone']);
            $buyer = json_decode(getBuyer($_POST['email']));
        }
    }
    // Get buyer_id
    $buyerId = $buyer->buyer_id;
    
    // Save order to database and save order id
    $orderId = saveOrder($buyerId, $total_cart);
    
    // Update quantity of items in stock in database and save order items to database
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $product = json_decode(getProduct($id));
        saveOrderItem($orderId, $id, $quantity, $product->unit_price);
        $new_stock_quantity = $quantity;
        updateStock($id, $new_stock_quantity);
    }

    // Empty cart
    $_SESSION['cart'] = [];
}

include 'layout.php';