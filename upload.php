<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the autoloader for Google Cloud Storage
require 'vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;

// MySQL connection settings
$servername = "127.0.0.1";
$username = "registration-user";  // Your MySQL username
$password = "Vamsi@123";  // Your MySQL password
$dbname = "user_registration";  // Your MySQL database name

// Create MySQL connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check MySQL connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure all form fields are set and not null
$name = isset($_POST['name']) ? $_POST['name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$resume = isset($_FILES['resume']) ? $_FILES['resume'] : null;
$image = isset($_FILES['image']) ? $_FILES['image'] : null;

if (!$name || !$email || !$resume || !$image) {
     die("All fields are required.");
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

// Check if file upload was successful
if ($_FILES['resume']['error'] != UPLOAD_ERR_OK) {
    die("Error uploading resume file: " . $_FILES['resume']['error']);
}

if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
    die("Error uploading image file: " . $_FILES['image']['error']);
}

// Step 1: Insert User Data into MySQL
$stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->bind_param('ss', $name, $email);
if (!$stmt->execute()) {
    die("Error inserting data into MySQL: " . $stmt->error);
}
$user_id = $stmt->insert_id; // Get the last inserted user ID
$stmt->close();

// Step 2: Upload Resume to Google Cloud Storage
$storage = new StorageClient();
$bucket = $storage->bucket('file_store6281');  // Replace with your GCS bucket name

/ Upload Resume
$resumeName = basename($resume["name"]);
$resumeFilePath = fopen($resume["tmp_name"], 'r');
$resumeObject = $bucket->upload($resumeFilePath, ['name' => 'resumes/' . $resumeName]);

// Check if resume upload was successful
if ($resumeObject === null) {
    die("Failed to upload the resume to Google Cloud Storage.");
}

// Upload Image to Google Cloud Storage
$imageName = basename($image["name"]);
$imageFilePath = fopen($image["tmp_name"], 'r');
$imageObject = $bucket->upload($imageFilePath, ['name' => 'images/' . $imageName]);

// Check if image upload was successful
if ($imageObject === null) {
    die("Failed to upload the image to Google Cloud Storage.");
}

// Step 3: Store GCS file paths in MySQL
$resumePath = 'gs://file_store6281/resumes/' . $resumeName;
$imagePath = 'gs://file_store6281/images/' . $imageName;

$stmt = $conn->prepare("UPDATE users SET resume_path = ?, image_path = ? WHERE id = ?");
$stmt->bind_param('ssi', $resumePath, $imagePath, $user_id);
if (!$stmt->execute()) {
    die("Error updating file paths in MySQL: " . $stmt->error);
}

$stmt->close();

echo "Contact submission successful!<br>";
echo "Resume and image uploaded successfully.<br>";
echo "Your details have been saved.";
$conn->close();
?>


