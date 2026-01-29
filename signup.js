const existingEmails = ["cand@com"];

function goToLogin() {
    window.location.href = '../login/login.html';
}

function validateForm() {
    // ดึงข้อมูลจากฟอร์ม
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const gender = document.getElementById("gender").value;
    const birthdate = document.getElementById("birthdate").value;

    // --- ส่วนการตรวจสอบข้อมูลเบื้องต้น ---
    if (!username || !email || !password || !confirmPassword || !gender || !birthdate) {
        alert("กรุณากรอกข้อมูลให้ครบทุกช่อง");
        return false;
    }
    if (password !== confirmPassword) {
        alert("รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน");
        return false;
    }
    // สามารถเพิ่มการตรวจสอบอื่นๆ ได้ตามต้องการ

    const userData = {
        username: username,
        email: email,
        password: password,
        gender: gender,
        birthdate: birthdate
    };

    // --- ส่วนการส่งข้อมูลไปยัง PHP API ด้วย Fetch (เวอร์ชันแก้ไข) ---
    fetch('/smartyoga-api/signup_api.php', { // <- แก้ไข 1: ใช้ Absolute Path
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData),
        credentials: 'include' // <- แก้ไข 2: เพิ่ม credentials เพื่อให้ session ทำงาน
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // แสดงข้อความที่ได้รับจากเซิร์ฟเวอร์
        
        // ถ้าสำเร็จ (ซึ่งตอนนี้หมายถึงล็อกอินให้แล้วด้วย)
        if (data.message && data.message.includes("สำเร็จ")) {
            // ไปยังหน้าหลักได้เลย
            window.location.href = '/frontend-user/page/page1.html'; // <- แก้ไข 3: แก้ไข Path ให้ถูกต้อง
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        alert("เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองอีกครั้ง");
    });

    // ป้องกันไม่ให้ฟอร์ม submit แบบดั้งเดิม
    return false;
}