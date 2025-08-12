<?php
// View/menu.php - Complete version with AI recommendations
session_start();
require_once '../Model/pdov2.php';
require_once '../Model/menumodal.php';

$menuModel = new MenuModel($pdo);
$selectedSession = $_GET['session'] ?? 'all';
$userPreference = $_GET['preference'] ?? 'all'; // all, vegetarian, non-vegetarian
$pricing = $menuModel->getPricing();

if ($selectedSession === 'all') {
    $menuItems = $menuModel->getMenuBySession();
} else {
    $menuItems = $menuModel->getMenuGroupedByCategory($selectedSession);
}

// Get user's recommendations
$userRecommendations = [];
if (isset($_SESSION['user_id'])) {
    $userRecommendations = $menuModel->getUserRecommendations($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Menu - Ristorante Con Fusion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            position: relative;
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
        .non-veg-badge {
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .spice-level {
            color: #ff6b35;
            font-size: 12px;
        }
        .recommended-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffc107;
            color: #000;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
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
        .preference-filter {
            background: #f8f9fa;
            padding: 15px 0;
        }
        .cuisine-tag {
            background: #e9ecef;
            color: #495057;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 11px;
            margin-right: 5px;
        }
        .allergen-warning {
            color: #dc3545;
            font-size: 11px;
        }
        .recommendation-card {
            background: linear-gradient(135deg, #ffeaa7, #fab1a0);
            border: none;
            color: #333;
        }
        .chef-suggestions {
            background: linear-gradient(135deg, #a8e6cf, #88d8c0);
            color: #333;
        }
        .empty-menu {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .loading {
            text-align: center;
            padding: 40px;
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
                    <li class="nav-item"><a class="nav-link" href="indexv2.php"><span class="fas fa-home"></span> Home</a></li>
                    <li class="nav-item active"><a class="nav-link" href="#"><span class="fas fa-utensils"></span> Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="seatstatus.php"><span class="fas fa-th"></span> Seat Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.html"><span class="fas fa-info"></span> About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contactus.php"><span class="fas fa-address-card"></span> Contact</a></li>
                </ul>
                <span class="navbar-text">
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <span class="text-white mr-3">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                        <a href="../Controller/authcontroller.php?action=logout" class="text-white">
                            <span class="fas fa-sign-out-alt"></span> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-white">
                            <span class="fas fa-sign-in-alt"></span> Login
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
            <?php if (!empty($userRecommendations)): ?>
                <div class="mt-3">
                    <span class="badge badge-warning badge-lg">
                        <i class="fas fa-magic"></i> Personalized recommendations available based on your preferences!
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Session Filter -->
    <div class="session-filter">
        <div class="container">
            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                <label class="btn btn-outline-primary <?php echo $selectedSession === 'all' ? 'active' : ''; ?> flex-fill">
                    <input type="radio" name="session" value="all" <?php echo $selectedSession === 'all' ? 'checked' : ''; ?> 
                           onchange="updateFilters('session', 'all')"> All Sessions
                </label>
                <label class="btn btn-outline-primary <?php echo $selectedSession === 'Breakfast' ? 'active' : ''; ?> flex-fill">
                    <input type="radio" name="session" value="Breakfast" <?php echo $selectedSession === 'Breakfast' ? 'checked' : ''; ?>
                           onchange="updateFilters('session', 'Breakfast')"> Breakfast
                </label>
                <label class="btn btn-outline-primary <?php echo $selectedSession === 'Lunch' ? 'active' : ''; ?> flex-fill">
                    <input type="radio" name="session" value="Lunch" <?php echo $selectedSession === 'Lunch' ? 'checked' : ''; ?>
                           onchange="updateFilters('session', 'Lunch')"> Lunch
                </label>
                <label class="btn btn-outline-primary <?php echo $selectedSession === 'Dinner' ? 'active' : ''; ?> flex-fill">
                    <input type="radio" name="session" value="Dinner" <?php echo $selectedSession === 'Dinner' ? 'checked' : ''; ?>
                           onchange="updateFilters('session', 'Dinner')"> Dinner
                </label>
            </div>
        </div>
    </div>

    <!-- Preference Filter -->
    <div class="preference-filter">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <strong>Dietary Preference:</strong>
                </div>
                <div class="col-md-9">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-secondary btn-sm <?php echo $userPreference === 'all' ? 'active' : ''; ?>">
                            <input type="radio" name="preference" value="all" <?php echo $userPreference === 'all' ? 'checked' : ''; ?>
                                   onchange="updateFilters('preference', 'all')"> Show All
                        </label>
                        <label class="btn btn-outline-success btn-sm <?php echo $userPreference === 'vegetarian' ? 'active' : ''; ?>">
                            <input type="radio" name="preference" value="vegetarian" <?php echo $userPreference === 'vegetarian' ? 'checked' : ''; ?>
                                   onchange="updateFilters('preference', 'vegetarian')"> <i class="fas fa-leaf"></i> Vegetarian Only
                        </label>
                        <label class="btn btn-outline-danger btn-sm <?php echo $userPreference === 'non-vegetarian' ? 'active' : ''; ?>">
                            <input type="radio" name="preference" value="non-vegetarian" <?php echo $userPreference === 'non-vegetarian' ? 'checked' : ''; ?>
                                   onchange="updateFilters('preference', 'non-vegetarian')"> <i class="fas fa-drumstick-bite"></i> Non-Vegetarian Only
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Pricing Information -->
                <h3 class="mb-3"><i class="fas fa-money-bill-wave"></i> Pricing & Timing</h3>
                <?php if (!empty($pricing)): ?>
                    <?php foreach ($pricing as $price): ?>
                    <div class="price-card">
                        <h5><?php echo htmlspecialchars($price['session_name']); ?></h5>
                        <p class="mb-1">
                            <i class="fas fa-clock"></i> 
                            <?php echo date('g:i A', strtotime($price['start_time'])); ?> - 
                            <?php echo date('g:i A', strtotime($price['end_time'])); ?>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-calendar"></i> 
                            <?php echo htmlspecialchars($price['working_days']); ?>
                        </p>
                        <h4 class="text-primary mt-2">৳<?php echo number_format($price['price'], 2); ?></h4>
                        <small class="text-muted">Including VAT</small>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="price-card">
                        <p class="text-muted">Pricing information not available</p>
                    </div>
                <?php endif; ?>

                <!-- AI Recommendations -->
                <?php if (!empty($userRecommendations)): ?>
                <div class="price-card recommendation-card">
                    <h5><i class="fas fa-magic"></i> Recommended for You</h5>
                    <?php foreach (array_slice($userRecommendations, 0, 3) as $rec): ?>
                        <div class="mb-2">
                            <strong><?php echo htmlspecialchars($rec['item_name']); ?></strong>
                            <br><small><?php echo htmlspecialchars($rec['reason']); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Chef's Suggestions -->
                <div class="price-card chef-suggestions">
                    <h5><i class="fas fa-chef-hat"></i> Chef's Suggestions</h5>
                    <div id="suggestions-content">
                        <?php if ($userPreference === 'vegetarian'): ?>
                            <p><strong>For Vegetarians:</strong></p>
                            <ul class="list-unstyled">
                                <li>• Try our Palak Paneer with fresh naan</li>
                                <li>• Don't miss the Vegetable Biryani</li>
                                <li>• Perfect proteins: Dal Tadka & Rajma</li>
                                <li>• End with our signature Gulab Jamun</li>
                            </ul>
                        <?php elseif ($userPreference === 'non-vegetarian'): ?>
                            <p><strong>For Non-Vegetarians:</strong></p>
                            <ul class="list-unstyled">
                                <li>• Must try: Butter Chicken with rice</li>
                                <li>• Popular choice: Mutton Biryani</li>
                                <li>• Fresh catch: Grilled Salmon</li>
                                <li>• Tandoori specialties highly recommended</li>
                            </ul>
                        <?php else: ?>
                            <p><strong>Popular Choices:</strong></p>
                            <ul class="list-unstyled">
                                <li>• Butter Chicken (Non-Veg)</li>
                                <li>• Palak Paneer (Veg)</li>
                                <li>• Vegetable Biryani (Veg)</li>
                                <li>• Gulab Jamun (Dessert)</li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Make Reservation Button -->
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a href="indexv2.php#reserveform" class="btn btn-success btn-block btn-lg">
                        <i class="fas fa-calendar-check"></i> Make Reservation
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Login to Reserve
                    </a>
                <?php endif; ?>
            </div>

            <!-- Menu Items -->
            <div class="col-md-8">
                <?php 
                // Filter menu items based on preference
                function filterByPreference($items, $preference) {
                    if ($preference === 'vegetarian') {
                        return array_filter($items, function($item) {
                            return $item['is_vegetarian'] == 1;
                        });
                    } elseif ($preference === 'non-vegetarian') {
                        return array_filter($items, function($item) {
                            return $item['is_vegetarian'] == 0;
                        });
                    }
                    return $items;
                }

                if ($selectedSession === 'all'): 
                    $sessions = ['Breakfast', 'Lunch', 'Dinner'];
                    $hasAnyItems = false;
                    
                    foreach ($sessions as $session): 
                        $sessionItems = array_filter($menuItems, function($item) use ($session) {
                            return $item['session'] === $session;
                        });
                        $sessionItems = filterByPreference($sessionItems, $userPreference);
                        
                        if (!empty($sessionItems)):
                            $hasAnyItems = true;
                ?>
                    <h3 class="mb-3">
                        <i class="fas fa-<?php echo $session === 'Breakfast' ? 'coffee' : ($session === 'Lunch' ? 'hamburger' : 'wine-glass'); ?>"></i> 
                        <?php echo $session; ?> Menu
                    </h3>
                    <div class="menu-card">
                        <?php 
                        $currentCategory = '';
                        foreach ($sessionItems as $item): 
                            if ($currentCategory !== $item['category']):
                                $currentCategory = $item['category'];
                        ?>
                            <div class="category-header">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($currentCategory); ?>
                            </div>
                        <?php endif; ?>
                            <div class="menu-item">
                                <?php if (!empty($userRecommendations) && in_array($item['item_name'], array_column($userRecommendations, 'item_name'))): ?>
                                    <span class="recommended-badge"><i class="fas fa-star"></i> Recommended</span>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <?php echo htmlspecialchars($item['item_name']); ?>
                                            <?php if ($item['is_vegetarian']): ?>
                                                <span class="vegetarian-badge"><i class="fas fa-leaf"></i> Veg</span>
                                            <?php else: ?>
                                                <span class="non-veg-badge"><i class="fas fa-drumstick-bite"></i> Non-Veg</span>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($item['cuisine_type'])): ?>
                                                <span class="cuisine-tag"><?php echo htmlspecialchars($item['cuisine_type']); ?></span>
                                            <?php endif; ?>
                                        </h6>
                                        
                                        <?php if (!empty($item['description'])): ?>
                                            <small class="text-muted d-block mb-1"><?php echo htmlspecialchars($item['description']); ?></small>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex align-items-center flex-wrap">
                                            <?php if (!empty($item['spice_level']) && $item['spice_level'] !== 'Mild'): ?>
                                                <span class="spice-level mr-2">
                                                    <i class="fas fa-pepper-hot"></i> <?php echo htmlspecialchars($item['spice_level']); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($item['allergens'])): ?>
                                                <span class="allergen-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> Contains: <?php echo htmlspecialchars($item['allergens']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php 
                        endif;
                    endforeach; 
                    
                    if (!$hasAnyItems):
                ?>
                    <div class="empty-menu">
                        <i class="fas fa-utensils fa-3x mb-3 text-muted"></i>
                        <h4>No items found</h4>
                        <p>No menu items match your current dietary preference.</p>
                        <button class="btn btn-primary" onclick="updateFilters('preference', 'all')">
                            <i class="fas fa-list"></i> Show All Items
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php else: 
                    // Single session view
                    $filteredItems = filterByPreference($menuItems, $userPreference);
                ?>
                    <h3 class="mb-3">
                        <i class="fas fa-<?php echo $selectedSession === 'Breakfast' ? 'coffee' : ($selectedSession === 'Lunch' ? 'hamburger' : 'wine-glass'); ?>"></i>
                        <?php echo htmlspecialchars($selectedSession); ?> Menu
                    </h3>
                    
                    <?php if (empty($filteredItems)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No items found for your current dietary preference. 
                            <a href="javascript:void(0)" onclick="updateFilters('preference', 'all')" class="alert-link">Show all items</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($filteredItems as $category => $items): ?>
                            <div class="category-header">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($category); ?>
                            </div>
                            <div class="menu-card">
                                <?php foreach ($items as $item): ?>
                                    <div class="menu-item">
                                        <?php if (!empty($userRecommendations) && in_array($item['item_name'], array_column($userRecommendations, 'item_name'))): ?>
                                            <span class="recommended-badge"><i class="fas fa-star"></i> Recommended</span>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                                    <?php if ($item['is_vegetarian']): ?>
                                                        <span class="vegetarian-badge"><i class="fas fa-leaf"></i> Veg</span>
                                                    <?php else: ?>
                                                        <span class="non-veg-badge"><i class="fas fa-drumstick-bite"></i> Non-Veg</span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($item['cuisine_type'])): ?>
                                                        <span class="cuisine-tag"><?php echo htmlspecialchars($item['cuisine_type']); ?></span>
                                                    <?php endif; ?>
                                                </h6>
                                                
                                                <?php if (!empty($item['description'])): ?>
                                                    <small class="text-muted d-block mb-1"><?php echo htmlspecialchars($item['description']); ?></small>
                                                <?php endif; ?>
                                                
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <?php if (!empty($item['spice_level']) && $item['spice_level'] !== 'Mild'): ?>
                                                        <span class="spice-level mr-2">
                                                            <i class="fas fa-pepper-hot"></i> <?php echo htmlspecialchars($item['spice_level']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($item['allergens'])): ?>
                                                        <span class="allergen-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Contains: <?php echo htmlspecialchars($item['allergens']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                        Road no. 8A, House no. 42<br>
                        Dhanmondi, Dhaka<br>
                        Bangladesh<br>
                        <i class="fas fa-phone"></i>: +852 1234 5678<br>
                        <i class="fas fa-envelope"></i>: <a href="mailto:confusion@food.net">confusion@food.net</a>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Update filters and reload page
        function updateFilters(type, value) {
            const url = new URL(window.location);
            url.searchParams.set(type, value);
            
            // Add loading effect
            document.body.style.opacity = '0.7';
            document.body.style.pointerEvents = 'none';
            
            window.location.href = url.toString();
        }
        
        // Dynamic suggestions based on preference
        function updateSuggestions(preference) {
            const suggestions = {
                'vegetarian': {
                    title: 'For Vegetarians:',
                    items: [
                        '• Try our Palak Paneer with fresh naan',
                        '• Don\'t miss the Vegetable Biryani',
                        '• Perfect proteins: Dal Tadka & Rajma',
                        '• End with our signature Gulab Jamun'
                    ]
                },
                'non-vegetarian': {
                    title: 'For Non-Vegetarians:',
                    items: [
                        '• Must try: Butter Chicken with rice',
                        '• Popular choice: Mutton Biryani',
                        '• Fresh catch: Grilled Salmon',
                        '• Tandoori specialties highly recommended'
                    ]
                },
                'all': {
                    title: 'Popular Choices:',
                    items: [
                        '• Butter Chicken (Non-Veg)',
                        '• Palak Paneer (Veg)',
                        '• Vegetable Biryani (Veg)',
                        '• Gulab Jamun (Dessert)'
                    ]
                }
            };
            
            const content = suggestions[preference];
            if (content && document.getElementById('suggestions-content')) {
                const html = `
                    <p><strong>${content.title}</strong></p>
                    <ul class="list-unstyled">
                        ${content.items.map(item => `<li>${item}</li>`).join('')}
                    </ul>
                `;
                document.getElementById('suggestions-content').innerHTML = html;
            }
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Update suggestions based on current preference
            const currentPreference = '<?php echo $userPreference; ?>';
            updateSuggestions(currentPreference);
            
            // Add smooth scrolling for internal links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Add hover effects to menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
            
            // Add click handlers for dietary preference buttons
            document.querySelectorAll('input[name="preference"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    updateSuggestions(this.value);
                });
            });
        });
        
        // Add search functionality (bonus feature)
        function searchMenu() {
            const searchTerm = document.getElementById('menuSearch')?.value.toLowerCase();
            if (!searchTerm) return;
            
            document.querySelectorAll('.menu-item').forEach(item => {
                const itemName = item.querySelector('h6').textContent.toLowerCase();
                const itemDescription = item.querySelector('.text-muted')?.textContent.toLowerCase() || '';
                
                if (itemName.includes(searchTerm) || itemDescription.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.style.backgroundColor = '#fff3cd'; // Highlight found items
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Reset search
        function resetSearch() {
            document.querySelectorAll('.menu-item').forEach(item => {
                item.style.display = 'block';
                item.style.backgroundColor = '';
            });
            if (document.getElementById('menuSearch')) {
                document.getElementById('menuSearch').value = '';
            }
        }
        
        // Show loading animation
        function showLoading() {
            const loadingHtml = `
                <div class="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading menu items...</p>
                </div>
            `;
            document.querySelector('.col-md-8').innerHTML = loadingHtml;
        }
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + F to focus search (if search box exists)
            if (e.ctrlKey && e.key === 'f' && document.getElementById('menuSearch')) {
                e.preventDefault();
                document.getElementById('menuSearch').focus();
            }
            
            // Escape to reset search
            if (e.key === 'Escape') {
                resetSearch();
            }
        });
        
        // Add tooltips to badges (if Bootstrap tooltips are available)
        $(document).ready(function() {
            if (typeof $().tooltip === 'function') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
        
        // Monitor for changes in preferences and update recommendations
        function updateRecommendationsBasedOnPreference(preference) {
            const recommendationCard = document.querySelector('.recommendation-card');
            if (recommendationCard && preference !== 'all') {
                // You could make an AJAX call here to get filtered recommendations
                // For now, we'll just update the suggestions
                updateSuggestions(preference);
            }
        }
        
        // Add animation to filter changes
        function animateFilterChange() {
            const menuContainer = document.querySelector('.col-md-8');
            if (menuContainer) {
                menuContainer.style.transition = 'opacity 0.3s ease';
                menuContainer.style.opacity = '0.5';
                
                setTimeout(() => {
                    menuContainer.style.opacity = '1';
                }, 300);
            }
        }
        
        // Enhanced filter update with animation
        function updateFiltersWithAnimation(type, value) {
            animateFilterChange();
            setTimeout(() => {
                updateFilters(type, value);
            }, 150);
        }
        
        // Add print functionality
        function printMenu() {
            const printWindow = window.open('', '_blank');
            const menuContent = document.querySelector('.col-md-8').innerHTML;
            const styles = `
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .menu-card { border: 1px solid #ddd; margin-bottom: 20px; }
                    .category-header { background: #f8f9fa; padding: 10px; font-weight: bold; }
                    .menu-item { padding: 10px; border-bottom: 1px solid #eee; }
                    .vegetarian-badge, .non-veg-badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
                    .vegetarian-badge { background: #28a745; color: white; }
                    .non-veg-badge { background: #dc3545; color: white; }
                    @media print { body { margin: 0; } }
                </style>
            `;
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Menu - Ristorante Con Fusion</title>
                    ${styles}
                </head>
                <body>
                    <h1>Ristorante Con Fusion - Menu</h1>
                    <p>Printed on: ${new Date().toLocaleDateString()}</p>
                    ${menuContent}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>