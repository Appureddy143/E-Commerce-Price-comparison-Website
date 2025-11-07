<?php
// This file is included by admin_panel.php, add_product.php, etc.
// Security check is handled by the parent file before including this.

// Get the current page filename to set the "active" class
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- This is the sidebar. It's meant to be included inside the <body> of another page. -->
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="admin_panel.php" class="logo">Admin Panel</a>
    </div>
    <ul class="nav-links">
        <li <?php echo ($current_page == 'admin_panel.php') ? 'class="active"' : ''; ?>>
            <a href="admin_panel.php">
                <span class="icon">?</span> <!-- Placeholder for icon -->
                <span>Dashboard</span>
            </a>
        </li>
        <li <?php echo ($current_page == 'add_product.php') ? 'class="active"' : ''; ?>>
            <a href="add_product.php">
                <span class="icon">+</span> <!-- Placeholder for icon -->
                <span>Add Product</span>
            </a>
        </li>
        <li <?php echo ($current_page == 'add_admin.php') ? 'class="active"' : ''; ?>>
            <a href="add_admin.php">
                <span class="icon">?</span> <!-- Placeholder for icon -->
                <span>Add Admin</span>
            </a>
        </li>
    </ul>
    
    <!-- Links to go to other parts of the site -->
    <ul class="nav-links bottom-links">
        <li>
            <a href="user_panel.php">
                <span class="icon">?</span> <!-- Placeholder for icon -->
                <span>View User Panel</span>
            </a>
        </li>
        <li>
            <a href="logout.php">
                <span class="icon">?</span> <!-- Placeholder for icon -->
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>