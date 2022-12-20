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

function func_food_items()
{
    $profile = profile_get();
    $profile_id = $profile['id'];

    $where = "oi.order_id IN(SELECT `id` FROM `order` WHERE `restaurant_id`={$profile_id})";
    $sql = "SELECT oi.*
                , fm.title as `food_name` 
                , fm.price as `food_price` 
                , fm.discount_percent as `food_discount_percent` 
                , fm.thumbnail as `food_thumbnail` 
                FROM `order_item` as oi
                LEFT JOIN `food_menu` as fm ON(oi.food_id=fm.id) WHERE {$where}";
    $items = db_get($sql);

    return $items;
}

function func_accept_order($id = 0)
{
    $data = [
        'status' => 1,
    ];
    $where = "id={$id}";

    return db_update('order', $data, $where);
}

function func_cancel_order($id = 0)
{
    // delete order
    db_delete('order', "id={$id}");
    // delete order item
    db_delete('order_item', "order_id={$id}");

    return true;
}

?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = input_get_post('action', 'index');
if ($action == 'accept') {
    controller_accept();
} elseif ($action == 'cancel') {
    controller_cancel();
}

function controller_accept()
{
    $id = input_get('id', 0);
    func_accept_order($id);
    redirect('/restaurant_ordering.php');
}

function controller_cancel()
{
    $id = input_get('id', 0);
    func_cancel_order($id);
    redirect('/restaurant_ordering.php');
}

// เตรียมค่าที่ส่งเข้าไปที่ view
$id = input_get('id', 0);

$active_menu = 'ordering';
$order_items = func_order_items();
$food_items = func_food_items();

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
                    <h2 class="text-center">รอยันยันการสั่งซื้อและประวัติการสั่งซื้อ</h2>

                    <?php
                    if (count($order_items) <= 0) {
                        echo '<h6 class="text-center p-4 border">ไม่มีข้อมูลสำหรับแสดงผล...</h6>';
                    } else {
                        for ($i = 0; $i < count($order_items); ++$i) {
                            $row = $order_items[$i];
                            $accept_link = "/restaurant_ordering.php?action=accept&id={$row['id']}";
                            $cancel_link = "/restaurant_ordering.php?action=cancel&id={$row['id']}";
                            $invoice_link = "/restaurant_ordering_invoice.php?id={$row['id']}";

                            $order_status_text = 'รอยืนยันการสั่งซื้อ';
                            $order_status_bg = 'text-bg-secondary';
                            if ($row['status'] == 1) {
                                $order_status_text = 'รับออร์เดอร์เรียบร้อยแล้ว...กำลังจัดเตรียมอาหาร';
                                $order_status_bg = 'text-bg-primary';
                            } elseif ($row['status'] == 2) {
                                $order_status_text = 'กำลังนำอาหารไปส่งลูกค้า...รอสักครู่';
                                $order_status_bg = 'text-bg-warning';
                            } elseif ($row['status'] == 3) {
                                $order_status_text = 'นำส่งอาหารและได้รับชำระเงินเรียบร้อยแล้ว';
                                $order_status_bg = 'text-bg-success';
                            }
                            ?>
                    <div class="border mb-3">
                        <div class="row g-0">
                            <div class="col-6">
                                <div class="<?php echo $order_status_bg; ?> p-2">ลูกค้า:
                                    <?php echo $row['customer_firstname'].' '.$row['customer_lastname']; ?> /
                                    สั่งซื้อเมื่อ: <?php echo $row['created_at']; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="<?php echo $order_status_bg; ?> p-2 h-100"><?php echo $order_status_text; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="text-bg-light p-2">
                                    <div class="row">
                                        <?php
                                        for ($j = 0; $j < count($food_items); ++$j) {
                                            $row2 = $food_items[$j];
                                            if ($row2['order_id'] == $row['id']) {
                                                ?>
                                        <div class="col-2 mt-2">
                                            <img src="<?php echo $row2['food_thumbnail']; ?>" width="100%">
                                        </div>
                                        <div class="col-10">
                                            <h5><?php echo $row2['food_name']; ?></h5>
                                            <div>x<?php echo $row2['food_amount']; ?></div>
                                        </div>
                                        <?php
                                            }
                                        }
                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-0 border">
                            <div class="col-8">
                                <div class="text-bg-light p-2 d-flex justify-content-between">
                                    <?php
                                    if ($row['status'] == 0) {
                                        ?>
                                    <div>
                                        <a href="<?php echo $accept_link; ?>"
                                            class="btn btn-primary">ยืนยันการสั่งซื้อ</a>
                                        <a href="<?php echo $cancel_link; ?>"
                                            class="btn btn-danger">ยกเลิกการสั่งซื้อ</a>
                                    </div>
                                    <?php
                                    } elseif ($row['status'] == 3) {
                                        ?>
                                    <a href="<?php echo $invoice_link; ?>" target="_blank"
                                        class="btn btn-warning">พิมพ์ใบเสร็จรับเงิน</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 h-100">
                                    <h5>ยอดคําสั่งซื้อทั้งหมด: <?php echo number_format($row['total_price']); ?> บาท
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    }
    ?>

                </div>
            </div>
            <!-- end: main content -->
        </div>
    </div>
</main>
<?php } ?>
<!-- end: main body -->

<?php require_once 'footer.php'; ?>