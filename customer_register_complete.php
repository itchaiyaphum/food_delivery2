<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';
?>



<?php
// ================================ CONTROLLER =======================
$active_menu = 'index';
?>



<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<?php require_once 'nav.php'; ?>
<!-- start: main body -->
<div class="container">
    <div class="row text-center mt-2">
        <div class="col">
            <div class="p-5">
                <i class="bi-check-circle" style="font-size: 6rem; color: #0d6efd"></i>
            </div>
            <h2 class="text-primary">ยินดีด้วย! การลงทะเบียบเรียบร้อยแล้ว!</h2>
            <p>
                กรุณารอผู้ดูแลระบบ อนุมัติการสมัครสมาชิกของท่าน ใช้เวลาตรวจสอบและดำเนินการประมาณ 5-10 นาที.
            </p>
            <div class="text-center m-5">
                <a href="/auth_login.php" class="btn btn-primary btn-lg"><i class="bi-person-circle"></i>
                    ลงชื่อเข้าสู่ระบบ</a>
                <a href="/" class="btn btn-link link-secondary"><i class="bi-house"></i> กลับหน้าหลัก</a>
            </div>
        </div>
    </div>
</div>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>