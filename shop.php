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
// دالة لإنشاء استعلام SQL بناءً على المعطيات
function buildSqlQuery($conn, $category, $search, $sort, $offset, $items_per_page) {
    $sql = "SELECT productID, productName, price, description, category, preview, imagesID FROM product WHERE 1=1";

    if (!empty($category)) {
        $category = $conn->real_escape_string($category);
        $sql .= " AND category = '" . $category . "'";
    }

    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (productName LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')";
    }

    switch ($sort) {
        case 'name_asc':
            $sql .= " ORDER BY productName ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY productName DESC";
            break;
        case 'latest':
            $sql .= " ORDER BY created_at DESC"; // على افتراض أن هناك عمود created_at للمنتجات الحديثة
            break;
        case 'price_asc':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY price DESC";
            break;
        default:
            $sql .= " ORDER BY productName ASC";
    }

    $sql .= " LIMIT " . (int)$offset . ", " . (int)$items_per_page;

    return $sql;
}
?>

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

    // البحث عن cartID للمستخدم في جدول cart
    $cart_sql = "SELECT cartID FROM cart WHERE customerID = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $customerID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // إنشاء عربة جديدة للمستخدم
        $create_cart_sql = "INSERT INTO cart (customerID, productCount) VALUES (?, 1)";
        $stmt->close();
        $stmt = $conn->prepare($create_cart_sql);
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        $cartID = $conn->insert_id;

        // إضافة المنتج إلى cart_product
        $insert_cart_product_sql = "INSERT INTO cart_product (cartID, productID, productQuantity) VALUES (?, ?, 1)";
        $stmt->close();
        $stmt = $conn->prepare($insert_cart_product_sql);
        $stmt->bind_param("ii", $cartID, $productID);
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
            $update_cart_product_sql = "UPDATE cart_product SET productQuantity = productQuantity + 1 WHERE cartID = ? AND productID = ?";
            $stmt->close();
            $stmt = $conn->prepare($update_cart_product_sql);
            $stmt->bind_param("ii", $cartID, $productID);
            $stmt->execute();
        } else {
            // إضافة المنتج إلى cart_product
            $insert_cart_product_sql = "INSERT INTO cart_product (cartID, productID, productQuantity) VALUES (?, ?, 1)";
            $stmt->close();
            $stmt = $conn->prepare($insert_cart_product_sql);
            $stmt->bind_param("ii", $cartID, $productID);
            $stmt->execute();
        }

        // تحديث عدد المنتجات في cart
        $update_cart_sql = "UPDATE cart_product SET productQuantity = productQuantity + 1 WHERE cartID = ? AND productID = ?";
        $stmt->close();
        $stmt = $conn->prepare($update_cart_sql);
        $stmt->bind_param("ii", $cartID, $productID);
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

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Cake Template">
    <meta name="keywords" content="Cake, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Caramella Cake</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">


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
        .spad {
            background: white;
            padding-top: 100px;
            padding-bottom: 100px;
        }
        h2{
            color: #253D4E;
            font-size: 40px;
            font-family: Quicksand,serif;
            font-weight: 700;
            line-height: 48px;
            word-wrap: break-word;
        }
        .breadcrumb__links a,
        .breadcrumb__links span{
            text-transform: capitalize;
            color: #df5586;
            font-size: 40px !important;
            font-weight: bold;
            font-family: "Abril Fatface",serif !important;


        }
        .breadcrumb__text,
        .breadcrumb__links{
            text-align: center;
        }.breadcrumb__text h2{
             text-align: center;
             font-size: 60px !important;
         }
        .breadcrumb-option {
            position: relative;
            background-image: url('img/ss.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 100px 0;
            color: white;
            height: 400px;
            overflow: hidden; /* تأكد من إخفاء أي محتوى خارج الـ div */
        }

        .breadcrumb-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3); /* لون أبيض نصف شفاف */
            backdrop-filter: blur(1.5px); /* مقدار التغبيش */
            z-index: 1; /* تأكد أن هذا العنصر خلف المحتوى النصي */
        }

        .breadcrumb-option * {
            position: relative;
            z-index: 2; /* اجعل النص فوق التغبيش */
        }
        .fa-search{
            color:#dd5b85;
        }
        .shop__option__search form{
            border: 1px solid #dedede;
            border-radius: 5px;
        }
        .nice-select .option:hover,
        .nice-select .option.focus,
        .nice-select .option.selected.focus {
            background-color: #dd5b85 !important; /* اللون المرغوب عند التمرير على الخيار */
        }
        .nice-select {
            font-family: "Abril Fatface", serif !important;

        }
        .nice-select .current {
            color: #dd5b85; /* لون النص للخيار الظاهر */
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
        .col-md-3.col-sm-6 {
            padding: 15px; /* Spacing inside the columns to avoid cards sticking together */
        }

        .row {
            margin: 0 15px; /* Space between rows */
        }

        .product-grid {
            font-family: 'Lato', sans-serif;
            text-align: center;
            border-radius: 30px;
            border: 2px dashed #dd5b85;
            overflow: hidden;
            height: 100%; /* Ensures all boxes are the same height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-grid .product-img {
            position: relative;
            flex-grow: 1; /* Makes the image section take the remaining space */

        }

        .product-grid .product-img a.img {
            display: block;
        }

        .product-grid .product-img img {
            width: 100%;
            height: 200px; /* Set image height to ensure boxes are of equal height */
            object-fit: cover; /* Ensures the image covers the available space */
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .product-grid:hover .product-img img {
            opacity: 0.5;
        }

        .product-grid .product-hot-label {
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
        .product-grid .product-hot-label:after {
            content: "";
            background: linear-gradient(to top right, transparent 49%, #f8e6e6 50%);
            width: 10px;
            height: 10px;
            position: absolute;
            bottom: -10px;
            left: 0;
        }

        .product-grid .product-hot-label:before {
            background: #f8e6e6;
            width: 10px;
            height: 100%;
            bottom: auto;
            top: 0;
            left: 100%;
            clip-path: polygon(0 0, 100% 0, 1% 50%, 100% 100%, 0 100%);
        }

        .product-grid .product-links {
            width: 100%;
            padding: 0;
            margin: 0;
            list-style: none;
            transform: translateX(-50%);
            position: absolute;
            bottom: 25px;
            left: 50%;
            transition: all .5s ease;
        }

        .product-grid .product-links li {
            margin: 0 2px;
            display: inline-block;
            opacity: 0;
            transform: translate(0, 125%);
            transition: all 0.5s ease;
        }

        .product-grid .product-links li:nth-child(1) { transition-duration: 0.2s; }
        .product-grid .product-links li:nth-child(2) { transition-duration: 0.4s; }
        .product-grid .product-links li:nth-child(3) { transition-duration: 0.6s; }
        .product-grid .product-links li:nth-child(4) { transition-duration: 0.8s; }

        .product-grid:hover .product-links li {
            opacity: 1;
            transform: translate(0, 0);
        }

        .product-grid .product-links li a {
            color: #212121;
            background: #fff;
            font-size: 16px;
            line-height: 40px;
            width: 40px;
            height: 40px;
            box-shadow: 0 0 1px 0 rgba(0,0,0,.5);
            display: block;
            transition: all 0.3s ease;
        }

        .product-grid .product-links li a:hover {
            color: #fff;
            background: #8fcab8;
        }

        .product-grid .product-links li a:before {
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
            transition: all 0.3s ease;
        }

        .product-grid .product-links li a:hover:before {
            visibility: visible;
            opacity: 1;
            top: -30px;
        }

        .product-grid .product-content {
            padding: 12px;
            background-color: #fff; /* Change the background color as per design */
        }

        .product-grid .title {
            font-family: "Abril Fatface", serif !important;
            font-size: 20px;
            font-weight: 500;
            text-transform: capitalize;
            margin: 0 0 7px;
        }

        .product-grid .title a {
            color: #212121;
            transition: all 0.3s ease;
        }

        .product-grid .title a:hover {
            color: #8fcab8;
        }

        .product-grid .price {
            color: #dd5b85;
            font-size: 18px;
            font-weight: 700;
        }

        @media screen and (max-width: 990px) {
            .product-grid { margin-bottom: 30px; }
        }

        .shop__pagination a {
            text-decoration: none;
            color: #000;
            padding: 10px 15px;
            border-radius: 50%;
            margin: 0 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid transparent;
            transition: background-color 0.3s, border 0.3s, color 0.3s;
            font-size: 16px;
            line-height: 1;
            height: 40px;
            width: 40px;
            text-align: center;
        }

        .shop__pagination a.active,
        .shop__pagination a:hover {
            background-color: #dd5b85;
            color: #fff;
            border: 2px solid #dd5b85;
        }

        .shop__pagination {
            text-align: center;
            margin: 20px 0;
        }

        .pagination-button {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 20px;
            text-align: center;
            text-decoration: none;
            color: #333;
            font-size: 16px;
            background-color: #f8f8f8;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination-button.active {
            background-color: #dd5b85;
            color: white;
        }

        .pagination-button:hover {
            background-color: #ddd;
        }

        .pagination-button:focus {
            outline: none;
        }

        .no-products-found {
            font-size: 18px;
            color: #dd5b85;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        .shop__option__search select,
        .shop__option__right select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 16px;
            cursor: pointer;
        }

        .shop__option__search select:hover,
        .shop__option__right select:hover {
            background-color: #e9e9e9;
        }

        .shop__option__search button,
        .shop__option__right button {
            padding: 10px 20px;
            background-color: #dd5b85;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .shop__option__search button:hover,
        .shop__option__right button:hover {
            background-color: #bb4767;
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

</header><!-- Header Section End -->

<!-- Breadcrumb Begin -->
<section class="hero" style="position: relative;background-color: #ffffff; overflow: hidden; padding-top: 1px; height:430px; margin-top: 72px; background-image: url('img/ss.jpg'); background-size: cover; background-position: center;">

    <!-- Transparent Banner -->
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(255, 255, 255, 0.7); padding: 20px; border-radius: 10px; z-index: 4;">
        <h1 style="font-size: 36px; color: #f4cccc; text-align: center; font-family: 'scr' !important; text-shadow: 2px 2px 5px rgb(96,42,48);">Shop <br> Home / Shop</h1>
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
<!-- Breadcrumb End -->

<!-- Shop Section Begin -->
<section class="shop spad">
    <div class="container">
        <div class="shop__option">
            <div class="row">
                <div class="col-lg-7 col-md-7">
                    <div class="shop__option__search">
                        <form action="" method="GET">
                            <!-- فئة -->
                            <select name="category" onchange="this.form.submit()">
                                <option value="">Categories</option>
                                <option value="Ready cake" <?php if(isset($_GET['category']) && $_GET['category'] == 'Ready cake') echo 'selected'; ?>>Ready Cake</option>
                                <option value="Mini cake" <?php if(isset($_GET['category']) && $_GET['category'] == 'Mini cake') echo 'selected'; ?>>Mini Cake</option>
                                <option value="Cake pieces" <?php if(isset($_GET['category']) && $_GET['category'] == 'Cake pieces') echo 'selected'; ?>>Cake Pieces</option>
                                <option value="Cerating" <?php if(isset($_GET['category']) && $_GET['category'] == 'Catering') echo 'selected'; ?>>Catering</option>
                                <option value="Flowers" <?php if(isset($_GET['category']) && $_GET['category'] == 'Flowers') echo 'selected'; ?>>Flowers</option>
                                <option value="Decorated cake" <?php if(isset($_GET['category']) && $_GET['category'] == 'Decorated cake') echo 'selected'; ?>>Decorated Cake</option>
                            </select>

                            <!-- بحث -->
                            <input type="text" name="search" placeholder="Search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">

                            <!-- إرسال -->
                            <button type="submit"><i class="fa fa-search"></i></button>

                            <!-- حقول مخفية للترتيب -->
                            <input type="hidden" name="sort" value="<?php echo isset($_GET['sort']) ? $_GET['sort'] : ''; ?>">
                        </form>
                    </div>
                </div>

                <div class="col-lg-5 col-md-5">
                    <div class="shop__option__right">
                        <form action="" method="GET">
                            <!-- ترتيب -->
                            <select name="sort" onchange="this.form.submit()">
                                <option value="">Default sorting</option>
                                <option value="name_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name_asc') echo 'selected'; ?>>A to Z</option>
                                <option value="name_desc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name_desc') echo 'selected'; ?>>Z to A</option>
                                <option value="latest" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'latest') echo 'selected'; ?>>Latest Product</option>
                                <option value="price_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_asc') echo 'selected'; ?>>Price Low First</option>
                                <option value="price_desc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_desc') echo 'selected'; ?>>Price High First</option>
                            </select>

                            <!-- حقول مخفية للفئات والبحث -->
                            <input type="hidden" name="category" value="<?php echo isset($_GET['category']) ? $_GET['category'] : ''; ?>">
                            <input type="hidden" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "caranellacake";
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }




            if (isset($_GET['action']) && $_GET['action'] == 'add_to_wishlist' && isset($_GET['productID'])) {
                $productID = intval($_GET['productID']);

                // البحث عن wishlistID للمستخدم في جدول wishlist
                $wishlist_sql = "SELECT wishlistID FROM wishlist WHERE customerID = ?";
                $stmt = $conn->prepare($wishlist_sql);
                $stmt->bind_param("i", $customerID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {
                    // إذا لم يكن لدى المستخدم قائمة مفضلة، قم بإنشاء واحدة جديدة
                    $create_wishlist_sql = "INSERT INTO wishlist (customerID) VALUES (?)";
                    $stmt->close(); // إغلاق البيان قبل إعادة استخدامه
                    $stmt = $conn->prepare($create_wishlist_sql);
                    $stmt->bind_param("i", $customerID);
                    $stmt->execute();

                    // احصل على wishlistID الذي تم إنشاؤه
                    $wishlistID = $conn->insert_id;
                } else {
                    // إذا كانت لدى المستخدم قائمة مفضلة، احصل على wishlistID
                    $wishlistID = $result->fetch_assoc()['wishlistID'];
                }

                $stmt->close(); // إغلاق البيان قبل إعادة استخدامه

                // التحقق إذا كان المنتج موجودًا بالفعل في قائمة المفضلة
                $check_wishlist_product_sql = "SELECT * FROM wishlist_product WHERE wishlistID = ? AND productID = ?";
                $stmt = $conn->prepare($check_wishlist_product_sql);
                $stmt->bind_param("ii", $wishlistID, $productID);
                $stmt->execute();
                $result = $stmt->get_result();

                if (!$result->num_rows > 0) {

                    // إذا لم يكن المنتج موجودًا، أضفه إلى قائمة المفضلة
                    $insert_wishlist_product_sql = "INSERT INTO wishlist_product (wishlistID, productID) VALUES (?, ?)";
                    $stmt->close(); // إغلاق البيان قبل إعادة استخدامه
                    $stmt = $conn->prepare($insert_wishlist_product_sql);
                    $stmt->bind_param("ii", $wishlistID, $productID);
                    $stmt->execute();
                }

                $stmt->close();
            }

            // إعداد بيانات العرض
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $items_per_page = 12;
            $offset = ($current_page - 1) * $items_per_page;

            $category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
            $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
            $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

            $sql = buildSqlQuery($conn, $category, $search, $sort, $offset, $items_per_page);

            $result = $conn->query($sql);

            // حساب إجمالي المنتجات وعدد الصفحات
            $total_products_sql = "SELECT COUNT(*) as count FROM product WHERE 1=1";
            if ($category) {
                $total_products_sql .= " AND category = '" . $category . "'";
            }
            if ($search) {
                $total_products_sql .= " AND (productName LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')";
            }
            $total_products = $conn->query($total_products_sql)->fetch_assoc()['count'];
            $total_pages = ceil($total_products / $items_per_page);

            if ($result->num_rows > 0) {
                echo '<div class="row">'; // بدء الصف الذي يحتوي على الكروت
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-3 col-sm-6">'; // العمود المخصص لكل كارد
                    echo '  <div class="product-grid">'; // بداية الكارد

                    // عرض الصورة والرابط
                    echo '    <div class="product-img">';
                    echo '      <a  class="img">';
                    $imagePath = $row['imagesID'];
                    echo '<img src="' . htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8') . '">';
                    echo '      </a>';

                    // إذا كانت مراجعة المنتج أكثر من 4.5، أضف تسمية hot
                    if ($row['preview'] > 4.5) {
                        echo '      <span class="product-hot-label">hot</span>';
                    }

                    // روابط العمليات مثل إضافة للسلة، إضافة لقائمة الرغبات، عرض سريع
                    echo '      <ul class="product-links">';
                    echo '        <li><a href="shop.php?action=add_to_cart&productID=' . urlencode($row['productID']) . '" data-tip="Add to Cart"><i class="fa fa-shopping-bag"></i></a></li>';
                    echo '        <li><a href="?action=add_to_wishlist&productID=' . urlencode($row['productID']) . '" data-tip="Add to Wishlist"><i class="fa fa-heart"></i></a></li>';
                    echo '        <li><a href="shop-details.php?productID=' . urlencode($row['productID']) . '" data-tip="Quick View"><i class="fa fa-search"></i></a></li>';
                    echo '      </ul>';

                    echo '    </div>'; // نهاية div الخاصة بالصورة والروابط

                    // محتوى المنتج (الاسم والسعر)
                    echo '    <div class="product-content">';
                    echo '      <h3 class="title"><a >' . htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8') . '</a></h3>';
                    echo '      <div class="price">$' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '</div>';
                    echo '    </div>'; // نهاية محتوى المنتج

                    echo '  </div>'; // نهاية الكارد
                    echo '</div>'; // نهاية العمود
                }
                echo '</div>'; // نهاية الصف
            } else {
                echo '<p>No products found.</p>'; // في حال عدم وجود منتجات
            }


            $conn->close();
            ?>
        </div>

        <div class="shop__pagination">
            <?php
            // توليد عنوان URL الأساسي للترقيم
            $base_url = '?page=';
            if (!empty($_GET['category'])) {
                $base_url .= '&category=' . urlencode($_GET['category']);
            }
            if (!empty($_GET['search'])) {
                $base_url .= '&search=' . urlencode($_GET['search']);
            }
            if (!empty($_GET['sort'])) {
                $base_url .= '&sort=' . urlencode($_GET['sort']);
            }

            // إضافة رقم الصفحة الحالية إلى عنوان URL الأساسي
            $base_url = rtrim($base_url, '&'); // إزالة '&' النهائي إذا كان موجودًا

            if ($current_page > 1) {
                echo '<a href="' . $base_url . '&page=' . ($current_page - 1) . '" class="pagination-button">&laquo; previous</a>';
            }

            for ($page = 1; $page <= $total_pages; $page++) {
                $class = ($page == $current_page) ? 'class="pagination-button active"' : 'class="pagination-button"';
                echo '<a href="' . $base_url . '&page=' . $page . '" ' . $class . '>' . $page . '</a>';
            }

            if ($current_page < $total_pages) {
                echo '<a href="' . $base_url . '&page=' . ($current_page + 1) . '" class="pagination-button">Next &raquo;</a>';
            }
            ?>
        </div>




</section>
<!-- Shop Section End -->


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
    document.getElementById("login1").addEventListener('click', function() {
        window.location.href = 'registrationFront.php';
    });

</script>
<!-- JavaScript to enhance interaction -->

</body>

</html>