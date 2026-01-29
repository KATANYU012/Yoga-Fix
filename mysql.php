<?php
/* blowfish ใช้เข้ารหัสคุกกี้ — ใส่สตริงสุ่มยาวๆ 32+ ตัวอักษร */
$cfg['blowfish_secret'] = 'jwK8uWmQZx3aJ1rH7pN2sV9yT4cL0eB6rD5fG8hK2mU3qW9z'; 

$i = 0;
$i++;

/* ให้ phpMyAdmin ถาม user/password ทุกครั้ง */
$cfg['Servers'][$i]['auth_type'] = 'cookie';

/* ชี้ host/port ตรงๆ */
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['port'] = '3306';

/* อย่า hardcode user/password (ลบทิ้ง/คอมเมนต์ถ้ามี) */
// $cfg['Servers'][$i]['user'] = 'root';
// $cfg['Servers'][$i]['password'] = '';

/* (ออปชัน) ปิด controluser ถ้ามีตั้งไว้ */
// unset($cfg['Servers'][$i]['controluser'], $cfg['Servers'][$i]['controlpass']);
