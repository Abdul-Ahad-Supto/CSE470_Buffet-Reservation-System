<?php 
session_start();
 ?>
 <!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap-social/bootstrap-social.css">
    <link rel="stylesheet" href="css/styles.css">
    <title></title>
</head>

<body>

	<nav class="navbar navbar-dark navbar-expand-sm  fixed-top">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#Navbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand mr-auto" href="#"><img src="img/logo.png" height="30" width="41"></a>
        <div class="collapse navbar-collapse" id="Navbar">
                <ul class="navbar-nav mr-auto">
                        <li class="nav-link active"><a class="nav-link" href="indexv2.php"><span class="fa fa-home"></span>Home</a></li>
                        <li class="nav-link"><a class="nav-link" href="aboutus.html"><span class="fa fa-info fa-lg"></span>About</a></li>
                        <li class="nav-link"><a class="nav-link" href="contactus.html"><span class="fa fa-address-card fa-lg"></span>Contact</a></li>
                    </ul>
                    <span class="navbar-text">
                        <a onclick="myfunction2()">
                        <span class="fa fa-sign-in"></span> Login</a>
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
                    <img src="img/logo.png" class="img-fluid">
                </div>
                <div class="col-12 col-sm align-self-center">
                    <a href="#reserveform" type="button" class="btn btn-block btn-success">Make Reservation</a>
                     <button type="button" class="btn btn-block btn-info">Menu</button>
                     <button type="button" class="btn btn-block btn-light" data-toggle="modal" data-target="#exampleModal">Cancel Reservation</button>
                </div>
            </div>
        </div>
    </header>
<div class="container">
    <div class="row row-content">
        <div class="col-12">
            <div class="container">
            <?php 
                                    unset($_SESSION['modal']);
                                    if (isset($_SESSION['message'])) {
                                        echo($_SESSION['message']);
                                        unset($_SESSION['message']);
                                        echo('<br>');
                                    }
                            ?>
                            <br>
            </div>                
            <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
    <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item">
      <img src="img/image1.jpg" class="d-block" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5>Mauro Colagreco</h5>
        <p>Award winning three-star Michelin chef with wide International experience having worked closely with whos-who in the culinary world, he specializes in creating mouthwatering Indo-Italian fusion experiences.</p>
      </div>
    </div>
    <div class="carousel-item active">
      <img src="img/image2.jpeg" class="d-block" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h2>Weekend Grand Buffet <span class="badge badge-danger">OFFER</span> <span class="badge badge-pill badge-default"></span></h2>
        <p>Featuring mouthwatering combinations with a choice of five different salads, six enticing appetizers, six main entrees and five choicest desserts. Free flowing bubbly and soft drinks. All for just 200tk per person. Call us on +8801925779647 for reservation.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="img/image3.jpeg" class="d-block" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h2>Indoppizza <span class="badge badge-danger">NEW</span> <span class="badge badge-pill badge-default"></span></h2>
        <p>A unique combination of Indian Uthappam (pancake) and Italian pizza, topped with Cerignola olives, ripe vine cherry tomatoes, Vidalia onion, Guntur chillies and Buffalo Paneer.</p>
      </div>
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
        </div>
    </div>

    <div class="row row-content">

        <div class="col-7 col-sm-6">
             <div>
                <h2>Our Branch Location</h2>
                            <div id="accordion">
                        <div class="card">
                                <div class="card-header" role="tab" id="peterhead">
                                <h3 class="mb-0">
                                    <a data-toggle="collapse" data-target="#sakib">
                                    Dhanmondi Branch<small></small>
                                    </a>
                                </h3>
                                </div>
                                <div class="collapse show" id="sakib" data-parent="#accordion">
                                    <div class="card-body">
                                        <p class="d-none d-sm-block">
                                            Address: RoadNo.8A, HouseNo.42 Keari Plaza
                                            (opposite of Unimat, 2ndFloor) Dhaka 1209<br>
                                            Manager: Rafsan Khan <br>
                                            Contact: 029345776, +8801925779632
                                    </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                    <div class="card-header" role="tab" id="dannyhead">
                                    <h3 class="mb-0">
                                        <a class="collapsed" data-toggle="collapse" data-target="#sadman">
                                        Uttara Branch<small></small>
                                        </a>
                                    </h3>
                                </div>
                                <div class="collapse" id="sadman" data-parent="#accordion">
                                    <div class="card-body">
                                        <p class="d-none d-sm-block">
                                            Address: Sector 13, 40 Gareeb-e-Nawaz Ave, Dhaka 1230<br>
                                            Manager: Assaduzzaman Sumon <br>
                                            Contact: 029345456, +8801834542089
                                        </p>
                                    </div>
                                </div>
                            </div>                
                            <div class="card">
                                    <div class="card-header" role="tab" id="rkbm">
                                    <h3 class="mb-0">
                                        <a class="collapsed" data-toggle="collapse" data-target="#RKBM">
                                        Banani Branch<small></small>
                                        </a>
                                    </h3>
                                </div>
                                <div class="collapse" id="RKBM" data-parent="#accordion">
                                    <div class="card-body">
                                        <p class="d-none d-sm-block">
                                            Address: RoadNo.13E, House#135, Opposite of Prescription Point, Dhaka 1213 <br>
                                            Manager: Joy Biswas <br>
                                            Contact: 029345887, +8801925779986
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                    <div class="card-header" role="tab" id="albertohead">
                                    <h3 class="mb-0">
                                        <a class="collapsed" data-toggle="collapse" data-target="#mauro">
                                        Mirpur Branch<small></small>
                                        </a>
                                    </h3>
                                </div>
                                <div class="collapse" id="mauro" data-parent="#accordion">
                                    <div class="card-body">
                                        <p class="d-none d-sm-block">
                                        Address: 2nd Floor, 25, Momota Plaza, Senpara, Mirpur-2, Dhaka-1216<br>
                                        Manager: Nusrat Mahima <br>
                                        Contact: 029345643, +8801711188876
                                        </p>
                                    </div>
                                </div>
                            </div>              
                </div>

            
            </div>
            
        </div>
        <div class="col-5 col-sm-6">
            <h2>Timing &amp; Prices</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>&nbsp;</th>
                            <th>Opening Hour</th>
                            <th>Working Days</th>
                            <th>Price(Including Vat)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Breakfast</th>
                            <td>9am-11am</td>
                            <td>Sunday-Thursday</td>
                            <td>500.00TK</td>
                        </tr>
                        <tr>
                            <th >Lunch</th>
                            <td>2pm-5pm</td>
                            <td>Friday-Thursday</td>
                            <td>600.00TK</td>
                        </tr>
                        <tr>
                            <th>Dinner</th>
                            <td>7pm-10pm</td>
                            <td>Friday-Thursday</td>
                            <td>700.00TK</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
   </div> 



   <div class="row row-content" align-items-center>
        <div class="col-12 col-sm-9 offset-1">
             <div class="card" id="reserveform">
                 <h3 class="card-header bg-success text-white align-items-center">Make Reservation</h3>
                <div class="card-body">
                    <form action="makereservationcontroller.php" method="POST">
                            <div class="form-group row">
                                    <label for="noofguest" class="col-md-2 col-form-label ">Number of Guests</label>
                                <div class="col-md-1">
                                    <input type="radio" class="form-control"  value="1" name="numberofguest">1
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="2" name="numberofguest">2
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="3" name="numberofguest">3
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="4" name="numberofguest">4
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="5" name="numberofguest">5
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="6" name="numberofguest">6
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="6" name="numberofguest">7
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="6" name="numberofguest">8
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="6" name="numberofguest">9
                                </div>
                                <div class="col-md-1">
                                        <input type="radio" class="form-control" value="6" name="numberofguest">10
                                </div>
                                </div>
                                <div class="form-group row">
                                        <label for="nameandemail" class="col-12 col-md-2 col-form-label">Name and Email</label>
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="form-control" id="nameandemail" name="name" placeholder="Fullname">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="email" class="form-control" id="nameandemail" name="email" placeholder="Email">
                                    </div>
                                    </div>
                                <div class="form-group row">
                                        <label for="phoneandaddress" class="col-12 col-md-2 col-form-label">Phone and Address</label>
                                    <div class="col-12 col-md-4">
                                        <input type="number" class="form-control" id="phoneandaddress" name="phone" placeholder="Phone number">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="form-control" id="phoneandaddress" name="address" placeholder="Address">
                                    </div>
                                    </div>    
                                <div class="form-group row">
                                        <label for="dateandtime" class="col-12 col-md-2 col-form-label">Date and Session</label>
                                    <div class="col-12 col-md-4">
                                        <input type="date" class="form-control" id="dateandtime" name="date" placeholder="Date">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="form-control" id="dateandtime" name="session" placeholder="Session">
                                    </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="branch" class="col-12 col-md-2 col-form-label">Branch</label>
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="form-control" id="branch" name="branch" placeholder="Branch Name">
                                    </div>
                                    </div>
                                    <div class="form-group row offset-2">
                                                <button type="submit" class="btn btn-primary">Reserve</button>
                                            </div>
                                        </div>
                    </form>

                </div>

             </div>

        </div>

    </div>


</div>


<div id="loginModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="content">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Login </h4>
                <button type="button" class="close" onclick="myfunction3()">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                        <div class="form-group col-sm-4">
                                <label class="sr-only" for="exampleInputEmail3">Email address</label>
                                <input type="email" class="form-control form-control-sm mr-1" id="exampleInputEmail3" placeholder="Enter email">
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="sr-only" for="exampleInputPassword3">Password</label>
                            <input type="password" class="form-control form-control-sm mr-1" id="exampleInputPassword3" placeholder="Password">
                        </div>
                        <div class="col-sm-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                                <label class="form-check-label"> Remember me
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="button" class="btn btn-secondary btn-sm ml-auto" onclick="myfunction3()">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm ml-1">Sign in</button>        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Cancel Reservation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="card" id="reserveform">
                 <h3 class="card-header bg-info text-white align-items-center">Cancel Reservation</h3>
                <div class="card-body">
                    <form action="cancelreservationcontroller.php" method="POST">
                                <div class="form-group row">
                                        <label for="nameandemail" class="col-12 col-md-2 col-form-label">Name andPhone</label>
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="form-control" id="nameandphone" name="name" placeholder="Fullname">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="number" class="form-control" id="nameandphone" name="phone" placeholder="Phone Number">
                                    </div>
                                    </div>
  
                                <div class="form-group row">
                                        <label for="dateandtime" class="col-12 col-md-2 col-form-label">Date and Session</label>
                                    <div class="col-12 col-md-6">
                                        <input type="date" class="form-control" id="dateandtime" name="date" placeholder="Date">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="form-control" id="dateandtime" name="session" placeholder="Session">
                                    </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="branch" class="col-12 col-md-3 col-form-label">Branch</label>
                                    <div class="col-12 col-md-5">
                                        <input type="text" class="form-control" id="branch" name="branch" placeholder="Branch Name">
                                    </div>
                                    </div>
                                    <div class="form-group row offset-1">
                                                <button type="submit" class="btn btn-primary">Reserve</button>
                                            </div>
                                        </div>
                    </form>
                </div>
             </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                        <li><a href="#">Home</a></li>
                        <li><a href="aboutus.html">About</a></li>
                        <li><a href="contactus.html">Contact</a></li>
                    </ul>
                </div>
                <div class="col-7 col-sm-5">
                    <h5>Our Office Address</h5>
                    <address>
		              Roadno.8A, Houseno.42<br>
		              Dhanmondi,(opposite of unimat,3rdFloor) Dhaka<br>
		              Bangladesh<br>
		              <i class="fa fa-phone fa-lg"></i>: +852 1234 5678<br>
		              <i class="fa fa-fax fa-lg"></i>: +852 8765 4321<br>
		              <i class="fa fa-envelope fa-lg"></i>: <a href="mailto:confusion@food.net">confusion@food.net</a>
		           </address>
                </div>
                <div class="col-12 col-sm-4 align-self-center">
                    <div class="text-center">
                        <a class="btn btn-social-icon btn-google" href="http://google.com/+"><i class="fa fa-google-plus fa-lg"></i></a>
                        <a class="btn btn-social-icon btn-facebook" href="http://www.facebook.com/profile.php?id="><i class="fa fa-facebook fa-lg"></i></a>
                        <a class="btn btn-social-icon btn-linkedin"href="http://www.linkedin.com/in/"><i class="fa fa-linkedin fa-lg"></i></a>
                        <a class="btn btn-social-icon btn-twitter"href="http://twitter.com/"><i class="fa fa-twitter fa-lg"></i></a>
                        <a class="btn btn-social-icon btn-google" href="http://youtube.com/"><i class="fa fa-youtube fa-lg"></i></a>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script> 
    <script>
    function myfunction2(){
        $('#loginModal').modal('show');
    }
    function myfunction3(){
        $('#loginModal').modal('hide');
    }
    </script>

</body>
</html>