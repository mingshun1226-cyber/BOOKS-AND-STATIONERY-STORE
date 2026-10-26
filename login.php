<?php
include '_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email    = req('email');
    $password = req('password');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    // Validate: password
    if ($password == '') {
        $_err['password'] = 'Required';
    }

    // Login user
    if (!$_err) {
        // find user
        $stm = $_db->prepare('SELECT * FROM users WHERE email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch();

        // if email = block status ？
        if ($u) {
            if ($u->blocked_until && strtotime($u->blocked_until) > time()) {
                $_err['password'] = 'Account temporarily blocked. Please try again later.';
            }
            else {
                // if over blocked time 
                if ($u->blocked_until && strtotime($u->blocked_until) <= time()) {
                    $stm = $_db->prepare('
                        UPDATE users
                        SET login_attempts = 0, blocked_until = NULL
                        WHERE id = ?
                    ');
                    $stm->execute([$u->id]);

                    // refresh new user data
                    $stm = $_db->prepare('SELECT * FROM users WHERE email = ?');
                    $stm->execute([$email]);
                    $u = $stm->fetch();
                }

                // check password
                $stm = $_db->prepare('
                    SELECT * FROM users
                    WHERE email = ? AND password = SHA1(?)
                ');
                $stm->execute([$email, $password]);
                $match = $stm->fetch();

                if ($match) {
                    // login✔ clear the input times
                    $stm = $_db->prepare('
                        UPDATE users
                        SET login_attempts = 0, blocked_until = NULL
                        WHERE id = ?
                    ');
                    $stm->execute([$match->id]);

                    temp('info', 'Login successfully');
                    login($match, '/homepage.php');
                }
                else {
                    // password wrong
                    $attempts = $u->login_attempts + 1;

                    if ($attempts >= 3) {
                        $stm = $_db->prepare('
                            UPDATE users
                            SET login_attempts = ?, blocked_until = DATE_ADD(NOW(), INTERVAL 5 MINUTE)
                            WHERE id = ?
                        ');
                        $stm->execute([$attempts, $u->id]);

                        $_err['password'] = 'Too many failed attempts. Account blocked for 5 minutes.';
                    }
                    else {
                        $stm = $_db->prepare('
                            UPDATE users
                            SET login_attempts = ?
                            WHERE id = ?
                        ');
                        $stm->execute([$attempts, $u->id]);

                        $_err['password'] = 'Email or password not matched. Attempt ' . $attempts . ' of 3.';
                    }
                }
            }
        }
        else {
            // dont have email
            $_err['password'] = 'Email or password not matched';
        }
    }
}
// ----------------------------------------------------------------------------

$_title = 'Login';
include '_head.php';
?>

<style>
.login-wrapper {
    max-width: 460px;
    margin: 40px auto 60px auto;
}

.login-card {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 14px;
    padding: 30px 28px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.login-card h2 {
    margin: 0 0 8px 0;
    font-size: 28px;
    color: #111;
}

.login-card p {
    margin: 0 0 24px 0;
    color: #666;
    font-size: 14px;
}

.login-form-row {
    margin-bottom: 18px;
}

.login-form-row label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #222;
}

.login-form-row input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    box-sizing: border-box;
    background: #fff;
}

.login-form-row input:focus {
    outline: none;
    border-color: #0058a3;
    box-shadow: 0 0 0 3px rgba(0, 88, 163, 0.10);
}

.login-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.login-btn {
    border: none;
    border-radius: 8px;
    padding: 12px 18px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
}

.login-btn-primary {
    background: #0058a3;
    color: #fff;
}

.login-btn-primary:hover {
    background: #00457d;
}

.login-btn-secondary {
    background: #f3f3f3;
    color: #222;
    border: 1px solid #ddd;
}

.login-btn-secondary:hover {
    background: #e9e9e9;
}

.login-links {
    margin-top: 18px;
    font-size: 14px;
}

.login-links a {
    color: #0058a3;
    text-decoration: none;
    font-weight: 600;
}

.login-links a:hover {
    text-decoration: underline;
}

.err {
    display: block;
    margin-top: 6px;
    color: #d93025;
    font-size: 13px;
}

[data-theme="dark"] .login-card {
    background: #1f1f1f;
    border-color: #333;
    box-shadow: none;
}

[data-theme="dark"] .login-card h2 {
    color: #f5f5f5;
}

[data-theme="dark"] .login-card p,
[data-theme="dark"] .login-form-row label {
    color: #ddd;
}

[data-theme="dark"] .login-form-row input {
    background: #2a2a2a;
    border-color: #444;
    color: #fff;
}

[data-theme="dark"] .login-btn-secondary {
    background: #2a2a2a;
    color: #eee;
    border-color: #444;
}
</style>


<div class="login-wrapper">
    <div class="login-card">
        <h2>Welcome Back</h2>
        <p>Login to your Books & Stationery account.</p>

        <form method="post">
            <div class="login-form-row">
                <label for="email">Email</label>
                <?= html_text('email', 'maxlength="100" placeholder="Enter your email"') ?>
                <?= err('email') ?>
            </div>

            <div class="login-form-row">
                <label for="password">Password</label>
                <?= html_password('password', 'maxlength="100" placeholder="Enter your password"') ?>
                <?= err('password') ?>
            </div>

            <div class="login-actions">
                <button class="login-btn login-btn-primary">Login</button>
                <button type="reset" class="login-btn login-btn-secondary">Reset</button>
            </div>

            <div class="login-links">
                <span>Don't have an account? </span>
                <a href="/user/register.php">Register now</a>
            </div>
        </form>
    </div>
</div>

<?php
include '_foot.php';