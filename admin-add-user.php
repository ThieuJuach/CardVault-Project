<?php 
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();

    // Include database configuration file
    include 'config.php';

    // Check if the form is submitted
    if (isset($_POST['signup'])) {

        // Get data from the form and sanitize inputs
        $name = mysqli_real_escape_string($conn, $_POST['fullName']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

        // Check if passwords match
        if ($password !== $confirmPassword) {
            echo "<script>
                    alert('Passwords do not match!');
                    window.location.href = 'administrator.php';
                </script>";
            exit();
        }

        // Validate the email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>
                    alert('Invalid email format!');
                    window.location.href = 'administrator.php';
                </script>";
            exit();
        }

        // Check if the Phone number already exists in the database
        $checkQuery = "SELECT * FROM customers WHERE phone = '$phone'";
        $result = mysqli_query($conn, $checkQuery);

        if (!$result) {
            // Query execution failed, display error message
            $errorMessage = mysqli_error($conn);
            echo "<script>
                    alert('Database Error: $errorMessage');
                    window.location.href = 'administrator.php';
                </script>";
            exit();
        }

        if (mysqli_num_rows($result) > 0) {
            // Phone number already exists, return an error
            echo "<script>
                    alert('Phone Number already exists! Please check the Phone Number and try again...');
                    window.location.href = 'administrator.php';
                </script>";
            exit();
        }

        // Hash the password using SHA-256
        $hashedPassword = hash('sha256', $password);

        // SQL insert query
        $sql = "INSERT INTO customers (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$hashedPassword')";

        // Perform the insert operation
        if (mysqli_query($conn, $sql)) {
            // Registration successful, redirect user to success page
            echo "<script>
                    alert('User Added Successful!');
                    window.location.href = 'administrator.php';
                </script>";
            exit();
        } else {
            // Registration failed, display error message
            echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                    window.location.href = 'administrator.php';
                </script>";
            exit();
        }

        mysqli_close($conn);
    }
?>
