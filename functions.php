<?php
    $conn = new mysqli('localhost', 'root', '', 'grocery');
    if ($conn->connect_error) {
        print($conn->connect_error);
        die('Connection failed: ' . $conn->connect_error);
    }

    function getProduct($productId) {
        global $conn;

        // Database query to get product details
        $sql = "SELECT * FROM products WHERE product_id = $productId";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Product found: product details returned
            $product = $result->fetch_assoc();
            return json_encode($product);
        } else {
            // Product not found
            return null;
        }
    }

    function getAllProducts() {
        global $conn;

        // Database query to get all products
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Products found: products returned
            $products = [];
            while ($product = $result->fetch_assoc()) {
                $products[] = $product;
            }
            return json_encode($products);
        } else {
            // No products found
            return null;
        }
    }

    function getSearchResults($searchString) {
        global $conn;

        // Database query to get search results
        $sql = "SELECT products.* FROM products INNER JOIN categories ON products.category_id=categories.category_id WHERE (products.product_name LIKE '%$searchString%') OR (products.product_description LIKE '%$searchString%') OR (categories.category_name LIKE '%$searchString%')";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Products found: products returned
            $products = [];
            while ($product = $result->fetch_assoc()) {
                $products[] = $product;
            }
            return json_encode($products);
        } else {
            // No products found
            return null;
        }
    }

    function getAllCategories($includeChildren=true) {
        global $conn;

        // Database query to get all categories
        $sql = "SELECT category_id, category_name FROM categories WHERE category_parent IS NULL";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Categories found: categories returned
            $categories = [];
            while ($category = $result->fetch_assoc()) {
                if ($includeChildren) {
                    // Database query to get child categories
                    $childSql = "SELECT category_id, category_name FROM categories WHERE category_parent = " . $category['category_id'];
                    $childResult = $conn->query($childSql);
                    $childCategories = [];
                    while ($childCategory = $childResult->fetch_assoc()) {
                        $childCategories[] = $childCategory;
                    }
                    $category['children'] = $childCategories;
                }
                $categories[] = $category;
            }
            return json_encode($categories);
        } else {
            // No categories found
            return null;
        }
    }

    function getCategory($id, $idType = 'category', $output = 'products', $includeChildrenProducts = true) {
        global $conn;

        // Figure out category ID (get from product details if a product ID is provided)
        if ($idType == 'product') {
            $categoryId = json_decode(getProduct($id))->category_id;
        }
        // Otherwise provided category ID
        elseif ($idType == 'category') {
            $categoryId = $id;
        }
        else {
            // Invalid ID type
            return null;
        }

        if ($output == 'category_id') {
            return $categoryId;
        }
        elseif ($output == 'details') {
            // Database query to get category details
            $sql = "SELECT * FROM categories WHERE category_id = $categoryId";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Category found: category details returned
                $category = $result->fetch_assoc();
                return json_encode($category);
            } else {
                // Category not found
                return null;
            }
        }
        elseif ($output == 'children') {
            // Database query to get child categories
            $sql = "SELECT category_id FROM categories WHERE category_parent = $categoryId";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Child categories found: categories returned
                $childCategoryIds = [];
                while ($category = $result->fetch_assoc()) {
                    $childCategoryIds[] = (int)$category['category_id'];
                }
                return (array)$childCategoryIds;
            } else {
                // No child categories found
                return null;
            }
        }
        elseif ($output == 'products') {
            // Database query to get products in category
            if ($includeChildrenProducts == false) {
                $sql = "SELECT * FROM products WHERE category_id = $categoryId";
            }
            else {
                $children = getCategory($categoryId, 'category', 'children');
                if ($children == null) {
                    $categoryIds = [$categoryId];
                }
                else {
                    $categoryIds = array_merge([$categoryId], $children);
                }
                $sql = "SELECT * FROM products WHERE category_id IN (" . implode(',', $categoryIds) . ")";
            }
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Products found: products returned
                $products = [];
                while ($product = $result->fetch_assoc()) {
                    $products[] = $product;
                }
                return json_encode($products);
            } else {
                // No products found
                return null;
            }
        }
        else {
            // Invalid output type
            return null;
        }
    }

    function getBreadcrumbs($id, $idType = 'category', $output = 'array') {
        // Get category id from arguments (as starting point)
        $categoryId = getCategory($id, $idType, 'category_id');

        // Empty array to be filled with category names
        $breadcrumbs = [];
        while ($categoryId != null) {
            // Get category details from category
            $category = json_decode(getCategory($categoryId, 'category', 'details'));
            // Add name of the category to the breadcrumbs array
            $breadcrumbs[] = $category->category_id;
            // Repeat with parent category (until parent category is null)
            $categoryId = $category->category_parent;
        }
        // Reverse the breadcrumbs array to get the correct order
        $breadcrumbs = array_reverse($breadcrumbs);

        if ($output == 'array') {
            // Return breadcrumbs array
            return (array) $breadcrumbs;
        }
        else {
            $html = '';
            foreach ($breadcrumbs as $index) {
                $category = json_decode(getCategory($index, 'category', 'details'));
                $html .= '<a href="./index.php?view=category&id=' . $category->category_id . '">' . $category->category_name . '</a>';
                if ($index != end($breadcrumbs)) {
                    $html .= ' > ';
                }
            }
            return $html;
        }
    }

    function updateStock($productId, $quantity) {
        global $conn;

        // Database query to update stock
        $sql = "UPDATE products SET stock_quantity = stock_quantity - $quantity WHERE product_id = $productId";
        $conn->query($sql);
    }

    function getBuyer($email) {
        global $conn;

        // Database query to check if buyer exists
        $sql = "SELECT * FROM order_buyer WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Buyer found: buyer details returned
            $buyer = $result->fetch_assoc();
            return json_encode($buyer);
        } else {
            // Buyer not found
            return null;
        }
    }

    function addBuyer($name, $address, $suburb, $state, $postcode, $email, $phone) {
        global $conn;

        // Database query to add buyer
        $sql = "INSERT INTO order_buyer (name, street, suburb, state, postcode, email, phone) VALUES ('$name', '$address', '$suburb', '$state', '$postcode', '$email', '$phone')";
        $conn->query($sql);
    }

    // Save order to database. Columns: order_id, buyer_id, order_total_price, order_status, order_date
    function saveOrder($buyerId, $totalPrice) {
        global $conn;

        // Database query to save order
        $sql = "INSERT INTO orders (buyer_id, order_total_price, order_status, order_date) VALUES ($buyerId, $totalPrice, 'pending', NOW())";
        $conn->query($sql);

        // Get order_id
        $orderId = $conn->insert_id;

        return $orderId;
    }

    // Save individual items to database. Columns: order_id, order_detail_id (auto-increment), product_id, quantity, unit_ordered_price
    function saveOrderItem($orderId, $productId, $quantity, $unitPrice) {
        global $conn;

        // Database query to save order details
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, unit_ordered_price) VALUES ($orderId, $productId, $quantity, $unitPrice)";
        $conn->query($sql);
    }