<?php

// init environment
session_start();
date_default_timezone_set('Asia/Bangkok');
define('DS', DIRECTORY_SEPARATOR);
define('PATH', realpath('./').DS);

error_reporting(-1);
ini_set('display_errors', 1);

// =========================== COMMON =====================================
function base_url($url = '')
{
    
    $base_url = 'http://dev.food-delivery2.itchaiyaphum.com';
    if ($url == '/') {
        $url = '';
    }

    return $base_url.$url;
}

function array_value($array_data = null, $key = '', $default = '')
{
    return (!empty($array_data) && isset($array_data[$key])) ? $array_data[$key] : $default;
}

function active_menu($data = null, $index = null)
{
    return ($data == $index) ? 'active' : '';
}

function redirect($url = '/')
{
    header('Location: '.base_url($url));
}

function get_datetime($date = '0000-00-00 00:00:00', $format = 'Y-m-d H:i:s')
{
    return date_format(date_create($date), $format);
}

function now()
{
    return date('Y-m-d H:i:s');
}

function validation_errors()
{
    global $validation_error_status,$validation_error_messages;

    if ($validation_error_status === true) {
        echo '<div class="alert alert-danger">'.$validation_error_messages.'</div>';
    }
}

function action_messages()
{
    global $validation_action_status,$validation_action_messages;

    if ($validation_action_status === 'success') {
        echo '<div class="alert alert-success">'.$validation_action_messages.'</div>';
    }
}

function html_escape($var, $double_encode = true)
{
    if (empty($var)) {
        return $var;
    }

    if (is_array($var)) {
        foreach (array_keys($var) as $key) {
            $var[$key] = html_escape($var[$key], $double_encode);
        }

        return $var;
    }

    return htmlspecialchars($var, ENT_QUOTES, 'UTF-8', $double_encode);
}

function set_value($field, $default = '', $html_escape = true)
{
    

    $value = input_post($field, false);

    isset($value) or $value = $default;

    return ($html_escape) ? html_escape($value) : $value;
}

function admin_filter_search_html($name = 'filter_search')
{
    return '
    <div class="input-group">
        <input type="text" name="'.$name.'" id="search" value="'.set_value($name).'" class="form-control" onchange="this.form.submit();" />
        <button class="input-group-text" onclick="this.form.submit();">
            <i class="bi-search"></i>
        </button>
        <button class="input-group-text" onclick="document.getElementById(\'search\').value=\'\'; this.form.submit();">
            <i class="bi-backspace"></i>
        </button>
    </div>
    ';
}

// ============================ INPUT =====================================
// get input type get
function input_get($index = null, $default = null)
{
    // หากไม่ส่ง index มา แสดงว่าต้องการดึงค่าทั้งหมดใน $_GET
    if (empty($index)) {
        return $_GET;
    }

    // หาก index ไม่มี ให้ return ค่า default กลับไป
    if (!isset($_GET[$index])) {
        return $default;
    }

    // หากมีการส่ง index เข้ามา และมีค่าใน index
    return $_GET[$index];
}

// get input type post
function input_post($index = null, $default = null)
{
    // หากไม่ส่ง index มา แสดงว่าต้องการดึงค่าทั้งหมดใน $_POST
    if (empty($index)) {
        return $_POST;
    }

    // หาก index ไม่มี ให้ return ค่า default กลับไป
    if (!isset($_POST[$index])) {
        return $default;
    }

    // หากมีการส่ง index เข้ามา และมีค่าใน index
    return $_POST[$index];
}

// get input type get,post
function input_get_post($index = null, $default = null)
{
    $result_get = input_get($index, $default);
    $result_post = input_post($index, $default);

    if (!empty($result_get)) {
        return $result_get;
    } elseif (!empty($result_post)) {
        return $result_post;
    }

    return $default;
}

// ============================== SESSION ===============================
function session_get($key = null)
{
    if (isset($key)) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    return [];
}

function session_set($data, $value = '')
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            $_SESSION[$key] = $val;
        }

        return;
    }

    $_SESSION[$data] = $value;
}

function session_delete($key = null)
{
    if (is_array($key)) {
        foreach ($key as $k) {
            unset($_SESSION[$k]);
        }

        return;
    }
    if (empty($key)) {
        foreach ($_SESSION as $k => $v) {
            unset($_SESSION[$k]);
        }

        return;
    }
    unset($_SESSION[$key]);
}

// ================================ UPLOAD ===================================

function upload_get_ext($file_name = '')
{
    $file_name_parts = explode('.', $file_name);

    return end($file_name_parts);
}

function upload_file($field = 'userfile')
{
    // upload status
    $status = true;
    $error = '';
    $upload_path = 'storage';

    // Is $_FILES[$field] set? If not, no reason to continue.
    if (isset($_FILES[$field])) {
        $_file = $_FILES[$field];
    } else {
        return false;
    }

    if (!isset($_file)) {
        $error = 'ไม่มีเลือกไฟล์สำหรับการอัพโหลด';
        $status = false;
    }

    $file_temp = $_file['tmp_name'];
    $file_size = $_file['size'];
    $file_name = $_file['name'];
    $file_ext = upload_get_ext($file_name);
    $new_file_name = md5($file_name).'.'.$file_ext;
    $upload_file_path = PATH.$upload_path.DS.$new_file_name;

    if (!@copy($file_temp, $upload_file_path)) {
        if (!@move_uploaded_file($file_temp, $upload_file_path)) {
            $error = 'อัพโหลดไฟล์รูปภาพไม่สำเร็จ';
            $status = false;
        }
    }

    $data = [
        'file_name' => $file_name,
        'file_size' => $file_size,
        'new_file_name' => $new_file_name,
        'full_path' => $upload_file_path,
        'status' => $status,
        'error' => $error,
    ];

    return $data;
}

// ===================================== DATABASE =====================

$db_result = null;

// Create connection
$db = new mysqli('localhost', 'root', 'root', 'food_delivery');
$db->set_charset('utf8');

// Check connection
if ($db->connect_error) {
    exit('ไม่สามารถเชื่อต่อฐานข้อมูลได้: '.$conn->connect_error);
}

function db_query($sql = '')
{
    global $db;
    // get result from database
    return $db->query($sql);
}

function db_get($sql = '')
{
    global $db, $db_result;
    $db_result = $db->query($sql);

    return $db_result->fetch_all(MYSQLI_ASSOC);
}

function db_row($sql = '')
{
    global $db, $db_result;
    $db_result = $db->query($sql);

    return $db_result->fetch_array(MYSQLI_ASSOC);
}

// insert data
function db_insert($table_name = null, $data = null)
{
    global $db;
    if (empty($table_name) || empty($data)) {
        exit('table / data ตั้งค่าไม่ครบถ้วน!');
    }

    // preparing data
    $data_keys = [];
    $data_values = [];
    foreach ($data as $key => $value) {
        array_push($data_keys, "`{$key}`");
        array_push($data_values, db_escape($value));
    }
    $data_keys_sql = implode(',', $data_keys);
    $data_values_sql = implode(',', $data_values);

    // insert data to database
    $sql = "INSERT INTO `{$table_name}` ({$data_keys_sql}) VALUES ({$data_values_sql})";

    // print_r($sql);
    // exit;

    return $db->query($sql);
}

function db_get_insert_id()
{
    global $db;

    return $db->insert_id;
}

// update data
function db_update($table_name = null, $data = null, $where = null)
{
    global $db;

    if (empty($table_name) || empty($data) || empty($where)) {
        exit('table / data / where ตั้งค่าไม่ครบถ้วน!');
    }

    // preparing data
    $data_array = [];
    foreach ($data as $key => $value) {
        $value_escape = db_escape($value);
        array_push($data_array, "`{$key}`={$value_escape}");
    }
    $data_to_sql = implode(',', $data_array);

    // update data to database
    $sql = "UPDATE `{$table_name}` SET {$data_to_sql} WHERE {$where}";
    if ($db->query($sql) === true) {
        return true;
    }

    // print_r($sql);
    // exit;

    return false;
}

// delete data
function db_delete($table_name = null, $where = null)
{
    global $db;
    if (empty($table_name) || empty($where)) {
        exit('table / where ตั้งค่าไม่ครบถ้วน!');
    }
    // delete data to database
    $sql = "DELETE FROM `{$table_name}` WHERE {$where}";
    if ($db->query($sql) === true) {
        return true;
    }

    // print_r($sql);
    // exit;

    return false;
}

function db_escape($str)
{
    global $db;
    if (is_string($str)) {
        return "'".mysqli_real_escape_string($db, $str)."'";
    } elseif (is_bool($str)) {
        return ($str === false) ? 0 : 1;
    } elseif ($str === null) {
        return 'NULL';
    }

    return $str;
}

// =========================== FORM VALIDATION ============================

$validation_check_vars = [];
$validation_error_status = false;
$validation_error_messages = '';

$validation_action_status = '';
$validation_action_messages = '';

function validation_set_rules($var_name = '', $var_label = '', $rule = 'required')
{
    global $validation_check_vars;
    array_push($validation_check_vars, ['var_name' => $var_name, 'var_label' => $var_label, 'rule' => $rule]);
}

function validation_run()
{
    global $validation_check_vars,$validation_error_status;
    validation_reset();
    if (empty($_POST)) {
        return false;
    } else {
        foreach ($validation_check_vars as $item) {
            if ($item['rule'] == 'required') {
                validation_check_required($item);
            }
        }
        if ($validation_error_status == true) {
            return false;
        }
    }

    return true;
}

function validation_set_error($error = '')
{
    global $validation_error_status,$validation_error_messages;

    $validation_error_status = true;
    $validation_error_messages .= "<li>{$error}</li>";
}

function validation_set_message($status = null, $message = '')
{
    global $validation_action_status,$validation_action_messages;

    $validation_action_status = $status;
    $validation_action_messages .= "<li>{$message}</li>";
}

function validation_check_required($item = [])
{
    global $validation_error_status,$validation_error_messages;

    $var_value = input_get_post($item['var_name']);
    if (empty($var_value)) {
        $validation_error_status = true;
        $validation_error_messages .= "<li>กรุณากรอกข้อมูล {$item['var_label']}.</li>";
    }
}

function validation_reset()
{
    global $validation_error_status, $validation_error_messages, $validation_action_status, $validation_action_messages;

    $validation_error_status = false;
    $validation_error_messages = '';

    $validation_action_status = '';
    $validation_action_messages = '';
}

// ============================= PROFILE =======================================

function profile_is_login()
{
    $is_login = session_get('is_login', false);
    if ($is_login == true || $is_login == 1) {
        return true;
    }

    return false;
}

function profile_data($row = null)
{
    if (empty($row)) {
        return false;
    }

    return [
        'id' => $row['id'],
        'firstname' => $row['firstname'],
        'lastname' => $row['lastname'],
        'email' => $row['email'],
        'address' => $row['address'],
        'thumbnail' => $row['thumbnail'],
        'mobile_no' => $row['mobile_no'],
        'user_type' => $row['user_type'],
        'status' => $row['status'],
        'password' => $row['password'],

        'restaurant_name' => $row['restaurant_name'],
        'restaurant_type_id' => $row['restaurant_type_id'],
        'restaurant_address' => $row['restaurant_address'],
        'restaurant_thumbnail' => $row['restaurant_thumbnail'],
    ];
}

function profile_get($email = null)
{
    (!empty($email)) or $email = session_get('email');

    $result = db_row(" SELECT * FROM user WHERE email='{$email}' ");

    return profile_data($result);
}

function profile_by_id($id = 0)
{
    $result = db_row("SELECT * FROM user WHERE id={$id}");

    return profile_data($result);
}

// ตรวจสอบว่ามีอีเมล์ในระบบ database อยู่หรือไม่
function profile_check_email_exists($email = null)
{
    $result = db_row("SELECT * FROM user WHERE email='{$email}'");

    return (!empty($result)) ? true : false;
}
