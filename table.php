<?php
session_start();


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

    <!--Icon-->
    <link rel="shortcut icon"  href="img\icon\lloo.png">
    <!-- Title Page-->
    <title>Tables</title>

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
        </header>
        <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <h2 class="title-1 m-b-25 text-center">Order Details</h2>
                                <div class="table-responsive table--no-card m-b-40" style="max-height: 400px; overflow-y: scroll;">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead style="background-color: #40E0D0; color: white;">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>Area</th>
                                            <th>Building Number</th>
                                            <th>Date of Submission</th>
                                            <th>Date of Receipt</th>
                                            <th class="text-right">Final Price</th>
                                            <th class="text-right">Delivery Price</th>
                                        </tr>
                                        </thead>
                                        <tbody style="color: #0000FF;">
                                        <?php
                                        $servername = "localhost";
                                        $username = "root";
                                        $password = "";
                                        $dbname = "caranellacake";

                                        $conn = new mysqli($servername, $username, $password, $dbname);

                                        if ($conn->connect_error) {
                                            die("Connection failed: " . $conn->connect_error);
                                        }

                                        $sql = "SELECT orderID, address, city, area, building_number, date_of_submission, date_of_receipt, final_price, delivery_price FROM `Order` ORDER BY date_of_submission DESC";

                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row['orderID'] . "</td>";
                                                echo "<td>" . $row['address'] . "</td>";
                                                echo "<td>" . $row['city'] . "</td>";
                                                echo "<td>" . $row['area'] . "</td>";
                                                echo "<td>" . $row['building_number'] . "</td>";
                                                echo "<td>" . $row['date_of_submission'] . "</td>";
                                                echo "<td>" . $row['date_of_receipt'] . "</td>";
                                                echo "<td class='text-right'>$" . number_format($row['final_price'], 2) . "</td>";
                                                echo "<td class='text-right'>$" . number_format($row['delivery_price'], 2) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='9'>No data found</td></tr>";
                                        }

                                        $conn->close();
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <h2 class="title-1 m-b-25 text-center">Product Details</h2>
                                <div class="table-responsive table--no-card m-b-40" style="max-height: 400px; overflow-y: scroll;">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead style="background-color: #40E0D0; color: white;">
                                        <tr>
                                            <th>Product ID</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th class="text-right">Price</th>
                                        </tr>
                                        </thead>
                                        <tbody style="color: #0000FF;">
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

                                        // استعلام لجلب بيانات المنتجات
                                        $sql = "SELECT productID, productName, category, description, price FROM product ORDER BY productName ASC";

                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row['productID'] . "</td>";
                                                echo "<td>" . $row['productName'] . "</td>";
                                                echo "<td>" . ucfirst(str_replace('_', ' ', $row['category'])) . "</td>";
                                                echo "<td>" . $row['description'] . "</td>";
                                                echo "<td class='text-right'>$" . number_format($row['price'], 2) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'>No data found</td></tr>";
                                        }

                                        // إغلاق الاتصال
                                        $conn->close();
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <h2 class="title-1 m-b-25 text-center">Post Details</h2>
                                <div class="table-responsive table--no-card m-b-40" style="max-height: 400px; overflow-y: scroll;">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead style="background-color: #40E0D0; color: white;">
                                        <tr>
                                            <th>Post Content</th>
                                            <th>Comments</th>
                                        </tr>
                                        </thead>
                                        <tbody style="color: #0000FF;">
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

                                        // استعلام لجلب بيانات البوستات مع التعليقات
                                        $sql = "SELECT Post.content AS postContent, Comment.commentContent 
                        FROM Post
                        LEFT JOIN Comment ON Post.postID = Comment.postID
                        ORDER BY Post.postID ASC";

                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td style='width: 50%; vertical-align: top; white-space: normal;'>" . $row['postContent'] . "</td>";
                                                echo "<td style='width: 50%; vertical-align: top; white-space: normal;'>" . $row['commentContent'] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='2'>No data found</td></tr>";
                                        }

                                        // إغلاق الاتصال
                                        $conn->close();
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-2">
                                    <a href="addpost.php" class="btn btn-success" style="background-color:#e34d82">Add Post</a>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright ©  2024 All rights reserved.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
