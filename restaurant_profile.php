<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';
function get_restaurant_type_items()
{
    

    $sql = 'SELECT * FROM `restaurant_type` WHERE `status`=1';
    $items = db_get($sql);

    return $items;
}

function save($data = [])
{
    

    // ดึงข้อมูล profile ปัจจุบันขึ้นมา
    $profile = profile_get();
    $profile_id = $profile['id'];

    // ดึงข้อมูลเดิมมาจาก database
    $sql = "SELECT * FROM `user` WHERE `id`={$profile_id}";
    $data_db = db_row($sql);

    if (empty($data_db)) {
        return false;
    }

    // หากมีการกรอกอีเมล์เข้ามาใหม่ ที่ไม่ตรงกับฐานข้อมูลปัจจุบัน แสดงว่าต้องการเปลี่ยนอีเมล์
    if ($data_db['email'] != $data['email']) {
        // ตรวจสอบว่าใน database มี email ซ้ำหรือไม่
        if (profile_check_email_exists($data['email'])) {
            validation_set_error("อีเมล์ ({$data['email']}) นี้ถูกใช้งานในระบบเราแล้ว กรุณาใช้อีเมล์อื่น!");

            return false;
        }
    }

    // default thumbnail
    $thumbnail = $data_db['thumbnail'];
    // upload thumbnail
    $thumbnail_data = upload_file('thumbnail');
    if ($thumbnail_data['status']) {
        $thumbnail = '/storage/'.$thumbnail_data['new_file_name'];
    }

    $restaurant_thumbnail = $data_db['restaurant_thumbnail'];
    // หากมีการอัพโหลดรูปภาพ (ร้านอาหาร) เข้ามาใหม่ ให้อัพโหลดรูปภาพใหม่
    if (isset($_FILES['restaurant_thumbnail'])) {
        // upload restaurant_thumbnail
        $restaurant_thumbnail_data = upload_file('restaurant_thumbnail');
        if ($restaurant_thumbnail_data['status']) {
            $restaurant_thumbnail = '/storage/'.$restaurant_thumbnail_data['new_file_name'];
        }
    }

    $hash_password = $data_db['password'];
    // หากมีการกรอกรหัสผ่านเข้ามา ให้ตั้งรหัสผ่านใหม่
    if (!empty($data['password'])) {
        $hash_password = md5($data['password']);
    }

    $data_update = [
        'firstname' => $data['firstname'],
        'lastname' => $data['lastname'],
        'mobile_no' => $data['mobile_no'],
        'address' => $data['address'],
        'thumbnail' => $thumbnail,
        'email' => $data['email'],
        'password' => $hash_password,

        'restaurant_name' => $data['restaurant_name'],
        'restaurant_type_id' => $data['restaurant_type_id'],
        'restaurant_address' => $data['restaurant_address'],
        'restaurant_thumbnail' => $restaurant_thumbnail,

        'updated_at' => now(),
    ];

    $where = "`id`={$profile_id}";

    // บันทึกข้อมูลลงใน database
    return db_update('user', $data_update, $where);
}
?>

<?php
// ================================ CONTROLLER =======================
// get data from login_form
$form_data = input_post();

$firstname = input_get_post('firstname', '');
$lastname = input_get_post('lastname', '');
$mobile_no = input_get_post('mobile_no', '');
$address = input_get_post('address', '');
$email = input_get_post('email', '');

// set rules for validation data
validation_set_rules('firstname', 'ชื่อ', 'required');
validation_set_rules('lastname', 'นามสกุล', 'required');
validation_set_rules('mobile_no', 'เบอร์โทร', 'required');
validation_set_rules('address', 'ที่อยู่', 'required');
validation_set_rules('email', 'อีเมล์', 'required');

// run validation
if (validation_run()) {
    if (save($form_data)) {
        validation_set_message('success', 'บันทึกข้อมูลเรียบร้อย');
    }
}

$profile = profile_get();
$restaurant_type_items = get_restaurant_type_items();
?>


<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<?php require_once 'nav.php'; ?>
<!-- start: main body -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card mb-5">
                <div class="card-header">
                    <h2 class="card-title h4">แก้ไขข้อมูลส่วนตัว</h2>
                </div>
                <div class="card-body">
                    <?php echo validation_errors(); ?>
                    <?php echo action_messages(); ?>
                    <form id="mainForm" method="post" enctype="multipart/form-data">
                        <!-- field: firstname -->
                        <div class="row mb-4">
                            <label for="firstnameLabel" class="col-sm-3 col-form-label form-label">ชื่อ *</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="firstname" id="firstnameLabel"
                                    value="<?php echo $profile['firstname']; ?>" required />
                            </div>
                        </div>
                        <!-- End field: firstname -->

                        <!-- field: lastname -->
                        <div class="row mb-4">
                            <label for="lastnameLabel" class="col-sm-3 col-form-label form-label">นามสกุล *</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="lastname" id="lastnameLabel"
                                    value="<?php echo $profile['lastname']; ?>" required />
                            </div>
                        </div>
                        <!-- End field: lastname -->

                        <!-- end field: mobile_no -->
                        <div class="row mb-4">
                            <label for="phoneLabel" class="col-sm-3 col-form-label form-label">เบอร์โทรศัพท์
                                *</label>

                            <div class="col-sm-9">
                                <input type="text" class="js-input-mask form-control" name="mobile_no" id="phoneLabel"
                                    value="<?php echo $profile['mobile_no']; ?>" required />
                            </div>
                        </div>
                        <!-- end field: mobile_no -->

                        <!-- field: address -->
                        <div class="row mb-4">
                            <label for="addressLine1Label" class="col-sm-3 col-form-label form-label">ที่อยู่
                                *</label>

                            <div class="col-sm-9">
                                <textarea row="4" class="form-control" name="address" id="addressLine1Label"
                                    required><?php echo $profile['address']; ?></textarea>
                            </div>
                        </div>
                        <!-- end field: address -->

                        <!-- field: thumbnail -->
                        <div class="row mb-4">
                            <label for="thumbnailLabel" class="col-sm-3 col-form-label form-label">รูปภาพประจำตัว
                                </label>
                            <div class="col-sm-9">
                                <img src="<?php echo $profile['thumbnail']; ?>" class="mb-3 w-25 border">
                                <input type="file" class="form-control" name="thumbnail" />
                            </div>
                        </div>
                        <!-- end field: thumbnail -->

                        <!-- field: email -->
                        <div class="row mb-4">
                            <label for="emailLabel" class="col-sm-3 col-form-label form-label">อีเมล์ *</label>

                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="email" id="emailLabel"
                                    value="<?php echo $profile['email']; ?>" required />
                            </div>
                        </div>
                        <!-- end field: email -->

                        <!-- field: password -->
                        <div class="row mb-4">
                            <label for="passwordLabel" class="col-sm-3 col-form-label form-label">รหัสผ่าน</label>

                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password" id="passwordLabel"
                                    value="" />
                            </div>
                        </div>
                        <!-- end field: password -->

                        <div class="text-bg-secondary mt-5 mb-5">
                            <h4 class="p-2">ข้อมูลร้านอาหาร</h4>
                        </div>
                        <!-- field: restaurant name -->
                        <div class="row mb-4">
                            <label for="restaurantNameLabel" class="col-sm-3 col-form-label form-label">ชื่อร้านอาหาร *</label>

                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="restaurant_name" id="restaurantNameLabel" value="<?php echo $profile['restaurant_name']; ?>"
                                    required />
                            </div>
                        </div>
                        <!-- end field: restaurant name -->

                        <!-- field: restaurant type -->
                        <div class="row mb-4">
                            <label for="restaurantTypeIdLabel" class="col-sm-3 col-form-label form-label">ประเภทร้านอาหาร *</label>

                            <div class="col-sm-9">
                                <select class="form-select" name="restaurant_type_id" id="restaurantTypeIdLabel" required>
                                    <option value="0">-- กรุณาเลือกประเภทร้านอาหาร --</option>
                                    <?php
                                    for ($i = 0; $i < count($restaurant_type_items); ++$i) {
                                        $row = $restaurant_type_items[$i];
                                        ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $profile['restaurant_type_id']) ? 'selected' : ''; ?>><?php echo $row['title']; ?></option>
                                    <?php
                                    }?>
                                </select>
                            </div>
                        </div>
                        <!-- end field: restaurant type -->

                        <!-- field: restaurant address -->
                        <div class="row mb-4">
                            <label for="restaurantAddressLabel" class="col-sm-3 col-form-label form-label">ที่อยู่ร้านอาหาร
                                *</label>

                            <div class="col-sm-9">
                                <textarea row="4" class="form-control" name="restaurant_address" id="restaurantAddressLabel" required><?php echo $profile['restaurant_address']; ?></textarea>
                            </div>
                        </div>
                        <!-- end field: restaurantaddress -->

                        <!-- field: restaurant thumbnail -->
                        <div class="row mb-4">
                            <label for="restaurantThumbnailLabel"
                                class="col-sm-3 col-form-label form-label">รูปภาพร้านอาหาร</label>
                            <div class="col-sm-9">
                                <img src="<?php echo $profile['restaurant_thumbnail']; ?>" class="mb-3 w-25">
                                <input type="file" class="form-control" name="restaurant_thumbnail" />
                            </div>
                        </div>
                        <!-- end field: restaurant thumbnail -->

                        <!-- submit button -->
                        <div class="row mb-4">
                            <label for="levelLabel" class="col-sm-3 col-form-label form-label">.</label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    บันทึกข้อมูล
                                </button>
                            </div>
                        </div>

                    </form>
                </div><!-- end: card-body -->
            </div><!-- end: card -->
        </div>
    </div>
</div>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>