<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';
function get_restaurant_type_items()
{
    

    $sql = 'SELECT * FROM `restaurant_type` WHERE `status`=1';
    $items = db_get($sql);

    return $items;
}

function register($register_data = [])
{
    

    // หากมี อีเมล์ อยู่ในระบบแล้ว
    if (profile_check_email_exists($register_data['email'])) {
        validation_set_error("อีเมล์ ({$register_data['email']}) นี้ถูกใช้ในการลงทะเบียนแล้ว กรุณาใช้อีเมล์อื่น!");

        return false;
    }

    // default thumbnail
    $thumbnail = '/storage/no-thumbnail.png';

    // upload thumbnail
    $thumbnail_data = upload_file('thumbnail');
    if ($thumbnail_data['status']) {
        $thumbnail = '/storage/'.$thumbnail_data['new_file_name'];
    }

    // restaurant_thumbnail
    $restaurant_thumbnail = '/storage/no-thumbnail.png';

    // upload restaurant_thumbnail
    $restaurant_thumbnail_data = upload_file('restaurant_thumbnail');
    if ($restaurant_thumbnail_data['status']) {
        $restaurant_thumbnail = '/storage/'.$restaurant_thumbnail_data['new_file_name'];
    }

    // จัดเตรียมข้อมูล เพื่อบันทึกลงใน database
    $hash_password = md5($register_data['password']);
    $data = [
        'firstname' => $register_data['firstname'],
        'lastname' => $register_data['lastname'],
        'user_type' => 'restaurant',
        'mobile_no' => $register_data['mobile_no'],
        'address' => $register_data['address'],
        'thumbnail' => $thumbnail,
        'email' => $register_data['email'],
        'password' => $hash_password,

        'restaurant_name' => $register_data['restaurant_name'],
        'restaurant_type_id' => $register_data['restaurant_type_id'],
        'restaurant_address' => $register_data['restaurant_address'],
        'restaurant_thumbnail' => $restaurant_thumbnail,

        'status' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    // บันทึกข้อมูลลงใน database
    return db_insert('user', $data);
}
?>

<?php
// ================================ CONTROLLER =======================
// get data from login_form
$register_data = input_post();

$firstname = input_get_post('firstname', '');
$lastname = input_get_post('lastname', '');
$mobile_no = input_get_post('mobile_no', '');
$address = input_get_post('address', '');
$email = input_get_post('email', '');
$password = input_get_post('password', '');

$restaurant_name = input_get_post('restaurant_name', '');
$restaurant_type_id = input_get_post('restaurant_type_id', '');
$restaurant_address = input_get_post('restaurant_address', '');

// set rules for validation data
validation_set_rules('firstname', 'ชื่อ', 'required');
validation_set_rules('lastname', 'นามสกุล', 'required');
validation_set_rules('mobile_no', 'เบอร์โทร', 'required');
validation_set_rules('address', 'ที่อยู่', 'required');
validation_set_rules('email', 'อีเมล์', 'required');
validation_set_rules('password', 'รหัสผ่าน', 'required');

validation_set_rules('restaurant_name', 'ชื่อร้านอาหาร', 'required');
validation_set_rules('restaurant_type_id', 'ประเภทร้านอาหาร', 'required');
validation_set_rules('restaurant_address', 'ที่อยู่ร้านอาหาร', 'required');

// run validation
if (validation_run()) {
    if (register($register_data)) {
        redirect('/restaurant_register_complete.php');
    }
}

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
                    <h2 class="card-title h4">ลงทะเบียนสำหรับผู้ส่งอาหาร (Rider)</h2>
                </div>
                <div class="card-body">
                    <?php echo validation_errors(); ?>
                    <form id="mainForm" method="post" enctype="multipart/form-data">
                        <!-- field: firstname -->
                        <div class="row mb-4">
                            <label for="firstnameLabel" class="col-sm-3 col-form-label form-label">ชื่อ *</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="firstname" id="firstnameLabel"
                                    value="<?php echo $firstname; ?>" required />
                            </div>
                        </div>
                        <!-- End field: firstname -->

                        <!-- field: lastname -->
                        <div class="row mb-4">
                            <label for="lastnameLabel" class="col-sm-3 col-form-label form-label">นามสกุล *</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="lastname" id="lastnameLabel"
                                    value="<?php echo $lastname; ?>" required />
                            </div>
                        </div>
                        <!-- End field: lastname -->

                        <!-- end field: mobile_no -->
                        <div class="row mb-4">
                            <label for="phoneLabel" class="col-sm-3 col-form-label form-label">เบอร์โทรศัพท์
                                *</label>

                            <div class="col-sm-9">
                                <input type="text" class="js-input-mask form-control" name="mobile_no" id="phoneLabel"
                                    value="<?php echo $mobile_no; ?>" required />
                            </div>
                        </div>
                        <!-- end field: mobile_no -->

                        <!-- field: address -->
                        <div class="row mb-4">
                            <label for="addressLine1Label" class="col-sm-3 col-form-label form-label">ที่อยู่
                                *</label>

                            <div class="col-sm-9">
                                <textarea row="4" class="form-control" name="address" id="addressLine1Label"
                                    required><?php echo $address; ?></textarea>
                            </div>
                        </div>
                        <!-- end field: address -->

                        <!-- field: thumbnail -->
                        <div class="row mb-4">
                            <label for="thumbnailLabel" class="col-sm-3 col-form-label form-label">รูปภาพประจำตัว
                                </label>
                            <div class="col-sm-9">
                                <img src="/storage/no-thumbnail.png" class="mb-3 w-25 border">
                                <input type="file" class="form-control" name="thumbnail" />
                            </div>
                        </div>
                        <!-- end field: thumbnail -->

                        <!-- field: email -->
                        <div class="row mb-4">
                            <label for="emailLabel" class="col-sm-3 col-form-label form-label">อีเมล์ *</label>

                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="email" id="emailLabel"
                                    value="<?php echo $email; ?>" required />
                            </div>
                        </div>
                        <!-- end field: email -->

                        <!-- field: password -->
                        <div class="row mb-4">
                            <label for="passwordLabel" class="col-sm-3 col-form-label form-label">รหัสผ่าน</label>

                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password" id="passwordLabel"
                                    value="<?php echo $password; ?>" />
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
                                <input type="text" class="form-control" name="restaurant_name" id="restaurantNameLabel" value="<?php echo $restaurant_name; ?>"
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
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['title']; ?></option>
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
                                <textarea row="4" class="form-control" name="restaurant_address" id="restaurantAddressLabel" required><?php echo $restaurant_address; ?></textarea>
                            </div>
                        </div>
                        <!-- end field: restaurantaddress -->

                        <!-- field: restaurant thumbnail -->
                        <div class="row mb-4">
                            <label for="restaurantThumbnailLabel"
                                class="col-sm-3 col-form-label form-label">รูปภาพร้านอาหาร</label>
                            <div class="col-sm-9">
                                <img src="/storage/no-thumbnail.png" class="mb-3 w-25">
                                <input type="file" class="form-control" name="restaurant_thumbnail" />
                            </div>
                        </div>
                        <!-- end field: restaurant thumbnail -->

                        <!-- submit button -->
                        <div class="row mb-4">
                            <label for="levelLabel" class="col-sm-3 col-form-label form-label">.</label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    ลงทะเบียน
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