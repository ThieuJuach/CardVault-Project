<?php
  include 'config.php';

  $key = "1234"; // Key for encryption
  $iv = "1234123412341234"; 

  // Function to decrypt data using AES decryption
  function decrypt($data, $key, $iv) {
    $cipher = "aes-128-cbc";
    $options = OPENSSL_RAW_DATA;
    $decryptedData = openssl_decrypt(base64_decode($data), $cipher, $key, $options, $iv);
    return $decryptedData;
  }
  
  $sql = "SELECT * FROM cards";
  $result = $conn->query($sql);
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
       
        <h1>SecureCard</h1>
        <span>.</span>
      </a>

      <!-- Nav Menu -->
      <nav id="navmenu" class="navmenu">
        
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
                        <h4 class="card-title mb-0 flex-grow-1">Credit Cards</h4>
                        <!-- Grids in modals -->
                        <div class="d-flex mb-2 ms-auto">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalgrid">
                                Add Card
                            </button>
                        </div>
                        
                        
                    </div><!-- end card header --> 

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Card Number</th>
                                        <th scope="col">Card Holder</th>                                        
                                        <th scope="col">Phone Number</th>
                                        <th scope="col">Expiry Month</th>
                                        <th scope="col">Expiry Year</th> 
                                        <th scope="col">Balance</th>  
                                        <th scope="col">CVV</th>                
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
                                            echo '<td>' . htmlspecialchars($decryptedCardNumber) . '</td>'; 
                                            echo '<td>' . htmlspecialchars($row['cardHolder']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['month']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['year']) . '</td>';                                             
                                            echo '<td>' . htmlspecialchars($row['balance']) . '</td>';
                                            echo '<td>' . htmlspecialchars($decryptedCvv) . '</td>'; 
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
                                        <h5 class="modal-title" id="exampleModalgridLabel">Add New Card</h5>
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
                                                                            <h5 class="card-title mb-0">Credit Card</h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="mx-auto" style="max-width: 400px">
                                                                                <div class="text-bg-info bg-gradient p-4 rounded-3 mb-3">
                                                                                    <div class="d-flex">
                                                                                        <div class="flex-grow-1">
                                                                                            <i class="bx bx-chip h1 text-warning"></i>
                                                                                        </div>
                                                                                        <div class="flex-shrink-0">
                                                                                            <i class="bx bxl-visa display-2 mt-n3"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="card-number fs-20" id="card-num-elem">
                                                                                        XXXX XXXX XXXX XXXX
                                                                                    </div>
                                                            
                                                                                    <div class="row mt-4">
                                                                                        <div class="col-4">
                                                                                            <div>
                                                                                                <div class="text-white-50">Card Holder</div>
                                                                                                <div id="card-holder-elem" class="fw-medium fs-14">Full Name</div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-4">
                                                                                            <div class="expiry">
                                                                                                <div class="text-white-50">Expires</div>
                                                                                                <div class="fw-medium fs-14">
                                                                                                    <span id="expiry-month-elem">00</span>
                                                                                                    /
                                                                                                    <span id="expiry-year-elem">0000</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-4">
                                                                                            <div>
                                                                                                <div class="text-white-50">CVC</div>
                                                                                                <div id="cvc-elem" class="fw-medium fs-14">---</div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- end card div elem -->
                                                                            </div>                                                            
                                                            
                                                                            <form id="custom-card-form" autocomplete="off" action="add-card.php" method="POST">
                                                                                <div class="mb-3">
                                                                                    <label for="card-num-input" class="form-label">Card Number</label>
                                                                                    <input type="text" class="form-control" id="cardNumber" name="cardNumber" maxlength="19" placeholder="0000 0000 0000 0000" oninput="formatCardNumber(this)">
                                                                                </div>
                                                            
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">Card Holder</label>
                                                                                    <input type="text" class="form-control" name="cardHolder" placeholder="Enter holder name" />
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">Phone Number</label>
                                                                                    <input type="text" class="form-control" name="phoneNumber" placeholder="Enter phone number" />
                                                                                </div>
                                                            
                                                                                <div class="row mb-3">
                                                                                    <div class="col-lg-4">
                                                                                        <div>
                                                                                            <label for="expiry-month-input" class="form-label">Expiry Month</label>
                                                                                            <select class="form-select" name="expiryMonth">
                                                                                                <option></option>
                                                                                                <option value="01">01</option>
                                                                                                <option value="02">02</option>
                                                                                                <option value="03">03</option>
                                                                                                <option value="04">04</option>
                                                                                                <option value="05">05</option>
                                                                                                <option value="06">06</option>
                                                                                                <option value="07">07</option>
                                                                                                <option value="08">08</option>
                                                                                                <option value="09">09</option>
                                                                                                <option value="10">10</option>
                                                                                                <option value="11">11</option>
                                                                                                <option value="12">12</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- end col -->
                                                            
                                                                                    <div class="col-lg-4">
                                                                                        <div>
                                                                                            <label for="expiry-year-input" class="form-label">Expiry Year</label>
                                                                                            <select class="form-select" name="expiryYear">
                                                                                                <option></option>                                                                                                
                                                                                                <option value="2024">2024</option>
                                                                                                <option value="2025">2025</option>
                                                                                                <option value="2026">2026</option>
                                                                                                <option value="2027">2027</option>
                                                                                                <option value="2028">2028</option>
                                                                                                <option value="2029">2029</option>
                                                                                                <option value="2030">2030</option>
                                                                                                <option value="2031">2031</option>
                                                                                                <option value="2032">2032</option>
                                                                                                <option value="2033">2033</option>
                                                                                                <option value="2034">2034</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- end col -->
                                                            
                                                                                    <div class="col-lg-4">
                                                                                        <div class="cvc">
                                                                                            <label for="cvc-input" class="form-label">CVV</label>
                                                                                            <input type="text" name="cvv" class="form-control" maxlength="3" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- end col -->
                                                                                </div>
                                                                                <!-- end row -->
                                                                                <div class="mb-3">
                                                                                    <label for="card-holder-input" class="form-label">Balance</label>
                                                                                    <input type="number" class="form-control" name="balance" placeholder="Enter card balance" />
                                                                                </div>
                                                                                <button class="btn btn-danger w-100 mt-3" type="submit" name="addCard">Add</button>
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