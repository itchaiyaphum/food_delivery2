<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';

function func_food_items($id = 0)
{
    
    $where = "oi.order_id IN (SELECT id FROM `order` WHERE id={$id})";
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

function func_item($id = 0)
{
    

    $where = "o.id={$id}";
    $sql = "SELECT o.*
                , u.firstname as `firstname` 
                , u.lastname as `lastname` 
                , u.mobile_no as `mobile_no` 
                , u.email as `email` 
                , u.address as `address` 
                , u.thumbnail as `thumbnail` 
                FROM `order` as o
                LEFT JOIN `user` as u ON(o.customer_id=u.id) WHERE {$where}";
    $item = db_row($sql);

    return $item;
}

?>

<?php
// ================================ CONTROLLER =======================
// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = input_get_post('action', 'index');

// เตรียมค่าที่ส่งเข้าไปที่ view
$id = input_get('id', 0);

$active_menu = 'ordering';
$profile = profile_get();

$item = func_item($id);
$food_items = func_food_items($id);
?>


<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<!-- start: main body -->

<!-- ================================= VIEW=INDEX ================================= -->
<?php if ($action == 'index') {?>
<main>
    <div class="container">
        <form id="mainForm" method="post">
            <div class="mb-5 mt-3">
                <h2 class="text-center">ใบเสร็จรับเงิน</h2>
                <div class="border mb-3 p-4 text-bg-light">
                    <div class="text-center fw-bold fs-5 text-decoration-underline">ข้อมูลร้านค้า</div>
                    <div><?php echo $profile['restaurant_name']; ?></div>
                    <div><?php echo $profile['mobile_no']; ?> / <?php echo $profile['email']; ?></div>
                    <div><?php echo $profile['restaurant_address']; ?></div>
                </div>

                <div class="border mb-3 p-4 text-bg-light">
                    <div class="text-center fw-bold fs-5 text-decoration-underline">ข้อมูลลูกค้า</div>
                    <div><?php echo $item['firstname'].' '.$item['lastname']; ?></div>
                    <div><?php echo $item['mobile_no']; ?> / <?php echo $item['email']; ?></div>
                    <div><?php echo $item['address']; ?></div>
                </div>

                <div>รหัสการสั่งซื้อ #<?php echo $item['id']; ?></div>
                <div>วันที่ซื้อ: <?php echo $item['created_at']; ?></div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">รายการอาหาร</th>
                        <th scope="col">ราคาต่อชิ้น</th>
                        <th scope="col">
                            จำนวน</th>
                        <th scope="col">ราคารวม</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
    $sum_total_price = 0;
    if (count($food_items) <= 0) {
        echo '<h6 class="text-center p-4 border">ไม่มีข้อมูลสำหรับแสดงผล...</h6>';
    } else {
        for ($i = 0; $i < count($food_items); ++$i) {
            $row = $food_items[$i];

            $food_amount = (int) $row['food_amount'];
            $percent = (float) $row['food_discount_percent'];
            $old_price = (float) $row['food_price'];
            $discount_value = ($old_price / 100) * $percent;
            $price_after_discount = $old_price - $discount_value;
            $total_price = $price_after_discount * $food_amount;

            $sum_total_price += $total_price;
            ?>
                    <tr>
                        <td><?php echo $row['food_name']; ?></td>
                        <td>
                            <div>
                                <span class="text-decoration-line-through text-danger"><?php echo $old_price; ?></span>
                                <span><?php echo $price_after_discount; ?></span>
                            </div>
                        </td>
                        <td><?php echo $food_amount; ?></td>
                        <td><?php echo $total_price; ?></td>
                    </tr>
                    <?php
        }
    }
    ?>
                </tbody>
            </table>
            <div class="fs-4 fw-bold text-center">ยอดรวมทั้งหมด: <?php echo number_format($sum_total_price); ?> บาท
            </div>
        </form>
    </div>
    <!-- end: main content -->

    <script type="text/javascript">
    // แสดง popup สำหรับ print
    (function() {
        window.print()
    })();
    </script>

</main>
<?php } ?>
<!-- end: main body -->

<?php require_once 'footer_printing.php'; ?>