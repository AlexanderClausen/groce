<?php
include 'functions.php';

// Parse URL parameters
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

$content = '';

if ($view == 'category') {
    $categoryId = isset($_GET['id']) ? $_GET['id'] : null; // Get category ID from URL
    $category = json_decode(getCategory($categoryId, 'category', 'details')); // Get category details
    $products = json_decode(getCategory($categoryId, 'category', 'products')); // Get category products
    $title = $category->category_name; // Set page title to category name
} elseif ($view == 'search') {
    $searchString = isset($_GET['q']) ? $_GET['q'] : null; // Get search string from URL
    $products = json_decode(getSearchResults($searchString)); // Get search results
    $title = 'Search results for "'.$searchString.'"'; // Set page title to search string
    $show_breadcrumbs = 0; // Hide breadcrumbs
    $content = '<p>Showing search results for <em>'.$searchString.'</em>:</p>'; // Add search string info to content
} else {
    $products = json_decode(getAllProducts()); // Get all products
    $title = 'All products'; // Set page title to default
    $show_breadcrumbs = 0; // Hide breadcrumbs
}

if (!empty($products)) {
    $content .= '<div id="products-grid">';
    foreach ($products as $product) {
        $productDetails = json_decode(getProduct($product->product_id));
        $stockStatus = $productDetails->stock_quantity > 0 ? 'In stock' : 'Not in stock';
        $stockStatusClass = $productDetails->stock_quantity > 0 ? 'in-stock' : 'not-in-stock';

        $content .= '
            <div class="grid-item">
                <a href="./product.php?id='.$productDetails->product_id.'"><p class="grid-product-name">'.$productDetails->product_name.'</p></a>
                <p class="grid-product-quantity">'.$productDetails->unit_quantity.'</p>
                <div class="bottom-aligned">
                    <a href="./product.php?id='.$productDetails->product_id.'"><img class="grid-product-image" src="./images/products/'.$productDetails->product_image.'" alt="'.$productDetails->product_name.'"></a>
                    <p class="grid-product-price">$'.$productDetails->unit_price.'</p>
                    <p class="grid-product-status '.$stockStatusClass.'">'.$stockStatus.'</p>
        ';

        if ($product->stock_quantity > 0) {
            $content .= '
                <form class="add-to-cart-form" action="cart.php" method="post">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="' . $product->product_id . '">
                    <input class="quantity" type="number" id="quantity" name="quantity" min="1" value="1" max="'.$product->stock_quantity.'">
                    <input class="add-to-cart button" type="submit" value="Add to cart">
                </form>
            ';
        } else {
            $content .= '
                <form class="add-to-cart-form">
                    <input class="quantity" type="number" id="quantity" name="quantity" min="0" max="1" disabled>
                    <input class="add-to-cart button" type="submit" value="Add to cart" disabled>
                </form>
            ';
        }

        $content .= '
                </div>
            </div>
        ';
    }
    $content .= '</div>';
} else {
    $content .= '<p><strong>No products found</strong></p>';
}

include 'layout.php';