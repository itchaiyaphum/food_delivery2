<?php
// ================================ BUSINESS LOGIC ===================
require_once 'functions.php';
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

    // จัดเตรียมข้อมูล เพื่อบันทึกลงใน database
    $hash_password = md5($register_data['password']);
    $data = [
        'firstname' => $register_data['firstname'],
        'lastname' => $register_data['lastname'],
        'user_type' => 'customer',
        'mobile_no' => $register_data['mobile_no'],
        'address' => $register_data['address'],
        'thumbnail' => $thumbnail,
        'email' => $register_data['email'],
        'password' => $hash_password,

        'restaurant_name' => '',
        'restaurant_type_id' => 0,
        'restaurant_address' => '',
        'restaurant_thumbnail' => '',

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

// set rules for validation data
validation_set_rules('firstname', 'ชื่อ', 'required');
validation_set_rules('lastname', 'นามสกุล', 'required');
validation_set_rules('mobile_no', 'เบอร์โทร', 'required');
validation_set_rules('address', 'ที่อยู่', 'required');

validation_set_rules('email', 'อีเมล์', 'required');
validation_set_rules('password', 'รหัสผ่าน', 'required');

// run validation
if (validation_run()) {
    if (register($register_data)) {
        redirect('/customer_register_complete.php');
    }
}
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
                    <h2 class="card-title h4">ลงทะเบียนสำหรับลูกค้า (Customer)</h2>
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