<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caranellacake";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

if (isset($_SESSION['customerID']) && !empty($_SESSION['customerID'])) {
    $customerID = $_SESSION['customerID'];
    $customerData = getCustomerData($conn, $customerID);
    $customerName = $customerData['name'];
    $customerImage = $customerData['image'];

} else {
    // إذا لم يكن مسجل دخول، تعيين القيمة إلى "Login"
    $customerID = "login";
    $customerName = "Login";
    $customerImage = "img/customers/default.png"; // صورة افتراضية
}
// الحصول على عدد المنتجات في عربة العميل
$productCount = getCartProductCount($conn, $customerID);
?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caranellacake";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

function getCustomerData($conn, $customerID) {

// دالة لجلب بيانات العميل
    $sql_customer = "SELECT name, customerImage FROM customer WHERE customerID = ?";
    $stmt_customer = $conn->prepare($sql_customer);

    if (!$stmt_customer) {
        echo "خطأ في تحضير استعلام بيانات العميل: " . $conn->error;
        return ['name' => 'Login', 'image' => 'img/customers/default.png'];
    }

    $stmt_customer->bind_param("i", $customerID);
    $stmt_customer->execute();
    $result_customer = $stmt_customer->get_result();

    if ($result_customer->num_rows > 0) {
        $row_customer = $result_customer->fetch_assoc();
        $customerName = $row_customer['name'];
        $customerImage = $row_customer['customerImage'];
    } else {
        $customerName = "Login";
        $customerImage = "img/customers/default.png"; // صورة افتراضية في حالة عدم العثور على صورة
    }

    $stmt_customer->close();

    return ['name' => $customerName, 'image' => $customerImage];
}

// دالة لجلب عدد المنتجات في عربة العميل
function getCartProductCount($conn, $customerID) {
    $sql_cart = "
        SELECT COUNT(*)
        FROM cart_product cp
        JOIN cart c ON cp.cartID = c.cartID
        WHERE c.customerID = ?";

    $stmt_cart = $conn->prepare($sql_cart);

    if (!$stmt_cart) {
        echo "خطأ في تحضير استعلام عدد المنتجات في العربة: " . $conn->error;
        return 0;
    }

    $stmt_cart->bind_param("i", $customerID);
    if (!$stmt_cart->execute()) {
        echo "فشل تنفيذ استعلام عدد المنتجات: " . $stmt_cart->error;
        $stmt_cart->close();
        return 0;
    }

    $rowCount = 0;
    $stmt_cart->bind_result($rowCount);

    if (!$stmt_cart->fetch()) {
        $rowCount = 0; // تعيين قيمة افتراضية إذا لم يتم العثور على بيانات
    }

    $stmt_cart->close();

    return $rowCount;
}

// استخدام الدوال



$conn->close();
?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Cake Template">
    <meta name="keywords" content="Cake, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Caramella Cake</title>

    <!--Icon-->
    <link rel="shortcut icon"  href="img\icon\lloo.png">
    <!--Bootstrap & OwlCarousel -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cakeshopco.com/assets/sass/general.min.css" rel="stylesheet" />
    <link href="https://cakeshopco.com/assets/bootstrap-toastr/toastr.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.3.1/swiper-bundle.css" integrity="sha512-cAtZ0Luj6XlQ7YGgi5mPW0szI2z/2+btPjOqVEqK3z4h1/qojUwvQyTcocgKKOFv8noUFH5GOuhheX7PeDwwPA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">


    <link href="https://cakeshopco.com/assets/css/pages/pagesOther/_CustomModalSearch.css" rel="stylesheet" />
    <link href="https://cakeshopco.com/assets/css/pages/pagesOther/_footer.css" rel="stylesheet" />
    <link href="https://cakeshopco.com/assets/css/pages/pagesOther/_layoutStyle.css" rel="stylesheet" />

    <style>

        /* a {
            font-family: sans-serif !important;
            font-weight: bold;
        }*/


        /* .hide {
             display: none;
             opacity: 0;
             color: red;
             height: 0;
             cursor: pointer
         }*/
        .hide_cart-d {
            display: none;
            opacity: 0;
            color: red;
            height: 0;
            cursor: pointer
        }

        .myDIV {
            cursor: pointer;
            color: burlywood;
            font-weight: bold;
        }

        .myDIV:hover {
            cursor: pointer;
            color: #e34c80;
        }

        .show {
            display: block;
            opacity: 1;
            height: 100%;
        }



        .faq-container {
            margin: 0 auto;
            max-width: 600px;
        }

        .faq {
            background-color: transparent;
            border: 1px solid #9fa4a8;
            border-radius: 10px;
            margin: 20px 0;
            overflow: hidden;
            padding: 30px;
            position: relative;
            transition: 0.3s ease;
        }

        .faq.active {
            background-color: #fff;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1), 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .faq.active::before,
        .faq.active::after {
            color: #2ecc71;
            /* content: "\f075";*/
            font-family: "Font Awesome 6 Free";
            font-size: 7rem;
            left: 20px;
            opacity: 0.2;
            position: absolute;
            top: 20px;
            z-index: 0;
        }

        .faq.active::before {
            color: #3498db;
            left: -30px;
            top: -10px;
            transform: rotateY(180deg);
        }

        .faq-title {
            margin: 0 35px 0 0;
        }

        .faq-text {
            display: none;
            margin: 30px 0 0;
        }

        .faq.active .faq-text {
            display: block;
        }

        .faq-toggle {
            align-items: center;
            background-color: transparent;
            border: 0;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            font-size: 1rem;
            height: 30px;
            justify-content: center;
            padding: 0;
            position: absolute;
            right: 30px;
            top: 30px;
            width: 30px;
        }

        .faq-toggle .fa-times,
        .faq.active .faq-toggle .fa-chevron-down {
            display: none;
        }

        .faq.active .faq-toggle .fa-times {
            color: #fff;
            display: block;
        }

        .faq-toggle .fa-chevron-down {
            display: block;
        }

        .faq.active .faq-toggle {
            background-color: #9fa4a8;
        }

        .logo {
            position: relative;
            top: 20px;
            width: 250px;
            height: 250px;
            transform: translateY(20px);
        }




    </style>
    <style>







        .footer {
            position: relative;
            background-image: url('img/footer.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 192, 203, 0.5);
            z-index: 1;
        }

        .footer > * {
            position: relative;
            z-index: 2;
        }


        @keyframes showBars {
            0% { opacity: 0; background-position: -400% 7%, 500% 21%, -400% 35%, 500% 49%, -400% 63%, 500% 77%, -400% 91% ; }
            14% { background-position: 0% 7%, 500% 21%, -400% 35%, 500% 49%, -400% 63%, 500% 77%, -400% 91% ; }
            28% { background-position: 0% 7%, 100% 21%, -400% 35%, 500% 49%, -400% 63%, 500% 77%, -400% 91% ; }
            42% { background-position: 0% 7%, 100% 21%,    0% 35%, 500% 49%, -400% 63%, 500% 77%, -400% 91% ; }
            56% { background-position: 0% 7%, 100% 21%,    0% 35%, 100% 49%, -400% 63%, 500% 77%, -400% 91% ; }
            70% { background-position: 0% 7%, 100% 21%,    0% 35%, 100% 49%,    0% 63%, 500% 77%, -400% 91% ; }
            84% { background-position: 0% 7%, 100% 21%,    0% 35%, 100% 49%,    0% 63%, 100% 77%, -400% 91% ; }
            98%, 100% { opacity: 1; background-position: 0% 7%, 100% 21%, 0% 35%, 100% 49%, 0% 63%, 100% 77%, 0% 91%; }
        }


        @keyframes showText {
            0% { opacity: 0; transform: translate(0, -100%); }
            20% { opacity: 0; }
            100% { opacity: 1; transform: translate(0, 0); }
        }

        div.ccc {
            position: relative;
            transform: translate(-100%, 0);
            opacity: 0;
            animation: showText 2s 1;
            animation-fill-mode: forwards;
            animation-delay: 3.5s;
            text-align: center;
        }





        @media all and (min-width: 768px) {

            @keyframes showBarsBig {
                0% { opacity: 0; background-position: 7% -400%, 21% 500%, 35% -400%, 49% 500%, 63% -400%, 77% 500%, 91% -400%; }
                14% { background-position: 7% 0%, 21% 500%, 35% -400%, 49% 500%, 63% -400%, 77% 500%, 91% -400%; }
                28% { background-position: 7% 0%, 21% 100%, 35% -400%, 49% 500%, 63% -400%, 77% 500%, 91% -400%; }
                42% { background-position: 7% 0%, 21% 100%, 35% 0%, 49% 500%, 63% -400%, 77% 500%, 91% -400%; }
                56% { background-position: 7% 0%, 21% 100%, 35% 0%, 49% 100%, 63% -400%, 77% 500%, 91% -400%; }
                70% { background-position: 7% 0%, 21% 100%, 35% 0%, 49% 100%, 63% 0%, 77% 500%, 91% -400%; }
                84% { background-position: 7% 0%, 21% 100%, 35% 0%, 49% 100%, 63% 0%, 77% 100%, 91% -400%; }
                98%, 100% { opacity: 1; background-position: 7% 0%, 21% 100%, 35% 0%, 49% 100%, 63% 0%, 77% 100%, 91% 0%; }
            }

            @keyframes showTextBig {
                0% { opacity: 0; transform: translate(-100%, 0); }
                20% { opacity: 0; }
                100% { opacity: 1; transform: translate(0vw, 0); }
            }



            section::after {

                animation-name: showBarsBig;
            }

            div.ccc{
                animation-name: showTextBig;

            }


        }

        @media (prefers-reduced-motion) {
            section::after {
                animation: none !important;
            }

            @keyframes showTextReduced {
                0% { opacity: 0; }
                100% { opacity: 1; }
            }


            div.ccc {
                transform: translate(0,0);
                animation-name: showTextReduced;
                animation-delay: 0.2s !important;
            }
        }



    </style>
    <!-- Include Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Custom CSS -->

    <!-- Include Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Custom CSS -->


    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Owl Carousel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>



    <link href="https://cakeshopco.com/assets/css/pages/IndexPage/_tabPills.css" rel="stylesheet" />
    <link href="https://cakeshopco.com/assets/css/pages/IndexPage/index_custom.css" rel="stylesheet" />
    <link href="https://cakeshopco.com/assets/css/pages/IndexPage/_card_category.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cakeshopco.com/assets/css/pages/IndexPage/_ShopProduct.css" rel="stylesheet" />
    <style>
    </style>


    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
          rel="stylesheet">


    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="css/barfiller.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <style>
        .drop-down2 {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu2 {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: rgba(128, 128, 128, 0.7);
            padding: 0;
            list-style: none;
            z-index: 1000;
            border-radius: 3px;
            width: 200px;
        }

        .dropdown-menu2 li {
            margin: 0;
        }

        .dropdown-menu2 li a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #fff;
            font-size: 20px; /* Smaller font size */
        }

        .dropdown-menu2 li a:hover {
            background-color: rgba(200, 200, 200, 0.2); /* Lighter gray on hover */
        }

        /* Show the dropdown menu when hovering over the parent element */
        .drop-down2:hover .dropdown-menu2 {
            display: block;
        }
        .header__customer-info {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f8f9fa; /* اختر لون الخلفية المناسب */
            border-bottom: 2px solid #ddd; /* تحديد حدود الصورة */
        }

        .customer-image {
            width: 40px !important;
            height: 40px !important;
            overflow: hidden; /* إخفاء الأجزاء الزائدة من الصورة */
            border-radius: 50%; /* جعل الصورة دائرية */
            margin-right: 10px; /* مسافة بين الصورة واسم العميل */
        }

        .customer-image img {
            width: 100%; /* ملء عرض العنصر الحاوي */
            height: auto; /* الحفاظ على نسبة العرض إلى الارتفاع */
            display: block; /* إزالة المسافة السفلية تحت الصورة */
        }


        @media (max-width: 768px) {
            .header__customer-info {
                padding: 5px;
                flex-direction: column;
                align-items: flex-start;
            }

            .customer-image {
                width: 50px;
                height: 50px;
            }

        }


    </style>



</head>

<body>
<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>

<!-- Header Section Begin -->
<header class="header">
    <div class="header__top">
        <a href="https://wa.me/+970595729583" class="whatsapp-btn" target="_blank">
            <i class="bi bi-whatsapp"></i>
        </a>
        <!--top of the page بالتركوازي فوق -->
        <div class="container-fluid p-0" id="navbar2">

            <nav class="navbar navbar-expand-sm">

                <div class="container justify-content justify-content-around">
                    <button class="navbar-toggler " type="button" data-bs-toggle="collapse" data-bs-target=".navbar-collapse" aria-controls="#navbarSupportedContent2" aria-expanded="true" aria-label="Toggle navigation" fdprocessedid="8z7qx9">

                        <div class="" onclick="myFunction(this)">
                            <div class="bar1"></div>
                            <div class="bar2"></div>
                            <div class="bar3"></div>
                        </div>
                    </button>
                    <!--اللوجو -->
                    <a href="index.php">
                        <img class="logo" src="img/logo1.png"  />
                    </a>

                    <button type="button" class="hide_cart cartimage col-md-3 m-0 p-0" data-bs-toggle="modal" data-bs-target="#cart-modal">
                        <h6 class="cart_counter"><?php echo $productCount ?></h6> <!-- العداد هنا -->
                        <img class="" src="/assets/images/shopping-cart.png">
                    </button>


                    <div class="navbar-collapse collapse mobile-menu" id="navbarSupportedContent2">
                        <div class="main-om">
                            <!-- Sidebar -->
                            <nav id="sidebars">
                                <div class="sidebar-header d-none">
                                    <div class="container d-none">
                                        <div class="row align-items-center ">
                                            <div class="col-12 text-center bg-dark rounded d-none ">

                                                <a class="navbar-brand" href="https://cakeshopco.com/home/index">
                                                    <img src="https://cakeshopco.com/assets/images/Final Logo.png" style="background: #e34c80;" />

                                                </a>

                                            </div>


                                        </div>
                                    </div>
                                </div>

                                <div class="faq-container">
                                    <div class="faq active">
                                        <h5 class="faq-title">pages Main</h5>
                                        <div class="faq-text">
                                            <ul class="list-unstyled components links">
                                                <li class="">
                                                    <a href="https://cakeshopco.com/home"><i class="fa fa-home px-2"></i><span>Home</span> </a>
                                                </li>
                                                <li>
                                                    <a href="https://cakeshopco.com/Shop/ProductList"><i class="fa fa-shopping-bag px-2"></i><span>Products</span> </a>
                                                </li>
                                                <li>
                                                    <a href="https://cakeshopco.com/home/About"><i class="fa fa-book-open px-2"></i><span>About us</span></a>
                                                </li>
                                                <li>
                                                    <a href="https://cakeshopco.com/home/contact"><i class="fa fa-phone px-2"></i><span>Contact us</span></a>
                                                </li>
                                            </ul>
                                        </div>
                                        <button class="faq-toggle">
                                            <i class="fas fa-chevron-down"></i>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="faq-container">
                                    <div class="faq active">
                                        <h5 class="faq-title">User Information</h5>
                                        <div class="faq-text">
                                            <ul class="list-unstyled components links">
                                                <li>
                                                    <a href="https://cakeshopco.com/home/login"><i class="fa fa-user-check px-2"></i><span>Login</span> </a>
                                                </li>
                                            </ul>

                                        </div>
                                        <button class="faq-toggle">
                                            <i class="fas fa-chevron-down"></i>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <ul class="list-unstyled mb-3">
                                </ul>
                                <div class="faq-container">
                                    <div class="faq active">
                                        <h5 class="faq-title">Social Media</h5>
                                        <div class="faq-text">
                                            <div class="social-icons py-3">
                                                <span><a href="https://www.facebook.com/theCakeShop.2010/" target="_blank" title=""><i class="fa-brands fa-facebook-square"></i></a></span>
                                                <span><a href="https://www.instagram.com/thecakeshop_company/?utm_source=ig_web_button_share_sheet&amp;igshid=OGQ5ZDc2ODk2ZA==" target="_blank" title=""><i class="fa-brands fa-twitter-square"></i></a></span>
                                                <span><a href="https://www.facebook.com/theCakeShop.2010/" target="_blank" title=""><i class="fa-brands fa-instagram"></i></a></span>
                                            </div>
                                        </div>

                                        <button class="faq-toggle">
                                            <i class="fas fa-chevron-down"></i>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="sidebar-header py-5">
                                    <div class="container">
                                        <div class="row align-items-center ">
                                            <div class="col-12 text-center  rounded ">

                                                <img src="https://cakeshopco.com/assets/images/FinalLogo.png" class="w-100" />


                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>


                    <div class="navbar-collapse collapse hide-mobile-menu" id="">
                        <ul class="navbar-nav justify-content-md-center col-md-9 mb-lg-0">

                            <link href="https://cakeshopco.com/assets/css/pages/pagesOther/_MainMenuStyle.css" rel="stylesheet" />
                            <style>

                            </style>
                            <!--                    المنيو -->
                            <nav id="BestNav">
                                <ul>
                                    <li><a href='index.php'>Home</a></li>
                                    <li><a href='about.php'>About us</a></li>
                                    <li class='drop-down'>
                                        <a href="shop.php">Shop <i class="fa-solid fa-angle-down fs-small"></i></a>
                                        <ul>
                                            <li><a href="shop.php?category=Ready cakes">Ready Cakes</a></li>
                                            <li><a href="shop.php?category=Mini cake">Mini Cake</a></li>
                                            <li><a href="shop.php?category=Cheese cake">Cheese Cake</a></li>
                                            <li><a href="shop.php?category=Cake pieces">Cake Pieces</a></li>
                                            <li><a href="shop.php?category=Flowers">Flowers</a></li>
                                            <li><a href="shop.php?category=Catering">Catering</a></li>
                                            <li><a href="shop.php?category=Decorated cake">Decorated Cake</a></li>
                                        </ul>
                                    </li>
                                    <li><a href='blog.php'>Blog</a></li>
                                    <li><a href='contact.php'>Contact us</a></li>

                                </ul>
                            </nav>


                        </ul>



                        <div class="navbar-header  col-md-3 " id="web">
                            <div class="customer-image">
                                <img src="<?php echo htmlspecialchars($customerImage); ?>" alt="<?php echo htmlspecialchars($customerName); ?>">
                            </div>

                            <!-- Log In Dropdown Button -->
                            <li class="drop-down2">
                                <!-- عرض اسم العميل أو "Login" إذا لم يكن مسجلًا -->
                                <a class="color-pink text-decoration-none" href="<?php echo $customerID === 'login' ? 'registrationFront.php' : '#'; ?>">
                                    <?php echo htmlspecialchars($customerName); ?>
                                </a>

                                <!-- قائمة منسدلة -->
                                <?php if ($customerID !== 'login'): ?>
                                    <!-- إذا كان مسجل دخول، تظهر القائمة المنسدلة -->
                                    <ul id="dropdownMenu" class="dropdown-menu2">
                                        <li><a href="edite-profile.php">Edit Profile</a></li>
                                        <li><a href="wishlist.php">WishList</a></li>
                                        <li><a href="logout.php">Sign out</a></li> <!-- تعديل تسجيل الخروج -->
                                    </ul>
                                <?php endif; ?>
                            </li>

                            <!-- عربة التسوق -->
                            <button type="button" id="butSearchModal" class="button cartimage col-md-3 m-0 p-0" data-bs-toggle="modal" data-bs-target="#cart-modal">
                                <h6 class="cart_counter"><?php echo "$productCount"?></h6>
                                <img id="cartImage" src="img/icons8-line-24 (1).png" width="48" height="28">
                            </button>

                            <script>
                                document.getElementById('butSearchModal').addEventListener('click', function() {
                                    // الحصول على قيمة $customerName من PHP
                                    var customerName = "<?php echo htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8'); ?>";

                                    // التحقق مما إذا كانت القيمة تساوي "Login"
                                    if (customerName.trim() === "Login") {
                                        window.location.href = 'registrationFront.php';
                                    } else {
                                        window.location.href = 'checkout.php';
                                    }
                                });
                            </script>

                            <!-- كبسة البحث -->
                            <div class="searchbar col-md-3 m-0 p-0">
                                <a href='shop.php'>
                                    <i class="fa fa-search text-white" id="f-search" aria-hidden="true"></i>
                                </a>
                                <form action="" class="togglesearch d-none">
                                    <input type="text" placeholder="Search..." name="search" id="search" class="search_input" autocomplete="off">
                                    <button type="submit" class="bg-transparent border-0"><i class="fa fa-arrow-right text-black-50"></i></button>
                                </form>
                            </div>
                        </div>                    </div>

                </div>
            </nav>
        </div>



    </div>

</header>
<!-- Header Section End -->


<!-- Breadcrumb Begin -->
<section class="hero" style="position: relative;background-color: #ffffff; overflow: hidden; padding-top: 1px; height:430px; margin-top: 72px; background-image: url('img/ss.jpg'); background-size: cover; background-position: center;">
    <!-- Transparent Banner -->
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(255, 255, 255, 0.7); padding: 20px; border-radius: 10px; z-index: 4;">
        <h1 style="font-size: 36px; color: #f4cccc; text-align: center; font-family: 'scr' !important; text-shadow: 2px 2px 5px rgb(96,42,48);">About Us <br> Home / About Us</h1>
    </div>

    <!-- SVG Overlay -->
    <div style="position: absolute; bottom: 0; width: 100%; z-index: 3;">
        <svg id="" preserveAspectRatio="xMidYMax meet" class="svg-separator sep3" viewBox="0 0 1600 100" style="display: block; data-height:auto ; width:100%; position: absolute; bottom: 0; left: 0; right: 0;">
            <path class="" style="opacity: 1; fill: #f6cece;" d="M-40,71.627C20.307,71.627,20.058,32,80,32s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,26H-60V72L-40,71.627z"></path>
            <path class="" style="opacity: 1; fill: #F1F1F1;" d="M-40,83.627C20.307,83.627,20.058,44,80,44s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,14H-60V84L-40,83.627z"></path>
            <path class="" style="fill: rgb(241,241,241);" d="M-40,95.627C20.307,95.627,20.058,56,80,56s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,138H-60V96L-40,95.627z"></path>
        </svg>
    </div>
</section>


<!-- Breadcrumb End -->

<!-- About Section Begin -->
<section class="about spad" style="position: relative; overflow: hidden; background-size: cover; background-color:#f1f1f1; ">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="about__video set-bg" style="background-image: url('img/ee.png'); position: relative; padding-bottom: 50%; height: 0; overflow: hidden;">
                    <div class="video-wrapper">
                        <img src="img/ee.png" alt="About Video" class="video-thumbnail" style="width: 100%; height: 100%; position: absolute; top: 0; left: 0;">
                        <button class="play-button" onclick="playVideo()" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: transparent; border: none; cursor: pointer;">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 5v14l11-7L8 5z" fill="#fff"/>
                            </svg>
                        </button>
                        <iframe id="youtube-video" width="100%" height="100%" src="https://www.youtube.com/embed/HTXB6flODww"
                                frameborder="0" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: none;"
                                allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="about__text">
                    <div class="section-title">
                        <span>About Cake shop</span>
                        <h2>Cakes and bakes from the house of Queens!</h2>
                    </div>
                    <p>The "Caramella Cake Shop" is a Palestinian Brand that started as a small family business. The owners are
                        Shifaa Barham and Aseel Zaid, supported by a staff of 80 employees.</p>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="about__bar">
                    <div class="about__bar__item">
                        <p>Cake design</p>
                        <div id="bar1" class="barfiller">
                            <div class="tipWrap"  ><span class="tip">95%</span></div>
                            <span class="fill" data-percentage="95" style=" background-color: #e57c98;!important;"></span>
                        </div>
                    </div>
                    <div class="about__bar__item">
                        <p>Cake Class</p>
                        <div id="bar2" class="barfiller">
                            <div class="tipWrap"><span class="tip">80%</span></div>
                            <span class="fill" data-percentage="80"></span>
                        </div>
                    </div>
                    <div class="about__bar__item">
                        <p>Cake Recipes</p>
                        <div id="bar3" class="barfiller">
                            <div class="tipWrap"><span class="tip">90%</span></div>
                            <span class="fill" data-percentage="90"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="position: absolute; width: 100%; z-index: 3; bottom: 0;">
        <svg id="" preserveAspectRatio="xMidYMax meet" class="svg-separator sep3" viewBox="0 0 1600 100" style="display: block; width: 100%; position: absolute; bottom: 0; left: 0; right: 0;">
            <path class="" style="opacity: 1; fill: #f1f1f1;" d="M-40,71.627C20.307,71.627,20.058,32,80,32s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,26H-60V72L-40,71.627z"></path>
            <path class="" style="opacity: 1; fill: #f1f1f1;" d="M-40,83.627C20.307,83.627,20.058,44,80,44s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,14H-60V84L-40,83.627z"></path>
            <path class="" style="fill: rgb(242,224,224);" d="M-40,95.627C20.307,95.627,20.058,56,80,56s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,138H-60V96L-40,95.627z"></path>
        </svg>
    </div>
</section>

<script>
    function playVideo() {
        document.querySelector('.video-thumbnail').style.display = 'none';
        document.querySelector('.play-button').style.display = 'none';
        document.querySelector('#youtube-video').style.display = 'block';
    }
</script>
<!-- About Section End -->

<!-- Team Section Begin -->
<section class="team spad" >

    <div class="container" style="margin-top: 2px; margin-bottom: 10px;padding-top: 20px;">

        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-7">
                <div class="section-title">
                    <span>Our team</span>
                    <h2>Sweet Baker </h2>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="team__item set-bg" data-setbg="img/team/team-1.jpg">
                    <div class="team__item__text">
                        <h5>Hassan Khaled</h5>
                        <span>Decorater</span>
                        <div class="team__item__social">
                            <svg class="svg--source" width="0" height="0" aria-hidden="true">

                                <symbol id="svg--twitter" viewbox="0 -7 15 30">
                                    <path d="M15.36 3.434c-0.542 0.241-1.124 0.402-1.735 0.476 0.624-0.374 1.103-0.966 1.328-1.67-0.583 0.346-1.23 0.598-1.917 0.733-0.551-0.587-1.336-0.954-2.205-0.954-1.668 0-3.020 1.352-3.020 3.019 0 0.237 0.026 0.467 0.078 0.688-2.51-0.126-4.735-1.328-6.224-3.155-0.261 0.446-0.41 0.965-0.41 1.518 0 1.048 0.534 1.972 1.344 2.514-0.495-0.016-0.961-0.151-1.368-0.378 0 0.013 0 0.025 0 0.038 0 1.463 1.042 2.683 2.422 2.962-0.253 0.069-0.52 0.106-0.796 0.106-0.194 0-0.383-0.018-0.568-0.054 0.384 1.2 1.5 2.073 2.821 2.097-1.034 0.81-2.335 1.293-3.75 1.293-0.244 0-0.484-0.014-0.72-0.042 1.336 0.857 2.923 1.357 4.63 1.357 5.554 0 8.592-4.602 8.592-8.593 0-0.13-0.002-0.261-0.009-0.39 0.59-0.426 1.102-0.958 1.507-1.563z"
                                    />
                                </symbol>

                                <symbol id="svg--google" viewbox="-13 -13 72 72">
                                    <path d="M48,22h-5v-5h-4v5h-5v4h5v5h4v-5h5 M16,21v6.24h8.72c-0.67,3.76-3.93,6.5-8.72,6.5c-5.28,0-9.57-4.47-9.57-9.75
s4.29-9.74,9.57-9.74c2.38,0,4.51,0.82,6.19,2.42v0.01l4.51-4.51C23.93,9.59,20.32,8,16,8C7.16,8,0,15.16,0,24s7.16,16,16,16
c9.24,0,15.36-6.5,15.36-15.64c0-1.17-0.11-2.29-0.31-3.36C31.05,21,16,21,16,21z" />
                                </symbol>

                                <symbol id="svg--facebook" viewbox="0 -7 16 30">
                                    <path d="M12 3.303h-2.285c-0.27 0-0.572 0.355-0.572 0.831v1.65h2.857v2.352h-2.857v7.064h-2.698v-7.063h-2.446v-2.353h2.446v-1.384c0-1.985 1.378-3.6 3.269-3.6h2.286v2.503z" />
                                </symbol>

                                <symbol id="svg--github" viewbox="-30 -30 150 150">
                                    <path d="M61.896,52.548c-3.59,0-6.502,4.026-6.502,8.996c0,4.971,2.912,8.999,6.502,8.999
		c3.588,0,6.498-4.028,6.498-8.999C68.395,56.574,65.484,52.548,61.896,52.548z M84.527,29.132c0.74-1.826,0.777-12.201-3.17-22.132
		c0,0-9.057,0.993-22.76,10.396c-2.872-0.793-7.736-1.19-12.597-1.19s-9.723,0.396-12.598,1.189C19.699,7.993,10.645,7,10.645,7
		c-3.948,9.931-3.913,20.306-3.172,22.132C2.834,34.169,0,40.218,0,48.483c0,35.932,29.809,36.508,37.334,36.508
		c1.703,0,5.088,0.004,8.666,0.009c3.578-0.005,6.965-0.009,8.666-0.009C62.191,84.991,92,84.415,92,48.483
		C92,40.218,89.166,34.169,84.527,29.132z M46.141,80.574H45.86c-18.859,0-33.545-2.252-33.545-20.58
		c0-4.389,1.549-8.465,5.229-11.847c6.141-5.636,16.527-2.651,28.316-2.651c0.045,0,0.093-0.001,0.141-0.003
		c0.049,0.002,0.096,0.003,0.141,0.003c11.789,0,22.178-2.984,28.316,2.651c3.68,3.382,5.229,7.458,5.229,11.847
		C79.686,78.322,65,80.574,46.141,80.574z M30.104,52.548c-3.588,0-6.498,4.026-6.498,8.996c0,4.971,2.91,8.999,6.498,8.999
		c3.592,0,6.502-4.028,6.502-8.999C36.605,56.574,33.695,52.548,30.104,52.548z" />
                                </symbol>

                                <symbol id="svg--pinterest" viewbox="-180 -180 850 850">
                                    <path d="M430.149,135.248C416.865,39.125,321.076-9.818,218.873,1.642
				C138.071,10.701,57.512,76.03,54.168,169.447c-2.037,57.029,14.136,99.801,68.399,111.84
				c23.499-41.586-7.569-50.676-12.433-80.802C90.222,77.367,252.16-6.718,336.975,79.313c58.732,59.583,20.033,242.77-74.57,223.71
				c-90.621-18.179,44.383-164.005-27.937-192.611c-58.793-23.286-90.013,71.135-62.137,118.072
				c-16.355,80.711-51.557,156.709-37.3,257.909c46.207-33.561,61.802-97.734,74.57-164.704
				c23.225,14.136,35.659,28.758,65.268,31.038C384.064,361.207,445.136,243.713,430.149,135.248z" />
                                </symbol>

                                <symbol id="svg--youtube" viewbox="-150 -150 800 800">
                                    <path d="M459,61.2C443.7,56.1,349.35,51,255,51c-94.35,0-188.7,5.1-204,10.2C10.2,73.95,0,163.2,0,255s10.2,181.05,51,193.8
			C66.3,453.9,160.65,459,255,459c94.35,0,188.7-5.1,204-10.2c40.8-12.75,51-102,51-193.8S499.8,73.95,459,61.2z M204,369.75v-229.5
			L357,255L204,369.75z" />
                                </symbol>

                            </svg>

                            <div class="wrapper">
                                <div class="connect">

                                    <a href="" class="share twitter">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--twitter" />
                                        </svg>
                                        <span class="clip">TWITTER</span>
                                    </a>



                                    <a href="" rel="author" class="share facebook">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--facebook" />
                                            <span class="clip">FACEBOOK</span>
                                        </svg>
                                    </a>




                                    <a href="" class="share  youtube">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--youtube" />
                                            <span class="clip">YOU-TUBE</span>
                                        </svg>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="team__item set-bg" data-setbg="img/team/team-2.jpg">
                    <div class="team__item__text">
                        <h5>Layla Ali</h5>
                        <span>Decorater</span>
                        <div class="team__item__social">
                            <svg class="svg--source" width="0" height="0" aria-hidden="true">

                                <symbol id="svg--twitter" viewbox="0 -7 15 30">
                                    <path d="M15.36 3.434c-0.542 0.241-1.124 0.402-1.735 0.476 0.624-0.374 1.103-0.966 1.328-1.67-0.583 0.346-1.23 0.598-1.917 0.733-0.551-0.587-1.336-0.954-2.205-0.954-1.668 0-3.020 1.352-3.020 3.019 0 0.237 0.026 0.467 0.078 0.688-2.51-0.126-4.735-1.328-6.224-3.155-0.261 0.446-0.41 0.965-0.41 1.518 0 1.048 0.534 1.972 1.344 2.514-0.495-0.016-0.961-0.151-1.368-0.378 0 0.013 0 0.025 0 0.038 0 1.463 1.042 2.683 2.422 2.962-0.253 0.069-0.52 0.106-0.796 0.106-0.194 0-0.383-0.018-0.568-0.054 0.384 1.2 1.5 2.073 2.821 2.097-1.034 0.81-2.335 1.293-3.75 1.293-0.244 0-0.484-0.014-0.72-0.042 1.336 0.857 2.923 1.357 4.63 1.357 5.554 0 8.592-4.602 8.592-8.593 0-0.13-0.002-0.261-0.009-0.39 0.59-0.426 1.102-0.958 1.507-1.563z"
                                    />
                                </symbol>

                                <symbol id="svg--google" viewbox="-13 -13 72 72">
                                    <path d="M48,22h-5v-5h-4v5h-5v4h5v5h4v-5h5 M16,21v6.24h8.72c-0.67,3.76-3.93,6.5-8.72,6.5c-5.28,0-9.57-4.47-9.57-9.75
s4.29-9.74,9.57-9.74c2.38,0,4.51,0.82,6.19,2.42v0.01l4.51-4.51C23.93,9.59,20.32,8,16,8C7.16,8,0,15.16,0,24s7.16,16,16,16
c9.24,0,15.36-6.5,15.36-15.64c0-1.17-0.11-2.29-0.31-3.36C31.05,21,16,21,16,21z" />
                                </symbol>

                                <symbol id="svg--facebook" viewbox="0 -7 16 30">
                                    <path d="M12 3.303h-2.285c-0.27 0-0.572 0.355-0.572 0.831v1.65h2.857v2.352h-2.857v7.064h-2.698v-7.063h-2.446v-2.353h2.446v-1.384c0-1.985 1.378-3.6 3.269-3.6h2.286v2.503z" />
                                </symbol>

                                <symbol id="svg--github" viewbox="-30 -30 150 150">
                                    <path d="M61.896,52.548c-3.59,0-6.502,4.026-6.502,8.996c0,4.971,2.912,8.999,6.502,8.999
		c3.588,0,6.498-4.028,6.498-8.999C68.395,56.574,65.484,52.548,61.896,52.548z M84.527,29.132c0.74-1.826,0.777-12.201-3.17-22.132
		c0,0-9.057,0.993-22.76,10.396c-2.872-0.793-7.736-1.19-12.597-1.19s-9.723,0.396-12.598,1.189C19.699,7.993,10.645,7,10.645,7
		c-3.948,9.931-3.913,20.306-3.172,22.132C2.834,34.169,0,40.218,0,48.483c0,35.932,29.809,36.508,37.334,36.508
		c1.703,0,5.088,0.004,8.666,0.009c3.578-0.005,6.965-0.009,8.666-0.009C62.191,84.991,92,84.415,92,48.483
		C92,40.218,89.166,34.169,84.527,29.132z M46.141,80.574H45.86c-18.859,0-33.545-2.252-33.545-20.58
		c0-4.389,1.549-8.465,5.229-11.847c6.141-5.636,16.527-2.651,28.316-2.651c0.045,0,0.093-0.001,0.141-0.003
		c0.049,0.002,0.096,0.003,0.141,0.003c11.789,0,22.178-2.984,28.316,2.651c3.68,3.382,5.229,7.458,5.229,11.847
		C79.686,78.322,65,80.574,46.141,80.574z M30.104,52.548c-3.588,0-6.498,4.026-6.498,8.996c0,4.971,2.91,8.999,6.498,8.999
		c3.592,0,6.502-4.028,6.502-8.999C36.605,56.574,33.695,52.548,30.104,52.548z" />
                                </symbol>

                                <symbol id="svg--pinterest" viewbox="-180 -180 850 850">
                                    <path d="M430.149,135.248C416.865,39.125,321.076-9.818,218.873,1.642
				C138.071,10.701,57.512,76.03,54.168,169.447c-2.037,57.029,14.136,99.801,68.399,111.84
				c23.499-41.586-7.569-50.676-12.433-80.802C90.222,77.367,252.16-6.718,336.975,79.313c58.732,59.583,20.033,242.77-74.57,223.71
				c-90.621-18.179,44.383-164.005-27.937-192.611c-58.793-23.286-90.013,71.135-62.137,118.072
				c-16.355,80.711-51.557,156.709-37.3,257.909c46.207-33.561,61.802-97.734,74.57-164.704
				c23.225,14.136,35.659,28.758,65.268,31.038C384.064,361.207,445.136,243.713,430.149,135.248z" />
                                </symbol>

                                <symbol id="svg--youtube" viewbox="-150 -150 800 800">
                                    <path d="M459,61.2C443.7,56.1,349.35,51,255,51c-94.35,0-188.7,5.1-204,10.2C10.2,73.95,0,163.2,0,255s10.2,181.05,51,193.8
			C66.3,453.9,160.65,459,255,459c94.35,0,188.7-5.1,204-10.2c40.8-12.75,51-102,51-193.8S499.8,73.95,459,61.2z M204,369.75v-229.5
			L357,255L204,369.75z" />
                                </symbol>

                            </svg>

                            <div class="wrapper">
                                <div class="connect">

                                    <a href="" class="share twitter">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--twitter" />
                                        </svg>
                                        <span class="clip">TWITTER</span>
                                    </a>



                                    <a href="" rel="author" class="share facebook">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--facebook" />
                                            <span class="clip">FACEBOOK</span>
                                        </svg>
                                    </a>




                                    <a href="" class="share  youtube">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--youtube" />
                                            <span class="clip">YOU-TUBE</span>
                                        </svg>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="team__item set-bg" data-setbg="img/team/team-3.jpg">
                    <div class="team__item__text">
                        <h5>Sami Nabil</h5>
                        <span>Decorater</span>
                        <div class="team__item__social">
                            <svg class="svg--source" width="0" height="0" aria-hidden="true">

                                <symbol id="svg--twitter" viewbox="0 -7 15 30">
                                    <path d="M15.36 3.434c-0.542 0.241-1.124 0.402-1.735 0.476 0.624-0.374 1.103-0.966 1.328-1.67-0.583 0.346-1.23 0.598-1.917 0.733-0.551-0.587-1.336-0.954-2.205-0.954-1.668 0-3.020 1.352-3.020 3.019 0 0.237 0.026 0.467 0.078 0.688-2.51-0.126-4.735-1.328-6.224-3.155-0.261 0.446-0.41 0.965-0.41 1.518 0 1.048 0.534 1.972 1.344 2.514-0.495-0.016-0.961-0.151-1.368-0.378 0 0.013 0 0.025 0 0.038 0 1.463 1.042 2.683 2.422 2.962-0.253 0.069-0.52 0.106-0.796 0.106-0.194 0-0.383-0.018-0.568-0.054 0.384 1.2 1.5 2.073 2.821 2.097-1.034 0.81-2.335 1.293-3.75 1.293-0.244 0-0.484-0.014-0.72-0.042 1.336 0.857 2.923 1.357 4.63 1.357 5.554 0 8.592-4.602 8.592-8.593 0-0.13-0.002-0.261-0.009-0.39 0.59-0.426 1.102-0.958 1.507-1.563z"
                                    />
                                </symbol>

                                <symbol id="svg--google" viewbox="-13 -13 72 72">
                                    <path d="M48,22h-5v-5h-4v5h-5v4h5v5h4v-5h5 M16,21v6.24h8.72c-0.67,3.76-3.93,6.5-8.72,6.5c-5.28,0-9.57-4.47-9.57-9.75
s4.29-9.74,9.57-9.74c2.38,0,4.51,0.82,6.19,2.42v0.01l4.51-4.51C23.93,9.59,20.32,8,16,8C7.16,8,0,15.16,0,24s7.16,16,16,16
c9.24,0,15.36-6.5,15.36-15.64c0-1.17-0.11-2.29-0.31-3.36C31.05,21,16,21,16,21z" />
                                </symbol>

                                <symbol id="svg--facebook" viewbox="0 -7 16 30">
                                    <path d="M12 3.303h-2.285c-0.27 0-0.572 0.355-0.572 0.831v1.65h2.857v2.352h-2.857v7.064h-2.698v-7.063h-2.446v-2.353h2.446v-1.384c0-1.985 1.378-3.6 3.269-3.6h2.286v2.503z" />
                                </symbol>

                                <symbol id="svg--github" viewbox="-30 -30 150 150">
                                    <path d="M61.896,52.548c-3.59,0-6.502,4.026-6.502,8.996c0,4.971,2.912,8.999,6.502,8.999
		c3.588,0,6.498-4.028,6.498-8.999C68.395,56.574,65.484,52.548,61.896,52.548z M84.527,29.132c0.74-1.826,0.777-12.201-3.17-22.132
		c0,0-9.057,0.993-22.76,10.396c-2.872-0.793-7.736-1.19-12.597-1.19s-9.723,0.396-12.598,1.189C19.699,7.993,10.645,7,10.645,7
		c-3.948,9.931-3.913,20.306-3.172,22.132C2.834,34.169,0,40.218,0,48.483c0,35.932,29.809,36.508,37.334,36.508
		c1.703,0,5.088,0.004,8.666,0.009c3.578-0.005,6.965-0.009,8.666-0.009C62.191,84.991,92,84.415,92,48.483
		C92,40.218,89.166,34.169,84.527,29.132z M46.141,80.574H45.86c-18.859,0-33.545-2.252-33.545-20.58
		c0-4.389,1.549-8.465,5.229-11.847c6.141-5.636,16.527-2.651,28.316-2.651c0.045,0,0.093-0.001,0.141-0.003
		c0.049,0.002,0.096,0.003,0.141,0.003c11.789,0,22.178-2.984,28.316,2.651c3.68,3.382,5.229,7.458,5.229,11.847
		C79.686,78.322,65,80.574,46.141,80.574z M30.104,52.548c-3.588,0-6.498,4.026-6.498,8.996c0,4.971,2.91,8.999,6.498,8.999
		c3.592,0,6.502-4.028,6.502-8.999C36.605,56.574,33.695,52.548,30.104,52.548z" />
                                </symbol>

                                <symbol id="svg--pinterest" viewbox="-180 -180 850 850">
                                    <path d="M430.149,135.248C416.865,39.125,321.076-9.818,218.873,1.642
				C138.071,10.701,57.512,76.03,54.168,169.447c-2.037,57.029,14.136,99.801,68.399,111.84
				c23.499-41.586-7.569-50.676-12.433-80.802C90.222,77.367,252.16-6.718,336.975,79.313c58.732,59.583,20.033,242.77-74.57,223.71
				c-90.621-18.179,44.383-164.005-27.937-192.611c-58.793-23.286-90.013,71.135-62.137,118.072
				c-16.355,80.711-51.557,156.709-37.3,257.909c46.207-33.561,61.802-97.734,74.57-164.704
				c23.225,14.136,35.659,28.758,65.268,31.038C384.064,361.207,445.136,243.713,430.149,135.248z" />
                                </symbol>

                                <symbol id="svg--youtube" viewbox="-150 -150 800 800">
                                    <path d="M459,61.2C443.7,56.1,349.35,51,255,51c-94.35,0-188.7,5.1-204,10.2C10.2,73.95,0,163.2,0,255s10.2,181.05,51,193.8
			C66.3,453.9,160.65,459,255,459c94.35,0,188.7-5.1,204-10.2c40.8-12.75,51-102,51-193.8S499.8,73.95,459,61.2z M204,369.75v-229.5
			L357,255L204,369.75z" />
                                </symbol>

                            </svg>

                            <div class="wrapper">
                                <div class="connect">

                                    <a href="" class="share twitter">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--twitter" />
                                        </svg>
                                        <span class="clip">TWITTER</span>
                                    </a>



                                    <a href="" rel="author" class="share facebook">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--facebook" />
                                            <span class="clip">FACEBOOK</span>
                                        </svg>
                                    </a>




                                    <a href="" class="share  youtube">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--youtube" />
                                            <span class="clip">YOU-TUBE</span>
                                        </svg>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="team__item set-bg" data-setbg="img/team/team-4.jpg">
                    <div class="team__item__text">
                        <h5>Kareem Bassam</h5>
                        <span>Decorater</span>
                        <div class="team__item__social">
                            <svg class="svg--source" width="0" height="0" aria-hidden="true">

                                <symbol id="svg--twitter" viewbox="0 -7 15 30">
                                    <path d="M15.36 3.434c-0.542 0.241-1.124 0.402-1.735 0.476 0.624-0.374 1.103-0.966 1.328-1.67-0.583 0.346-1.23 0.598-1.917 0.733-0.551-0.587-1.336-0.954-2.205-0.954-1.668 0-3.020 1.352-3.020 3.019 0 0.237 0.026 0.467 0.078 0.688-2.51-0.126-4.735-1.328-6.224-3.155-0.261 0.446-0.41 0.965-0.41 1.518 0 1.048 0.534 1.972 1.344 2.514-0.495-0.016-0.961-0.151-1.368-0.378 0 0.013 0 0.025 0 0.038 0 1.463 1.042 2.683 2.422 2.962-0.253 0.069-0.52 0.106-0.796 0.106-0.194 0-0.383-0.018-0.568-0.054 0.384 1.2 1.5 2.073 2.821 2.097-1.034 0.81-2.335 1.293-3.75 1.293-0.244 0-0.484-0.014-0.72-0.042 1.336 0.857 2.923 1.357 4.63 1.357 5.554 0 8.592-4.602 8.592-8.593 0-0.13-0.002-0.261-0.009-0.39 0.59-0.426 1.102-0.958 1.507-1.563z"
                                    />
                                </symbol>

                                <symbol id="svg--google" viewbox="-13 -13 72 72">
                                    <path d="M48,22h-5v-5h-4v5h-5v4h5v5h4v-5h5 M16,21v6.24h8.72c-0.67,3.76-3.93,6.5-8.72,6.5c-5.28,0-9.57-4.47-9.57-9.75
s4.29-9.74,9.57-9.74c2.38,0,4.51,0.82,6.19,2.42v0.01l4.51-4.51C23.93,9.59,20.32,8,16,8C7.16,8,0,15.16,0,24s7.16,16,16,16
c9.24,0,15.36-6.5,15.36-15.64c0-1.17-0.11-2.29-0.31-3.36C31.05,21,16,21,16,21z" />
                                </symbol>

                                <symbol id="svg--facebook" viewbox="0 -7 16 30">
                                    <path d="M12 3.303h-2.285c-0.27 0-0.572 0.355-0.572 0.831v1.65h2.857v2.352h-2.857v7.064h-2.698v-7.063h-2.446v-2.353h2.446v-1.384c0-1.985 1.378-3.6 3.269-3.6h2.286v2.503z" />
                                </symbol>

                                <symbol id="svg--github" viewbox="-30 -30 150 150">
                                    <path d="M61.896,52.548c-3.59,0-6.502,4.026-6.502,8.996c0,4.971,2.912,8.999,6.502,8.999
		c3.588,0,6.498-4.028,6.498-8.999C68.395,56.574,65.484,52.548,61.896,52.548z M84.527,29.132c0.74-1.826,0.777-12.201-3.17-22.132
		c0,0-9.057,0.993-22.76,10.396c-2.872-0.793-7.736-1.19-12.597-1.19s-9.723,0.396-12.598,1.189C19.699,7.993,10.645,7,10.645,7
		c-3.948,9.931-3.913,20.306-3.172,22.132C2.834,34.169,0,40.218,0,48.483c0,35.932,29.809,36.508,37.334,36.508
		c1.703,0,5.088,0.004,8.666,0.009c3.578-0.005,6.965-0.009,8.666-0.009C62.191,84.991,92,84.415,92,48.483
		C92,40.218,89.166,34.169,84.527,29.132z M46.141,80.574H45.86c-18.859,0-33.545-2.252-33.545-20.58
		c0-4.389,1.549-8.465,5.229-11.847c6.141-5.636,16.527-2.651,28.316-2.651c0.045,0,0.093-0.001,0.141-0.003
		c0.049,0.002,0.096,0.003,0.141,0.003c11.789,0,22.178-2.984,28.316,2.651c3.68,3.382,5.229,7.458,5.229,11.847
		C79.686,78.322,65,80.574,46.141,80.574z M30.104,52.548c-3.588,0-6.498,4.026-6.498,8.996c0,4.971,2.91,8.999,6.498,8.999
		c3.592,0,6.502-4.028,6.502-8.999C36.605,56.574,33.695,52.548,30.104,52.548z" />
                                </symbol>

                                <symbol id="svg--pinterest" viewbox="-180 -180 850 850">
                                    <path d="M430.149,135.248C416.865,39.125,321.076-9.818,218.873,1.642
				C138.071,10.701,57.512,76.03,54.168,169.447c-2.037,57.029,14.136,99.801,68.399,111.84
				c23.499-41.586-7.569-50.676-12.433-80.802C90.222,77.367,252.16-6.718,336.975,79.313c58.732,59.583,20.033,242.77-74.57,223.71
				c-90.621-18.179,44.383-164.005-27.937-192.611c-58.793-23.286-90.013,71.135-62.137,118.072
				c-16.355,80.711-51.557,156.709-37.3,257.909c46.207-33.561,61.802-97.734,74.57-164.704
				c23.225,14.136,35.659,28.758,65.268,31.038C384.064,361.207,445.136,243.713,430.149,135.248z" />
                                </symbol>

                                <symbol id="svg--youtube" viewbox="-150 -150 800 800">
                                    <path d="M459,61.2C443.7,56.1,349.35,51,255,51c-94.35,0-188.7,5.1-204,10.2C10.2,73.95,0,163.2,0,255s10.2,181.05,51,193.8
			C66.3,453.9,160.65,459,255,459c94.35,0,188.7-5.1,204-10.2c40.8-12.75,51-102,51-193.8S499.8,73.95,459,61.2z M204,369.75v-229.5
			L357,255L204,369.75z" />
                                </symbol>

                            </svg>

                            <div class="wrapper">
                                <div class="connect">

                                    <a href="" class="share twitter">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--twitter" />
                                        </svg>
                                        <span class="clip">TWITTER</span>
                                    </a>



                                    <a href="" rel="author" class="share facebook">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--facebook" />
                                            <span class="clip">FACEBOOK</span>
                                        </svg>
                                    </a>




                                    <a href="" class="share  youtube">
                                        <svg role="presentation" class="svg--icon">
                                            <use xlink:href="#svg--youtube" />
                                            <span class="clip">YOU-TUBE</span>
                                        </svg>
                                    </a>

                                </div>
                            </div>
                        </div>                        </div>
                </div>
            </div>
        </div>
    </div>

</section>
<!-- Team Section End -->

<!-- Testimonial Section Begin -->
<section class="testimonial spad" style="position: relative; overflow: hidden;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-title">
                    <span>Testimonial</span>
                    <h2>Our client say</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="testimonial__slider owl-carousel">
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/ta-1.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Emily Johnson</h5>
                                <span>New york</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Caramella Cake is my go-to spot for every celebration. The cakes are always so fresh and beautifully decorated. I especially love their Red Velvet—it's simply the best!</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/ta-2.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Sophia Martinez</h5>
                                <span>New york</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>I can't get enough of the cakes from Caramella! Every time I visit, I'm amazed by the creativity and flavor combinations. The service is always friendly.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/t1.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Michael Thompson</h5>
                                <span>London</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Caramella Cake never disappoints. Whether it's a birthday, anniversary, or just a treat-yourself day, their cakes are consistently delicious</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/t2.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Olivia White</h5>
                                <span>New york</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>The cakes at Caramella Cake are a work of art! Not only do they look amazing, but they taste incredible too. The Chocolate Fudge is my absolute favorite—rich.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/t3.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Daniel Wilson</h5>
                                <span>London</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Caramella Cake is the best bakery in town. Their customer service is excellent, and the cakes are always top quality.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/t4.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>James Anderson</h5>
                                <span>New york</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>I recently ordered a custom cake from Caramella for my sister's wedding, and it was a huge hit! The attention to detail and the flavor were beyond our expectations.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</section>
<!-- Testimonial Section End -->

<!-- Footer Section Begin -->
<footer class="footer set-bg" data-setbg="img/fo2.jpg" style="position: relative; overflow: hidden; background-size: cover;">


    <div class="container" style="z-index: 2;">

        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">

                <div class="footer__widget">

                    <h6>WORKING HOURS</h6>
                    <ul>
                        <li>Monday - Friday: 08:00 am – 08:30 pm</li>
                        <li>Saturday: 10:00 am – 16:30 pm</li>
                        <li>Sunday: 10:00 am – 16:30 pm</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="#"><img src="img/logo2.png" alt=""></a>
                    </div>
                    <p>Lorem ipsum dolor amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore dolore magna aliqua.</p>
                    <div class="footer__social">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-instagram"></i></a>
                        <a href="#"><i class="fa fa-youtube-play"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="footer__newslatter">
                    <h6>Subscribe</h6>
                    <p>Get latest updates and offers.</p>
                    <form action="#">
                        <input type="text" placeholder="Email">
                        <button type="submit"><i class="fa fa-send-o"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <p class="copyright__text text-white">
                        Copyright &copy;<script>document.write(new Date().getFullYear());</script> all rights reserved 2024
                    </p>
                </div>
                <div class="col-lg-5">
                    <div class="copyright__widget">
                        <!-- Add any additional copyright content here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->



<!-- Js Plugins -->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.nice-select.min.js"></script>
<script src="js/jquery.barfiller.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/jquery.slicknav.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/jquery.nicescroll.min.js"></script>
<script src="js/main.js"></script>
</body>

</html>