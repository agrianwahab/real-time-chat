<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing" data-msg-id="'.$row['msg_id'].'">
                                <div class="details">
                                    <i class="fas fa-trash delete-btn" onclick="deleteMessage('.$row['msg_id'].')"></i>
                                    <p>'. $row['msg'];

                                    // File attachment untuk outgoing message
                                    if($row['file_name'] != ''){
                                        $file_path = 'php/uploads/'.$row['file_name'];
                                        $file_extension = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
                                        
                                        if(in_array($file_extension, ['png', 'jpg', 'jpeg', 'gif'])){
                                            $output .= '<br><img src="'.$file_path.'" style="max-width:200px;"><br>';
                                        } else {
                                            $output .= '<div class="file-attachment">
                                                        <a href="'.$file_path.'" target="_blank">
                                                            <i class="fas fa-file"></i> '.$row['file_name'].'
                                                        </a>
                                                    </div>';
                                        }
                                    }

                                    $output .= '</p>
                                </div>
                            </div>';
                }else{
                    $output .= '<div class="chat incoming" data-msg-id="'.$row['msg_id'].'">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    <p>'. $row['msg'];

                                    // File attachment untuk incoming message
                                    if($row['file_name'] != ''){
                                        $file_path = 'php/uploads/'.$row['file_name'];
                                        $file_extension = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
                                        
                                        if(in_array($file_extension, ['png', 'jpg', 'jpeg', 'gif'])){
                                            $output .= '<br><img src="'.$file_path.'" style="max-width:200px;"><br>';
                                        } else {
                                            $output .= '<div class="file-attachment">
                                                        <a href="'.$file_path.'" target="_blank">
                                                            <i class="fas fa-file"></i> '.$row['file_name'].'
                                                        </a>
                                                    </div>';
                                        }
                                    }

                                    $output .= '</p>
                                </div>
                            </div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }
?>