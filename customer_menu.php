<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'index') ? 'active' : ''; ?>" href="/customer.php">
            <i class="bi-box"></i> หน้าแรก
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'restaurant') ? 'active' : ''; ?>"
            href="/customer_restaurant.php">
            <i class="bi-box"></i> แสดงร้านอาหารทั้งหมด
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'cart') ? 'active' : ''; ?>" href="/customer_cart.php">
            <i class="bi-shop"></i> ตะกร้าสินค้าทั้งหมด
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($active_menu == 'history_order') ? 'active' : ''; ?>"
            href="/customer_history_order.php">
            <i class="bi-car-front"></i> ประวัติการสั่งซื้ออาหาร
        </a>
    </li>
</ul>