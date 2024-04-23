<?php 
include_once 'functions.php';
if (isset($_GET['view'])) {
    $view = $_GET['view'];
} elseif (!isset($view)) {
    $view = null;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <!-- Set title to $title variable or "Groce" if not set -->
    <title>
        <?php
            if (empty($title)) {
                echo 'Groce';
            } else {
                echo $title . ' | Groce';
            }
        ?>
    </title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> <!-- Use Material Icons from Google Fonts -->
</head>

<body>
    <!-- Header template -->
    <header>
        <!-- Top bar menu with logo, search bar, and cart -->
        <div id="top-bar">
            <!-- Logo -->
            <a id="top-bar-logo" class="logo" href="./">Groce</a>
            <!-- Search bar -->
            <form id="search-bar-form" <?php echo isset($show_searchbar) && $show_searchbar == 0 ? 'style="display: none;"' : '' ?> action="./index.php" method="get">
                <input type="hidden" name="view" value="search">
                <input id="search-bar" type="text" name="q" placeholder="Search for products">
                <button id="search-bar-button" class="button" type="submit"><span class="material-symbols-outlined">search</span></button>
            </form>
            <!-- Cart -->
            <a href="cart.php" id="cart-icon"><span class="material-symbols-outlined">shopping_cart</span></a>
        </div>

        <!-- Nav bar; hidden if PHP variable $show_categories is true -->
        <nav>
            <ul id="nav-bar" <?php echo isset($show_categories) && $show_categories == 0 ? 'style="display: none;"' : '' ?>>
                <?php foreach (json_decode(getAllCategories()) as $category) { ?>
                    <li>
                        <a href="./index.php?view=category&id=<?php echo $category->category_id ?>"><?php echo $category->category_name ?></a>
                        <?php if (isset($category->children)) { ?>
                            <ul>
                                <?php foreach ($category->children as $child) { ?>
                                    <li><a href="./index.php?view=category&id=<?php echo $child->category_id ?>"><?php echo $child->category_name ?></a></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
            <?php if (isset($show_categories) && $show_categories == 0) { ?>
                <hr id=nav-bar-replacement>
            <?php } ?>
        </nav>

        <!-- Breadcrumbs; hidden if PHP variable $show_breadcrumbs is true -->
        <div id="breadcrumbs" <?php echo isset($show_breadcrumbs) && $show_breadcrumbs == 0 ? 'style="display: none;"' : '' ?>>
                <?php echo getBreadcrumbs($_GET['id'], $view, 'html') ?>
        </div>
    </header>

    <!-- Main content insertion -->
    <main>
        <?php echo isset($content) ? $content : 'Error: No content provided' ?>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Alexander Clausen</p>
    </footer>

    <!-- JavaScript -->
    <script>
        const queryParams = new URLSearchParams(window.location.search);
        const categoryId = queryParams.get('category');
        const productId = queryParams.get('product');
    </script>
</body>
</html>