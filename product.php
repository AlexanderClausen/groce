<?php
include 'functions.php';

// Parse product parameter
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Set view type
$view = 'product';

if ($id === null) {
    // Product parameter not set: redirect to index
    header('Location: ./index.php');
    exit();
} else {
    // Product parameter set: get product details
    $product = json_decode(getProduct($id));
    $title = $product->product_name;
    $content = '
        <div id="product-details">
            <div class="product-image">
                <img src="./images/products/'.$product->product_image.'" alt="'.$product->product_name.'">
            </div>
            <div class="product-info">
                <p class="product-name">'.$product->product_name.'</p>
                <p class="product-quantity">'.$product->unit_quantity.'</p>
                <p class="product-price">$'.$product->unit_price.'</p>
                <p class="product-status '.($product->stock_quantity > 0 ? 'in-stock' : 'not-in-stock').'">'.($product->stock_quantity > 0 ? 'In stock' : 'Not in stock').'</p>
                <form class="add-to-cart-form" action="cart.php" method="post">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="' . $product->product_id . '">
            ';

    if ($product->stock_quantity > 0) {
        $content .= '
                    <input class="quantity" type="number" id="quantity" name="quantity" min="1" value="1" max="'.$product->stock_quantity.'">
                    <input class="add-to-cart button" type="submit" value="Add to cart">
        ';
    } else {
        $content .= '
                    <input class="quantity" type="number" id="quantity" name="quantity" min="0" max="1" disabled>
                    <input class="add-to-cart button" type="submit" value="Add to cart" disabled>
        ';
    }

    $content .= '
                </form>
                <p class="product-description">'.$product->product_description.'</p>
            </div>
        </div>
    ';
}

include 'layout.php';