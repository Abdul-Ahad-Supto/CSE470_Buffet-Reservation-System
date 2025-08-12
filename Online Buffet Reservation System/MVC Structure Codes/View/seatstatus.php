<?php
// View/seatstatus.php - Fixed version
session_start();
require_once '../Model/pdov2.php';
require_once '../Model/seatstatusmodal.php';
require_once '../Model/branchmodal.php';

$seatModel = new SeatStatusModel($pdo);
$branches = get_all_branches($pdo);

$selectedDate = $_GET['date'] ?? date('Y-m-d');
$selectedBranch = $_GET['branch'] ?? null;

$seatStatus = $seatModel->getSeatStatus($selectedDate, $selectedBranch);
$reservations = $seatModel->getReservationsByDate($selectedDate, $selectedBranch);

// Ensure variables are arrays
if (!is_array($seatStatus)) $seatStatus = [];
if (!is_array($branches)) $branches = [];
if (!is_array($reservations)) $reservations = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Seat Status - Ristorante Con Fusion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .status-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .availability-high {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .availability-medium {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }
        .availability-low {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        .seat-visual {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        .seat {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        .seat-available {
            background: #28a745;
        }
        .seat-reserved {
            background: #dc3545;
        }
        .reservation-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
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
                    <li class="nav-item"><a class="nav-link" href="menu.php"><span class="fa fa-cutlery"></span> Menu</a></li>
                    <li class="nav-item active"><a class="nav-link" href="#"><span class="fa fa-th"></span> Seat Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.html"><span class="fa fa-info"></span> About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contactus.php"><span class="fa fa-address-card"></span> Contact</a></li>
                </ul>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <span class="navbar-text text-white">
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                        <a href="../Controller/authcontroller.php?action=logout" class="text-white ml-3">
                            <span class="fa fa-sign-out"></span> Logout
                        </a>
                    </span>
                <?php else: ?>
                    <span class="navbar-text">
                        <a href="login.php" class="text-white">
                            <span class="fa fa-sign-in"></span> Login
                        </a>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="jumbotron">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Real-Time Seat Availability</h1>
                    <p>Check available seats for your preferred date and session</p>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="seatstatus.php" class="form-inline justify-content-center">
                <div class="form-group mx-2">
                    <label for="date" class="mr-2">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="<?php echo htmlspecialchars($selectedDate); ?>" min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group mx-2">
                    <label for="branch" class="mr-2">Branch:</label>
                    <select class="form-control" id="branch" name="branch">
                        <option value="">All Branches</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>" 
                                    <?php echo $selectedBranch == $branch['branch_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($branch['branch_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mx-2">
                    <i class="fa fa-search"></i> Check Status
                </button>
            </form>
        </div>

        <!-- Seat Status Display -->
        <div class="row">
            <?php
            // Replace the seat status display logic in your seatstatus.php
            // This section should come after the filter section

            // Seat Status Display with Fixed Logic
            $allSessions = ['BREAKFAST', 'LUNCH', 'DINNER'];

            // Group status by branch and ensure all sessions are represented
            $statusByBranch = [];

            // First, initialize all branches with all sessions (default values)
            foreach ($branches as $branch) {
                if (!$selectedBranch || $selectedBranch == $branch['branch_id']) {
                    $statusByBranch[$branch['branch_name']] = [];
                    
                    // Initialize all sessions with default values
                    foreach ($allSessions as $session) {
                        $statusByBranch[$branch['branch_name']][$session] = [
                            'session' => $session,
                            'total_seats' => 100,
                            'reserved_seats' => 0,
                            'available_seats' => 100,
                            'branch_id' => $branch['branch_id']
                        ];
                    }
                }
            }

            // Now override with actual data from database
            foreach ($seatStatus as $status) {
                if (isset($statusByBranch[$status['branch_name']])) {
                    $statusByBranch[$status['branch_name']][$status['session']] = [
                        'session' => $status['session'],
                        'total_seats' => $status['total_seats'],
                        'reserved_seats' => $status['reserved_seats'],
                        'available_seats' => $status['total_seats'] - $status['reserved_seats'],
                        'branch_id' => $status['branch_id'] ?? null
                    ];
                }
            }
            ?>

            <!-- Seat Status Display -->
            <div class="row">
                <?php if (empty($statusByBranch)): ?>
                    <div class="col-12">
                        <div class="no-data">
                            <i class="fa fa-calendar-times-o fa-3x mb-3"></i>
                            <h4>No data available</h4>
                            <p>Please select a valid date and branch to view seat availability.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($statusByBranch as $branchName => $sessions): ?>
                        <div class="col-md-6 col-lg-4">
                            <h4 class="mb-3"><?php echo htmlspecialchars($branchName); ?></h4>
                            
                            <?php 
                            // Sort sessions in proper order
                            $orderedSessions = [];
                            foreach ($allSessions as $sessionName) {
                                if (isset($sessions[$sessionName])) {
                                    $orderedSessions[] = $sessions[$sessionName];
                                }
                            }
                            
                            foreach ($orderedSessions as $session): 
                                $availableSeats = $session['available_seats'];
                                $totalSeats = $session['total_seats'];
                                $reservedSeats = $session['reserved_seats'];
                                $percentage = $totalSeats > 0 ? ($availableSeats / $totalSeats) * 100 : 0;
                                
                                if ($percentage >= 70) {
                                    $statusClass = 'availability-high';
                                } elseif ($percentage >= 30) {
                                    $statusClass = 'availability-medium';
                                } else {
                                    $statusClass = 'availability-low';
                                }
                                
                                // Session timing display
                                $sessionTimes = [
                                    'BREAKFAST' => '9:00 AM - 11:00 AM',
                                    'LUNCH' => '2:00 PM - 5:00 PM',
                                    'DINNER' => '7:00 PM - 10:00 PM'
                                ];
                            ?>
                            <div class="status-card <?php echo $statusClass; ?>">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($session['session']); ?></h5>
                                    <?php if ($reservedSeats > 0): ?>
                                        <span class="badge badge-light"><?php echo $reservedSeats; ?> booked</span>
                                    <?php endif; ?>
                                </div>
                                
                                <small class="d-block mb-3" style="opacity: 0.8;">
                                    <?php echo $sessionTimes[$session['session']] ?? 'Time TBD'; ?>
                                </small>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3><?php echo $availableSeats; ?>/<?php echo $totalSeats; ?></h3>
                                        <small>Available Seats</small>
                                    </div>
                                    <div class="text-right">
                                        <div class="progress" style="width: 100px; height: 10px; background-color: rgba(255,255,255,0.3);">
                                            <div class="progress-bar bg-white" style="width: <?php echo $percentage; ?>%"></div>
                                        </div>
                                        <small><?php echo round($percentage); ?>% Available</small>
                                    </div>
                                </div>
                                
                                <!-- Visual seat representation -->
                                <div class="seat-visual">
                                    <?php for ($i = 0; $i < min(25, $totalSeats); $i++): ?>
                                        <div class="seat <?php echo $i < $reservedSeats ? 'seat-reserved' : 'seat-available'; ?>" 
                                            title="Seat <?php echo $i + 1; ?>"></div>
                                    <?php endfor; ?>
                                    <?php if ($totalSeats > 25): ?>
                                        <small class="ml-2">+<?php echo $totalSeats - 25; ?> more</small>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Action buttons -->
                                <div class="mt-3">
                                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && $availableSeats > 0): ?>
                                        <a href="indexv2.php#reserveform" class="btn btn-light btn-sm">
                                            <i class="fa fa-calendar-plus-o"></i> Book Now
                                        </a>
                                    <?php elseif ($availableSeats <= 0): ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fa fa-ban"></i> Fully Booked
                                        </button>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-light btn-sm">
                                            <i class="fa fa-sign-in"></i> Login to Book
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Show reservation details for admin -->
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $reservedSeats > 0): ?>
                                        <button class="btn btn-outline-light btn-sm ml-1" onclick="showReservationDetails('<?php echo $branchName; ?>', '<?php echo $session['session']; ?>')">
                                            <i class="fa fa-eye"></i> Details
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
                    <!-- Recent Reservations (Admin View) -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && !empty($reservations)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3>Recent Reservations for <?php echo date('F j, Y', strtotime($selectedDate)); ?></h3>
                <div class="table-responsive reservation-list">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Branch</th>
                                <th>Session</th>
                                <th>Guests</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $res): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($res['reservation_id']); ?></td>
                                <td><?php echo htmlspecialchars($res['Customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($res['branch_name']); ?></td>
                                <td><?php echo htmlspecialchars($res['Session']); ?></td>
                                <td><?php echo htmlspecialchars($res['Numberofguest']); ?></td>
                                <td><?php echo htmlspecialchars($res['customer_phone']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $res['Status'] === 'confirm' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($res['Status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('H:i', strtotime($res['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Legend -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Legend</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <span class="seat seat-available mr-2"></span>
                                <small>Available Seats</small>
                            </div>
                            <div class="col-md-4">
                                <span class="seat seat-reserved mr-2"></span>
                                <small>Reserved Seats</small>
                            </div>
                            <div class="col-md-4">
                                <small><strong>Green:</strong> 70%+ available, <strong>Orange:</strong> 30-70%, <strong>Red:</strong> &lt;30%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
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
        // Auto-refresh every 30 seconds (optional)
        // setTimeout(function() {
        //     location.reload();
        // }, 30000);
        
        // Form validation
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                const date = $('#date').val();
                if (!date) {
                    alert('Please select a date');
                    e.preventDefault();
                    return false;
                }
                
                const selectedDate = new Date(date);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    alert('Please select a current or future date');
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
</body>
</html>