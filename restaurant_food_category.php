<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';

function get_query_where()
{
    

    $filter_search = input_get_post('filter_search');

    $wheres = [];

    // filter: status
    $wheres[] = 'status IN(0,1)';

    // filter: search
    if ($filter_search != '') {
        $filter_search_value = $filter_search;
        $wheres[] = "(title LIKE '%{$filter_search_value}%')";
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

// ดึงข้อมูลจาก food_category ใน database ขึ้นมาทั้งหมด
function func_items()
{
    
    $where = get_query_where();
    $sql = "SELECT * FROM `food_category` WHERE {$where}";
    $items = db_get($sql);

    return $items;
}

// ดึงข้อมูลจาก food_category ใน database ขึ้นมาเฉพาะ id นั้นๆ
function func_item($id = 0)
{
    
    $sql = "SELECT * FROM food_category WHERE id={$id}";
    $item = db_row($sql);

    return $item;
}

// เพิ่ม/แก้ไข ข้อมูล food_category ใน database
function func_save($form_data = null)
{
    

    // หากค่า id ไม่เท่ากับ 0 แสดงว่าคือการอัพเดต
    if ($form_data['id'] != 0) {
        // ดึงข้อมูลเดิมมาจาก database
        $sql = "SELECT * FROM `food_category` WHERE `id`={$form_data['id']}";
        $data_db = db_row($sql);

        if (empty($data_db)) {
            return false;
        }
    }

    // หากค่า id=0 แสดงว่าคือ การเพิ่ม
    if ($form_data['id'] == 0) {
        // เตรียมข้อมูลสำหรับบันทึกลงใน database
        $data = [
            'title' => $form_data['title'],
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return db_insert('food_category', $data);
    }
    // หากไม่ใช่ ก็คือการ อัพเดต
    else {
        // เตรียมข้อมูลสำหรับบันทึกลงใน database
        $data = [
            'title' => $form_data['title'],
            'updated_at' => now(),
        ];
        $where = "`id`={$form_data['id']}";

        return db_update('food_category', $data, $where);
    }
}

// ลบข้อมูล food_category ใน database
function func_delete($id = 0)
{
    
    $where = "`id`={$id}";

    return db_delete('food_category', $where);
}
?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = input_get_post('action', 'index');
if ($action == 'edit') {
    controller_edit();
} elseif ($action == 'delete') {
    controller_delete();
}

// เพิ่ม/แก้ไขข้อมูล
function controller_edit()
{
    
    $form_data = input_post();

    // set rules for validation data
    validation_set_rules('title', 'หมวดหมู่อาหาร', 'required');

    // run validation
    if (validation_run()) {
        func_save($form_data);
        redirect('/restaurant_food_category.php');
    }
}

// ลบข้อมูล
function controller_delete()
{
    
    $id = input_get_post('id',0);
    func_delete($id);
    redirect('/restaurant_food_category.php');
}

// เตรียมค่าที่ส่งเข้าไปที่ view
$active_menu = 'food_category';
$items = func_items();

$id = input_get_post('id',0);
$item = func_item($id);
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
                <div class="page-header mt-3">
                    <div class="d-flex justify-content-between">
                        <h1>จัดการหมวดหมู่อาหาร</h1>
                    </div>
                </div>
                <form id="adminForm" method="post">
                    <div class="card mb-3" id="mainSection">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-sm-5 mb-2">
                                    <?php echo admin_filter_search_html('filter_search'); ?>
                                </div>
                                <div class="col-12 col-sm-7 mb-2">
                                    <div class="d-flex justify-content-end">
                                        <a href="/restaurant_food_category.php?action=edit&id=0"
                                            class="btn btn-primary">เพิ่ม</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                for ($i = 0; $i < count($items); ++$i) {
                                    $row = $items[$i];
                                    $edit_link = "/restaurant_food_category.php?action=edit&id={$row['id']}";
                                    $delete_link = "/restaurant_food_category.php?action=delete&id={$row['id']}";
                                    ?>
                                <div class="col-4">
                                    <div class="card mb-4">
                                        <div class="card-body text-center">
                                            <h5 class="card-title"><?php echo $row['title']; ?></h5>
                                            <a href="<?php echo $edit_link; ?>" class="btn btn-primary">แก้ไข</a>
                                            <a href="<?php echo $delete_link; ?>" class="btn btn-danger">ลบ</a>
                                        </div>
                                    </div>
                                </div>
                                <?php
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

<!-- ================================= VIEW=ADD/EDIT ================================= -->
<?php } elseif ($action == 'edit') {?>
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
                <!-- start: page header -->
                <form id="mainForm" method="post" enctype="multipart/form-data">
                    <div class="page-header mt-3">
                        <div class="d-flex justify-content-between">
                            <h1>จัดการหมวดหมู่อาหาร [เพิ่ม/แก้ไข]</h1>
                            <div>
                                <a class="btn btn-outline-secondary align-self-end mb-2"
                                    href="/restaurant_food_category.php">
                                    <i class="bi-x me-1"></i> ยกเลิก
                                </a>
                                <button class="btn btn-primary align-self-end mb-2" type="submit">
                                    <i class="bi-save me-1"></i> บันทึกข้อมูล
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- end: page header -->
                    <div class="card mb-5">
                        <div class="card-body">
                            <?php echo validation_errors(); ?>
                            <!-- field: title -->
                            <div class="row mb-4">
                                <label for="titleLabel" class="col-sm-3 col-form-label form-label">หมวดหมู่อาหาร *
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="title" id="titleLabel"
                                        value="<?php echo array_value($item, 'title'); ?>" required />
                                </div>
                            </div>
                            <!-- End field: title -->

                            <input type="hidden" name="id"
                                value="<?php echo array_value($item, 'id', 0); ?>" />
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