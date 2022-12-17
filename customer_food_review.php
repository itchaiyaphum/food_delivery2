<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';
function func_order_items($id = 0)
{
    
    $where = "o.id={$id} AND o.status=3";
    $sql = "SELECT o.*
                , u.restaurant_name as `restaurant_name` 
                , u.thumbnail as `restaurant_thumbnail` 
                FROM `order` as o
                LEFT JOIN `user` as u ON(o.customer_id=u.id) WHERE {$where} ORDER BY o.created_at DESC";
    $items = db_get($sql);

    return $items;
}

function func_food_items($id = 0)
{
    
    $where = "oi.order_id={$id}";
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

function func_review($review_data = [])
{
    
    $profile = profile_get();
    $profile_id = $profile['id'];

    // บันทึกข้อมูลการรีวิว
    $data = [
        'order_id' => $review_data['id'],
        'customer_id' => $profile_id,
        'restaurant_id' => $review_data['restaurant_id'],
        'detail' => $review_data['detail'],
        'created_at' => now(),
        'updated_at' => now(),
    ];
    db_insert('review', $data);

    // ตั้งค่าให้รู้ว่ามีการ review แล้ว
    $data = [
        'review_status' => 1,
        'updated_at' => now(),
    ];
    $review_where = "id={$review_data['id']}";

    return db_update('order', $data, $review_where);
}
?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = input_get_post('action', 'index');

// เตรียมค่าที่ส่งเข้าไปที่ view
// get data form form
$order_id = input_get_post('id',0);
$review_data = input_post();

// set rules for validation data
validation_set_rules('detail', 'กรอกความคิดเห็น', 'required');

// run validation
if (validation_run()) {
    if (func_review($review_data)) {
        redirect('/customer_history_order.php');
    }
}

$active_menu = 'history_order';
$order_items = func_order_items($order_id);
$food_items = func_food_items($order_id);
$restaurant_id = 0;
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
                    <?php require_once 'customer_menu.php'; ?>
                </div>
            </div>
            <!-- end: left menu -->

            <!-- start: main content -->
            <div class="col-lg-9">
                <div class="mb-5 mt-3">
                    <h2 class="text-center">รีวิวอาหาร</h2>
                    <?php
                    if (count($order_items) <= 0) {
                        echo '<h6 class="text-center p-4">ไม่มีข้อมูลสำหรับแสดงผล...</h6>';
                    } else {
                        for ($i = 0; $i < count($order_items); ++$i) {
                            $row = $order_items[$i];
                            $restaurant_id = $row['restaurant_id'];
                            $review_link = "/customer_food_review.php?id={$row['id']}";

                            $order_status_text = 'รอยืนยันการสั่งซื้อ';
                            $order_status_bg = 'text-bg-secondary';
                            if ($row['status'] == 1) {
                                $order_status_text = 'รับออร์เดอร์เรียบร้อยแล้ว...กำลังจัดเตรียมอาหาร';
                                $order_status_bg = 'text-bg-primary';
                            } elseif ($row['status'] == 2) {
                                $order_status_text = 'กำลังนำอาหารไปส่งลูกค้า... รอสักครู่';
                                $order_status_bg = 'text-bg-warning';
                            } elseif ($row['status'] == 3) {
                                $order_status_text = 'นำส่งอาหารและได้รับชำระเงิน เรียบร้อยแล้ว';
                                $order_status_bg = 'text-bg-success';
                            }
                            ?>
                    <div class="border mb-3">
                        <div class="row g-0">
                            <div class="col-6">
                                <div class="<?php echo $order_status_bg; ?> p-2">ร้าน:
                                    <?php echo $row['restaurant_name']; ?> /
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
                                    <h6>รหัสการสั่งซื้อ: #<?php echo $row['id']; ?></h6>
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

                    <div class="text-bg-secondary">
                        <h3 class="p-2">แสดงความคิดเห็น</h3>
                    </div>
                    <div class="border p-2 mt-2">
                        <form method="post">
                            <textarea class="form-control" name="detail" rows="5" required></textarea>
                            <button type="submit" class="btn btn-primary btn-lg mt-3">รีวิวอาหาร</button>
                            <input type="hidden" name="id" value="<?php echo $order_id; ?>" />
                            <input type="hidden" name="restaurant_id" value="<?php echo $restaurant_id; ?>" />
                        </form>
                    </div>
                </div>
            </div>
            <!-- end: main content -->
        </div>
    </div>
</main>
<?php } ?>
<!-- end: main body -->

<?php require_once 'footer.php'; ?>