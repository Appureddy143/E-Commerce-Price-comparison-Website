<?php
// This file is included on all admin pages.
// It assumes a session has already been started.

// Get the name of the currently active script
$current_page = basename($_SERVER['PHP_SELF']);

?>
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="admin_panel.php" class="logo">Admin Panel</a>
    </div>
    <ul class="nav-links">
        <li <?php echo ($current_page == 'admin_panel.php') ? 'class="active"' : ''; ?>>
            <a href="admin_panel.php">
                <span class="icon">📈</span> <!-- Simple icon placeholder -->
                Dashboard
            </a>
        </li>
        <li <?php echo ($current_page == 'add_product.php') ? 'class="active"' : ''; ?>>
            <a href="add_product.php">
                <span class="icon">➕</span>
                Add Product
            </a>
        </li>
        <li>
            <a href="user_panel.php">
                <span class="icon">🏠</span>
                User Panel
            </a>
        </li>
        <li>
            <a href="logout.php">
                <span class="icon">🚪</span>
                Logout
            </a>
        </li>
    </ul>
</nav>