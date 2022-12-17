<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';

function get_query_where()
{
    $filter_search = input_get_post('filter_search');

    $wheres = [];

    // filter: status
    $wheres[] = 'user.status=1';
    $wheres[] = "user.user_type='restaurant'";

    // filter: search
    if ($filter_search != '') {
        $filter_search_value = $filter_search;
        $wheres[] = "(user.restaurant_name LIKE '%{$filter_search_value}%')";
    }

    // render query
    $result = '';
    if (count($wheres) >= 2) {
        $result = implode(' AND ', $wheres);
    } else {
        $result = implode(' ', $wheres);
    }

    return $result;
}

// ดึงข้อมูลจาก restaurant_type ใน database ขึ้นมาทั้งหมด
function func_items()
{
    $where = get_query_where();
    $sql = "SELECT user.*, rt.title as `restaurant_type_name` FROM `user`
                LEFT JOIN `restaurant_type` as rt ON(user.id=rt.id) WHERE {$where}";
    $items = db_get($sql);

    return $items;
}
?>

<?php
// ================================ CONTROLLER =======================
$action = input_get_post('action', 'index');

// เตรียมค่าที่ส่งเข้าไปที่ view
$active_menu = 'restaurant';
$items = func_items();
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
                <div class="page-header mt-3">
                    <div class="d-flex justify-content-between">
                        <h1>แสดงร้านอาหารทั้งหมด</h1>
                    </div>
                </div>
                <form id="mainForm" method="post">
                    <div class="card mb-3" id="mainSection">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-sm-5 mb-2">
                                    <?php echo admin_filter_search_html('filter_search'); ?>
                                </div>
                                <div class="col-12 col-sm-7 mb-2">
                                    <div class="d-flex justify-content-end">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                if (count($items) <= 0) {
                                    echo '<dov class="p-4 text-center">ไม่มีข้อมูลสำหรับแสดงผล</div>';
                                } else {
                                    for ($i = 0; $i < count($items); ++$i) {
                                        $row = $items[$i];
                                        $detail_link = "/customer_restaurant_detail.php?id={$row['id']}";
                                        ?>
                                <div class="col-4">
                                    <div class="card mb-4">
                                        <a href="<?php echo $detail_link; ?>"><img src="<?php echo $row['restaurant_thumbnail']; ?>" width="100%" class="card-img-top"></a>
                                        <div class="card-body text-center">
                                            <h5 class="card-title"><a href="<?php echo $detail_link; ?>"><?php echo $row['restaurant_name']; ?></a></h5>
                                            <h5 class="card-title"><?php echo $row['restaurant_type_name']; ?></h5>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
    ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- end: main content -->
        </div>
    </div>
</main>
<?php }?>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>