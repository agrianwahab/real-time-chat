<?php
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";
    $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);
    $outgoing_id = $_SESSION['unique_id'];
    
    // Hapus file jika ada
    $sql = mysqli_query($conn, "SELECT file_name FROM messages WHERE msg_id = {$msg_id} AND outgoing_msg_id = {$outgoing_id}");
    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
        if($row['file_name']){
            $file_path = "../php/uploads/".$row['file_name'];
            if(file_exists($file_path)){
                unlink($file_path);
            }
        }
    }
    
    // Hapus pesan dari database
    $sql = mysqli_query($conn, "DELETE FROM messages WHERE msg_id = {$msg_id} AND outgoing_msg_id = {$outgoing_id}");
    if($sql){
        echo "success";
    }
}
?>