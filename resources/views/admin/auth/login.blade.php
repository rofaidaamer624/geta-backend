<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - TransGate</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: sans-serif;
            background: #f5f5f5;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 80px auto;
            padding: 24px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        h1 {
            font-size: 22px;
            margin-bottom: 16px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 12px;
        }
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }
        button:disabled {
            opacity: 0.6;
            cursor: default;
        }
        .alert {
            margin-top: 10px;
            padding: 8px;
            border-radius: 4px;
            font-size: 13px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h1>Admin Login</h1>

    <form id="login-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" required placeholder="admin@example.com">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" required placeholder="••••••••">
        </div>

        <button type="submit" id="login-btn">Login</button>

        <div id="alert" style="display:none;"></div>
    </form>
</div>

<script>
    const form = document.getElementById('login-form');
    const btn = document.getElementById('login-btn');
    const alertBox = document.getElementById('alert');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        alertBox.style.display = 'none';
        alertBox.className = '';
        alertBox.innerHTML = '';

        btn.disabled = true;

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        fetch('/api/admin/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Laravel يحتاج CSRF في web عادة، لكن هنا request → api.php
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email, password })
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw data;
            }
            // لو نجاح
            // خزّني التوكن في localStorage (كمثال)
            localStorage.setItem('admin_token', data.data.token);

            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-success';
            alertBox.innerHTML = 'Login successful.';

            // TODO: هنا لاحقًا تعملي redirect لصفحة dashboard (مثلاً /admin/dashboard)
            // window.location.href = '/admin/dashboard';

        })
        .catch(err => {
            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-error';
            alertBox.innerHTML = (err && err.message) ? err.message : 'Login failed.';
        })
        .finally(() => {
            btn.disabled = false;
        });
    });
</script>

</body>
</html>
