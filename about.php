<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';
?>



<?php
// ================================ CONTROLLER =======================
$active_menu = 'about';
?>



<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<?php require_once 'nav.php'; ?>
<!-- start: main body -->
<div class="container">
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">เกี่ยวกับเรา</h1>
            <div class="col-md-8 fs-4">
                <div>ระบบสั่งอาหารออนไลน์นี้ ถูกพัฒนาขึ้นจากนักศึกษา ระดับ ปวช. แผนกเทคโนโลยีสารสนเทศ
                    วิทยาลัยเทคนิคชัยภูมิ</div>
                <div class="mt-3">ฝึกสอนโดย: คุณครูกฤษณา แนววิเศษ (หัวหน้าแผนก)</div>
            </div>
        </div>
    </div>
</div>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>