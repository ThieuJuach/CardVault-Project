<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include 'config.php';

    if(isset($_POST['addTaller'])){
        $tallerID = mysqli_real_escape_string($conn, $_POST['tallerID']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

        if ($password !== $confirmPassword) {
            echo "<script>
                    alert('Passwords do not match!');
                    window.location.href = 'index.html';
                </script>";
            exit();
        }


        // Hash the password using SHA-256
        $hashedPassword = hash('sha256', $password);

        $sql = "INSERT INTO tallers (tallerID, password) VALUES ('$tallerID', '$hashedPassword')";

        // Perform the insert operation
        if (mysqli_query($conn, $sql)) {
            // Registration successful, redirect user to success page
            echo "<script>
                    alert('Successful!');
                    window.location.href = 'administrator.html';
                </script>";
            exit();
        } else {
            // Registration failed, display error message
            echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                    window.location.href = 'administrator.html';
                </script>";
            exit();
        }
        
    }
    mysqli_close($conn);

?>