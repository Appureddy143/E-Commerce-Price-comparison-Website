<?php
// This file is included by admin_panel.php, add_product.php, etc.
// Security check is handled by the parent file before including this.

$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar">
    <div class="sidebar-header">
        <a href="admin_panel.php" class="sidebar-logo">Admin Panel</a>
    </div>
    <ul class="sidebar-menu">
        <li <?php echo ($current_page == 'admin_panel.php') ? 'class="active"' : ''; ?>>
            <a href="admin_panel.php">
                <span>Dashboard</span>
            </a>
        </li>
        <li <?php echo ($current_page == 'add_product.php') ? 'class="active"' : ''; ?>>
            <a href="add_product.php">
                <span>Add Product</span>
            </a>
        </li>
        <li <?php echo ($current_page == 'add_admin.php') ? 'class="active"' : ''; ?>>
            <a href="add_admin.php">
                <span>Add Admin</span>
            </a>
        </li>
        
        <!-- Link back to the main user panel -->
        <li>
            <a href="user_panel.php">
                <span>User Panel</span>
            </a>
        </li>
        <li>
            <a href="logout.php">
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>

