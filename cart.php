<?php
include 'functions.php';

// Start session
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Set title and hide stuff
$title = 'Your Cart';
$show_breadcrumbs = 0;
$show_categories = 0;
$show_searchbar = 0;

// Initialize content
$content = '';

// // Debug button that adds an unavailable product (id 2) to the cart
// $content .= '
//     <form action="cart.php" method="post">
//         <input type="hidden" name="action" value="add">
//         <input type="hidden" name="id" value="3">
//         <input type="hidden" name="quantity" value="2">
//         <input type="submit" value="Add unavailable product to cart">
//     </form>
// ';

// Check if something is being added to cart
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        // Get product ID and quantity
        $added_id = $_POST['id'];
        $quantity = $_POST['quantity'];

        // Check if product is already in cart
        if (isset($_SESSION['cart'][$added_id])) {
            // Product is already in cart: increase quantity
            $_SESSION['cart'][$added_id] += $quantity;
        } else {
            // Product is not in cart: add to cart
            $_SESSION['cart'][$added_id] = $quantity;
        }

        // Success message for adding to cart
        $added_product = json_decode(getProduct($added_id));
        $content .= '
            <div class="success">
                <p>Product added to cart</p>
                <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                <a href="./index.php">Continue shopping</a>
            </div>';
    }
    elseif ($_POST['action'] == 'empty') {
        // Empty cart
        $_SESSION['cart'] = [];
        $content .= '
            <div class="alert">
                <p>Cart emptied</p>
                <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                <a href="./index.php">Continue shopping</a>
            </div>';
    }
    elseif ($_POST['action'] == 'quantity') {
        // Get product ID and quantity
        $changed_id = $_POST['id'];
        $changed_product = json_decode(getProduct($changed_id));
        $changed_quantity = (int)$_POST['amount'];

        // Delete item from cart
        if ($changed_quantity === 0) {
            unset($_SESSION['cart'][$changed_id]);
            $content .= '
            <div class="success">
                <p>The product "'.$changed_product->product_name.'" has been removed from your cart.</p>
                <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                <a href="./index.php">Continue shopping</a>
            </div>';
        }

        // Change quantity
        else {
            $_SESSION['cart'][$changed_id] = $changed_quantity;
            $content .= '
            <div class="success">
                <p>Quantity of product "'.$changed_product->product_name.'" changed successfully.</p>
                <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                <a href="./index.php">Continue shopping</a>
            </div>';
        }
    }
    elseif ($_POST['action'] == 'unavailable') {
        // Get unavailable items and save them as list items
        $unavailable_items = json_decode($_POST['unavailable_items']);
        $unavailable_items_list = '';
        foreach ($unavailable_items as $id) {
            $product = json_decode(getProduct($id));
            $unavailable_items_list .= '<li>'.$product->product_name.'</li>';
        }
        $content .= '
            <div class="alert">
                <p>The following items in your cart are no longer available in your selected quantity:</p>
                <ul>'.$unavailable_items_list.'</ul>
                <p>Please review your cart.</p>
                <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                <a href="./index.php">Continue shopping</a>
            </div>';
    }
}

// List what's in cart
$content .= '
<div id="cart">
    <h1>Shopping Cart</h1>
    <table class="cart-table">
        <tr>
            <th>Remove</th>
            <th>Quantity</th>
            <th>Preview</th>
            <th>Product</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
';

$total_cart = 0;


// <form class="add-to-cart-form" action="cart.php" method="post">
//                 <input type="hidden" name="action" value="add">
//                 <input type="hidden" name="id" value="' . $product->product_id . '">


foreach ($_SESSION['cart'] as $id => $quantity) {
    $product = json_decode(getProduct($id));
    $total_product = $product->unit_price * $quantity;
    $total_cart += $total_product;

    $content .= '
        <tr>
            <td class="cart-remove-column">
                <form class="remove-from-cart-form action="cart.php" method="post">
                    <input type="hidden" name="action" value="quantity">
                    <input type="hidden" name="amount" value="0">
                    <input type="hidden" name="id" value="'.$product->product_id.'">
                    <button class="remove-from-cart-button button" type="submit" ><span class="material-symbols-outlined">delete</span></button>
                </form>
            </td>
            <td class="cart-changequantity-column">
                <form class="change-quantity-form" action="cart.php" method="post">
                    <input type="hidden" name="action" value="quantity">
                    <input type="hidden" name="id" value="'.$product->product_id.'">
                    <input class="change-quantity-input" type="number" id="amount" name="amount" min="0" max="'.$product->stock_quantity.'" value="'.$quantity.'">
                    <button class="change-quantity-button button" type="submit" ><span class="material-symbols-outlined">done</span></button>
                </form>
            </td>
            <td class="cart-preview-column"><img class="cart-preview" src="./images/products/'.$product->product_image.'" alt="'.$product->product_name.'"></td>
            <td class="cart-name-column" style="padding-left: 10px; text-align: left;"><a href="./product.php?id='.$product->product_id.'">'.$product->product_name.'</a></td>
            <td class="cart-unitprice-column" style="padding-right: 10px; text-align: right;">$'.$product->unit_price.'</td>
            <td class="cart-totalprice-column" style="padding-right: 10px; text-align: right;">$'.number_format($total_product, 2).'</td>
        </tr>
    ';
}

if ($_SESSION['cart'] == []) {
    $content .= '
        <tr>
            <td colspan="6" style="padding: 10px">Your cart is empty.</td>
        </tr>
    ';
}

$content .= '
        <tr id="cart-grandtotal">
            <td colspan="5">Grand Total:</td>
            <td>$'.number_format($total_cart, 2).'</td>
        </tr>
    </table>
</div>
';

$content .= '<div id="cart-buttons">';
// Checkout button using POST
if ($_SESSION['cart'] != []) {
    $content .= '
        <form action="checkout.php" method="post">
            <input class="checkout button" type="submit" value="Checkout">
        </form>';
} else {
    $content .= '
        <form action="checkout.php" method="post">
            <input class="checkout button" type="submit" value="Checkout" disabled>
        </form>';
}

// Empty cart button using POST
if ($_SESSION['cart'] != []) {
    $content .= '
        <form action="cart.php" method="post">
            <input type="hidden" name="action" value="empty">
            <input class="empty_cart button" type="submit" value="Empty Cart">
        </form>
';
} else {
    $content .= '
        <form>
            <input class="empty_cart button" type="submit" value="Empty Cart" disabled>
        </form>
        ';
}
$content .= '</div>';


include 'layout.php';