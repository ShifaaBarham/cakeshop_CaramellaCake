<?php
session_start(); // بدء الجلسة

// التحقق من أن الطلب هو طلب POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = isset($_POST['formType']) ? $_POST['formType'] : '';
    $email = isset($_POST['txtEmail']) ? filter_var($_POST['txtEmail'], FILTER_SANITIZE_EMAIL) : '';
    $password = isset($_POST['txtPass']) ? htmlspecialchars($_POST['txtPass'], ENT_QUOTES, 'UTF-8') : '';
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? htmlspecialchars($_POST['confirmPassword'], ENT_QUOTES, 'UTF-8') : '';

    $phone = "0000000000"; // رقم الهاتف الافتراضي
    $customerImage = "img/customers/default.jpg"; // صورة افتراضية
    $gender =  'F';
    $birthday = "0000-00-00";
    $conn = new mysqli('localhost', 'root', '', 'caranellacake');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    switch ($formType) {
        case 'login':
            $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $customer_result = $stmt->get_result();

            $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $admin_result = $stmt->get_result();

            if ($customer_result->num_rows > 0) {
                $customer_data = $customer_result->fetch_assoc();
                $_SESSION['customerID'] = $customer_data['customerID'];

                // توجيه إلى index.php بعد تسجيل الدخول بنجاح
                echo '<script>
                    window.location.href = "index.php";
                </script>';
            } elseif ($admin_result->num_rows > 0) {
                $admin_data = $admin_result->fetch_assoc();
                $_SESSION['admin_id'] = $admin_data['adminID'];
                echo '<script>
                    window.location.href = "admainhome.php";
                </script>';
            } else {
                echo '<script>
                    document.getElementById("errorMessage").style.display = "block";
                </script>';
            }

            break;
            case 'signup':
            // التحقق من أن كلمتي المرور متطابقتان
            if ($password !== $confirmPassword) {
                echo "Passwords do not match.";
                exit();
            }

            // تشفير كلمة المرور

            // التحقق إذا كان البريد الإلكتروني موجود بالفعل
            $stmt = $conn->prepare("SELECT email FROM customer WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Email already exists.";
                exit();
            } else {
                // إدخال البيانات في قاعدة البيانات
                $stmt = $conn->prepare("INSERT INTO customer (name, gender, email, birth_date, password, phone, customerImage) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $name, $gender, $email, $birthday, $password, $phone, $customerImage);

                if ($stmt->execute()) {
                    // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
                    echo '<script>
                            alert("Signup successful! Please log in.");
                            window.location.href = "registrationFront.php";
                          </script>';
                } else {
                    echo "An error occurred during registration. Please try again later.";
                }
            }

            break;

        default:
            echo "Unknown form type.";
            break;
    }

    // إغلاق الاتصال بقاعدة البيانات
    $conn->close();
}
?>

<!DOCTYPE html>
<!-- Coding by CodingLab || www.codinglabweb.com -->
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Caramella cake</title>
    <link rel="icon" type="image/png" href="img/logo1.png">

    <link href="https: //fonts.googleapis.com/css2?family Latoddisplay=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/registrationStyle.css" />
    <!-- Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <style>
        .error-message {
            color: red;
            font-size: 10px;
            margin-top: 7px;
        }

    </style>
</head>
<body>

<!-- Home -->
<section class="home">
    <!-- Video background -->
    <video autoplay muted loop class="background_video">
        <source src="img/images/background1.mp4" type="video/mp4" />
        Your browser does not support the video tag.
    </video>
    <div class="acenter-container">
        <img src="img/images/logo.png" alt="Logo" class="logo">
        <div class="button-container">
            <button class="button1" id="form-open" onclick="showForm('login')">login</button>
            <button class="button1" id="form-open2" onclick="showForm('signup')">signup</button>
        </div>
    </div>
    <div class="overlay"></div>
    <div class="form_container">
        <i class="uil uil-times form_close" onclick="hideAllForms()"></i>



        <!-- Login Form -->
        <form id="loginForm" action="registrationFront.php" method="POST">
            <div class="login_form">
                <h2>Login</h2>
                <div class="input_box">
                    <input type="email" name="txtEmail" placeholder="Enter your email" id="loginEmail" required />
                    <i class="uil uil-envelope-alt email"></i>
                </div>
                <div class="input_box">
                    <input type="password" name="txtPass" placeholder="Enter your password" id="loginPassword" required />
                    <i class="uil uil-lock password"></i>
                    <i class="uil uil-eye-slash pw_hide"></i>
                </div>
                <p id="errorMessage" class="error-message" style="display: none;">Incorrect email or password.</p>
                <!-- Hidden Message Container -->
                <div id="responseMessage"></div>
                <div class="option_field">
                    <p id="errorMessage" class="error-message" style="display: none;">Incorrect email or password.</p>

                </div>
                <!-- Hidden field to specify the form type -->
                <input type="hidden" name="formType" value="login">
                <button type="submit" class="button" id="loginButton">
                    <video autoplay muted loop class="video-bg" id="videoBg">
                        <source src="img/images/cupcake.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <span class="button-text">Login</span>
                </button>
            </div>
        </form>

        <!-- Signup From -->
        <form action="registrationFront.php" method="POST">
            <input type="hidden" name="formType" value="signup">
            <div class="signup_form">
                <h2>Signup</h2>

                <div class="input_box1">
                    <input type="text" name="name" placeholder="Enter your name" required />
                    <i class="uil uil-user"></i>
                </div>
                <div class="input_box1">
                    <input type="email" name="txtEmail" placeholder="Enter your email" required />
                    <i class="uil uil-envelope-alt email"></i>
                </div>
                <div class="input_box1">
                    <input type="password" name="txtPass" placeholder="Create password" required />
                    <i class="uil uil-lock password"></i>
                    <i class="uil uil-eye-slash pw_hide"></i>
                </div>
                <div class="input_box1">
                    <input type="password" name="confirmPassword" placeholder="Confirm password" required />
                    <i class="uil uil-lock password"></i>
                    <i class="uil uil-eye-slash pw_hide"></i>
                </div>

                <button class="button" id="signupButton" type="submit">
                    <video autoplay muted loop class="video-bg" id="videoBg1">
                        <source src="img/images/cupcake.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <span class="button-text">Signup</span>
                </button>
            </div>
        </form>



        <!-- forgot password -->

    </div>
</section>

<script src="js/registrationJS.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var errorMessage = document.getElementById('errorMessage');
        var loginEmail = document.getElementById('loginEmail');
        var loginPassword = document.getElementById('loginPassword');

        // عندما يقوم المستخدم بالنقر على أي من حقول الإدخال
        loginEmail.addEventListener('focus', function() {
            errorMessage.style.display = 'none';
        });

        loginPassword.addEventListener('focus', function() {
            errorMessage.style.display = 'none';
        });


    });



</script>

</body>
</html>
