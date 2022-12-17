<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'index') ? 'active' : ''; ?>" href="/restaurant.php">
            <i class="bi-box"></i> หน้าแรก
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'food_category') ? 'active' : ''; ?>"
            href="/restaurant_food_category.php">
            <i class="bi-box"></i> หมวดหมู่อาหาร
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'food_menu') ? 'active' : ''; ?>"
            href="/restaurant_food_menu.php">
            <i class="bi-justify"></i> รายการอาหาร
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'ordering') ? 'active' : ''; ?>" href="/restaurant_ordering.php">
            <i class="bi-minecart"></i> รายการสั่งซื้ออาหาร
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'reporting') ? 'active' : ''; ?>"
            href="/restaurant_reporting.php">
            <i class="bi-graph-up"></i> รายงานการขาย
        </a>
    </li>
</ul>