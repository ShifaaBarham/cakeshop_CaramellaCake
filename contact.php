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
            background-color: rgba(255, 192, 203, 0.7);
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
    <link rel="stylesheet" href="css/contactus.css" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>



    <style>
        /* تنسيق القائمة المنسدلة */
        .dropdown-content {
            display: none; /* إخفاء القائمة افتراضيًا */
            position: absolute;
            background-color: rgb(147, 227, 225); /* خلفية بنفس لون الموقع */
            border: 1px solid #ddd; /* حدود خفيفة */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            list-style: none;
            padding: 0;
            margin: 0;
            width: 200px; /* تحديد عرض القائمة */
        }

        /* تنسيق العناصر داخل القائمة */
        .dropdown-content li {
            display: block;
            padding: 10px 20px;
            text-align: left;
            font-family: 'Dancing Script', cursive; /* استخدام نفس الخط */
            font-size: 60px;
        }

        .dropdown-content li a {
            color: #e57c98; /* لون النص داخل القائمة */
            text-decoration: none;
            display: block;
            font-size: 20px;
        }

        .dropdown-content li a:hover {
            color: rgb(255, 253, 253);

        ;/* لون الخلفية عند التمرير */
        }

        /* زر القائمة (3 شحطات) */
        .menu-button {
            display: none; /* إخفاء الزر افتراضيًا */
            z-index: 1000;
            cursor: pointer;
        }

        /* اظهار زر القائمة عند تصغير الشاشة */
        @media (max-width: 768px) {
            .menu-button {
                display: block;
            }

            .navbar-collapse {
                display: none; /* اخفاء القائمة الكبيرة عند تصغير الشاشة */
            }
        }

    </style>

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

    <script>
        function toggleMenu() {
            var menu = document.getElementById("dropdownMenu");
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }

        /* إخفاء القائمة عند تكبير الشاشة تلقائيًا */
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById("dropdownMenu").style.display = "none";
            }
        });



    </script>


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


    <section class="hero" style="position: relative;background-color: #ffffff; overflow: hidden; padding-top: 1px; height:430px; margin-top: 72px; background-image: url('img/ss.jpg'); background-size: cover; background-position: center;">
        <!-- Transparent Banner -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(255, 255, 255, 0.7); padding: 20px; border-radius: 10px; z-index: 4;">
            <h1 style="font-size: 36px; color: #f4cccc; text-align: center; font-family: 'scr' !important; text-shadow: 2px 2px 5px rgb(96,42,48);">Contact US <br> Home / Contact Us</h1>
        </div>

        <!-- SVG Overlay -->
        <div style="position: absolute; bottom: 0; width: 100%; z-index: 3;">
            <svg id="" preserveAspectRatio="xMidYMax meet" class="svg-separator sep3" viewBox="0 0 1600 100" style="display: block; data-height:auto ; width:100%; position: absolute; bottom: 0; left: 0; right: 0;">
                <path class="" style="opacity: 1; fill: #f6cece;" d="M-40,71.627C20.307,71.627,20.058,32,80,32s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,26H-60V72L-40,71.627z"></path>
                <path class="" style="opacity: 1; fill: #ffffff;" d="M-40,83.627C20.307,83.627,20.058,44,80,44s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,14H-60V84L-40,83.627z"></path>
                <path class="" style="fill: rgb(255,255,255);" d="M-40,95.627C20.307,95.627,20.058,56,80,56s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,138H-60V96L-40,95.627z"></path>
            </svg>
        </div>
    </section>

    <section class="card1" style="position: relative; overflow: hidden; padding-top: 1px;">
        <div class="fixed-content">
            <h1>Contact Us</h1>
        </div>
        <div class="left-content fixed-side">
            <h2>Opening Hours</h2>
            <p>Monday – Friday<br>08:00 am – 08:30 pm<br>Saturday<br>10:00 am – 16:30 pm<br>Sunday<br>10:00 am – 16:30 pm</p>
        </div>
        <div class="right-content fixed-side">
                <h2>Our Contacts</h2>
                <p>Caramella Cake</p>
                <p>+970 9 234 5678</p>
                <p>+970 9 234 5679</p>
                <p>info@caramella.ps</p>
                <p>sales@caramella.ps</p>
                </div>
        <div class="container2">
            <div class="front2 side2">
                <div class="content2">
                    <h1>Caramella Cake</h1>
                    <p>Welcome to Caramella Cake Shop! As a dedicated team of cake designers and pastry artists, we specialize in creating delicious, beautifully designed cakes that add sweetness to every occasion. Whether you're planning a wedding, birthday, or any special event, our cakes are crafted to bring your vision to life. We pride ourselves on providing an exceptional experience for our customers, from the first consultation to the final product. Let's make your celebrations unforgettable!</p>
                </div>
            </div>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "caranellacake";

            // إنشاء الاتصال بقاعدة البيانات
            $conn = new mysqli($servername, $username, $password, $dbname);

            // التحقق من الاتصال
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // التحقق من الطلب إذا كان POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $message = $_POST['message'];
                $timestamp = date('Y-m-d H:i:s'); // الحصول على الوقت الحالي

                // التحقق من الحقول الفارغة
                if (!empty($name) && !empty($email) && !empty($message)) {
                    // إدراج البيانات في جدول messages
                    $stmt = $conn->prepare("INSERT INTO messages (message, customerEmail, timestamp) VALUES (?, ?, ?)");
                    if ($stmt === false) {
                        die("Error preparing the statement: " . $conn->error);
                    }
                    $stmt->bind_param("sss", $message, $email, $timestamp);
                    if ($stmt->execute()) {
                        echo "Message sent successfully!";

                        // تحديث عدد الرسائل غير المقروءة في جدول admin
                        $update_stmt = $conn->prepare("UPDATE admin SET unread_messages_count = unread_messages_count + 1 WHERE adminID = 1");
                        if ($update_stmt === false) {
                            die("Error preparing the update statement: " . $conn->error);
                        }
                        if ($update_stmt->execute()) {
                            echo "Unread messages count updated!";
                        } else {
                            echo "Error updating unread messages count: " . $update_stmt->error;
                        }
                        $update_stmt->close();
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "All fields are required!";
                }
            }

            $conn->close();
            ?>

            <div class="back2 side2">
                <div class="content2">
                    <h1>Sened Message</h1>
                    <form method="POST" action="">
                        <label style="color: #6c2d34;">Your Name :</label>
                        <input type="text" name="name" placeholder="Your Name" style="color: #098079;">
                        <label style="color: #6c2d34;">Your E-mail :</label>
                        <input type="email" name="email" placeholder="Example@mail.com" style="color: #098079;">
                        <label style="color: #6c2d34;">Your message :</label>
                        <textarea name="message" placeholder="The Subject" style="color: #098079;"></textarea>
                        <input type="submit" value="Done" style="background-color: #e37b97">
                    </form>
                </div>
            </div>
        </div>
        <div style="position: absolute; bottom: 0; width: 100%; z-index: 3;">
            <svg id="" preserveAspectRatio="xMidYMax meet" class="svg-separator sep3" viewBox="0 0 1600 100" style="display: block; data-height:auto ; width:100%; position: absolute; bottom: 0; left: 0; right: 0;">
                <path class="" style="opacity: 1; fill: #f4e2e2;" d="M-40,71.627C20.307,71.627,20.058,32,80,32s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,26H-60V72L-40,71.627z"></path>
                <path class="" style="opacity: 1; fill: #f4e2e2;" d="M-40,83.627C20.307,83.627,20.058,44,80,44s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,14H-60V84L-40,83.627z"></path>
                <path class="" style="fill: rgb(244,226,226);" d="M-40,95.627C20.307,95.627,20.058,56,80,56s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,138H-60V96L-40,95.627z"></path>
            </svg>
        </div>
    </section>

    <!-- Contact Section Begin -->
    <section class="contact spad backgr">
        <div class="container">
            <div class="map">
                <div class="map__iframe">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3375.1341015534717!2d35.22320032490134!3d32.22755551186417!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151ce019107d110f%3A0x12ebfea4ddbb7ccb!2z2KzYp9mF2LnYqSDYp9mE2YbYrNin2K0g2KfZhNmI2LfZhtmK2Kk!5e0!3m2!1sar!2s!4v1723711299765!5m2!1sar!2s"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                </div>
                <div class="map__inner">
                    <h6>Palestine</h6>
                    <ul>
                        <li>جامعة النجاح الوطنية, فلسطين</li>
                        <li>CaramellaCake@gmail.com</li>
                        <li>+1 800-786-1000</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section End -->

    <!-- Footer Section Begin -->
    <footer class="footer set-bg" data-setbg="img/fo2.jpg" style="position: relative; overflow: hidden; background-size: cover;">
        <div style="position: absolute; top: 700px; width: 100%; z-index: 3;">
            <svg preserveAspectRatio="xMidYMax meet" class="svg-separator sep3" viewBox="0 0 1600 100" style="display: block; width: 100%; position: absolute; bottom: 0; left: 0; right: 0;">
                <path style="opacity: 1; fill: #f6cece;" d="M-40,71.627C20.307,71.627,20.058,32,80,32s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,26H-60V72L-40,71.627z"></path>
                <path style="opacity: 1; fill: #F3F3F3;" d="M-40,83.627C20.307,83.627,20.058,44,80,44s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,14H-60V84L-40,83.627z"></path>
                <path style="fill: rgb(158,15,15);" d="M-40,95.627C20.307,95.627,20.058,56,80,56s60.003,40,120,40s59.948-40,120-40s60.313,40,120,40s60.258-40,120-40s60.202,40,120,40s60.147-40,120-40s60.513,40,120,40s60.036-40,120-40c59.964,0,60.402,40,120,40s59.925-40,120-40s60.291,40,120,40s60.235-40,120-40s60.18,40,120,40s59.82,0,59.82,0l0.18,138H-60V96L-40,95.627z"></path>
            </svg>
        </div>

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