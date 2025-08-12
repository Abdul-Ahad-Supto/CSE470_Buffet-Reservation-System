<?php 
session_start();

// Authentication check
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = $_SESSION['username'] ?? '';
$userEmail = $_SESSION['email'] ?? '';

require_once '../Model/pdov2.php';
require_once '../Model/branchmodal.php';

// Get the branch data from the database
$branches = get_all_branches($pdo);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Ristorante con Fusion - Buffet Reservation</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/bootstrap-social/bootstrap-social.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="navbar navbar-dark navbar-expand-sm fixed-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#Navbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand mr-auto" href="indexv2.php"><img src="img/logo.png" height="30" width="41" alt="Logo"></a>
            <div class="collapse navbar-collapse" id="Navbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active"><a class="nav-link" href="indexv2.php"><span class="fa fa-home"></span> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php"><span class="fa fa-utensils"></span> Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="seatstatus.php"><span class="fa fa-th"></span> Seat Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.html"><span class="fa fa-info"></span> About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contactus.php"><span class="fa fa-address-card"></span> Contact</a></li>
                </ul>
                <span class="navbar-text">
                    <?php if ($isLoggedIn): ?>
                        <span class="text-white mr-3">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                        <a href="../Controller/authcontroller.php?action=logout" class="text-white">
                            <span class="fa fa-sign-out-alt"></span> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-white" onclick="showLoginOption(event)">
                            <span class="fa fa-sign-in-alt"></span> Login
                        </a>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </nav>

    <header class="jumbotron">
        <div class="container">
            <div class="row row-header">
                <div class="col-12 col-sm-6">
                    <h1>Ristorante con Fusion</h1>
                    <p>We take inspiration from the World's best cuisines, and create a unique Buffet experience. Our creations will tickle your culinary senses!</p>
                </div>
                <div class="col-12 col-sm align-self-center">
                    <img src="img/logo.png" class="img-fluid" alt="Restaurant Logo">
                </div>
                <div class="col-12 col-sm align-self-center">
                    <?php if ($isLoggedIn): ?>
                        <a href="#reserveform" class="btn btn-block btn-success">Make Reservation</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-block btn-success">Login to Reserve</a>
                    <?php endif; ?>
                    <a href="menu.php" class="btn btn-block btn-info">Menu</a>
                    <button type="button" class="btn btn-block btn-light" data-toggle="modal" data-target="#cancelModal">Cancel Reservation</button>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Alert Messages -->
        <div class="row">
            <div class="col-12">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?php 
                            echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8');
                            unset($_SESSION['message']);
                        ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Carousel -->
        <div class="row row-content">
            <div class="col-12">
                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
                        <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item">
                            <img src="img/image1.jpg" class="d-block w-100" alt="Chef">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Mauro Colagreco</h5>
                                <p>Award winning three-star Michelin chef with wide International experience having worked closely with whos-who in the culinary world, he specializes in creating mouthwatering Indo-Italian fusion experiences.</p>
                            </div>
                        </div>
                        <div class="carousel-item active">
                            <img src="img/image2.jpeg" class="d-block w-100" alt="Buffet">
                            <div class="carousel-caption d-none d-md-block">
                                <h2>Weekend Grand Buffet <span class="badge badge-danger">OFFER</span></h2>
                                <p>Featuring mouthwatering combinations with a choice of five different salads, six enticing appetizers, six main entrees and five choicest desserts. Free flowing bubbly and soft drinks. All for just 200tk per person.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="img/image3.jpeg" class="d-block w-100" alt="Special Dish">
                            <div class="carousel-caption d-none d-md-block">
                                <h2>Indoppizza <span class="badge badge-danger">NEW</span></h2>
                                <p>A unique combination of Indian Uthappam (pancake) and Italian pizza, topped with Cerignola olives, ripe vine cherry tomatoes, Vidalia onion, Guntur chillies and Buffalo Paneer.</p>
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Branch Locations and Pricing -->
        <div class="row row-content">
            <div class="col-12 col-md-6">
                <h2>Our Branch Locations</h2>
                <div id="accordion">
                    <?php foreach ($branches as $index => $branch): ?>
                        <div class="card">
                            <div class="card-header" role="tab" id="heading<?php echo $branch['branch_id']; ?>">
                                <h3 class="mb-0">
                                    <a class="<?php if ($index > 0) echo 'collapsed'; ?>" data-toggle="collapse" data-target="#collapse<?php echo $branch['branch_id']; ?>">
                                        <?php echo htmlspecialchars($branch['branch_name']); ?>
                                    </a>
                                </h3>
                            </div>
                            <div id="collapse<?php echo $branch['branch_id']; ?>" class="collapse <?php if ($index == 0) echo 'show'; ?>" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        <strong>Address:</strong> <?php echo htmlspecialchars($branch['address']); ?><br>
                                        <strong>Manager:</strong> <?php echo htmlspecialchars($branch['manager_name']); ?><br>
                                        <strong>Contact:</strong> <?php echo htmlspecialchars($branch['contact_phone']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
              
            <div class="col-12 col-md-6">
                <h2>Timing &amp; Prices</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Session</th>
                                <th>Opening Hours</th>
                                <th>Working Days</th>
                                <th>Price (Including VAT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Breakfast</th>
                                <td>9am-11am</td>
                                <td>Sunday-Thursday</td>
                                <td>৳500.00</td>
                            </tr>
                            <tr>
                                <th>Lunch</th>
                                <td>2pm-5pm</td>
                                <td>Friday-Thursday</td>
                                <td>৳600.00</td>
                            </tr>
                            <tr>
                                <th>Dinner</th>
                                <td>7pm-10pm</td>
                                <td>Friday-Thursday</td>
                                <td>৳700.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reservation Form -->
        <?php if ($isLoggedIn): ?>
        <div class="row row-content">
            <div class="col-12 col-lg-9 offset-lg-1">
                <div class="card" id="reserveform">
                    <h3 class="card-header bg-success text-white">Make Reservation</h3>
                    <div class="card-body">
                        <form id="reservationForm" action="../Controller/makereservationcontroller.php" method="POST">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Number of Guests</label>
                                <div class="col-md-10">
                                    <div class="form-check-inline">
                                        <?php for($i = 1; $i <= 10; $i++): ?>
                                            <label class="form-check-label mr-3">
                                                <input type="radio" class="form-check-input" value="<?php echo $i; ?>" name="numberofguest" required> <?php echo $i; ?>
                                            </label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-12 col-md-2 col-form-label">Name</label>
                                <div class="col-12 col-md-4">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($username); ?>" required>
                                </div>
                                <label for="email" class="col-12 col-md-1 col-form-label">Email</label>
                                <div class="col-12 col-md-5">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone" class="col-12 col-md-2 col-form-label">Phone</label>
                                <div class="col-12 col-md-4">
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone number" required>
                                </div>
                                <label for="address" class="col-12 col-md-1 col-form-label">Address</label>
                                <div class="col-12 col-md-5">
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="date" class="col-12 col-md-2 col-form-label">Date</label>
                                <div class="col-12 col-md-4">
                                    <input type="date" class="form-control" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <label for="session" class="col-12 col-md-1 col-form-label">Session</label>
                                <div class="col-12 col-md-5">
                                    <select class="form-control" id="session" name="session" required>
                                        <option value="">Select Session</option>
                                        <option value="BREAKFAST">Breakfast (9am-11am)</option>
                                        <option value="LUNCH">Lunch (2pm-5pm)</option>
                                        <option value="DINNER">Dinner (7pm-10pm)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="branch" class="col-12 col-md-2 col-form-label">Branch</label>
                                <div class="col-12 col-md-4">
                                    <select class="form-control" id="branch" name="branch" required>
                                        <option value="">Select Branch</option>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?php echo htmlspecialchars($branch['branch_name']); ?>">
                                                <?php echo htmlspecialchars($branch['branch_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-10 offset-md-2">
                                    <button type="submit" class="btn btn-primary">Make Reservation</button>
                                    <button type="reset" class="btn btn-secondary ml-2">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row row-content">
            <div class="col-12 text-center">
                <div class="card">
                    <div class="card-body">
                        <h3>Want to make a reservation?</h3>
                        <p>Please login to your account to make a reservation.</p>
                        <a href="login.php" class="btn btn-primary">Login</a>
                        <a href="register.php" class="btn btn-outline-primary ml-2">Register</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cancel Reservation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Reservation</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="../Controller/cancelreservationcontroller.php" method="POST">
                        <div class="form-group">
                            <label for="cancel_name">Full Name</label>
                            <input type="text" class="form-control" id="cancel_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="cancel_phone">Phone Number</label>
                            <input type="tel" class="form-control" id="cancel_phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="cancel_date">Reservation Date</label>
                            <input type="date" class="form-control" id="cancel_date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="cancel_session">Session</label>
                            <select class="form-control" id="cancel_session" name="session" required>
                                <option value="">Select Session</option>
                                <option value="BREAKFAST">Breakfast</option>
                                <option value="LUNCH">Lunch</option>
                                <option value="DINNER">Dinner</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cancel_branch">Branch</label>
                            <select class="form-control" id="cancel_branch" name="branch" required>
                                <option value="">Select Branch</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?php echo htmlspecialchars($branch['branch_name']); ?>">
                                        <?php echo htmlspecialchars($branch['branch_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger">Cancel Reservation</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">             
                <div class="col-4 offset-1 col-sm-2">
                    <h5>Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="indexv2.php">Home</a></li>
                        <li><a href="menu.php">Menu</a></li>
                        <li><a href="aboutus.html">About</a></li>
                        <li><a href="contactus.php">Contact</a></li>
                    </ul>
                </div>
                <div class="col-7 col-sm-5">
                    <h5>Our Office Address</h5>
                    <address>
                        Road no. 8A, House no. 42<br>
                        Dhanmondi, Dhaka<br>
                        Bangladesh<br>
                        <i class="fa fa-phone"></i>: +880 1925 779647<br>
                        <i class="fa fa-envelope"></i>: <a href="mailto:info@ristorantefusion.com">info@ristorantefusion.com</a>
                    </address>
                </div>
                <div class="col-12 col-sm-4 align-self-center">
                    <div class="text-center">
                        <a class="btn btn-social-icon btn-google" href="#"><i class="fab fa-google-plus"></i></a>
                        <a class="btn btn-social-icon btn-facebook" href="#"><i class="fab fa-facebook"></i></a>
                        <a class="btn btn-social-icon btn-linkedin" href="#"><i class="fab fa-linkedin"></i></a>
                        <a class="btn btn-social-icon btn-twitter" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-social-icon btn-youtube" href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">             
                <div class="col-auto">
                    <p>© Copyright 2025 Ristorante Con Fusion</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Show login option
        function showLoginOption(event) {
            <?php if ($isLoggedIn): ?>
                event.preventDefault();
                alert('You are already logged in!');
            <?php else: ?>
                // Allow normal navigation to login.php
                return true;
            <?php endif; ?>
        }

        // Reservation form validation
        document.addEventListener('DOMContentLoaded', function() {
            const reservationForm = document.getElementById('reservationForm');
            if (reservationForm) {
                reservationForm.addEventListener('submit', function(e) {
                    const guests = document.querySelector('input[name="numberofguest"]:checked');
                    const name = document.querySelector('input[name="name"]').value;
                    const email = document.querySelector('input[name="email"]').value;
                    const phone = document.querySelector('input[name="phone"]').value;
                    const date = document.querySelector('input[name="date"]').value;
                    const session = document.querySelector('select[name="session"]').value;
                    const branch = document.querySelector('select[name="branch"]').value;
                    
                    if (!guests) {
                        alert('Please select number of guests');
                        e.preventDefault();
                        return;
                    }
                    
                    if (!name || !email || !phone || !date || !session || !branch) {
                        alert('Please fill all required fields');
                        e.preventDefault();
                        return;
                    }
                    
                    // Check if date is in the future
                    const selectedDate = new Date(date);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < today) {
                        alert('Please select a future date');
                        e.preventDefault();
                        return;
                    }
                    
                    // Phone validation
                    const phoneRegex = /^[0-9]{10,11}$/;
                    if (!phoneRegex.test(phone)) {
                        alert('Please enter a valid phone number');
                        e.preventDefault();
                        return;
                    }
                });
            }
        });
    </script>
</body>
</html>