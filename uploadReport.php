<?php
// 1. Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "healthsphere";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. Check if form is submitted
if (isset($_POST['submit'])) {
    $report_id = mysqli_real_escape_string($conn, $_POST['report_id']);
    $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
    $patient_email = mysqli_real_escape_string($conn, $_POST['patient_email']);
    $sample_type = mysqli_real_escape_string($conn, $_POST['sample_type']);

    // 3. File Upload Logic
    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["report_file"]["name"]); // Added timestamp to prevent overwriting
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Basic Validation: Check if it's a real file and limit types
    $allowed_types = array("pdf", "doc", "docx", "jpg", "png");
    
    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES["report_file"]["tmp_name"], $target_file)) {
            
            // 4. Insert into Database
            $sql = "INSERT INTO reports (report_id, patient_name, patient_email, sample_type, file_path) 
                    VALUES ('$report_id', '$patient_name', '$patient_email', '$sample_type', '$target_file')";

            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Report uploaded successfully!'); window.location.href='hospital.html';</script>";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Invalid file type. Only PDF, DOC, JPG, and PNG are allowed.";
    }
}

mysqli_close($conn);
?>