<?php

require_once 'stefangabos/Zebra_Image/Zebra_Image.php';

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

define('BASE_URL','http://localhost/Retrojuegos');

$conn = new mysqli('localhost', 'root', '', 'retrojuegos');


// Bloque para la 'currency exchange':
function get_usd_to_eur_rate() {
    $url = 'https://api.frankfurter.dev/latest?from=USD&to=EUR';
    $response = @file_get_contents($url);

    if ($response === false) return 0.85;

    $data = json_decode($response, true);
    return $data['rates']['EUR'] ?? 0.85;
}

function get_usd_to_eur_rate_cached() {
    if (isset($_SESSION['eur_rate']) && $_SESSION['eur_rate_date'] === date('Y-m-d')) {
        return $_SESSION['eur_rate'];
    }

    $rate = get_usd_to_eur_rate();
    $_SESSION['eur_rate'] = $rate;
    $_SESSION['eur_rate_date'] = date('Y-m-d');
    return $rate;
}

function convert_price($price_usd) {
    $currency = $_SESSION['currency'] ?? 'USD';

    if ($currency === 'EUR') {
        $rate = get_usd_to_eur_rate_cached(); // ✅ use cached version
        $converted = $price_usd * $rate;
        return '€' . number_format($converted, 2);
    }

    return '$' . number_format($price_usd, 2);
}

// Bloque de funciones para la traducción:
if (isset($_GET['lang'])) {
  $lang = $_GET['lang'];
  if (in_array($lang, ['en', 'es'])) {
    $_SESSION['lang'] = $lang;
  }
}

$lang = $_SESSION['lang'] ?? 'en';
$translations = include "lang/$lang.php";

function __($key) {
  global $translations;
  return $translations[$key] ?? $key;
}


function isLoggedIn() {
    return isset($_SESSION['user']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireRole($allowedRoles = []) {
    requireLogin();
    $userRole = $_SESSION['user']['user_type'] ?? 'customer';
    if (!in_array($userRole, $allowedRoles)) {
        header('Location: unauthorized.php');
        exit;
    }
}


function get_user_orders($customer_id) {
    return db_select('orders', 'customer_id = ' . intval($customer_id) . ' ORDER BY order_date DESC');
}


function db_select_one($table, $where_clause) {
    global $conn; 
    $sql = "SELECT * FROM `$table` WHERE $where_clause LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($result);
}

function db_update($table, $data, $where_clause) {
    global $conn;

    $updates = [];
    foreach ($data as $column => $value) {
        $safe_value = mysqli_real_escape_string($conn, $value);
        $updates[] = "`$column` = '$safe_value'";
    }

    $update_str = implode(', ', $updates);
    $sql = "UPDATE `$table` SET $update_str WHERE $where_clause";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Update failed: " . mysqli_error($conn));
    }

    return mysqli_affected_rows($conn);
}

function db_delete($table, $where_clause) {
    global $conn; 
    $sql = "DELETE FROM `$table` WHERE $where_clause";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Delete failed: " . mysqli_error($conn));
    }

    return mysqli_affected_rows($conn);
}

function get_product($id) {
    global $conn;
    if (!is_numeric($id)) return ['pro' => null, 'cat' => null];

    $sql = "SELECT * FROM products WHERE id = $id";
    $data['pro'] = $conn->query($sql)->fetch_assoc();
    $data['cat'] = null;

    if ($data['pro'] != null) {
        $cat_id = $data['pro']['category_id'];
        $sql = "SELECT * FROM categories WHERE id = $cat_id";
        $data['cat'] = $conn->query($sql)->fetch_assoc();
    }

    return $data;
}

function get_product_photos($json) {
    $photos = [];

    if (is_array($json)) {
        $decoded = $json;
    } elseif (is_string($json) && strlen($json) >= 4) {
        $decoded = json_decode($json, true);
    } else {
        $decoded = null;
    }

    if (!is_array($decoded)) {
        return [[
            'src' => 'no_image.jpg',
            'thumb' => 'no_image.jpg'
        ]];
    }

    $count = 0;
    foreach ($decoded as $entry) {
        if (isset($entry['src']) && isset($entry['thumb'])) {
            $photos[] = $entry;
            $count++;
            if ($count >= 4) break;
        }
    }

    return count($photos) ? $photos : [[
        'src' => 'no_image.jpg',
        'thumb' => 'no_image.jpg'
    ]];
}

function get_product_thumb($json){
    $img = "public/assets/img/no_inage.jpg";
    if($json == null){
        return $img;
    }
    if(strlen($img) < 4){
        return $img;
    }
    $objects = json_decode($json);
    if(empty($objects)){
        return $img;
    }
    if(!isset($objects[0]->thumb)){
        return $img;
    }
    return $objects[0]->thumb;
}

function db_select($table, $condition = null){
    $sql = "SELECT * FROM $table ";
    if($condition != null){
       $sql .= " WHERE $condition ";
    }
    global $conn;
    $res = $conn->query($sql);
    $rows = [];
    while($row = $res->fetch_assoc()){
        $rows[] = $row;
    }
    return $rows;
}

function db_insert($table_name,$data){
    $sql = "INSERT INTO $table_name";
    
    $column_names = "(";
    $column_values = "(";
    
    $is_first = true;
    global $conn;
    
    foreach ($data as $key => $value) {
        
        if($is_first){
            $is_first = false;
        }else{
            $column_names .= ",";
            $column_values .= ",";
        }
        $column_names .= $key;
        $gettype = gettype($value);
        if($gettype == 'string'){
            $value = $conn->real_escape_string($value);
            $column_values .= "'$value'";
        }else{
            $value = $conn->real_escape_string($value);
            $column_values .= $value;
        }        
    }
    $column_names .= ")";
    $column_values .= ")";
    $sql .= $column_names. " VALUES " .$column_values;
    
    
    if($conn->query($sql)){
        return true;
    }else{
        return false;
    }
}

function create_thumb($source, $target) {
    $image = new stefangabos\Zebra_Image\Zebra_Image();

    $image->auto_handle_exif_orientation = true;
    $image->source_path = $source;
    $image->target_path = $target;
    $image->preserve_aspect_ratio = true;
    $image->enlarge_smaller_images = true;
    $image->preserve_time = true;
    $image->jpeg_quality = get_jpeg_quality(filesize($image->source_path));

    if (!$image->resize(300, 300, ZEBRA_IMAGE_CROP_CENTER)) {
        return false;
    } else {
        return $target;
    }
}

function get_jpeg_quality($_size){
    $size=($_size/1000000);
    
    $qt=50;
    if($size > 5){
        $qt = 10;
    }else if($size > 4){
        $qt = 13;
    }else if($size > 2){
        $qt = 15;
    }else if($size > 1){
        $qt = 17;
    }else if($size > 0.8){
        $qt = 50;
    }else if($size > .5){
        $qt = 80;
    }else{
        $qt = 90;
    }
    return $qt;
}

function upload_images($files) {
    ini_set('memory_limit', '512M');

    if ($files == null || empty($files)) {
        return [];
    }

    $uploaded_images = [];

    // Si sólo es un archivo, se mete en un vector par amayor consistencia
    if (!isset($files[0])) {
        $files = [$files];
    }

    foreach ($files as $file) {
        if (
            isset($file['name']) &&
            isset($file['type']) &&
            isset($file['tmp_name']) &&
            isset($file['error']) &&
            isset($file['size'])
        ) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['upload_error'] = "File upload error: " . $file['error'];
                return false;
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_name = time() . "-" . rand(100000, 1000000) . "." . $ext;
            $destination = 'uploads/' . $file_name;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $_SESSION['upload_error'] = "Failed to move uploaded file.";
                return false;
            }

            $thumb_destination = create_thumb($destination, 'uploads/thumb_' . $file_name);
            if (!$thumb_destination) {
                $_SESSION['upload_error'] = "Thumbnail creation failed.";
                return false;
            }

            $uploaded_images[] = [
                'src' => $destination,
                'thumb' => $thumb_destination
            ];
        }
    }

    return $uploaded_images;
}

function url($path = "/"){
    return BASE_URL . $path;
}

function protected_area(){
    if(!isset($_SESSION['user'])){
       alert('warning', __('unauthorized_access_warning'));
       header('Location: login.php');
       die(); 
    }
}

function logout(){
    if(isset($_SESSION['user'])){
       unset($_SESSION['user']);
    }
    alert('success', __('logout_success'));
    header('Location: login.php');
    die();
}

function is_logged_in(){
    if(isset($_SESSION['user'])){
        return true;
    } else {
        return false;
    }
}

function alert($type,$message){   
    $_SESSION['alert']['type'] = $type;
    $_SESSION['alert']['message'] = $message;
}

function login_user($email, $password){
    global $conn;
    $sql = "SELECT * FROM users WHERE email = '{$email}'";
    $res = $conn->query($sql);

    if($res->num_rows < 1){
        return false;
    }

    $row = $res->fetch_assoc();

    if(!password_verify($password, $row['password'])){
        return false;
    }

    $_SESSION['user'] = $row;

    return true;
}

function text_input($data){
    
    $name = (isset($data['name'])) ? $data['name'] : "";
    $attributes = (isset($data['attributes'])) ? $data['attributes'] : "";
    
    $value="";
    $error="";
    $error_text="";
    if(isset($_SESSION['form'])){
        if(isset($_SESSION['form']['value'])){
            if(isset($_SESSION['form']['value'][$name])){
                $value = $_SESSION['form']['value'][$name];
            }   
        }
    }
    
    if(isset($_SESSION['form'])){
        if(isset($_SESSION['form']['error'])){
            if(isset($_SESSION['form']['error'][$name])){
                $error = $_SESSION['form']['error'][$name];
                $error_text='<div class="form-text text-danger">' . $error . '</div>';
            }
        }       
    }
    
    $label = (isset($data['label'])) ? $data['label'] : $name;
    $value = (isset($data['value'])) ? $data['value'] : $value;
    $error = (isset($data['error'])) ? $data['error'] : $error;
    
    return 
        '<label class="form-label text-capitalize" for="'.$name.'">'.$label.'</label>
        <input name="'.$name.'" value="'.$value.'" class="form-control" type="text" id="'.$name.'" placeholder="'.$label.'"'.$attributes.'>'
        .$error_text;
}

function select_input($data, $options) {
    $name = isset($data['name']) ? $data['name'] : "";
    $attributes = isset($data['attributes']) ? $data['attributes'] : "";

    $value = "";
    $error = "";
    $error_text = "";

    // Carga valores del formulario de una antigua sesión
    if (isset($_SESSION['form']['value'][$name])) {
        $value = $_SESSION['form']['value'][$name];
    }

    // Carga mensajes de error
    if (isset($_SESSION['form']['error'][$name])) {
        $error = $_SESSION['form']['error'][$name];
        $error_text = '<div class="form-text text-danger">' . $error . '</div>';
    }

    $label = isset($data['label']) ? $data['label'] : ucfirst($name);
    $value = isset($data['value']) ? $data['value'] : $value;
    $error = isset($data['error']) ? $data['error'] : $error;

    $select_options = "";

    if (empty($options)) {
        $select_options = '<option disabled selected>No categories available</option>';
    } else {
        foreach ($options as $key => $val) {
            $selected = ($key == $value) ? " selected" : "";
            $select_options .= '<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
        }
    }

    $select_tag = '<select name="' . $name . '" class="form-select" id="' . $name . '" placeholder="' . $label . '" ' . $attributes . '>
        ' . $select_options . '
    </select>';

    return 
        '<label class="form-label text-capitalize" for="' . $name . '">' . $label . '</label>' .
        $select_tag .
        $error_text;
}

function product_item_iu_1($pro){
    $thumb = get_product_thumb($pro['photos']);
    $str = <<<EOF
    <div class="col-md-4 col-sm-6 px-2 mb-4">
        <div class="card product-card">
            <button class="btn-wishlist btn-sm" type="button" data-bs-toggle="tooltip" data-bs-placement="left" title="Add to wishlist"><i class="ci-heart"></i></button><a class="card-img-top d-block overflow-hidden" href="product.php?id={$pro['id']}">
              <img src="{$thumb}" alt="Product"></a>
            <div class="card-body py-2"><a class="product-meta d-block fs-xs pb-1" href="javascript:;">Sneakers &amp; Keds</a>
            <h3 class="product-title fs-sm"><a href="product.php?id={$pro['id']}">{$pro['name']}</a></h3>
            <div class="d-flex justify-content-between">
              <div class="product-price"><span class="text-accent">{$pro['price']}.<small>00</small></span></div>
              <div class="star-rating"><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star"></i>
              </div>
            </div>
            </div>
            <div class="card-body card-body-hidden">
            <div class="text-center pb-2">
              <div class="form-check form-option form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="size1" id="s-75">
                <label class="form-option-label" for="s-75">7.5</label>
              </div>
              <div class="form-check form-option form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="size1" id="s-80" checked>
                <label class="form-option-label" for="s-80">8</label>
              </div>
              <div class="form-check form-option form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="size1" id="s-85">
                <label class="form-option-label" for="s-85">8.5</label>
              </div>
              <div class="form-check form-option form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="size1" id="s-90">
                <label class="form-option-label" for="s-90">9</label>
              </div>
            </div>
            <button class="btn btn-primary btn-sm d-block w-100 mb-2" type="button"><i class="ci-cart fs-sm me-1"></i>Add to Cart</button>
            <div class="text-center"><a class="nav-link-style fs-ms" href="#quick-view" data-bs-toggle="modal"><i class="ci-eye align-middle me-1"></i>Quick view</a></div>
            </div>
            </div>
        <hr class="d-sm-none">
    </div>
    EOF;
    return $str;
}

?>