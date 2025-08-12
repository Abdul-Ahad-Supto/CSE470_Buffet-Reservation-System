<?php
// View/menu.php
session_start();
require_once '../Model/pdov2.php';
require_once '../Model/menumodal.php';

$menuModel = new MenuModel($pdo);
$selectedSession = $_GET['session'] ?? 'all';
$pricing = $menuModel->getPricing();

if ($selectedSession === 'all') {
    $menuItems = $menuModel->getMenuBySession();
} else {
    $menuItems = $menuModel->getMenuGroupedByCategory($selectedSession);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Menu - Ristorante Con Fusion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .menu-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-top: 56px;
        }
        .menu-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.2);
        }
        .category-header {
            background: #f8f9fa;
            padding: 10px 15px;
            margin: 20px 0 10px 0;
            border-left: 4px solid #667eea;
            font-weight: bold;
        }
        .menu-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .menu-item:last-child {
            border-bottom: none;
        }
        .vegetarian-badge {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .price-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .session-filter {
            position: sticky;
            top: 70px;
            z-index: 100;
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark navbar-expand-sm fixed-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#Navbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand mr-auto" href="indexv2.php"><img src="img/logo.png" height="30" width="41"></a>
            <div class="collapse navbar-collapse" id="Navbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="indexv2.php"><span class="fa fa-home"></span> Home</a></li>
                    <li class="nav-item active"><a class="nav-link" href="#"><span class="fa fa-cutlery"></span> Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.html"><span class="fa fa-info"></span> About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contactus.php"><span class="fa fa-address-card"></span> Contact</a></li>
                </ul>
                <span class="navbar-text">
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <span class="text-white mr-3">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                        <a href="../Controller/authcontroller.php?action=logout" class="text-white">
                            <span class="fa fa-sign-out"></span> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-white">
                            <span class="fa fa-sign-in"></span> Login
                        </a>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </nav>

    <!-- Menu Header -->
    <div class="menu-header">
        <div class="container text-center">
            <h1>Our Buffet Menu</h1>
            <p class="lead">Discover our delicious selection of cuisines from around the world</p>
        </div>
    </div>

    <!-- Session Filter -->
    <div class="session-filter">
        <div class="container">
            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                <label class="btn btn-outline-primary <?php echo $selectedSession === 'all' ? 'active' : ''; ?> flex-fill">
                    <input type="radio" name="session" value="all" <?php echo $selectedSession === 'all' ? 'checked' : ''; ?> 
                           onchange="window.location.href='menu.php?session=all'"> All Sessions
                </label>
                <label class="btn btn-outline-primary <?php echo $selectedSession === 'Breakfast' ? 'active' : ''; ?> flex-fill">
                    <input type="radio" name="session" value="Breakfast" <?php echo $selectedSession === 'Breakfast' ? 'checked' : ''; ?>
                           onchange="window.location.href='menu.php?session=Breakfast'"> Breakfast
                </label>
                <label class="btn btn-outline-primary <?php echo $selectedSession === 'Lunch' ? 'active' : ''; ?> flex-fill">
                    <input type="radio" name="session" value="Lunch" <?php echo $selectedSession === 'Lunch' ? 'checked' : ''; ?>
                           onchange="window.location.href='menu.php?session=Lunch'"> Lunch
                </label>
                <label class="btn btn-outline-primary <?php echo $selectedSession === 'Dinner' ? 'active' : ''; ?> flex-fill">
                    <input type="radio" name="session" value="Dinner" <?php echo $selectedSession === 'Dinner' ? 'checked' : ''; ?>
                           onchange="window.location.href='menu.php?session=Dinner'"> Dinner
                </label>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Pricing Information -->
            <div class="col-md-4">
                <h3 class="mb-3">Pricing & Timing</h3>
                <?php foreach ($pricing as $price): ?>
                <div class="price-card">
                    <h5><?php echo htmlspecialchars($price['session_name']); ?></h5>
                    <p class="mb-1">
                        <i class="fa fa-clock-o"></i> 
                        <?php echo date('g:i A', strtotime($price['start_time'])); ?> - 
                        <?php echo date('g:i A', strtotime($price['end_time'])); ?>
                    </p>
                    <p class="mb-1">
                        <i class="fa fa-calendar"></i> 
                        <?php echo htmlspecialchars($price['working_days']); ?>
                    </p>
                    <h4 class="text-primary mt-2">৳<?php echo number_format($price['price'], 2); ?></h4>
                    <small class="text-muted">Including VAT</small>
                </div>
                <?php endforeach; ?>
                
                <!-- Make Reservation Button -->
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a href="indexv2.php#reserveform" class="btn btn-success btn-block">
                        <i class="fa fa-calendar-check-o"></i> Make Reservation
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-block">
                        <i class="fa fa-sign-in"></i> Login to Reserve
                    </a>
                <?php endif; ?>
            </div>

            <!-- Menu Items -->
            <div class="col-md-8">
                <?php if ($selectedSession === 'all'): ?>
                    <!-- Show all items grouped by session -->
                    <?php 
                    $sessions = ['Breakfast', 'Lunch', 'Dinner'];
                    foreach ($sessions as $session): 
                        $sessionItems = array_filter($menuItems, function($item) use ($session) {
                            return $item['session'] === $session;
                        });
                        if (!empty($sessionItems)):
                    ?>
                        <h3 class="mb-3"><?php echo $session; ?> Menu</h3>
                        <div class="menu-card">
                            <?php 
                            $currentCategory = '';
                            foreach ($sessionItems as $item): 
                                if ($currentCategory !== $item['category']):
                                    $currentCategory = $item['category'];
                            ?>
                                <div class="category-header"><?php echo htmlspecialchars($currentCategory); ?></div>
                            <?php endif; ?>
                                <div class="menu-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php echo htmlspecialchars($item['item_name']); ?>
                                                <?php if ($item['is_vegetarian']): ?>
                                                    <span class="vegetarian-badge">Veg</span>
                                                <?php endif; ?>
                                            </h6>
                                            <?php if ($item['description']): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['description']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                <?php else: ?>
                    <!-- Show items for selected session grouped by category -->
                    <h3 class="mb-3"><?php echo htmlspecialchars($selectedSession); ?> Menu</h3>
                    <?php foreach ($menuItems as $category => $items): ?>
                        <div class="category-header"><?php echo htmlspecialchars($category); ?></div>
                        <div class="menu-card">
                            <?php foreach ($items as $item): ?>
                                <div class="menu-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php echo htmlspecialchars($item['item_name']); ?>
                                                <?php if ($item['is_vegetarian']): ?>
                                                    <span class="vegetarian-badge">Veg</span>
                                                <?php endif; ?>
                                            </h6>
                                            <?php if ($item['description']): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['description']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-4 offset-1 col-sm-2">
                    <h5>Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="indexv2.php">Home</a></li>
                        <li><a href="#">Menu</a></li>
                        <li><a href="aboutus.html">About</a></li>
                        <li><a href="contactus.php">Contact</a></li>
                    </ul>
                </div>
                <div class="col-7 col-sm-5">
                    <h5>Our Office Address</h5>
                    <address>
                        Roadno.8A, Houseno.42<br>
                        Dhanmondi, Dhaka<br>
                        Bangladesh<br>
                        <i class="fa fa-phone fa-lg"></i>: +852 1234 5678<br>
                        <i class="fa fa-envelope fa-lg"></i>: <a href="mailto:confusion@food.net">confusion@food.net</a>
                    </address>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <p>© Copyright 2025 Ristorante Con Fusion</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>