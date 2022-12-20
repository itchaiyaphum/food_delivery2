<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';

function func_order_items()
{
    $profile = profile_get();
    $profile_id = $profile['id'];

    $where = "o.restaurant_id={$profile_id}";
    $sql = "SELECT o.*
                , u.firstname as `customer_firstname` 
                , u.lastname as `customer_lastname` 
                , u.thumbnail as `customer_thumbnail` 
                FROM `order` as o
                LEFT JOIN `user` as u ON(o.customer_id=u.id) WHERE {$where} ORDER BY o.created_at DESC";
    $items = db_get($sql);

    return $items;
}

?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = input_get_post('action', 'index');

// เตรียมค่าที่ส่งเข้าไปที่ view
$active_menu = 'reporting';
$order_items = func_order_items();

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
                    <?php require_once 'restaurant_menu.php'; ?>
                </div>
            </div>
            <!-- end: left menu -->

            <!-- start: main content -->
            <div class="col-lg-9">
                <div class="mb-5 mt-3">
                    <h2 class="text-center">รายงานการขาย</h2>
                </div>
                <table class="table">
                    <tr>
                        <td>รหัสสั่งซื้อ</td>
                        <td>วันที่สั่งซื้อ</td>
                        <td>ราคารวมทั้งหมด</td>
                        <td>ชื่อลูกค้า</td>
                    </tr>
                <?php
                $summary_price = 0;
    for ($i = 0; $i < count($order_items); ++$i) {
        $row = $order_items[$i];
        $summary_price += (int) $row['total_price'];
        ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><?php echo number_format($row['total_price']); ?></td>
                    <td><?php echo $row['customer_firstname'].' '.$row['customer_lastname']; ?></td>
                </tr>
                <?php
    }
    ?>
                </table>
                <h4 class="text-center">ราคารวมทั้งหมด : <?php echo $summary_price; ?>บาท</h4>
            </div>
            <!-- end: main content -->
        </div>
    </div>
</main>
<?php } ?>
<!-- end: main body -->

<?php require_once 'footer.php'; ?>