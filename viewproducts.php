<?php

session_start();

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

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
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

    <!--Icon-->
    <link rel="shortcut icon"  href="img\icon\lloo.png">
    <!-- Title Page-->
    <title>Dashboard</title>

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
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

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">
    <style>
        :root {
            --background: #fde4e4;
            --navbar-width: 256px;
            --navbar-width-min: 80px;
            --navbar-dark-primary: #acf3f1;
            --navbar-dark-secondary: #68ccca;
            --navbar-light-primary: #93354b;
            --navbar-light-secondary: #ea7393;
        }

        html, body {
            margin: 0;
            background: var(--background);
            overflow-x: hidden;
        }

        #nav-toggle:checked ~ #nav-header {
            width: calc(var(--navbar-width-min) - 16px);
        }
        #nav-toggle:checked ~ #nav-content, #nav-toggle:checked ~ #nav-footer {
            width: var(--navbar-width-min);
        }
        #nav-toggle:checked ~ #nav-header #nav-title {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.1s;
        }
        #nav-toggle:checked ~ #nav-header label[for=nav-toggle] {
            left: calc(50% - 8px);
            transform: translate(-50%);
        }
        #nav-toggle:checked ~ #nav-header #nav-toggle-burger {
            background: var(--navbar-light-primary);
        }
        #nav-toggle:checked ~ #nav-header #nav-toggle-burger:before, #nav-toggle:checked ~ #nav-header #nav-toggle-burger::after {
            width: 16px;
            background: var(--navbar-light-secondary);
            transform: translate(0, 0) rotate(0deg);
        }
        #nav-toggle:checked ~ #nav-content .nav-button span {
            opacity: 0;
            transition: opacity 0.1s;
        }
        #nav-toggle:checked ~ #nav-content .nav-button .fas {
            min-width: calc(100% - 16px);
        }
        #nav-toggle:checked ~ #nav-footer #nav-footer-avatar {
            margin-left: 0;
            left: 50%;
            transform: translate(-50%);
        }
        #nav-toggle:checked ~ #nav-footer #nav-footer-titlebox, #nav-toggle:checked ~ #nav-footer label[for=nav-footer-toggle] {
            opacity: 0;
            transition: opacity 0.1s;
            pointer-events: none;
        }

        #nav-bar {
            position: fixed;
            left: 1vw;
            top: 1vw;
            height: calc(100% - 2vw);
            background-image: url("images/vv.jpg");
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            color: var(--navbar-light-primary);
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            overflow: hidden;
            user-select: none;
            z-index: 1000;
        }
        #nav-bar hr {
            margin: 0;
            position: relative;
            left: 16px;
            width: calc(100% - 32px);
            border: none;
            border-top: solid 1px var(--navbar-dark-secondary);
        }
        #nav-bar a {
            color: inherit;
            text-decoration: inherit;
        }
        #nav-bar input[type=checkbox] {
            display: none;
        }

        #nav-header {
            position: relative;
            width: var(--navbar-width);
            left: 16px;
            width: calc(var(--navbar-width) - 16px);
            min-height: 80px;
            background-image: url("images/vv.jpg");
            border-radius: 16px;
            z-index: 2;
            display: flex;
            align-items: center;
            transition: width 0.2s;
        }
        #nav-header hr {
            position: absolute;
            bottom: 0;
        }

        #nav-title {
            font-size: 1.5rem;
            transition: opacity 1s;
        }

        label[for=nav-toggle] {
            position: absolute;
            right: 0;
            width: 3rem;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        #nav-toggle-burger {
            position: relative;
            width: 16px;
            height: 2px;
            background-image: url("images/vv.jpg");
            border-radius: 99px;
            transition: background 0.2s;
        }
        #nav-toggle-burger:before, #nav-toggle-burger:after {
            content: "";
            position: absolute;
            top: -6px;
            width: 10px;
            height: 2px;
            background: var(--navbar-light-primary);
            border-radius: 99px;
            transform: translate(2px, 8px) rotate(30deg);
            transition: 0.2s;
        }
        #nav-toggle-burger:after {
            top: 6px;
            transform: translate(2px, -8px) rotate(-30deg);
        }

        #nav-content {
            margin: -16px 0;
            padding: 16px 0;
            position: relative;
            flex: 1;
            width: var(--navbar-width);
            background-image: url("images/vv.jpg");
            direction: rtl;
            overflow-x: hidden;
            transition: width 0.2s;
        }
        #nav-content::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        #nav-content::-webkit-scrollbar-thumb {
            border-radius: 99px;
            background-color: #D62929;
        }
        #nav-content::-webkit-scrollbar-button {
            height: 16px;
        }

        #nav-content-highlight {
            position: absolute;
            left: 16px;
            top: -70px;
            width: calc(100% - 16px);
            height: 54px;
            background: var(--background);
            background-attachment: fixed;
            border-radius: 16px 0 0 16px;
            transition: top 0.2s;
        }
        #nav-content-highlight:before, #nav-content-highlight:after {
            content: "";
            position: absolute;
            right: 0;
            bottom: 100%;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            box-shadow: 16px 16px var(--background);
        }
        #nav-content-highlight:after {
            top: 100%;
            box-shadow: 16px -16px var(--background);
        }

        .nav-button {
            position: relative;
            margin-left: 16px;
            height: 54px;
            display: flex;
            align-items: center;
            color: var(--navbar-light-secondary);
            direction: ltr;
            cursor: pointer;
            z-index: 1;
            transition: color 0.2s;
        }
        .nav-button span {
            transition: opacity 1s;
        }
        .nav-button .fas {
            transition: min-width 0.2s;
        }
        .nav-button:nth-of-type(1):hover {
            color: var(--navbar-dark-primary);
        }
        .nav-button:nth-of-type(1):hover ~ #nav-content-highlight {
            top: 16px;
        }
        .nav-button:nth-of-type(2):hover {
            color: var(--navbar-dark-primary);
        }
        .nav-button:nth-of-type(2):hover ~ #nav-content-highlight {
            top: 70px;
        }
        .nav-button:nth-of-type(3):hover {
            color: var(--navbar-dark-primary);
        }
        .nav-button:nth-of-type(3):hover ~ #nav-content-highlight {
            top: 124px;
        }
        .nav-button:nth-of-type(4):hover {
            color: var(--navbar-dark-primary);
        }
        .nav-button:nth-of-type(4):hover ~ #nav-content-highlight {
            top: 178px;
        }
        .nav-button:nth-of-type(5):hover {
            color: var(--navbar-dark-primary);
        }
        .nav-button:nth-of-type(5):hover ~ #nav-content-highlight {
            top: 232px;
        }
        .nav-button:nth-of-type(6):hover {
            color: var(--navbar-dark-primary);
        }
        .nav-button:nth-of-type(6):hover ~ #nav-content-highlight {
            top: 286px;
        }
        .nav-button:nth-of-type(7):hover {
            color: var(--navbar-dark-primary);
        }
        .nav-button:nth-of-type(7):hover ~ #nav-content-highlight {
            top: 340px;
        }
        .nav-button:nth-of-type(8):hover {
            color: var(--navbar-dark-primary);
        }
        .nav-button:nth-of-type(8):hover ~ #nav-content-highlight {
            top: 394px;
        }

        #nav-bar .fas {
            min-width: 3rem;
            text-align: center;
        }

        #nav-footer {
            position: relative;
            width: var(--navbar-width);
            height: 54px;
            background: var(--navbar-dark-secondary);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            z-index: 2;
            transition: width 0.2s, height 0.2s;
        }

        #nav-footer-heading {
            position: relative;
            width: 100%;
            height: 54px;
            display: flex;
            align-items: center;
        }

        #nav-footer-avatar {
            position: relative;
            margin: 11px 0 11px 16px;
            left: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            overflow: hidden;
            transform: translate(0);
            transition: 0.2s;
        }
        #nav-footer-avatar img {
            height: 100%;
        }

        #nav-footer-titlebox {
            position: relative;
            margin-left: 16px;
            width: 10px;
            display: flex;
            flex-direction: column;
            transition: opacity 1s;
        }

        #nav-footer-subtitle {
            color: var(--navbar-light-secondary);
            font-size: 0.6rem;
        }

        #nav-toggle:not(:checked) ~ #nav-footer-toggle:checked + #nav-footer {
            height: 30%;
            min-height: 54px;
        }
        #nav-toggle:not(:checked) ~ #nav-footer-toggle:checked + #nav-footer label[for=nav-footer-toggle] {
            transform: rotate(180deg);
        }

        label[for=nav-footer-toggle] {
            position: absolute;
            right: 0;
            width: 3rem;
            height: 100%;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
        }

        #nav-footer-content {
            margin: 0 16px 16px 16px;
            border-top: solid 1px var(--navbar-light-secondary);
            padding: 16px 0;
            color: var(--navbar-light-secondary);
            font-size: 0.8rem;
            overflow: auto;
        }
        #nav-footer-content::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        #nav-footer-content::-webkit-scrollbar-thumb {
            border-radius: 99px;
            background-color: #D62929;
        }
    </style>
</head>

<body class="animsition" >

<div class="page-wrapper" style="background-color: #fde4e4">
    <!-- HEADER MOBILE-->
    <header class="header-mobile d-block d-lg-none">
        <div class="header-mobile__bar">
            <div class="container-fluid">
                <div class="header-mobile-inner">

                    <button class="hamburger hamburger--slider" style="z-index: 55;" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                    </button>
                </div>
            </div>
        </div>


        <nav class="navbar-mobile">
            <div class="container-fluid">
                <ul class="navbar-mobile__list list-unstyled">
                    <li>
                        <a href="admainhome.html">
                            <i class="fas fa-chart-bar"></i>DashBoard</a>
                    </li>
                    <li>
                        <a href="addproducts.php">
                            <i class="fas fa-chart-bar"></i>Add Products</a>
                    </li>
                    <li>
                        <a href="viewproducts.php">
                            <i class="fas fa-chart-bar"></i>View Products</a>
                    </li>
                    <li>
                        <a href="table.php">
                            <i class="fas fa-chart-bar"></i>Tables</a>
                    </li>


                </ul>
            </div>
        </nav>
    </header>
    <!-- END HEADER MOBILE-->

    <!-- MENU SIDEBAR-->
    <aside class="menu-sidebar d-none d-lg-block" style="background-color: #fde4e4; height: 600px;">
        <div id="nav-bar" style="margin-top: 0px;">
            <input id="nav-toggle" type="checkbox"/>
            <div id="nav-header">
                <a id="nav-title" target="_blank">Caramella Cake</a>
                <label for="nav-toggle">
                    <span id="nav-toggle-burger"></span>
                </label>
                <hr/>
            </div>
            <div id="nav-content">
                <div class="nav-button">
                    <i class="fas fa-palette"></i>
                    <a href="admainhome.php"><span>DashBoard</span></a>
                </div>
                <div class="nav-button">
                    <i class="fas fa-images"></i>
                    <a href="addproducts.php"><span>Add Products</span></a>
                </div>
                <div class="nav-button">
                    <i class="fas fa-thumbtack"></i>
                    <a href="viewproducts.php"><span>View Products</span></a>
                </div>
                <hr/>
                <div class="nav-button">
                    <i class="fas fa-heart"></i>
                    <a href="table.php"><span>Tables</span></a>
                </div>

                <div id="nav-content-highlight"></div>
            </div>
            <input id="nav-footer-toggle" type="checkbox" />
            <div id="nav-footer" >
                <div id="nav-footer-heading">
                    <div id="nav-footer-avatar"><img src=""/></div>
                    <div id="nav-footer-titlebox">
                        <a id="nav-footer-title" href="" target="_blank">uahnbu</a>
                        <span id="nav-footer-subtitle">Admin</span>
                    </div>
                    <label for="nav-footer-toggle"><i class="fas fa-caret-up"></i></label>
                </div>
                <div id="nav-footer-content">
                    <p>Welcome, Admin! Manage your tasks efficiently, keep track of updates, and ensure smooth operation. Let's keep everything running seamlessly!</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- END MENU SIDEBAR-->

    <!-- PAGE CONTAINER-->
    <div class="page-container" style="background-color: #fde4e4">
        <!-- HEADER DESKTOP-->

        <header class=" header">
            <div class="header__top">

                <div class="container-fluid p-0 header-button" id="navbar2" style="height:70px; z-index: 6; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); margin-left: 0px;width: 100%">
                    <nav class="navbar navbar-expand-sm" style="display: flex; align-items: center; justify-content: space-between; padding: 0 20px;width: 100%">


                        <div class="header-wrap">

                            <div class="header-button "  id="navbar2" style="position: fixed; top: 0; right: 0; z-index: 1000;  padding: 10px; ">
                                <div class="noti-wrap"style="padding-right: 60px">

                                    <?php
                                    $servername = "localhost";
                                    $username = "root";
                                    $password = "";
                                    $dbname = "caranellacake";

                                    $conn = new mysqli($servername, $username, $password, $dbname);

                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }

                                    if (isset($_POST['reset_message_count'])) {
                                        $resetSql = "UPDATE admin SET unread_messages_count = 0"; // تصفير الرسائل غير المقروءة
                                        $conn->query($resetSql);
                                        echo "Messages count reset to 0.";
                                        exit;
                                    }

                                    //  أحدث الرسائل
                                    $sql = "SELECT customerEmail, message, timestamp FROM messages ORDER BY timestamp DESC LIMIT 3";
                                    $result = $conn->query($sql);

                                    $unreadMessagesSql = "SELECT unread_messages_count FROM admin";
                                    $unreadMessagesResult = $conn->query($unreadMessagesSql);
                                    $unreadMessagesRow = $unreadMessagesResult->fetch_assoc();
                                    $unreadMessages = $unreadMessagesRow['unread_messages_count'];

                                    $conn->close();
                                    ?>

                                    <div class="noti__item js-item-menu" >
                                        <i class="zmdi zmdi-email"></i>
                                        <span id="messageCount" class="quantity"><?php echo $unreadMessages; ?></span>
                                        <div class="email-dropdown js-dropdown" style="">
                                            <?php if ($result->num_rows > 0) : ?>
                                                <?php while($row = $result->fetch_assoc()) : ?>
                                                    <div class="email__item">
                                                        <div class="content">
                                                            <p><?php echo htmlspecialchars($row['message']); ?></p>
                                                            <span><?php echo htmlspecialchars($row['customerEmail']); ?>, <?php echo date('F j, Y, g:i a', strtotime($row['timestamp'])); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                            <?php else : ?>
                                                <div class="email__item">
                                                    <div class="content">
                                                        <p>No messages found.</p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <div class="email__footer">
                                                <a href="inbox.php" id="seeAllMessages">See all emails</a>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function markAllAsRead() {
                                            document.getElementById('messageCount').textContent = '0';

                                            var xhr = new XMLHttpRequest();
                                            xhr.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
                                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                            xhr.send("reset_message_count=1");

                                            xhr.onreadystatechange = function() {
                                                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                                                    console.log("Message count reset.");
                                                }
                                            };
                                        }

                                        document.getElementById('seeAllMessages').addEventListener('click', markAllAsRead);
                                    </script>




                                    <div class="account-wrap">
                                        <div class="account-item clearfix js-item-menu">
                                            <div class="image">
                                                <?php
                                                $servername = "localhost";
                                                $username = "root";
                                                $password = "";
                                                $dbname = "caranellacake";

                                                $conn = new mysqli($servername, $username, $password, $dbname);

                                                if ($conn->connect_error) {
                                                    die("Connection failed: " . $conn->connect_error);
                                                }

                                                $sql = "SELECT name, adminImage FROM admin";
                                                $result = $conn->query($sql);

                                                if ($result->num_rows > 0) {
                                                    $row = $result->fetch_assoc();

                                                    if (!empty($row['adminImage'])) {
                                                        echo '<img src="images/icon/' . htmlspecialchars($row['adminImage']) . '" alt="' . htmlspecialchars($row['name']) . '" />';
                                                    } else {
                                                        echo '<img src="images/icon/avatar-default.jpg" alt="Default Avatar" />';
                                                    }
                                                } else {
                                                    echo '<img src="images/icon/avatar-default.jpg" alt="Default Avatar" />';
                                                }

                                                $conn->close();
                                                ?>
                                            </div>
                                            <div class="content">
                                                <a class="js-acc-btn" href="#">
                                                    <?php
                                                    if (!empty($row['name'])) {
                                                        echo htmlspecialchars($row['name']);
                                                    } else {
                                                        echo 'Admin';
                                                    }
                                                    ?>
                                                </a>
                                            </div>
                                            <div class="account-dropdown js-dropdown">
                                                <div class="account-dropdown__body">
                                                    <div class="account-dropdown__item">
                                                        <a href="accounts.php">
                                                            <i class="zmdi zmdi-account"></i>Account
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="account-dropdown__footer">
                                                    <a href="registrationFront.php">
                                                        <i class="zmdi zmdi-power"></i>Logout

                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></div>
                    </nav>

                </div>
            </div>
        </header>        <!-- HEADER DESKTOP-->
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

                    $customerID = 1;
                    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['productID'])) {
                        $productID = intval($_GET['productID']);

                        // حذف المنتج من جدول product
                        $delete_sql = "DELETE FROM product WHERE productID = ?";
                        $stmt = $conn->prepare($delete_sql);
                        $stmt->bind_param("i", $productID);
                        if ($stmt->execute()) {
                            echo '<div class="alert alert-success">Product deleted successfully.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Failed to delete product.</div>';
                        }
                        $stmt->close();
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
                            $create_cart_sql = "INSERT INTO cart (customerID, productCount) VALUES (?, 1)";
                            $stmt->close(); // إغلاق البيان قبل إعادة استخدامه
                            $stmt = $conn->prepare($create_cart_sql);
                            $stmt->bind_param("i", $customerID);
                            $stmt->execute();

                            // احصل على cartID الذي تم إنشاؤه
                            $cartID = $conn->insert_id;
                        } else {
                            // إذا كانت لدى المستخدم عربة، احصل على cartID
                            $cartID = $result->fetch_assoc()['cartID'];
                        }

                        $stmt->close(); // إغلاق البيان قبل إعادة استخدامه

                        // إضافة المنتج إلى جدول cart_product
                        $check_cart_product_sql = "SELECT * FROM cart_product WHERE cartID = ? AND productID = ?";
                        $stmt = $conn->prepare($check_cart_product_sql);
                        $stmt->bind_param("ii", $cartID, $productID);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            // تحديث الكمية إذا كان المنتج موجودًا بالفعل
                            $update_cart_product_sql = "UPDATE cart_product SET productQuantity = productQuantity + 1 WHERE cartID = ? AND productID = ?";
                            $stmt->close(); // إغلاق البيان قبل إعادة استخدامه
                            $stmt = $conn->prepare($update_cart_product_sql);
                            $stmt->bind_param("ii", $cartID, $productID);
                            $stmt->execute();
                        } else {
                            // إضافة المنتج إلى العربة إذا لم يكن موجودًا
                            $insert_cart_product_sql = "INSERT INTO cart_product (cartID, productID, productQuantity) VALUES (?, ?, 1)";
                            $stmt->close(); // إغلاق البيان قبل إعادة استخدامه
                            $stmt = $conn->prepare($insert_cart_product_sql);
                            $stmt->bind_param("ii", $cartID, $productID);
                            $stmt->execute();
                        }

                        $stmt->close();
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
                            echo '        <li><a href="updateproduct.php?action=update&productID=' . urlencode($row['productID']) . '" data-tip="Update"><i class="fa fa-pencil"></i></a></li>';
                            echo '        <li><a href="?action=delete&productID=' . urlencode($row['productID']) . '" data-tip="Delete"><i class="fa fa-trash"></i></a></li>';
                            echo '      </ul>';

                            echo '    </div>'; // نهاية div الخاصة بالصورة والروابط

                            // محتوى المنتج (الاسم والسعر)
                            echo '    <div class="product-content">';
                            echo '      <h3 class="title"><a >' . htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8') . '</a></h3>';                            echo '      <div class="price">$' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '</div>';
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

    </div>
    <!-- END PAGE CONTAINER-->
</div>

</div>

<!-- Jquery JS-->
<script src="vendor/jquery-3.2.1.min.js"></script>
<!-- Bootstrap JS-->
<script src="vendor/bootstrap-4.1/popper.min.js"></script>
<script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
<!-- Vendor JS       -->
<script src="vendor/slick/slick.min.js">
</script>
<script src="vendor/wow/wow.min.js"></script>
<script src="vendor/animsition/animsition.min.js"></script>
<script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
</script>
<script src="vendor/counter-up/jquery.waypoints.min.js"></script>
<script src="vendor/counter-up/jquery.counterup.min.js">
</script>
<script src="vendor/circle-progress/circle-progress.min.js"></script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="vendor/chartjs/Chart.bundle.min.js"></script>
<script src="vendor/select2/select2.min.js">
</script>

<!-- Main JS-->
<script src="js/main1.js"></script>

</body>

</html>
<!-- end document-->
