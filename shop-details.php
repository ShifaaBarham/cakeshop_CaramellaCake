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

<?php
if (isset($_GET['productID'])) {
    $productID = intval($_GET['productID']); // احصل على معرف المنتج بشكل آمن
    // باقي الكود لاسترجاع وعرض تفاصيل المنتج
}?>





<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caranellacake";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['action']) && $_GET['action'] == 'add_to_cart' && isset($_GET['productID'])) {
    $productID = intval($_GET['productID']);
    $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1; // التقاط كمية المنتج من الـ GET

    // البحث عن cartID للمستخدم في جدول cart
    $cart_sql = "SELECT cartID FROM cart WHERE customerID = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $customerID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // إنشاء عربة جديدة للمستخدم
        $create_cart_sql = "INSERT INTO cart (customerID) VALUES (?)";
        $stmt->close();
        $stmt = $conn->prepare($create_cart_sql);
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        $cartID = $conn->insert_id;

        // إضافة المنتج إلى cart_product
        $insert_cart_product_sql = "INSERT INTO cart_product (cartID, productID, productQuantity) VALUES (?, ?, ?)";
        $stmt->close();
        $stmt = $conn->prepare($insert_cart_product_sql);
        $stmt->bind_param("iii", $cartID, $productID, $quantity);
        $stmt->execute();
    } else {
        $cartID = $result->fetch_assoc()['cartID'];

        // التحقق مما إذا كان المنتج موجودًا في cart_product
        $check_cart_product_sql = "SELECT * FROM cart_product WHERE cartID = ? AND productID = ?";
        $stmt->close();
        $stmt = $conn->prepare($check_cart_product_sql);
        $stmt->bind_param("ii", $cartID, $productID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // تحديث كمية المنتج إذا كان موجودًا
            $update_cart_product_sql = "UPDATE cart_product SET productQuantity = productQuantity + ? WHERE cartID = ? AND productID = ?";
            $stmt->close();
            $stmt = $conn->prepare($update_cart_product_sql);
            $stmt->bind_param("iii", $quantity, $cartID, $productID);
            $stmt->execute();
        } else {
            // إضافة المنتج إلى cart_product
            $insert_cart_product_sql = "INSERT INTO cart_product (cartID, productID, productQuantity) VALUES (?, ?, ?)";
            $stmt->close();
            $stmt = $conn->prepare($insert_cart_product_sql);
            $stmt->bind_param("iii", $cartID, $productID, $quantity);
            $stmt->execute();
        }

        // تحديث عدد المنتجات في cart
        $update_cart_sql = "UPDATE cart SET productCount = productCount + ? WHERE cartID = ?";
        $stmt->close();
        $stmt->prepare($update_cart_sql);
        $stmt->bind_param("ii", $quantity, $cartID);
        $stmt->execute();
    }

    $stmt->close();
}

// الحصول على بيانات العميل
$customerData = getCustomerData($conn, $customerID);
$customerName = $customerData['name'];
$customerImage = $customerData['image'];

// الحصول على عدد المنتجات في عربة العميل
$productCount = getCartProductCount($conn, $customerID);
$conn->close();
?>



<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caranellacake";

// إنشاء اتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['productId']) && isset($_POST['rating'])) {

    $productId = intval($_POST['productId']);
    $rating = intval($_POST['rating']);

    // تحقق من صحة التقييم
    if ($rating >= 1 && $rating <= 5) {
        // جلب التقييمات الحالية والتقييمات السابقة من قاعدة البيانات
        $sql = "SELECT preview FROM product WHERE productID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentReview = $row['preview'];

            // حساب التقييم الجديد
            $newReview = ($currentReview  + $rating) / 2;


            // تحديث قاعدة البيانات
            $update_sql = "UPDATE product SET preview = ? WHERE productID = ?";
            $stmt->close();
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("dii", $newReview, $newReviewCount, $productId);
            $stmt->execute();

            // إرسال استجابة JSON بنجاح
            echo "<script>alert('Rating submitted successfully" . $conn->error . "'); window.location.href='shop-details.php';</script>";

        } else {
            // إرسال استجابة JSON إذا لم يتم العثور على المنتج
            echo "<script>alert('Product not found" . $conn->error . "'); window.location.href='shop-details.php';</script>";
        }

        $stmt->close();
    } else {
        // إرسال استجابة JSON إذا كان التقييم غير صحيح
        echo "<script>alert('Invalid rating value" . $conn->error . "'); window.location.href='shop-details.php';</script>";

    }
} else {
    // إرسال استجابة JSON إذا كانت البيانات غير موجودة
}

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
    <link rel="shortcut icon"  href="img\icon\lloo.png">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


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
            font-family: "Font Awesome 6 Free",serif;
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
    <!-- Include Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Custom CSS -->

    <!-- Include Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Custom CSS -->
    <link href="https://cakeshopco.com/assets/css/pages/IndexPage/_tabPills.css" rel="stylesheet" />
    <link href="https://cakeshopco.com/assets/css/pages/IndexPage/index_custom.css" rel="stylesheet" />
    <link href="https://cakeshopco.com/assets/css/pages/IndexPage/_card_category.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cakeshopco.com/assets/css/pages/IndexPage/_ShopProduct.css" rel="stylesheet" />


    <style>
        .spad {
            background: white;
            padding-top: 100px;
            padding-bottom: 100px;
        }
        h2{
            color: #253D4E;
            font-size: 30px;
            font-family: Quicksand,serif;
            font-weight: 700;
            line-height: 48px;
            word-wrap: break-word;
        }
        .breadcrumb__links a,
        .breadcrumb__links span{
            text-transform: capitalize;
            color: #df5586;
            font-size: 20px !important;
            font-weight: bold;
            font-family: "Abril Fatface",serif;
        }
        .breadcrumb__text,
        .breadcrumb__links{
            text-align: center;
        }.breadcrumb__text h2{
             text-align: center;
             font-size: 60px !important;
         }

        .fa-search{
            color:#dd5b85;
        }
        .shop__option__search form{
            border: 1px solid #dedede;
            border-radius: 5px;
        }
        .primary-btn {
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 14px 30px;
            color: #ffffff;
            border-radius: 5px;
            background: #dd5b85;
            letter-spacing: 2px;
        }
        .primary-btn:hover {
            color: #000000; /* Darken the button on hover */
            transition: background-color 0.3s ease;
        }
        .F {
            position: relative;
            display: inline-block;
            width: 24px; /* نفس حجم الأيقونة */
            height: 24px; /* نفس حجم الأيقونة */
        }

        .heart-empty, .heart-filled {
            position: absolute;
            top: 0;
            left: 0;
            font-size: 40px;
            color: #df5586; /* اللون الأساسي */
            transition: opacity 0.3s ease, background-color 0.3s ease;
        }

        .heart-filled {
            opacity: 0; /* الأيقونة الممتلئة مخفية افتراضياً */
        }

        .F.active .heart-empty {
            opacity: 0; /* أخفِ الأيقونة الفارغة عند النقر */
        }

        .F.active .heart-filled {
            opacity: 1; /* أظهر الأيقونة الممتلئة عند النقر */
        }

        .F .heart-filled:hover {
            padding: 5px; /* إضافة فراغ داخلي لتعبئة الأيقونة */
        }
        .product__details__option .pro-qty {
            height: 50px;
            width: 145px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
            padding: 0 20px;
        }
        .product__details__option .pro-qty .qtybtn {
            font-size: 16px;
            float: left;
            height: 100%;
            line-height: 48px;
            cursor: pointer;
            font-weight: 600;
        }
        .product__details__option .pro-qty .qtybtn.dec {
            color: #111111;
        }
        .product__details__option .pro-qty .qtybtn.inc {
            color: #111111;
        }
        .product__details__option .pro-qty input {
            border: none;
            height: 100%;
            width: 82px;
            font-size: 16px;
            font-weight: 600;
            color: #111111;
            float: left;
            text-align: center;
        }
        .breadcrumb-option .container{
            padding: 40px 0;
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
    <style>

        .col-md-3 {
            padding: 15px 15px; /* مسافة داخل الأعمدة لتفادي التصاق الكاردات ببعضها */
        }
        .row {
            margin: 0 15px; /* مسافة بين الصفوف */
        }
        .
        .product-grid .product-img{
            position: relative;
            flex-grow: 1; /* يجعل قسم الصورة يأخذ المساحة المتبقية */
        }
        .product-grid .product-img a.img{ display: block; }
        .product-grid .product-img img{
            width: 100%;
            height: 200px; /* ضبط ارتفاع الصورة لجعل المربعات متساوية */
            object-fit: cover; /* لضمان أن الصورة تغطي المساحة المتاحة بالكامل */
            border-radius: 30px;
            transition: all 0.3s ease 0s;
        }
        .product-grid:hover .product-img img{ opacity: 0.5; }
        .product-grid .product-hot-label{
            color: #dd5b85;
            background: #f8e6e6;
            font-size: 16px;
            font-weight: 700;
            line-height: 30px;
            width: 50px;
            height: 30px;
            position: absolute;
            top: 20px;
            left: -10px;
        }
        .product-grid .product-hot-label:before,
        .product-grid .product-hot-label:after{
            content: "";
            background: linear-gradient(to top right, transparent 49%,#f8e6e6 50%);
            width: 10px;
            height: 10px;
            position: absolute;
            bottom: -10px;
            left: 0;
        }
        .product-grid .product-hot-label:before{
            background:#f8e6e6;
            width: 10px;
            height: 100%;
            bottom: auto;
            top: 0;
            left: 100%;
            clip-path: polygon(0 0, 100% 0, 1% 50%, 100% 100%, 0 100%);
        }
        .product-grid .product-links{
            width: 100%;
            padding: 0;
            margin: 0;
            list-style: none;
            transform: translateX(-50%);
            position: absolute;
            bottom: 25px;
            left: 50%;
            transition: all .5s ease 0s;
        }
        .product-grid .product-links li{
            margin: 0 2px;
            display: inline-block;
            opacity: 0;
            transform: translate(0, 125%);
            transition: all 0.5s ease;
        }
        .product-grid .product-links li:nth-child(1){ transition-duration: 0.2s; }
        .product-grid .product-links li:nth-child(2){ transition-duration: 0.4s; }
        .product-grid .product-links li:nth-child(3){ transition-duration: 0.6s; }
        .product-grid .product-links li:nth-child(4){ transition-duration: 0.8s; }
        .product-grid:hover .product-links li{
            opacity: 1;
            transform: translate(0, 0);
        }
        .product-grid .product-links li a{

            color: #212121;
            background: #fff;
            font-size: 16px;
            line-height: 40px;
            width: 40px;
            height: 40px;
            box-shadow: 0 0 1px 0 rgba(0,0,0,.5);
            display: block;
            transition: all 0.3s ease 0s;
        }
        .product-grid .product-links li a:hover{
            color: #fff;
            background: #8fcab8;
        }
        .product-grid .product-links li a:before{
            content: attr(data-tip);
            color: #fff;
            background-color: #333;
            font-size: 12px;
            line-height: 20px;
            padding: 0 5px 2px;
            white-space: nowrap;
            visibility: hidden;
            opacity: 0;
            transform: translateX(-50%);
            position: absolute;
            left: 50%;
            top: -20px;
            transition: all 0.3s ease 0s;
        }
        .product-grid .product-links li a:hover:before{
            visibility: visible;
            opacity: 1;
            top: -30px;
        }
        .product-grid .product-content{
            padding: 12px;
            background-color: #fff; /* يمكن تغيير لون الخلفية حسب التصميم */
        }
        .product-grid .title{
            font-family: "Abril Fatface",serif !important;

            font-size: 20px;
            font-weight: 500;
            text-transform: capitalize;
            margin: 0 0 7px;
        }
        .product-grid .title a{
            color: #212121;
            transition: all 0.3s ease 0s;
        }
        .product-grid .title a:hover{ color: #8fcab8;
        }
        .product-grid .price{
            color: #dd5b85;
            font-size: 18px;
            font-weight: 700;
        }
        @media screen and (max-width: 990px){
            .product-grid{ margin-bottom: 30px; }
        }
        .related-products .container {
            max-width: 100%;
            padding: 0 15px;
        }

        .related-products .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .related-products .col-lg-3 {
            flex: 1 1 calc(25% - 20px); /* يجعل عرض العمود 25% ويترك مسافة بين الكروت */
            margin-bottom: 30px; /* مسافة بين الصفوف */
            min-width: 250px; /* يحدد عرض أدنى للكارت */
        }

        .related-products .product-grid {
            font-family: 'Lato', sans-serif;
            text-align: center;
            border-radius: 30px;
            border: 2px dashed #dd5b85;
            overflow: hidden;
            height: 100%; /* لضمان أن المربعات كلها متساوية الطول */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .related-products .product-grid:hover {
            transform: scale(1.05);
        }

        .related-products .product-img {
            position: relative;
            overflow: hidden;
            padding: 20px;
            text-align: center;
        }

        .related-products .product-img img {
            width: 100%;
            height: auto;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .related-products .product-links {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .related-products .product-grid:hover .product-links {
            opacity: 1;
        }

        .related-products .product-content {
            padding: 15px;
            text-align: center;
        }

        .related-products .title a {
            font-size: 16px;
            color: #333;
            text-transform: uppercase;
            display: block;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .related-products .title a:hover {
            color: #df5586;
        }

        .related-products .price {
            font-size: 18px;
            color: #df5586;
            font-weight: bold;
        }

        @media (max-width: 991.98px) {
            .related-products .col-lg-3 {
                flex: 1 1 calc(33.33% - 20px);
            }
        }

        @media (max-width: 767.98px) {
            .related-products .col-lg-3 {
                flex: 1 1 calc(50% - 20px);
            }
        }

        @media (max-width: 575.98px) {
            .related-products .col-lg-3 {
                flex: 1 1 calc(100% - 20px);
            }
        }
        .product-details {
            padding: 0 40px;
        }
        .product__details__tab .nav-tabs .nav-item .nav-link {
            color: #333; /* اللون الأساسي */
            font-weight: normal;
            transition: color 0.3s ease, font-weight 0.3s ease;
        }

        .product__details__tab .nav-tabs .nav-item .nav-link.active {
            color: #df5586;
            font-weight: bold;
            border-bottom: 1px solid #df5586;
        }


        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .tab-content .tab-pane.active p span {
            display: block; /* عرض كل span كسطر منفصل */
            opacity: 0;
            transform: translateX(-100%);
            animation: slideIn 1.4s ease-out forwards !important;
        }

        .tab-content .tab-pane.active p .line-1{
            animation-delay: 0.2s;
        }

        .tab-content .tab-pane.active p .line-2 {
            animation-delay: 0.4s;
        }

        .tab-content .tab-pane.active p .line-3 {
            animation-delay: 0.6s;
        }

        .tab-content .tab-pane.active p .line-4 {
            animation-delay: 0.8s;
        }

        .tab-content .tab-pane.active p .line-5 {
            animation-delay: 1s;
        }
        .tab-content .tab-pane.active p .line-6 {
            animation-delay: 1.2s;
        }

        .tab-content .tab-pane.active p .line-7 {
            animation-delay: 1.4s;
        }
        .review-container {
            background-color: #ffffff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 20px;
            width: 400px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .star-rating {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .star {
            font-size: 30px;
            color: #e4e4e4; /* لون النجوم الافتراضي */
            margin: 0 5px;
            cursor: pointer;
        }

        .star.selected {
            color: #dd5b85; /* لون النجوم عند التحديد */
        }

        button {
            background-color: #f0f0f0;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #dd5b85; /* لون الزر عند التحويم */
            color: #fff; /* لون النص في الزر عند التحويم */
        }
        .star {
            font-size: 30px;
            color: #ccc; /* لون النجوم الغير مختارة */
            cursor: pointer;
            transition: color 0.3s;
        }

        .star.selected, .star:hover {
            color: #df5586; /* لون النجوم عند التحديد أو التمرير */
        }




        .custom-rating-stars {
            display: flex;
            font-size: 20px; /* حجم النجوم */
            color: #f8d64b; /* لون النجوم (ذهبي) */
        }

        .custom-star {
            display: inline-block;
            position: relative;
            font-size: 23px; /* حجم النجوم */
            color: #dcdcdc; /* لون النجوم الفارغة */
        }

        .custom-star.custom-filled {
            color: #f8d64b; /* لون النجوم الممتلئة (ذهبي) */
        }

        .custom-star.custom-half::before {
            content: '\f005'; /* استخدام نجمة ممتلئة */
            font-family: FontAwesome; /* FontAwesome لأيقونات النجوم */
            color: #f8d64b; /* لون النجمة النصفية */
            position: absolute;
            width: 50%;
            overflow: hidden;
            display: block;
        }

        .custom-star.custom-quarter::before {
            content: '\f005'; /* استخدام نجمة ممتلئة */
            font-family: FontAwesome; /* FontAwesome لأيقونات النجوم */
            color: #f8d64b; /* لون النجمة الربع */
            position: absolute;
            width: 25%;
            overflow: hidden;
            display: block;
        }

        .custom-star.custom-half, .custom-star.custom-quarter {
            position: relative;
            overflow: hidden;
        }
        .drop-down2 {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu2 {
            display: none; /* Hide menu by default */
            position: absolute;
            top: 100%; /* Position below the parent element */
            left: 0;
            background-color: rgba(128, 128, 128, 0.7); /* Gray with 50% transparency */
            padding: 0;
            list-style: none; /* Remove list styles */
            z-index: 1000; /* Ensure menu is above other content */
            border-radius: 3px;
        }

        .dropdown-menu2 li {
            margin: 0;
        }

        .dropdown-menu2 li a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #fff; /* White text color */
            font-size: 14px; /* Smaller font size */
        }

        .dropdown-menu2 li a:hover {
            background-color: rgba(200, 200, 200, 0.3); /* Lighter gray on hover */
        }

        /* Show the dropdown menu when hovering over the parent element */
        .drop-down2:hover .dropdown-menu2 {
            display: block;
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
    <style>
        .product-grid {
            border: 3px dashed #dd5b85;
            padding: 15px;
            margin-bottom: 30px;
            transition: transform 0.3s;
        }

        .product-grid:hover {
            transform: scale(1.05);
        }

        .product-img img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }



        .product-links {
            list-style: none;
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .product-links li {
            margin: 0 5px;
        }

        .product-links li a {

            border-radius: 50%;

        }

        .product-links li a:hover {
            background-color: #555;
        }

        .product-content h3.title {
            font-size: 18px;
            margin: 15px 0;
        }

        .product-content .price {
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }

        .shop__pagination {
            display: flex;
            justify-content: center;
            padding: 20px 0;
        }

        .pagination-button {
            padding: 10px 15px;
            border: 1px solid #ddd;
            margin: 0 5px;
            color: #333;
            text-decoration: none;
        }

        .pagination-button.active {
            background-color: #333;
            color: #fff;
        }

        .pagination-button:hover {
            background-color: #555;
            color: #fff;
        }



        .hide_cart {
            position: relative; /* لتحديد الموضع النسبي للزر */
        }
        #navbar2 .navbar-header {
            display: flex;
            width: 260px;
            justify-content: space-evenly;
            align-items: center;
        }
        .cart_counter {
            position: absolute;
            top: 2px; /* زيادة القيمة لتنزيل الدائرة للأسفل */
            right: -2px; /* لضبط الموضع الأفقي */
            background-color: #dd5b85; /* لون الخلفية للعداد */
            color: #ffffff; /* لون النص للعداد */
            border-radius: 50%; /* جعل العداد دائري الشكل */
            font-size: 12px; /* تصغير حجم الخط للعداد */
            font-weight: bold; /* جعل الخط عريضًا */
            height: 13px; /* زيادة الارتفاع ليتناسب مع حجم الخط */
            width: 13px; /* زيادة العرض ليتناسب مع حجم الخط */
            display: flex;
            align-items: center;
            justify-content: center;
        }


    </style>
    <style>
        /* تهيئة خلفية بيضاء */
        .review-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* توسيط النجوم والزر */
        .star-rating {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .star {
            font-size: 2rem; /* تغيير حجم النجوم حسب الحاجة */
            color: #ccc; /* اللون الافتراضي للنجوم */
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star.filled {
            color: #dd5b85; /* اللون عند التمرير أو التحديد */
        }

        .star.selected {
            color: #dd5b85; /* اللون عند النقر */
        }

        /* تنسيق زر الإدخال */
        #submit-review {
            display: inline-block; /* عرض الزر ككتلة في سطر */
            padding: 10px 20px; /* التباعد الداخلي للزر */
            font-size: 1rem; /* حجم النص */
            color: #fff; /* لون النص */
            background-color: #dd5b85; /* لون الخلفية للزر */
            border: none; /* إزالة الحدود الافتراضية للزر */
            border-radius: 4px; /* حواف مدورة للزر */
            cursor: pointer; /* تغيير شكل المؤشر عند المرور فوق الزر */
            transition: background-color 0.2s ease, box-shadow 0.2s ease; /* تأثير الانتقال عند التمرير */
            text-align: center; /* محاذاة النص في وسط الزر */
            width: 200px;
        }

        #submit-review:hover {
            background-color: #df95ac; /* لون الخلفية عند مرور الماوس فوق الزر */
        }

        #submit-review:active {
            background-color: #dd5b85; /* لون الخلفية عند النقر على الزر */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* تأثير الظل عند النقر */
        }

        #submit-review:focus {
            outline: none; /* إزالة الإطار الافتراضي عند التركيز */
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.5); /* تأثير التركيز عند النقر */
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
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">

            <div class="col-12">
                <div class="breadcrumb__links">
                    <a href="index.php">Home</a>
                    <a href="shop.php">Shop</a>
                    <span>homemade chocolate cake</span>
                </div>
            </div>
            <div class="col-12" style="padding-top: 15px">
                <div class="breadcrumb__text">
                    <h2>Product Details</h2>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Breadcrumb End -->

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caranellacake";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Prepare and execute query
$sql = "SELECT * FROM product WHERE productID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

$conn->close();

if (!$product) {
    echo "<p>Product not found!</p>";
}
?>

<!-- Shop Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product__details__img">
                    <div class="product__details__big__img">
                        <img class="big_img" src="<?php echo htmlspecialchars($product['imagesID'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                    </div>
                    <div class="product__details__thumb">
                        <div class="pt__item active">
                            <img data-imgbigurl="<?php echo htmlspecialchars($product['imageID2'], ENT_QUOTES, 'UTF-8'); ?>"
                                 src="<?php echo htmlspecialchars($product['imageID2'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                        </div>
                        <div class="pt__item active">
                            <img data-imgbigurl="<?php echo htmlspecialchars($product['imageID3'], ENT_QUOTES, 'UTF-8'); ?>"
                                 src="<?php echo htmlspecialchars($product['imageID3'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                        </div>
                        <div class="pt__item active">
                            <img data-imgbigurl="<?php echo htmlspecialchars($product['imageID4'], ENT_QUOTES, 'UTF-8'); ?>"
                                 src="<?php echo htmlspecialchars($product['imageID3'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                        </div>
                        <div class="pt__item active">
                            <img data-imgbigurl="<?php echo htmlspecialchars($product['imageID5'], ENT_QUOTES, 'UTF-8'); ?>"
                                 src="<?php echo htmlspecialchars($product['imageID3'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-lg-6">
                <div class="product__details__text">
                    <div class="product__label"><?php echo htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?></div>

                    <!-- Rating Stars -->
                    <div class="custom-rating-stars">

                        <?php
                        $rating = isset($product['preview']) ? (float)$product['preview'] : 0;
                        $fullStars = floor($rating); // النجوم الممتلئة
                        $halfStar = ($rating - $fullStars >= 0.5) ? true : false; // نصف نجمة
                        $quarterStar = ($rating - $fullStars >= 0.25 && !$halfStar) ? true : false; // ربع نجمة

                        // عرض النجوم الممتلئة
                        for ($i = 1; $i <= $fullStars; $i++) {
                            echo '<span class="custom-star custom-filled">&#9733;</span>';
                        }

                        // عرض نصف نجمة إذا كانت موجودة
                        if ($halfStar) {
                            echo '<span class="custom-star custom-half">&#9733;</span>';
                        } elseif ($quarterStar) {
                            echo '<span class="custom-star custom-quarter">&#9733;</span>';
                        }

                        // عرض النجوم الفارغة لتكملة العدد إلى 5
                        for ($i = $fullStars + ($halfStar || $quarterStar ? 1 : 0); $i < 5; $i++) {
                            echo '<span class="custom-star">&#9733;</span>';
                        }
                        ?>
                    </div>

                    <h4 style="font-family: Abril Fatface,serif; color: #df5586;"><?php echo htmlspecialchars($product['productName'], ENT_QUOTES, 'UTF-8'); ?></h4>
                    <h5 style="font-size: 30px">$<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></h5>
                    <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <ul>
                        <li>Category: <span><?php echo htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?></span></li>
                    </ul>

                    <div class="product__details__option">
                        <div class="quantity">
                            <div class="pro-qty">
                                <input type="text" value="1" id="product-quantity">
                            </div>
                        </div>

                        <!-- زر Add to Cart -->
                        <a href="#" onclick="addToCart(<?php echo $productID; ?>)" class="primary-btn add-to-cart">Add to cart</a>



                    </div>

                </div>
            </div>
        </div>
        <!-- Tabs section -->
        <div class="product__details__tab">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Previews(1)</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tabs-1" role="tabpanel">
                        <div class="row d-flex justify-content-center">
                            <div class="col-lg-8">
                                <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabs-3" role="tabpanel">
                        <!-- Preview content and review form -->
                        <div class="row d-flex justify-content-center">
                            <div class="col-lg-8 review-section">
                                <form id="review-form" action="shop-details.php" method="POST">
                                    <div class="star-rating">
                                        <span id="s1" class="star" data-value="1">&#9733;</span>
                                        <span id="s2" class="star" data-value="2">&#9733;</span>
                                        <span id="s3" class="star" data-value="3">&#9733;</span>
                                        <span id="s4" class="star" data-value="4">&#9733;</span>
                                        <span id="s5" class="star" data-value="5">&#9733;</span>
                                    </div>
                                    <input type="hidden" id="rating" name="rating" value="">
                                    <input type="hidden" id="productId" name="productId" value="<?php echo $product['productID']; ?>">
                                    <input type="submit" id="submit-review" value="Submit review">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
</section>
<!-- Shop Details Section End -->


<!-- Related Products Section Begin -->
<section class="related-products spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-title">
                    <h2 style="font-family: Abril Fatface,serif;">Related Products</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="related__products__slider owl-carousel">

                <?php

                $pdo = new PDO('mysql:host=localhost;dbname=caranellacake', 'root', '');
                // استعلام لجلب تفاصيل المنتج بناءً على معرف المنتج
                $stmt = $pdo->prepare("SELECT category FROM product WHERE productID = :productID");
                $stmt->execute(['productID' => $productID]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $category = $product['category'];

                    // استعلام لجلب المنتجات التي تنتمي إلى نفس الصنف
                    $stmt = $pdo->prepare("SELECT * FROM product WHERE category = :category AND productID != :productID");
                    $stmt->execute(['category' => $category, 'productID' => $productID]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($result) {
                        foreach ($result as $row) {
                            echo '<div class="col-lg-3">'; // العمود المخصص لكل كارد
                            echo '  <div class="product-grid">'; // بداية الكارد

                            // عرض الصورة والرابط
                            echo '    <div class="product-img">';
                            echo '      <a href="shop-details.php?productID=' . urlencode($row['productID']) . '" class="img">';
                            $imagePath = $row['imagesID'];
                            echo '<img src="' . htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8') . '">';
                            echo '      </a>';

                            // إذا كانت مراجعة المنتج أكثر من 4.5، أضف تسمية hot
                            if ($row['preview'] > 4.5) {
                                echo '      <span class="product-hot-label">hot</span>';
                            }

                            // روابط العمليات مثل إضافة للسلة، إضافة لقائمة الرغبات، عرض سريع
                            echo '      <ul class="product-links">';
                            echo '        <li><a href="?action=add_to_cart&productID=' . urlencode($row['productID']) . '" data-tip="Add to Cart"><i class="fa fa-shopping-bag"></i></a></li>';
                            echo '        <li><a href="?action=add_to_wishlist&productID=' . urlencode($row['productID']) . '" data-tip="Add to Wishlist"><i class="fa fa-heart"></i></a></li>';
                            echo '        <li><a href="shop-details.php?productID=' . urlencode($row['productID']) . '" data-tip="Quick View"><i class="fa fa-search"></i></a></li>';
                            echo '      </ul>';

                            echo '    </div>'; // نهاية div الخاصة بالصورة والروابط

                            // محتوى المنتج (الاسم والسعر)
                            echo '    <div class="product-content">';
                            echo '      <h3 class="title"><a href="shop-details.php?productID=' . urlencode($row['productID']) . '">' . htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8') . '</a></h3>';
                            echo '      <div class="price">$' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '</div>';
                            echo '    </div>'; // نهاية محتوى المنتج

                            echo '  </div>'; // نهاية الكارد
                            echo '</div>'; // نهاية العمود
                        }
                    } else {
                        echo "<p>No related products found.</p>";
                    }
                } else {
                    echo "<p>Product not found.</p>";
                }

                ?>
            </div>
        </div>
    </div>
</section>
<!-- Related Products Section End -->



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
<script>
    document.querySelectorAll('.F').forEach(function(heartIcon) {
        // تحقق إذا كان المنتج في المفضلة بناءً على خاصية data-in-wishlist
        if (heartIcon.getAttribute('data-in-wishlist') === 'true') {
            heartIcon.querySelector('.heart-filled').style.display = 'inline';
            heartIcon.querySelector('.heart-empty').style.display = 'none';
        } else {
            heartIcon.querySelector('.heart-empty').style.display = 'inline';
            heartIcon.querySelector('.heart-filled').style.display = 'none';
        }

        // عند الضغط على الأيقونة
        heartIcon.addEventListener('click', function(event) {
            event.preventDefault();
            const heartEmpty = this.querySelector('.heart-empty');
            const heartFilled = this.querySelector('.heart-filled');

            if (heartFilled.style.display === 'none') {
                heartEmpty.style.display = 'none';
                heartFilled.style.display = 'inline';
                // أضف المنتج إلى المفضلة هنا
            } else {
                heartFilled.style.display = 'none';
                heartEmpty.style.display = 'inline';
                // احذف المنتج من المفضلة هنا
            }

            this.classList.toggle('active');
        });
    });

    $(document).ready(function(){
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href"); // Get the target tab pane
            $(target).find('p span').each(function(index, element) {
                $(element).css('animation-delay', (index * 0.2) + 's');
                $(element).addClass('animated'); // Add any custom class to trigger the animation
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        let selectedRating = 0;

        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                selectedRating = index + 1;
                updateStars();
            });

            star.addEventListener('mouseover', function() {
                updateStars(index + 1);
            });

            star.addEventListener('mouseout', function() {
                updateStars(selectedRating);
            });
        });

        function updateStars(rating = selectedRating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        }
    });
    // Function to send data to PHP using GET request
    function sendToPHP(productId, action) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "process_product_action.php?action=" + action + "&productID=" + productId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText); // عرض الاستجابة من PHP
            }
        };
        xhr.send();
    }

    // Function to send action to PHP using GET request
    function sendActionToPHP(action) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "process_product_action.php?action=" + action, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText); // عرض الاستجابة من PHP
            }
        };
        xhr.send();
    }

    // Function to send action and quantity to PHP using GET request
    function sendActionToPHP(action, quantity) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "process_product_action.php?action=" + action + "&quantity=" + quantity, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText); // عرض الاستجابة من PHP
            }
        };
        xhr.send();
    }

    // Event listener for "Add to Cart"
    document.querySelector('.add-to-cart').addEventListener('click', function(e) {

        var quantity = document.getElementById('product-quantity').value; // الحصول على الكمية من الحقل
        sendActionToPHP('add_to_cart', quantity); // إرسال الأمر والكمية
    });

    // Event listener for "Add to Wishlist"
    document.querySelector('.add-to-wishlist').addEventListener('click', function(e) {
        e.preventDefault(); // منع إعادة تحميل الصفحة

        var heartEmpty = this.querySelector('.heart-empty');
        var heartFilled = this.querySelector('.heart-filled');

        if (heartEmpty.style.display === 'none') {
            // إذا كان القلب مليان، نحذفه من المفضلة
            heartEmpty.style.display = 'inline';
            heartFilled.style.display = 'none';
            sendActionToPHP('remove_from_wishlist'); // إرسال الأمر 'حذف من المفضلة'
        } else {
            // إذا كان القلب فارغ، نضيفه إلى المفضلة
            heartEmpty.style.display = 'none';
            heartFilled.style.display = 'inline';
            sendActionToPHP('add_to_wishlist'); // إرسال الأمر 'إضافة للمفضلة'
        }
    });






</script>

<script>
    function addToCart(productID) {
        var quantity = document.getElementById('product-quantity').value;

        // تأكد من أن الكمية عدد صحيح وأنها أكبر من 0
        if (quantity <= 0 || isNaN(quantity)) {
            alert("Please enter a valid quantity.");
            return;
        }

        // إعادة توجيه المستخدم إلى الرابط مع الكمية
        window.location.href = 'shop-details.php?action=add_to_cart&productID=' + productID + '&quantity=' + quantity;
    }
</script>

<script>
    let selectedRating = 0;

    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            selectedRating = this.getAttribute('data-value');

            // Highlight stars up to the selected one
            document.querySelectorAll('.star').forEach(star => {
                star.style.color = star.getAttribute('data-value') <= selectedRating ? '#FFD700' : '#333';
            });
        });
    });


</script>




<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('.star-rating .star');
        const ratingInput = document.getElementById('rating');
        const reviewForm = document.getElementById('review-form');
        let selectedRating = 0;

        // وظيفة لتحديث الألوان عند التمرير
        stars.forEach(star => {
            star.addEventListener('mouseover', function () {
                const ratingValue = parseInt(this.getAttribute('data-value'));
                stars.forEach(s => {
                    s.style.color = (parseInt(s.getAttribute('data-value')) <= ratingValue) ? '#FFD700' : '#333';
                });
            });

            // إعادة النجوم إلى الحالة الأصلية عند مغادرة الماوس
            star.addEventListener('mouseout', function () {
                stars.forEach(s => {
                    s.style.color = (parseInt(s.getAttribute('data-value')) <= selectedRating) ? '#FFD700' : '#333';
                });
            });

            // تحديث تقييم النجوم عند النقر
            star.addEventListener('click', function () {
                selectedRating = this.getAttribute('data-value');
                ratingInput.value = selectedRating; // تحديث قيمة الحقل المخفي بالتقييم المحدد

                stars.forEach(s => {
                    s.style.color = (parseInt(s.getAttribute('data-value')) <= selectedRating) ? '#FFD700' : '#333';
                });
            });
        });

        // إرسال النموذج عبر fetch
        reviewForm.addEventListener('submit', function (event) {
            event.preventDefault(); // إيقاف إرسال النموذج الافتراضي

            const rating = ratingInput.value;
            if (rating) {
                fetch(this.action, {
                    method: this.method,
                    body: new FormData(this)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            window.location.href = 'shop-details.php'; // إعادة التوجيه بعد التقييم الناجح
                        } else {
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            } else {
            }
        });
    });
</script>



</body>

</html>