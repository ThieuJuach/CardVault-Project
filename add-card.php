<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();

    include 'config.php';
    
    $key = "1234"; // Key for encryption
    $iv = "1234123412341234"; // Initialization Vector for encryption

    // Function to encrypt data using AES encryption
    function encrypt($data, $key, $iv) {
        $cipher = "aes-128-cbc";
        $options = OPENSSL_RAW_DATA;
        $encryptedData = openssl_encrypt($data, $cipher, $key, $options, $iv);
        return base64_encode($encryptedData);
    }

    // Function to decrypt data using AES decryption
    function decrypt($data, $key, $iv) {
        $cipher = "aes-128-cbc";
        $options = OPENSSL_RAW_DATA;
        $decryptedData = openssl_decrypt(base64_decode($data), $cipher, $key, $options, $iv);
        return $decryptedData;
    }

    if(isset($_POST['addCard']))
        // Get the form input data and sanitize
        $cardNumber = mysqli_real_escape_string($conn, $_POST['cardNumber']);
        $cardHolder = mysqli_real_escape_string($conn, $_POST['cardHolder']);
        $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
        $expiryMonth = mysqli_real_escape_string($conn, $_POST['expiryMonth']);
        $expiryYear = mysqli_real_escape_string($conn, $_POST['expiryYear']);
        $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);
        $balance = mysqli_real_escape_string($conn, $_POST['balance']);
        
        // Check if the phone number exists in the customers table
        $checkCustomerQuery = "SELECT * FROM customers WHERE phone = '$phoneNumber'";
        $result = mysqli_query($conn, $checkCustomerQuery);

        if (mysqli_num_rows($result) == 0) {
            // Phone number does not exist, return an error
            echo "<script>
                    alert('Error! User not found!');
                    window.location.href = 'manage-cards.php';
                </script>";
            exit();
        }

        // Encrypt the card number and CVV using AES encryption        
        $encryptedCardNumber = mysqli_real_escape_string($conn, encrypt($cardNumber, $key, $iv));
        $encryptedCvv = mysqli_real_escape_string($conn, encrypt($cvv, $key, $iv));

        // Check if the card number exists in the cards table
        $cardQuery = "SELECT * FROM cards WHERE cardNumber = '$encryptedCardNumber'";
        $cardResult = mysqli_query($conn, $cardQuery);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>
                    alert('Error! Card Number Already Exist!');
                    window.location.href = 'manage-cards.php';
                </script>";
            exit();
        }

        // Insert the card details into the cards table
        $sql = "INSERT INTO cards (cardNumber, cardHolder, phone, month, year, cvv, balance) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);  // Prepare the statement
        if (!$stmt) {
            echo "Error: Unable to prepare statement: " . mysqli_error($conn);
            exit();
        }

        // Bind values to the prepared statement using binary data type for encrypted fields
        mysqli_stmt_bind_param($stmt, "ssiiisi", $encryptedCardNumber, $cardHolder, $phoneNumber, $expiryMonth, $expiryYear, $encryptedCvv, $balance);

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Card insertion successful, redirect user to success page
            echo "<script>
                    alert('Card added successfully!');
                    window.location.href = 'manage-cards.php';
                </script>";
            exit();
        } else {
            // Card insertion failed, display error message
            echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                    window.location.href = 'manage-cards.php';
                </script>";
            exit();
        }
        mysqli_stmt_close($stmt);
    
    mysqli_close($conn);
?>
