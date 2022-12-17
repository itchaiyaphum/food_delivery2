<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'index') ? 'active' : ''; ?>" href="/admin.php">
            <i class="bi-house"></i> หน้าแรก
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'restaurant_type') ? 'active' : ''; ?>"
            href="/admin_restaurant_type.php">
            <i class="bi-box"></i> จัดการประเภทร้านอาหาร
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'restaurant') ? 'active' : ''; ?>" href="/admin_restaurant.php">
            <i class="bi-shop"></i> จัดการร้านอาหาร
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'rider') ? 'active' : ''; ?>" href="/admin_rider.php">
            <i class="bi-car-front"></i> จัดการผู้ส่งอาหาร
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'customer') ? 'active' : ''; ?>" href="/admin_customer.php">
            <i class="bi-person"></i> จัดการลูกค้า
        </a>
    </li>
</ul>