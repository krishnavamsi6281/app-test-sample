<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $resume_path = "uploads/" . basename($_FILES["resume"]["name"]);
    $image_path = "uploads/" . basename($_FILES["image"]["name"]);

    if (move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_path) && move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
        $sql = "INSERT INTO contacts (name, email, resume_path, image_path) VALUES ('$name', '$email', '$resume_path', '$image_path')";

        if ($conn->query($sql) === TRUE) {
            echo "Contact submission successful";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading files.";
    }
}

$conn->close();
?>
