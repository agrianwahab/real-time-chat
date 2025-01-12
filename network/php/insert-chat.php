<?php
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $file_name = $file_type = "";
    if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
        $file = $_FILES['file'];
        $file_name = time() . '_' . $file['name'];
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed = ['txt', 'pdf', 'docx', 'png', 'jpg'];
        
        if(in_array(strtolower($file_type), $allowed)){
            move_uploaded_file($file['tmp_name'], "uploads/".$file_name);
        }
    }
    
    if(!empty($message) || !empty($file_name)){
        $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, file_name, file_type)
                                  VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$file_name}', '{$file_type}')");
    }
}
?>