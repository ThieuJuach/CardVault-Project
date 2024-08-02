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
          <li><a href="manage-card.php" class="active">Add Credit Card</a></li>
          <li><a data-bs-toggle="modal" data-bs-target="#viewTransaction" class="active">View Transaction</a></li>
          <li><a data-bs-toggle="modal" data-bs-target="#addCustomer" class="active">Add User</a></li>
          <li><a data-bs-toggle="modal" data-bs-target="#addTaller" class="active">Add Teller</a></li>         
          
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
          <div class="col-lg-10">
            <h2 data-aos="fade-up" data-aos-delay="100">Welcome Mr. Administrator</h2>
            <p data-aos="fade-up" data-aos-delay="200">We are team of talented designers </p>
          </div>
          
        </div>
      </div>

    </section><!-- End Hero Section -->

    <!-- View Transaction Modal -->
<div class="modal fade modal-xl" id="viewTransaction" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="card-body">
          <div class="table-responsive">
              <table class="table table-dark table-striped table-hover" id="cardTable">
                  <thead>
                      <tr>
                        <th scope="col">Transaction ID</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Card Number</th>
                        <th scope="col">CVV</th>
                        <th scope="col">Description</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Date</th>            
                      </tr>
                    </thead>
                    <tbody id="cardTableBody">
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
                              echo "</tr>";
                          }
                      } else {
                          echo "<tr><td colspan='7'>No data available</td></tr>";
                      }
                      ?>
                    </tbody>
                    
              </table>
          </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>

<!-- ======= Add Customer Modal ======= -->
<div id="addCustomer" class="modal" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 overflow-hidden">
          <div class="modal-header p-3">
              <h4 class="card-title mb-0">Sign Up</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
          <div class="modal-body">
              <form action="admin-add-user.php" method="POST">
                  <div class="mb-3">
                      <label for="fullName" class="form-label">Full Name</label>
                      <input type="text" class="form-control" name="fullName" placeholder="Enter your name">
                  </div>
                  <div class="mb-3">
                      <label for="emailInput" class="form-label">Email address</label>
                      <input type="email" class="form-control" name="email" placeholder="Enter your email">
                  </div>
                  <div class="mb-3">
                    <label for="emailInput" class="form-label">Phone Number</label>
                    <input type="number" class="form-control" name="phone" placeholder="Enter your email">
                </div>
                  
                  <div class="mb-3">
                      <label for="exampleInputPassword1" class="form-label">Password</label>
                      <input type="password" class="form-control" name="password" placeholder="Enter your password">
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirmPassword" placeholder="Enter your password">
                </div>
                  <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="checkTerms">
                      <label class="form-check-label" for="checkTerms">I agree to the <span class="fw-semibold">Terms of Service</span> and Privacy Policy</label>
                  </div>
                  <div class="text-end">
                      <button type="submit" class="btn btn-primary" name="signup">Sign Up Now</button>
                  </div>
              </form>
          </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Add Taller Modal -->
<div class="modal fade" id="addTaller" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Taller</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="add-taller.php" method="POST">
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Taller ID</label>
            <input type="number" class="form-control" name="tallerID" aria-describedby="emailHelp">
           
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control" name="password">
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirmPassword">
          </div>
         
          <button type="submit" class="btn btn-primary" name="addTaller">Submit</button>
        </form>
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