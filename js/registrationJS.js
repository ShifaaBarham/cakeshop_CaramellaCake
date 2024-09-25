const  home = document.querySelector(".home"),
    formContainer = document.querySelector(".form_container"),
    formCloseBtn = document.querySelector(".form_close"),
    formCloseBtn1 = document.querySelector(".form_close1"),
    pwShowHide = document.querySelectorAll(".pw_hide");
// افترض أن لديك أزرار للتحكم في إظهار النماذج
const showLoginBtn = document.getElementById("form-open");
const showSignupBtn = document.getElementById("form-open2");

// افترض أن لديك النماذج نفسها
const loginForm = document.querySelector(".login_form");
const signupForm = document.querySelector(".signup_form");
const passwordForm = document.querySelector(".forgot_password");


// عند النقر على زر "Login"
showLoginBtn.addEventListener("click", () => {
    home.classList.add("show")
    formContainer.classList.add("active");
    signupForm.style.display = "none"; // إخفاء نموذج التسجيل
    loginForm.style.display = "block"; // إظهار نموذج تسجيل الدخول

});

// عند النقر على زر "Signup"
showSignupBtn.addEventListener("click", () => {
    home.classList.add("show")
    formContainer.classList.add("active");

    loginForm.style.display = "none"; // إخفاء نموذج تسجيل الدخول
    signupForm.style.display = "block"; // إظهار نموذج التسجيل

});



formCloseBtn.addEventListener("click", () => home.classList.remove("show"));
document.querySelector('.form_close').addEventListener('click', function() {
    const inputs = document.querySelectorAll('.signup_form input');
    inputs.forEach(input => input.value = '');
    const inputs1 = document.querySelectorAll('.login_form input');
    inputs1.forEach(input => input.value = '');
});

formCloseBtn1.addEventListener("click", () => {
        passwordForm.style.display = 'none';
        home.classList.add("show")
        formContainer.classList.add("active");
        loginForm.style.display = "block";

    }
);
document.querySelector('.form_close1').addEventListener('click', function() {
    const inputs = document.querySelectorAll('.forgot_password input');
    inputs.forEach(input => input.value = '');

});

pwShowHide.forEach((icon) => {
    icon.addEventListener("click", () => {
        let getPwInput = icon.parentElement.querySelector("input");
        if (getPwInput.type === "password") {
            getPwInput.type = "text";
            icon.classList.replace("uil-eye-slash", "uil-eye");
        } else {
            getPwInput.type = "password";
            icon.classList.replace("uil-eye", "uil-eye-slash");
        }
    });
});
const signupButton = document.getElementById('signupButton');
const loginButton = document.getElementById('loginButton');

const videoBg1 = document.getElementById('videoBg1');
const videoBg = document.getElementById('videoBg');


signupButton.addEventListener('mouseover', () => {
    videoBg1.style.display = 'block'; // إظهار الفيديو
    videoBg1.currentTime = 0; // إعادة تعيين وقت الفيديو إلى البداية
    videoBg1.play(); // تشغيل الفيديو

});

signupButton.addEventListener('mouseout', () => {
    videoBg1.pause(); // إيقاف تشغيل الفيديو
    videoBg1.style.display = 'none'; // إخفاء الفيديو
});

loginButton.addEventListener('mouseover', () => {
    videoBg.style.display = 'block'; // إظهار الفيديو
    videoBg.currentTime = 0; // إعادة تعيين وقت الفيديو إلى البداية
    videoBg.play(); // تشغيل الفيديو
});

loginButton.addEventListener('mouseout', () => {
    videoBg.pause(); // إيقاف تشغيل الفيديو
    videoBg.style.display = 'none'; // إخفاء الفيديو
});
//****************



// إضافة مستمع للأحداث للنقر على "نسيت كلمة المرور"
document.getElementById("forgot_pw").addEventListener('click', function(){
    loginForm.style.display = 'none';
    home.classList.add("show")
    formContainer.classList.add("active");
    passwordForm.style.display = "block";
});

const codeBtn = document.getElementById("goCode");

codeBtn.addEventListener("click", () => {
    // إخفاء العناصر المطلوبة
    const form = document.getElementById("password");
    document.getElementById("i1").style.display = "none";
    document.getElementById("head").style.display = "none";
    document.getElementById("l2").style.display = "none";
    document.getElementById("goCode").style.display = "none";

    const inputBox = document.getElementById("box");
    if (inputBox) {
        inputBox.style.display = "none"; // إخفاء الـ div الذي يحتوي على المدخلات والأيقونات
    }
    document.getElementById("backEmail").style.display = "block";
    document.getElementById("l1").style.display = "block";
    document.querySelector(".input_box2").style.display = "block";
    document.getElementById("confirm").style.display = "block";




});

const backEmail = document.getElementById("backEmail");

backEmail.addEventListener("click", () => {

    document.getElementById("backEmail").style.display = "none";
    document.getElementById("l1").style.display = "none";
    document.querySelector(".input_box2").style.display = "none";
    document.getElementById("confirm").style.display = "none";
    document.getElementById("l3").style.display = "none";
    document.querySelector(".input_box3").style.display = "none";
    document.getElementById("confirm1").style.display = "none";

    const inputBox = document.querySelector(".input_box2");
    if (inputBox) {
        inputBox.style.display = "none"; // إخفاء الـ div الذي يحتوي على المدخلات والأيقونات
    }
    const text = document.getElementById("code");
    text.value = "";

    const inputBox2 = document.querySelector(".input_box3");
    if (inputBox2) {
        inputBox2.style.display = "none"; // إخفاء الـ div الذي يحتوي على المدخلات والأيقونات
    }
    const text2 = document.getElementById("pass");
    text2.value = "";
    const text3 = document.getElementById("comnfirmPass");
    text2.value = "";


    document.getElementById("i1").style.display = "block";
    document.getElementById("head").style.display = "block";
    document.getElementById("box").style.display = "block";
    const Box = document.getElementById("box");
    if (Box) {
        Box.style.display = "block"; // إخفاء الـ div الذي يحتوي على المدخلات والأيقونات
    }
    document.getElementById("l2").style.display = "block";
    document.getElementById("goCode").style.display = "block";

});

const passBtn = document.getElementById("confirm");

passBtn.addEventListener("click", () => {
    // إخفاء العناصر المطلوبة
    document.getElementById("l1").style.display = "none";
    document.querySelector(".input_box2").style.display = "none";
    document.getElementById("confirm").style.display = "none";


    document.getElementById("l3").style.display = "block";
    document.querySelector(".input_box3").style.display = "block";
    document.getElementById("confirm1").style.display = "block";




});