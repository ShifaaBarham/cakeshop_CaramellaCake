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
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تأكد من وجود البيانات قبل استخدامها
    $address = isset($_POST['Address1']) ? trim($_POST['Address1']) : '';
    $buildingNumber = isset($_POST['postal']) ? trim($_POST['postal']) : '';
    $city = isset($_POST['City']) ? trim($_POST['City']) : '';
    $area = isset($_POST['Area']) ? trim($_POST['Area']) : '';
    $phone1 = isset($_POST['Phone1']) ? trim($_POST['Phone1']) : '';
    $phone2 = isset($_POST['Phone2']) ? trim($_POST['Phone2']) : '';

    // التحقق من صحة البيانات
    if (empty($address) || empty($buildingNumber) || empty($city) || empty($area) || empty($phone1) || empty($phone2)) {
        echo "<script>alert('يرجى ملء جميع الحقول.');</script>";
    } elseif (!is_numeric($buildingNumber) || !is_numeric($phone1) || !is_numeric($phone2)) {
        echo "<script>alert('رقم المبنى والهاتف يجب أن يكون أرقام.');</script>";
    } else {
        // الحصول على التاريخ الحالي
        $dateOfSubmission = date("Y-m-d");
        // الحصول على تاريخ بعد يومين
        $dateOfReceipt = date("Y-m-d", strtotime("+2 days"));

        // الاتصال بقاعدة البيانات
        $conn = new mysqli('localhost', 'root', '', 'caranellacake');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // التحقق من وجود السلة للعميل
        $stmt_cart = $conn->prepare("SELECT cartID FROM cart WHERE customerID = ?");
        $stmt_cart->bind_param("i", $customerID);
        $stmt_cart->execute();
        $cartRow = $stmt_cart->get_result()->fetch_assoc();

        if ($cartRow) {
            $cartID = $cartRow['cartID'];
        } else {
            echo "<script>alert('لم يتم العثور على سلة تسوق للعميل.');</script>";
            $conn->close();
            exit;
        }

        $checkCartQuery = "SELECT * FROM cart_product WHERE cartID = ?";
        $stmt_check_cart = $conn->prepare($checkCartQuery);
        $stmt_check_cart->bind_param("i", $cartID);
        $stmt_check_cart->execute();
        $result = $stmt_check_cart->get_result();

        if ($result->num_rows > 0) {
            $finalPrice = 0; // متغير لتخزين السعر النهائي

            // جلب بيانات المنتجات في السلة مع أسعارها
            $cartItemsQuery = "
                SELECT cp.productID, cp.productQuantity, p.price
                FROM cart_product cp
                JOIN product p ON cp.productID = p.productID
                WHERE cp.cartID = ?
            ";

            $stmt_cart_items = $conn->prepare($cartItemsQuery);
            $stmt_cart_items->bind_param("i", $cartID);
            $stmt_cart_items->execute();
            $cartItemsResult = $stmt_cart_items->get_result();

            if ($cartItemsResult->num_rows > 0) {
                while ($row = $cartItemsResult->fetch_assoc()) {
                    $productID = $row['productID'];
                    $quantity = $row['productQuantity'];
                    $price = $row['price'];

                    // حساب السعر الإجمالي لكل منتج وضمه للسعر النهائي
                    $productTotal = $price * $quantity;
                    $finalPrice += $productTotal;
                }
            }

            // إضافة الطلب إلى جدول 'order'
            $orderQuery = "INSERT INTO `order` (customerID, address, city, area, building_number, date_of_submission, date_of_receipt, final_price, delivery_price, phone1, phone2)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt_order = $conn->prepare($orderQuery);
            $deliveryPrice = 0; // افترض تكلفة التوصيل
            $stmt_order->bind_param("issssssssss", $customerID, $address, $city, $area, $buildingNumber, $dateOfSubmission, $dateOfReceipt, $finalPrice, $deliveryPrice, $phone1, $phone2);

            if ($stmt_order->execute()) {
                $orderID = $conn->insert_id; // الحصول على معرف الطلب الجديد

                // نقل المنتجات من السلة إلى جدول الطلبات 'order_product'
                $insertOrderItems = "
                    INSERT INTO order_product (orderID, productID, productQuentity1)
                    SELECT ?, cp.productID, cp.productQuantity
                    FROM cart_product cp
                    WHERE cp.cartID = ?
                ";

                $stmt_insert_order_items = $conn->prepare($insertOrderItems);
                $stmt_insert_order_items->bind_param("ii", $orderID, $cartID);

                if ($stmt_insert_order_items->execute()) {
                    // حذف السلة بعد إتمام الطلب
                    $deleteCartQuery = "DELETE FROM cart WHERE cartID = ?";
                    $stmt_delete_cart = $conn->prepare($deleteCartQuery);
                    $stmt_delete_cart->bind_param("i", $cartID);

                    if ($stmt_delete_cart->execute()) {
                        $finalPrice = number_format($finalPrice, 2); // تنسيق السعر ليكون بقيمة عشرية صحيحة
                        echo "<script>
                                var totalPrice = " . json_encode($finalPrice) . ";
                                alert('تم تقديم الطلب بنجاح! سعر طلبك هو $' + totalPrice + '.');
                             </script>";
                    } else {
                        echo "<script>alert('حدث خطأ أثناء إفراغ السلة: " . $conn->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('حدث خطأ أثناء إدخال عناصر الطلب: " . $conn->error . "');</script>";
                }
            } else {
                echo "<script>alert('حدث خطأ أثناء تقديم الطلب: " . $conn->error . "');</script>";
            }
        } else {
            // السلة فارغة
            echo "<script>alert('سلتك فارغة. يرجى إضافة عناصر إلى السلة قبل تقديم الطلب.');</script>";
        }

        // إغلاق الاتصال بقاعدة البيانات
        $conn->close();
    }
} else {
}
?>







<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    $conn = new mysqli("localhost", "root", "", "caranellacake");

    if ($conn->connect_error) {
        die("فشل الاتصال: " . $conn->connect_error);
    }

    if ($_GET['action'] == 'update_quantity') {
        $productID = $conn->real_escape_string($_GET['productID']);
        $newQuantity = intval($_GET['newQuantity']);
        $customerID = $conn->real_escape_string($_GET['customerID']);

        if ($newQuantity >= 0) { // التأكد من أن الكمية غير سالبة
            // التحقق من وجود سلة للعميل
            $cartQuery = "SELECT cartID FROM cart WHERE customerID = '$customerID'";
            $cartResult = $conn->query($cartQuery);

            if ($cartResult->num_rows > 0) {
                $cartRow = $cartResult->fetch_assoc();
                $cartID = $cartRow['cartID'];

                // التحقق من وجود المنتج في السلة
                $checkProductQuery = "SELECT * FROM cart_product WHERE cartID = $cartID AND productID = '$productID'";
                $checkProductResult = $conn->query($checkProductQuery);

                if ($checkProductResult->num_rows > 0) {
                    // تحديث الكمية في جدول cart_product
                    $updateQuery = "UPDATE cart_product SET productQuantity = $newQuantity WHERE cartID = $cartID AND productID = '$productID'";

                    if ($conn->query($updateQuery) === TRUE) {
                    } else {
                        echo "<script>alert('حدث خطأ أثناء تحديث الكمية: " . $conn->error . "'); window.location.href='checkout.php';</script>";
                    }
                } else {
                    echo "<script>alert('المنتج غير موجود في السلة.'); window.location.href='checkout.php';</script>";
                }
            } else {
                echo "<script>alert('لم يتم العثور على سلة لهذا العميل.'); window.location.href='checkout.php';</script>";
            }
        } else {
            echo "<script>alert('الكمية يجب أن تكون أكبر من أو تساوي الصفر.'); window.location.href='checkout.php';</script>";
        }
        $customerData = getCustomerData($conn, $customerID);
        $customerName = $customerData['name'];
        $customerImage = $customerData['image'];

// الحصول على عدد المنتجات في عربة العميل
        $productCount = getCartProductCount($conn, $customerID);
    }

    if ($_GET['action'] == 'remove_from_cart') {
        $productID = $conn->real_escape_string($_GET['productID']);
        $customerID = $conn->real_escape_string($_GET['customerID']);

        // التحقق من وجود سلة للعميل
        $cartQuery = "SELECT cartID FROM cart WHERE customerID = '$customerID'";
        $cartResult = $conn->query($cartQuery);

        if ($cartResult->num_rows > 0) {
            $cartRow = $cartResult->fetch_assoc();
            $cartID = $cartRow['cartID'];

            // التحقق من وجود المنتج في السلة
            $checkProductQuery = "SELECT * FROM cart_product WHERE cartID = $cartID AND productID = '$productID'";
            $checkProductResult = $conn->query($checkProductQuery);

            if ($checkProductResult->num_rows > 0) {
                // حذف المنتج من جدول cart_product
                $deleteQuery = "DELETE FROM cart_product WHERE cartID = $cartID AND productID = '$productID'";

                if ($conn->query($deleteQuery) === TRUE) {
                } else {
                    echo "<script>alert('حدث خطأ أثناء حذف المنتج: " . $conn->error . "'); window.location.href='checkout.php';</script>";
                }
            } else {
                echo "<script>alert('المنتج غير موجود في السلة.'); window.location.href='checkout.php';</script>";
            }
        } else {
            echo "<script>alert('لم يتم العثور على سلة لهذا العميل.'); window.location.href='checkout.php';</script>";
        }
        $customerData = getCustomerData($conn, $customerID);
        $customerName = $customerData['name'];
        $customerImage = $customerData['image'];

// الحصول على عدد المنتجات في عربة العميل
        $productCount = getCartProductCount($conn, $customerID);
    }

}
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">


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
        .breadcrumb-option{
            padding-top: 150px;
        }
        .breadcrumb__text ,.breadcrumb__links ,.billing-details {
            text-align: center;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .breadcrumb__text h2 {
            font-size: 50px;
            color: #dd5b85;
            font-weight: 700;
            font-style: italic;
            font-family: "Playfair Display", serif;
        }



        .breadcrumb__links{
            font-size: 16px;
            font-family: Quicksand,serif;
            font-weight: 700;
            line-height: 20px;
            word-wrap: break-word;
        }
        /* زيادة ارتفاع الحقول */
        .form-control.input_form {
            height: 50px; /* قم بتعديل الارتفاع حسب الحاجة */
        }
        /* زيادة المسافات حول الحقول */
        .form-group.row {
            margin-bottom: 1.5rem; /* قم بتعديل المسافة بين الحقول حسب الحاجة */
        }
        /* تعديل ارتفاع الحقول في المودال */
        .modal-content {
            padding: 2rem; /* قم بتعديل الحشو حول محتوى المودال حسب الحاجة */
        }
        /* تعديل إطار الحقول */
        .form-control.input_form {
            border-radius: 5px; /* تعديل زاوية الإطار */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* إضافة ظل للإطار */
        }

        /* تغيير إطار قائمة الاختيارات */
        select.form-control.input_form {
            border: 2px solid #e34d82; /* تغيير لون ونوع الإطار */
            border-radius: 5px; /* تعديل زاوية الإطار */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* إضافة ظل للإطار */
        }

        /* تعديل إطار المودال */
        .modal-content {
            border-radius: 10px; /* تعديل زاوية الإطار للمودال */
            border: 1px solid #ddd; /* تغيير لون الإطار للمودال */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* إضافة ظل للإطار */
        }
        /* افتراضيًا */
        input.input_form {
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 16px;
        }


        input.input_form:focus {
            border-color: #e34d82;
            outline: none;
        }

        .text-order {
            text-align: center;
            color: white;
            font-size: 20px;
            font-family: Quicksand,serif;
            font-weight: 700;
            line-height: 20px;
            word-wrap: break-word;
        }[type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled) {
             cursor: pointer;
         }
        .justify-content-around {
            justify-content: space-around !important;
        }
        .d-flex {
            display: flex !important;
        }
        [type=button], [type=reset], [type=submit], button {
            -webkit-appearance: button;
        }
        [type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled) {
            cursor: pointer;
        }
        .btn-div {
            border: 0;
            background: #E9258A;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
        }
        @media only screen and (min-width: 601px) and (max-width: 1200px) {
            button, select {
                font-size: 1.1rem !important;
            }
        }
        [type=button], [type=reset], [type=submit], button {
            -webkit-appearance: button;
        }
        button, select {
            text-transform: none;
        }
        button, input, optgroup, select, textarea {
            margin: 0;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }
        button {
            border-radius: 0;
        }
        .btn-div:hover {
            background-color: #8edcda; /* اللون الأزرق عند التمرير */
        }

        /* تحديد تنسيق أيقونة SVG لتكون واضحة عند التمرير */
        .btn-div svg {
            fill: white; /* لون الأيقونة */
        }

        /* تغيير لون الأيقونة عند التمرير فوق الزر */
        .btn-div:hover svg {
            fill: #fff; /* تغيير لون الأيقونة حسب الحاجة */
        }
        .spad {
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .btn:disabled {
            opacity: 0.5; /* لون باهت للأزرار المعطلة */
            cursor: not-allowed; /* تغيير شكل المؤشر عند التمرير فوق الزر المعطل */
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
        .responsive-image {
            max-width: 100%; /* تجعل الصورة تتكيف مع عرض العنصر الحاوي */
            height: auto;   /* يحافظ على نسبة العرض إلى الارتفاع */
            width: 150px;   /* عرض الصورة يكون 150 بكسل على الشاشات الكبيرة */
        }

        /* تحكم بحجم الصورة حسب الشاشة */
        @media (max-width: 768px) {
            .responsive-image {
                width: 100px; /* عرض الصورة يكون 100 بكسل على الشاشات المتوسطة */
            }
        }

        @media (max-width: 480px) {
            .responsive-image {
                width: 80px; /* عرض الصورة يكون 80 بكسل على الشاشات الصغيرة */
            }
        }
        .increment_q {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .increment_q .btn-Count {
            padding: 5px 10px;
        }

        .countCart {
            font-size: 16px;
            font-weight: bold;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .increment_q .btn-Count {
                padding: 4px 8px; /* تصغير الأزرار قليلاً */
            }

            .countCart {
                font-size: 14px; /* تصغير حجم الرقم */
            }
        }

        @media (max-width: 480px) {
            .increment_q .btn-Count {
                padding: 3px 6px; /* تصغير أكبر للأزرار على الشاشات الصغيرة */
            }

            .countCart {
                font-size: 12px; /* تصغير حجم الرقم أكثر للشاشات الصغيرة */
            }
        }




    </style>

    <style>
        /* تنسيق الزر */
        .btn-default-1 {
            background-color: #dd5b85; /* لون الخلفية */
            color: white; /* لون النص */
            border-radius: 20px; /* تدوير الزوايا */
            padding: 10px 20px; /* المسافات الداخلية (الحشو) */
            font-size: 16px; /* حجم الخط */
            font-weight: bold; /* سمك الخط */
            text-transform: uppercase; /* تحويل النص إلى أحرف كبيرة */
            transition: background-color 0.3s, border-color 0.3s, transform 0.2s; /* تأثيرات التحويل */
            font-size: 30px; font-family:Quicksand,serif;
            font-weight: 700;
            line-height: 32px;
        }

        /* تأثير عند التمرير */
        .btn-default-1:hover {
            background-color: #8fcab8; /* لون الخلفية عند التمرير */
            border-color: white; /* لون الحدود عند التمرير */
            transform: scale(1.05); /* تكبير الزر قليلاً */
        }

        /* تأثير عند الضغط */
        .btn-default-1:active {
            background-color: #dd5b85; /* لون الخلفية عند الضغط */
            border-color: white; /* لون الحدود عند الضغط */
            transform: scale(0.98); /* تصغير الزر قليلاً */
        }

        /* تأثير التركيز */
        .btn-default-1:focus {
            outline: none; /* إزالة إطار التركيز الافتراضي */
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
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="breadcrumb__text">
                    <h2>Checkout</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="breadcrumb__links">
                    <span>There are <?php echo $productCount ?> products in your cart</span>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Breadcrumb End -->

<!-- Checkout Section Begin -->
<section class="checkout spad">
    <div class="container">
        <div class="checkout__form">
            <div >
                <div class="container">
                    <!-- Billing Details Section -->
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="billing-details" style="border-bottom: 1px solid #e1e1e1">
                                <h4 class="checkout__title" style="border-bottom:none">Billing Details</h4>
                            </div>
                            <div class="modal-content">
                                <form id="myForm" action="checkout.php" method="post" enctype="multipart/form-data">

                                        <div class="modal-body">
                                            <div class="form-horizontal">
                                                <div class="form-group row my-2">
                                                    <div class="col-md-6">
                                                        <label for="Address1">Street Name</label>
                                                        <input id="Address1" name="Address1"  class="form-control input_form w-100" type="text" placeholder="Street Name">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="Name">Receiver Name</label>
                                                        <input id="Name" type="text" name="Name"  class="form-control input_form w-100" placeholder="Receiver Name">
                                                    </div>
                                                    <div class="col-md-6 d-none">
                                                        <label for="Street">Street Address</label>
                                                        <input id="Street" type="text" name="Street"  class="form-control input_form w-100" placeholder="Street Address">
                                                    </div>
                                                </div>

                                                <div class="form-group row my-2">
                                                    <div class="col-md-6">
                                                        <label for="select-city">City</label>
                                                        <select id="select-city" name="City" class="form-control input_form w-100" >
                                                            <option value="" class="khaledoption">City</option>
                                                            <option value="6101" class="khaledoption">Nablus</option>
                                                            <option value="6102" class="khaledoption">Hebron</option>
                                                            <option value="6103" class="khaledoption">Tulkarm</option>
                                                            <option value="6104" class="khaledoption">Rammallah</option>
                                                            <option value="6105" class="khaledoption">Qalqilya</option>
                                                            <option value="6106" class="khaledoption">Bethlehem</option>
                                                            <option value="6107" class="khaledoption">Jericho</option>
                                                            <option value="6108" class="khaledoption">Salfit</option>
                                                            <option value="6109" class="khaledoption">Tubas</option>
                                                            <option value="6110" class="khaledoption">Jenin</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="Area">Area Name</label>
                                                        <input id="Area" type="text" name="Area" class="form-control input_form w-100" placeholder="Area name" >
                                                    </div>
                                                </div>

                                                <div class="form-group row my-2">
                                                    <div class="col-md-12">
                                                        <label for="postal">Building Number</label>
                                                        <input id="postal" type="text" name="postal" class="form-control input_form w-100" placeholder="Building Number" >
                                                    </div>
                                                </div>

                                                <div class="form-group row my-2">
                                                    <div class="col-md-6">
                                                        <label for="Phone1">Phone</label>
                                                        <input id="Phone1" type="tel" name="Phone1" class="form-control input_form w-100" placeholder="Phone" >
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="Phone2">Alternate Phone</label>
                                                        <input id="Phone2" type="tel" name="Phone2" class="form-control input_form w-100" placeholder="Alternate Phone" >
                                                    </div>
                                                    <input type="hidden" name="customerID" value="<?php echo htmlspecialchars($customerID); ?>">

                                                </div>
                                            </div>
                                        </div>


                                    <input type="submit" id="submitBtn"  class="btn-default-1 py-2 border-0 rounded px-5">
                                </form>
                            </div>


                        </div>
                    </div>


                    <!-- Spacer between sections -->
                    <div class="row" style="margin-top: 30px;"></div>

                    <!-- Order Summary Section -->
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="checkout__order">
                                <h6 class="order__title">Your order</h6>
                                <section class="wishlist spad">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="wishlist__cart__table">
                                                    <table>
                                                        <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Unit Price</th>
                                                            <th>Quantity</th>
                                                            <th>Total</th>
                                                            <th>Remove</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $pdo = new PDO('mysql:host=localhost;dbname=caranellacake', 'root', '');
                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                        if (isset($customerID)) {
                                                            // استعلام للحصول على cartID للعميل
                                                            $stmt = $pdo->prepare("SELECT cartID FROM cart WHERE customerID = :customer_id");
                                                            $stmt->execute(['customer_id' => $customerID]);
                                                            $cart = $stmt->fetch(PDO::FETCH_ASSOC);

                                                            if ($cart) {
                                                                $cart_id = $cart['cartID'];

                                                                // استعلام للحصول على تفاصيل المنتجات من cart_product
                                                                $stmt = $pdo->prepare("SELECT productID, productQuantity FROM cart_product WHERE cartID = :cart_id");
                                                                $stmt->execute(['cart_id' => $cart_id]);
                                                                $cart_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                if ($cart_products) {
                                                                    $product_ids = array_column($cart_products, 'productID');

                                                                    // استعلام للحصول على تفاصيل المنتجات
                                                                    $stmt = $pdo->prepare("SELECT * FROM product WHERE productID IN (" . implode(',', array_map('intval', $product_ids)) . ")");
                                                                    $stmt->execute();
                                                                    $productDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                    foreach ($cart_products as $cart_product) {
                                                                        $product_id = $cart_product['productID'];
                                                                        $product_quantity = $cart_product['productQuantity'];

                                                                        foreach ($productDetails as $product) {
                                                                            if ($product['productID'] == $product_id) {
                                                                                echo "<tr>
                            <td class='product__cart__item'>
                                <div class='product__cart__item__pic'>
                                    <img src='" . htmlspecialchars($product['imagesID']) . "' alt='' class='responsive-image'>
                                </div>
                                <div class='product__cart__item__text'>
                                    <h6>" . htmlspecialchars($product['productName']) . "</h6>
                                </div>
                            </td>
                            <td class='cart__price'>$ " . number_format($product['price'], 2) . "</td>
                            <td class='cart__stock'>
                                <div class='increment_q d-flex align-items-center py-2'>
                                    <a href='checkout.php?action=update_quantity&productID=" . urlencode($product['productID']) . "&newQuantity=" . urlencode($product_quantity - 1) . "&customerID=" . urlencode($customerID) . "' class='btn btn-sm btn-dark btn-Count'>
                                        <span class='minus add'><i class='fa-solid fa-minus'></i></span>
                                    </a>
                                    <span class='px-2 countCart' id='count-" . $product['productID'] . "'>" . htmlspecialchars($product_quantity) . "</span>
                                    <a href='checkout.php?action=update_quantity&productID=" . urlencode($product['productID']) . "&newQuantity=" . urlencode($product_quantity + 1) . "&customerID=" . urlencode($customerID) . "' class='btn btn-sm btn-dark btn-Count'>
                                        <span class='plus add'><i class='fa-solid fa-plus'></i></span>
                                    </a>
                                </div>
                            </td>
                            <td class='cart__price'>$" . number_format($product['price'] * $product_quantity, 2) . "</td>
                            <td class='cart__close'>
                                <a href='checkout.php?action=remove_from_cart&productID=" . urlencode($product['productID']) . "&customerID=" . urlencode($customerID) . "' class='icon_close'>
                                    <i class='fa fa-times' aria-hidden='true'></i>
                                </a>
                            </td>
                        </tr>";
                                                                            }
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo "<tr><td colspan='5'>Your cart is empty.</td></tr>";
                                                                }
                                                            } else {
                                                                echo "<tr><td colspan='5'>Your cart is empty.</td></tr>";
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='5'>Customer ID is missing.</td></tr>";
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>


                    <!-- Spacer between sections -->
                    <div class="row" style="margin-top: 30px;"></div>

                </div>

            </div>
        </div>
    </div>
</section>
<!-- Checkout Section End -->




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
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>

</html>
