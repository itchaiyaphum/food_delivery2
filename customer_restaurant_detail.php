<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';

// ดึงข้อมูลจาก restaurant ใน database ขึ้นมาเฉพาะ id นั้นๆ
function func_item($id = 0)
{
    $where = "user.id={$id}";
    $sql = "SELECT user.*, rt.title as `restaurant_type_name` FROM `user`
                LEFT JOIN `restaurant_type` as rt ON(user.id=rt.id) WHERE {$where}";
    $item = db_row($sql);

    return $item;
}

function func_reviews($restaurant_id = 0)
{
    $where = "review.restaurant_id={$restaurant_id}";
    $sql = "SELECT review.*
                    , user.firstname as `customer_firstname`
                    ,user.lastname as `customer_lastname` 
                FROM `review`
                LEFT JOIN `user` ON(review.customer_id=user.id) WHERE {$where}";
    $items = db_get($sql);

    return $items;
}

function func_food_category_items($restaurant_id = 0)
{
    $sql = "SELECT * FROM `food_category` WHERE `restaurant_id`={$restaurant_id} AND `status`=1";
    $items = db_get($sql);

    return $items;
}

function func_food_menu_items($restaurant_id = 0, $food_category_id = 0)
{
    $where_food_category = '';
    // หากมีการเลือก food_category ด้วย
    if ($food_category_id != 0) {
        $where_food_category = "AND fm.food_category_id={$food_category_id}";
    }

    $sql = "SELECT fm.*, fc.title as `food_category_name` FROM `food_menu` as fm 
                LEFT JOIN `food_category` as fc ON(fm.food_category_id=fc.id) WHERE fm.restaurant_id={$restaurant_id} {$where_food_category}";
    $items = db_get($sql);

    return $items;
}

function func_add_to_cart($restaurant_id = 0, $food_id = 0)
{
    
    // ลูกค้าที่สั่งซื้ออาหาร
    $profile = profile_get();
    $profile_id = $profile['id'];

    // เตรียมข้อมูลก่อนจะบันทึกลง database
    $data = [
        'customer_id' => $profile_id,
        'restaurant_id' => $restaurant_id,
        'food_id' => $food_id,
        'food_amount' => 1,
    ];

    // บันทึกข้อมูลลงใน database
    return db_insert('cart', $data);
}
?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = input_get_post('action', 'index');
if ($action == 'add_to_cart') {
    $id = input_get_post('id', 0);
    $cate_id = input_get_post('cate_id', 0);
    $food_id = input_get_post('food_id', 0);

    func_add_to_cart($id, $food_id);
    redirect("/customer_restaurant_detail.php?id={$id}&cate_id={$cate_id}&msg_status=addtocart_ok");
}

// เตรียมค่าที่ส่งเข้าไปที่ view
$active_menu = 'restaurant';

$id = input_get_post('id',0);
$cate_id = input_get_post('cate_id', 0);
$item = func_item($id);
$food_categories = func_food_category_items($id);
$food_menus = func_food_menu_items($id, $cate_id);
$reviews = func_reviews($id);
?>


<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<?php require_once 'nav.php'; ?>
<!-- start: main body -->

<!-- ================================= VIEW=INDEX ================================= -->
<?php if ($action == 'index') {?>
<main>
    <div class="container">
        <div>
            <div class="border"><img src="<?php echo array_value($item, 'restaurant_thumbnail'); ?>" width="100%"></div>
            <h2 class="text-center mt-5"><?php echo array_value($item, 'restaurant_name'); ?></h2>
            <h5 class="text-center mb-3">(<?php echo array_value($item, 'restaurant_type_name'); ?>)</h5>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="card mb-3 mt-3 d-none d-sm-flex">
                <?php require_once 'customer_menu.php'; ?>
                </div>

                <!-- start: food categories -->
                <div class="card mb-3 mt-3 d-none d-sm-flex">
                    <div class="card-header">
                        หมวดหมู่อาหาร
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($cate_id == 0) ? 'active' : ''; ?>"
                                href="/customer_restaurant_detail.php?id=<?php echo $id; ?>">
                                <i class="bi-box"></i> แสดงอาหารทุกหมวดหมู่
                            </a>
                        </li>
                        <?php
                        if (count($food_categories) <= 0) {
                            echo '<li class="nav-item p-4">ไม่มีหมวดหมู่อาหาร</li>';
                        } else {
                            for ($i = 0; $i < count($food_categories); ++$i) {
                                $item = $food_categories[$i];
                                $category_link = "/customer_restaurant_detail.php?id={$id}&cate_id={$item['id']}";
                                ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($item['id'] == $cate_id) ? 'active' : ''; ?>"
                                href="<?php echo $category_link; ?>">
                                <i class="bi-box"></i> <?php echo array_value($item, 'title'); ?>
                            </a>
                        </li>
                        <?php
                            }
                        }
    ?>
                    </ul>
                </div>
                <!-- end: food categories -->
            </div>
            <!-- end: col-lg-3 -->


            <!-- start: main content -->
            <div class="col-lg-9">
                <!-- menus -->
                <div class="row">
                    <?php
            if (count($food_menus) <= 0) {
                echo '<h6 class="text-center p-4 border">ไม่มีข้อมูลสำหรับแสดงผล...</h6>';
            } else {
                for ($i = 0; $i < count($food_menus); ++$i) {
                    $row = $food_menus[$i];
                    $add_to_cart_link = "/customer_restaurant_detail.php?action=add_to_cart&id={$id}&cate_id={$cate_id}&food_id={$row['id']}";

                    $percent = (float) $row['discount_percent'];
                    $old_price = (float) $row['price'];
                    $discount_value = ($old_price / 100) * $percent;
                    $price_after_discount = $old_price - $discount_value;

                    ?>
                    <div class="col-4">
                        <div class="card mb-4">
                            <img src="<?php echo $row['thumbnail']; ?>" width="100%" class="card-img-top">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo $row['title']; ?></h5>
                                <p class="card-text">
                                <div class="fw-bold fs-3">( <?php echo $old_price; ?> บาท )</div>
                                <div class="text-danger text-decoration-line-through">(
                                    <?php echo $price_after_discount; ?> บาท )</div>
                                </p>
                                <a href="<?php echo $add_to_cart_link; ?>" class="btn btn-primary">เพิ่มลงตะกร้า</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
    ?>
                </div>
                <!-- end: menus -->

                <!-- start: reviews -->
                <div>
                    <div class="text-bg-secondary">
                        <h3 class="p-2">รีวิวอาหาร</h3>
                    </div>
                    <div>
                        <?php
                            if (count($reviews) <= 0) {
                                echo '<h6 class="text-center p-4 border">ไม่มีข้อมูลรีวิวอาหาร...</h6>';
                            } else {
                                for ($i = 0; $i < count($reviews); ++$i) {
                                    $row = $reviews[$i];
                                    ?>
                        <div class="border p-2 mt-2">
                            <h4><?php echo $row['customer_firstname'].' '.$row['customer_lastname']; ?>
                            </h4>
                            <h6><?php echo $row['created_at']; ?></h6>
                            <div class="mt-2"><?php echo $row['detail']; ?></div>
                        </div>
                        <?php
                                }
                            }
    ?>
                    </div>
                </div>
                <!-- end: reviews -->
            </div>
            <!-- end: main content -->
        </div>
        <!-- end: row -->
    </div>
    <!-- end: container -->
</main>

<?php
// แสดงผลเมื่อมีการกดปุ่ม add_to_cart
if (input_get('msg_status') == 'addtocart_ok') {
    ?>
<script type="text/javascript">
(function() {
    alert('เพิ่มรายการอาหารลงในตระกร้าเรียบร้อยค่ะ.');
})();
</script>
<?php
}
    ?>

<?php }?>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>