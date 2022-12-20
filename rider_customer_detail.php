<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';
?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = input_get_post('action', 'index');

// เตรียมค่าที่ส่งเข้าไปที่ view
$active_menu = 'order_delivery';

$id = input_get_post('id', 0);
$customer_detail = profile_by_id($id);

?>


<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<?php require_once 'nav.php'; ?>
<!-- start: main body -->

<!-- ================================= VIEW=INDEX ================================= -->
<?php if ($action == 'index') {?>
    <main>
    <div class="container">
        <div class="row">
            <!-- start: left menu -->
            <div class="col-lg-3">
                <div class="card mb-3 mt-3 d-none d-sm-flex">
                <?php require_once 'rider_menu.php'; ?>
                </div>
            </div>
            <!-- end: left menu -->

            <div class="col-lg-9 mt-3">
                <div class="card mb-5">
                    <div class="card-header">
                        <h2 class="card-title h4">แสดงข้อมูลผู้สั่งอาหาร</h2>
                    </div>
                    <div class="card-body">
                        <form id="mainForm">
                            <div class="row mb-4">
                                <label class="col-sm-3 col-form-label form-label">ชื่อ-นามสกุล</label>
                                <div class="col-sm-9">
                                    <div class="form-control"><?php echo $customer_detail['firstname'].' '.$customer_detail['lastname']; ?></div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label class="col-sm-3 col-form-label form-label">เบอร์โทร</label>
                                <div class="col-sm-9">
                                    <div class="form-control"><?php echo $customer_detail['mobile_no']; ?></div>
                                </div>
                            </div>

                            <!-- field: address -->
                            <div class="row mb-4">
                                <label for="addressLine1Label"
                                    class="col-sm-3 col-form-label form-label">ที่อยู่</label>
                                <div class="col-sm-9">
                                    <div class="form-control">
                                    <?php echo $customer_detail['address']; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- end field: address -->

                            <!-- field: thumbnail -->
                            <div class="row mb-4">
                                <label for="thumbnailLabel"
                                    class="col-sm-3 col-form-label form-label">รูปภาพประจำตัว</label>
                                <div class="col-sm-9">
                                    <img src="<?php echo $customer_detail['thumbnail']; ?>" class="mb-3 w-25">
                                </div>
                            </div>
                            <!-- end field: thumbnail -->
                        </form>
                        <hr />
                        <div class="text-center">
                            <a href="/rider_order_delivery.php" class="btn btn-primary">กลับหน้า
                                รายการสั่งอาหารที่รอจัดส่ง</a>
                        </div>
                    </div><!-- end: card-body -->
                </div><!-- end: card -->
            </div>
        </div>
    </div>
</main>
<?php } ?>
<!-- end: main body -->

<?php require_once 'footer.php'; ?>