<?php
  include 'config.php';

  session_start();

  $phone = $_SESSION['phone'];

  $key = "1234"; // Key for encryption
  $iv = "1234123412341234"; 

  // Function to decrypt data using AES decryption
  function decrypt($data, $key, $iv) {
      $cipher = "aes-128-cbc";
      $options = OPENSSL_RAW_DATA;
      $decryptedData = openssl_decrypt(base64_decode($data), $cipher, $key, $options, $iv);
      return $decryptedData;
  }

  // Retrieve all card numbers related to the logged-in user
  $cardQuery = "SELECT * FROM cards WHERE phone = '$phone'";
  $cardResult = $conn->query($cardQuery);

  if (!$cardResult) {
    die('Error executing query: ' . $conn->error);
  }

  $encryptedCardNumbers = array();
  if ($cardResult->num_rows > 0) {
      while ($row = $cardResult->fetch_assoc()) {
          // Store encrypted card number
          $encryptedCardNumbers[] = $row['cardNumber'];
      }
  }

  // Construct the IN clause with encrypted card numbers
  $transactionIDs = "'" . implode("','", $encryptedCardNumbers) . "'";

  // Query transactions related to the encrypted card numbers
  $sql = "SELECT * FROM transactions WHERE cardNumber IN ($transactionIDs)";
  $result = $conn->query($sql);

  if (!$result) {
    die('Error executing query: ' . $conn->error);
  }
  /*echo "<script>
          alert('Decrypted Card Numbers: $cardNumbersString');
          window.location.href = 'customer.php';
      </script>";
  exit();*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Home - SecureCard</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  
</head>

<body class="index-page" data-bs-spy="scroll" data-bs-target="#navmenu">

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="container-fluid d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1>SecureCard</h1>
        <span>.</span>
      </a>

      <!-- Nav Menu -->
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="manage-transactions.php" class="active">Manage Transactions</a></li>          
        </ul>

        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav><!-- End Nav Menu -->

      <a class="btn-getstarted" href="index.html#about">Log Out</a>

    </div>
  </header><!-- End Header -->

  <main id="main">

    <!-- Hero Section - Home Page -->
    <section id="hero" class="hero">

      <img src="assets/img/teller.png" alt="" data-aos="fade-in">

      <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Transactions</h4>
                        <!-- Grids in modals -->
                        <div class="d-flex mb-2 ms-auto">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalgrid">
                                Add Transaction
                            </button>
                        </div>

                        
                    </div><!-- end card header --> 

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Transaction ID</th>
                                        <th scope="col">Customer Name</th>
                                        <th scope="col">Card Number</th>
                                        <th scope="col">CVV</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Date</th>  
                                        <th scope="col">Action</th>          
                                      </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            // Decrypt card number and CVV
                                            $decryptedCardNumber = decrypt($row['cardNumber'], $key, $iv);
                                            $decryptedCvv = decrypt($row['cvv'], $key, $iv);

                                            echo "<tr>";                                            
                                            echo '<td>' . htmlspecialchars($row['transactionID']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['customerName']) . '</td>';
                                            echo '<td>' . htmlspecialchars($decryptedCardNumber) . '</td>'; 
                                            echo '<td>' . htmlspecialchars($decryptedCvv) . '</td>'; 
                                            echo '<td>' . htmlspecialchars($row['description']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['amount']) . '</td>';                                             
                                            echo '<td>' . htmlspecialchars($row['date']) . '</td>';                                            
                                            echo "<td><a href=''>Edit</a>     <button>Delete</button></td>";                                            
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>No data available</td></tr>";
                                    }
                                    //echo "<td><a href='edit_student_info.php?id=" . $row["id"] . "'>Edit</a></td>";
                                    //echo "<td><button onclick='deleteCourse(" . $row["id"] . ")'>Delete</button></td>";
                                    ?>
                                  </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end card-->
            </div>
    </div>
    </div>


    </section><!-- End Hero Section -->

    <div class="modal fade" id="exampleModalgrid" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalgridLabel">Add New Transaction</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-xxl-9 bg-light">                                              
                                                    
                                                        <div class="tab-content">
                                                            
                                                            <!--end tab-pane-->
                                                            <div class="tab-pane active" id="personalDetails" role="tabpanel">                                            
                                                                <div class="col-xxl-4">
                                                                    <div class="card card-height-100 ">
                                                                        <div class="card-header">
                                                                            <h5 class="card-title mb-0">Transaction Details</h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                             
                                                                            <form id="custom-card-form" autocomplete="off" action="add-transaction.php" method="POST">
                                                                                <div class="mb-3">
                                                                                    <label for="card-num-input" class="form-label">Transaction ID</label>
                                                                                    <input name="transactionID" class="form-control" maxlength="19" placeholder="0000" />
                                                                                </div>
                                                            
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">Customer Name</label>
                                                                                    <input type="text" class="form-control" name="customerName" placeholder="Enter customer name" />
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">Card Number</label>
                                                                                      <input type="text" class="form-control" id="cardNumber" name="cardNumber" maxlength="19" placeholder="0000 0000 0000 0000" oninput="formatCardNumber(this)">
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">CVV</label>
                                                                                    <input type="number" class="form-control" name="cvv" placeholder="Enter cvv" />
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">Description</label>
                                                                                    <input type="text" class="form-control" name="description" placeholder="Enter description" />
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">Amount</label>
                                                                                    <input type="text" class="form-control" name="amount" placeholder="Enter amount" />
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">Date</label>
                                                                                    <input type="datetime-local" class="form-control" name="date" placeholder="Enter holder name" />
                                                                                </div>
                                                            
                                                                                <button class="btn btn-danger w-100 mt-3" type="submit" name="addTransaction">Add</button>
                                                                            </form>
                                                                            <!-- end card form elem -->
                                                                        </div>
                                                                    </div>
                                                                    <!-- end card -->
                                                                </div>
                                                                <!-- end col -->
                                                            </div>
                                                            <!--end tab-pane-->                                        
                                                        </div>
                                                
                                            
                                            </div>
                                            <!--end col-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

  <!-- Scroll Top Button -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
  </div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>