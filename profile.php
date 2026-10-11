<?php
/**
 * profile.php
 * Renders the registered member's information together with the uploaded photo.
 */
session_start();
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/includes/helpers.php';

// Nobody has registered yet -> go to the form.
if (empty($_SESSION['member'])) {
    header('Location: index.php');
    exit;
}

$m   = $_SESSION['member'];
$age = (new DateTime($m['birthdate'] ?? 'today'))->diff(new DateTime('today'))->y;

/*
 * IMPORTANT: Every value typed by the user must be printed through e()
 *            e.g.  <?= e($m['email']) ?>
 *            e() calls htmlspecialchars() so <script> tags cannot run (XSS).
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Complete</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="container">
    <div class="alert alert-success">
        Registration successful! Welcome, <?= e($m['first_name']) ?>.
    </div>

    <article class="card member-card">
        <div class="member-banner">
            <span class="member-label">Official Member</span>
            <span class="member-no"><?= e($m['member_no']) ?></span>
        </div>

        <div class="member-body">
            <figure class="member-photo">
                <!-- TODO 17: Display the uploaded photo. src = $m['photo'], alt = "Membership photo of <full name>" -->

                <img
                src="<?= e($m['photo']) ?>"
                alt="Membership photo of <?= e($m['first_name'] . ' ' . $m['last_name']) ?>"
>

            </figure>

            <div class="member-details">
                <!-- TODO 18: Full name (first + last) inside <h1 class="member-name"> -->
                    <h1 class="member-name">
                    <?= e($m['first_name'] . ' ' . $m['last_name']) ?>
                    </h1>

                <!-- TODO 19: Username with an @ prefix inside <p class="member-username"> -->
                    <p class="member-username">
                            @<?= e($m['username']) ?>
                    </p>


                <table class="details-table">
                <tr>
                    <th>Email</th>  
                        <td>
                            <!-- TODO 20 -->
                              <?= e($m['email']) ?>
                    </td>
                </tr>
               
                <tr>
                    <th>Mobile</th>
                        <td>
                            <!-- TODO 21 -->
                             <?= e($m['mobile']) ?>
                        </td>
                </tr>
                    <!-- TODO 22: show the birthdate as "August 15, 2004 (22 years old)"
                                  Hint: date('F j, Y', strtotime($m['birthdate'])) and $age -->
                <tr>
                <th>Birthdate</th>  
                    <td> <?= e(date('F j, Y', strtotime($m['birthdate']))) ?>
                        (<?= $age ?> years old)
                    </td>
                </tr>
                <tr>
                    <th>ZIP Code</th>  
                        <td>
                            <!-- TODO 23 -->
                             <?= e($m['zip_code']) ?>
                        </td>
                </tr>
                <tr>
                    <th>Password</th>   
                        <td>&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull; 
                            <span class="muted">(hidden)

                            </span>
                    </td>
                </tr>
                <tr>
                    <th>Registered</th> 
                        <td>
                            <?= e($m['registered_at']) ?>
                        </td>
                </tr>
                </table>
            </div>
        </div>
    </article>

    <div class="actions center">
        <a href="index.php" class="btn btn-secondary">Register Another Member</a>
    </div>
</main>
</body>
</html>
