<?php
$profile = profile_get();

$edit_profile = '/';
if ($profile['user_type'] == 'customer') {
    $edit_profile = '/customer_profile.php';
} elseif ($profile['user_type'] == 'restaurant') {
    $edit_profile = '/restaurant_profile.php';
} elseif ($profile['user_type'] == 'rider') {
    $edit_profile = '/rider_profile.php';
} elseif ($profile['user_type'] == 'admin') {
    $edit_profile = '/admin_profile.php';
}

$is_login = profile_is_login();
$firstname = $profile['firstname'];
$lastname = $profile['lastname'];
$thumbnail = $profile['thumbnail'];

$link_homepage = '/';
$link_about = '/about.php';

$link_login = '/auth_login.php';
$link_logout = '/auth_logout.php';

?>
<header class="border-bottom sticky-top">
    <nav class="navbar bg-light p-0">
        <div class="container justify-content-between justify-sm-content-start">
            <a class="navbar-brand" href="<?php echo $link_homepage; ?>">
                ระบบสั่งอาหารออนไลน์
            </a>
            <!-- start: responsive menu -->
            <button class="navbar-toggler m-2 d-sm-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-start d-block d-sm-none" id="navbarMenu">
                <ul class="list-group">
                    <li class="list-group-item">เมนูหลัก</li>
                    <li>
                        <a class="list-group-item" href="<?php echo $link_homepage; ?>"><i class="bi-house"></i>
                            หน้าหลัก</a>
                    </li>
                    <li>
                        <a class="list-group-item" href="<?php echo $link_about; ?>"><i class="bi-house"></i>
                            เกี่ยวกับเรา</a>
                    </li>
                    <?php
                    if ($is_login) {
                        ?>
                    <li>
                        <a class="list-group-item" href="<?php echo $edit_profile; ?>">
                            <i class="bi-pencil-square"></i>
                            แก้ไขข้อมูลส่วนตัว</a>
                    </li>
                    <li>
                        <a class="list-group-item" href="<?php echo $link_logout; ?>"><i class="bi-power"></i>
                            ออกจากระบบ</a>
                    </li>
                    <?php
                    } else {
                        ?>
                    <a class="list-group-item" href="<?php echo $link_login; ?>"><i class="bi-person"></i>
                        เข้าสู่ระบบ</a>
                    <?php
                    }
?>
                </ul>
            </div>
            <!-- end: responsive menu -->

            <div class="d-none d-sm-flex flex-grow-1">
                <ul class="nav flex-row">
                    <li class="nav-item <?php echo active_menu($active_menu, 'homepage'); ?>">
                        <a href="<?php echo $link_homepage; ?>" class="nav-link">หน้าหลัก</a>
                    </li>
                    <li class="nav-item <?php echo active_menu($active_menu, 'about'); ?>">
                        <a href="<?php echo $link_about; ?>" class="nav-link">เกี่ยวกับเรา</a>
                    </li>
                </ul>
            </div>

            <div class="dropdown dropstart d-none d-sm-flex">
                <?php
                if ($is_login) {
                    ?>
                <a href="#" class="d-block link-dark text-decoration-none" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <img src="<?php echo $thumbnail; ?>" width="30" height="30" class="rounded-circle" />
                    <span class="position-absolute translate-middle rounded-circle profile-online"></span>
                </a>
                <ul class="dropdown-menu text-small">
                    <li>
                        <a class="dropdown-item" href="/"><i class="bi-person-circle"></i>
                            <?php echo $firstname.' '.$lastname; ?></a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo $edit_profile; ?>"><i class="bi-pencil-square"></i>
                            แก้ไขข้อมูลส่วนตัว</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo $link_logout; ?>"><i class="bi-power"></i>
                            ออกจากระบบ</a>
                    </li>
                </ul>
                <?php } else {
                    ?>
                <a class="btn btn-primary" href="<?php echo $link_login; ?>"><i class="bi-person"></i>
                    ลงชื่อเข้าสู่ระบบ</a>
                <?php
                } ?>
            </div>
        </div>
    </nav>
</header>