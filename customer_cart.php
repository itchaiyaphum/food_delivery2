<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';

function func_items()
{
    $profile = profile_get();
    $profile_id = $profile['id'];

    $where = "cart.customer_id={$profile_id} AND fm.status=1";
    $sql = "SELECT cart.*
                , fm.title as `food_name` 
                , fm.price as `food_price` 
                , fm.discount_percent as `food_discount_percent` 
                , fm.thumbnail as `food_thumbnail` 
                FROM `cart`
                LEFT JOIN `food_menu` as fm ON(cart.food_id=fm.id) WHERE {$where}";
    $items = db_get($sql);

    return $items;
}

function func_is_multiple_restaurant_order()
{
    $profile = profile_get();
    $profile_id = $profile['id'];

    $where = "customer_id={$profile_id}";
    $sql = "SELECT count(restaurant_id) as restaurant_amount FROM `cart`  WHERE {$where} GROUP BY restaurant_id";
    $items = db_get($sql);

    if (count($items) >= 2) {
        return true;
    }

    return false;
}

/*
    status: 0 = รอการยืนยันสั่งซื้อ (customer)
    status: 1 = ยืนยันสั่งซื้ออาหารแล้ว (staff)
    status: 2 = รับออร์เดอร์ไปส่งอาหาร (rider)
    status: 3 = ส่งอาหารและได้รับเงินเรียบร้อย (rider)
    */
function func_order()
{
    $profile = profile_get();
    $profile_id = $profile['id'];

    // ดึงค่ารายการอาหารที่ใส่ตระกร้าไว้มาจาก database
    $sql = "SELECT cart.* 
                ,fm.price as price
                ,fm.discount_percent as discount_percent
                FROM `cart` 
                LEFT JOIN food_menu as fm ON (cart.food_id=fm.id)
                WHERE cart.customer_id={$profile_id}";
    $cart_items = db_get($sql);
    if (empty($cart_items)) {
        return false;
    }

    $restaurant_id = 0;
    $total_summary_price = 0;
    for ($i = 0; $i < count($cart_items); ++$i) {
        $item = $cart_items[$i];
        $restaurant_id = $item['restaurant_id'];

        $percent = (float) $item['discount_percent'];
        $old_price = (float) $item['price'];
        $discount_value = ($old_price / 100) * $percent;
        $price_after_discount = $old_price - $discount_value;

        $total_summary_price += $price_after_discount;
    }

    // เตรียมข้อมูลก่อนจะบันทึกลง database
    $data = [
        'customer_id' => $profile_id,
        'restaurant_id' => $restaurant_id,
        'rider_id' => 0,
        'total_price' => $total_summary_price,
        'status' => 0,
        'review_status' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    // บันทึกข้อมูลลงใน database
    if (!db_insert('order', $data)) {
        return false;
    }
    $order_id = db_get_insert_id();

    // insert food items
    foreach ($cart_items as $item) {
        $percent = (float) $item['discount_percent'];
        $old_price = (float) $item['price'];
        $discount_value = ($old_price / 100) * $percent;
        $price_after_discount = $old_price - $discount_value;
        $food_amount = (int) $item['food_amount'];
        $total_price = $price_after_discount * $food_amount;

        $data = [
            'order_id' => $order_id,
            'food_id' => $item['food_id'],
            'food_price' => $old_price,
            'food_discount_price' => $price_after_discount,
            'food_amount' => $food_amount,
            'food_total' => $total_price,
        ];
        db_insert('order_item', $data);
    }

    // เมื่อสั่งซื้อเรียบร้อย ให้ทำการเครียร์ตะกร้าสินค้าให้ว่าง
    db_delete('cart', "customer_id={$profile_id}");

    return true;
}
function func_delete($id = 0)
{
    

    return db_delete('cart', "id={$id}");
}
?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = input_get_post('action', 'index');
if ($action == 'order') {
    if (func_order()) {
        redirect('/customer_cart.php?action=order_complete');

        return true;
    }
    redirect('/customer_cart.php');
} elseif ($action == 'delete') {
    $food_id = input_get_post('id', 0);
    func_delete($food_id);
    redirect('/customer_cart.php');
}

// เตรียมค่าที่ส่งเข้าไปที่ view
$active_menu = 'cart';
$items = func_items();
$multiple_restaurant = func_is_multiple_restaurant_order();
$profile = profile_get();
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
            <div class="col-lg-9 mb-3">
                <form id="mainForm" method="post" action="/customer_cart.php?action=order">
                    <div class="mb-5 mt-3">
                        <h2 class="text-center">ตระกร้าสินค้า</h2>
                        <div class="border mb-3 p-4 text-bg-light">
                            <div class="text-center fw-bold fs-5 text-decoration-underline">ข้อมูลการจัดส่งสินค้า</div>
                            <div><?php echo $profile['firstname'].' '.$profile['lastname']; ?>
                            </div>
                            <div><?php echo $profile['mobile_no']; ?> /
                                <?php echo $profile['email']; ?></div>
                            <div><?php echo $profile['address']; ?></div>
                        </div>
                    </div>

                    <div class="row d-none d-md-flex bg-light p-2">
                        <div class="col-4"><strong>รายการอาหาร</strong></div>
                        <div class="col-2"><strong>ราคาต่อชิ้น</strong></div>
                        <div class="col-2"><strong>จำนวน</strong></div>
                        <div class="col-2"><strong>ราคารวม</strong></div>
                        <div class="col-2"><strong>-</strong></div>
                    </div>

                    <?php
                    $sum_total_price = 0;
    $enable_order_button = false;
    if (count($items) <= 0) {
        echo '<h6 class="text-center p-4 border">ไม่มีข้อมูลสำหรับแสดงผล...</h6>';
    } else {
        for ($i = 0; $i < count($items); ++$i) {
            $item = $items[$i];

            $enable_order_button = true;

            $delete_link = "/customer_cart.php?action=delete&id={$item['id']}";

            $food_amount = (int) $item['food_amount'];
            $percent = (float) $item['food_discount_percent'];
            $old_price = (float) $item['food_price'];
            $discount_value = ($old_price / 100) * $percent;
            $price_after_discount = $old_price - $discount_value;

            $total_price = $price_after_discount * $food_amount;

            $sum_total_price += $total_price;
            ?>
                    <div class="row gy-2 p-2">
                        <div class="col-12 col-md-4">
                            <div class="d-flex">
                                <span class="d-md-none w-50">รายการอาหาร: </span>
                                <div><?php echo $item['food_name']; ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="d-flex">
                                <span class="d-md-none w-50">ราคาต่อชิ้น:</span>
                                <div>
                                    <span
                                        class="text-decoration-line-through text-danger"><?php echo $old_price; ?></span>
                                    <span><?php echo $price_after_discount; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="d-flex">
                                <span class="d-md-none w-50">จำนวน:</span>
                                <?php echo $food_amount; ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="d-flex">
                                <span class="d-md-none w-50">ราคารวม: </span>
                                <?php echo $total_price; ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="d-flex">
                                <span class="d-md-none w-50"></span>
                                <a class="btn btn-sm btn-danger" href="<?php echo $delete_link; ?>"><i
                                        class="bi-trash"></i></a>

                            </div>
                        </div>
                    </div>
                    <hr />
                    <?php
        }
    }
    ?>
                    <div class="fs-4 fw-bold text-center mt-5">ยอดรวมทั้งหมด:
                        <?php echo number_format($sum_total_price); ?> บาท</div>
                    <div class="fs-4 fw-bold text-center mt-5">
                        <?php
                        if ($multiple_restaurant) {
                            ?>
                        <div class="alert alert-danger">คุณมีการสั่งซื้ออาหารจากหลายร้านอาหาร, กรุณาสั่งซื้ออาหารจาก 1
                            ร้านอาหาร/1 การสั่งซื้อเท่านั้น</div>
                        <?php
                        } else {
                            ?>
                        <button type="submit" class="btn btn-primary btn-lg"
                            <?php echo ($enable_order_button) ? '' : 'disabled'; ?>>
                            สั่งซื้ออาหารเดี๋ยวนี้
                        </button>
                        <?php
                        }
    ?>
                    </div>
                </form>
            </div>
            <!-- end: main content -->
        </div>
    </div>
</main>
<?php } elseif ($action == 'order_complete') {?>
<div class="container">
    <div class="row text-center mt-2">
        <div class="col">
            <div class="p-5">
                <i class="bi-check-circle" style="font-size: 6rem; color: #0d6efd"></i>
            </div>
            <h2 class="text-primary">สั่งซื้อสินค้าเรียบร้อยแล้ว!</h2>
            <p>
                กรุณารอสักครู่ ร้านอาหารจะดำเนินการตามคำสั่งซื้อ และผู้ส่งอาหารจะนำอาหารมาส่งให้ท่าน ภายใน 30-45 นาที.
            </p>
            <div class="text-center m-5">
                <a href="/customer_history_order.php" class="btn btn-primary btn-lg"><i class="bi-person-circle"></i>
                    ดูรายการอาหารที่สั่งซื้อไว้</a>
                <a href="/" class="btn btn-link link-secondary"><i class="bi-house"></i> กลับหน้าหลัก</a>
            </div>
        </div>
    </div>
</div>
<?php }?>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>