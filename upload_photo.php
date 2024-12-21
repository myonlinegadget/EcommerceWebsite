<?php
session_start();
include('config.php');

$user_id = $_SESSION['user_id'];

if (isset($_FILES['photo'])) {
    $photo = $_FILES['photo']['name'];
    $target = "uploads/" . basename($photo);

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
        $update_photo_query = "UPDATE users SET photo='$target' WHERE id=$user_id";
        mysqli_query($conn, $update_photo_query);
        $_SESSION['message'] = "Photo uploaded successfully";
    } else {
        $_SESSION['message'] = "Failed to upload photo";
    }
}

header("Location: user_profile.php");
exit();
?>