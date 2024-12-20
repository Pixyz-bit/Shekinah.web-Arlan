<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shekinah | Forgot Password</title>
  <link rel="icon" type="image/x-icon" href="/../Images/favico.svg">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <?php
    require_once __DIR__ . '/../Assets/User_auth_head.php';
    ?>
    <style>
        .container h1 {
            margin-top: .5em;
            margin-bottom: 1em;
        }

        .form input[type="submit"] {
            margin-bottom: 0;
            margin-top: 1em;
        }

        .form #inputs input[type="email"] {
            border: 1px solid rgb(164, 164, 165);
            border-radius: 5px;
            width: 100%;
        }


        .header {
            display: flex;
            gap: 3em;
            align-items: center;
        }

        .success {
            color: green;
        }

        .errors {
            color: red;
        }
    </style>

</head>

<body>

    <div class="auth-container container">
        <a class="a" href="./user_login.php"><i class="fa-solid fa-arrow-left"></i></a>
            <center>
                <h1>Forgot Password</h1>
            </center>

        <?php

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST['email'])) {
                $email = $_POST["email"];
                $token = bin2hex(random_bytes(16));
                $token_hash = hash("sha256", $token);
                $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

                $mysqli = require_once __DIR__ . "/../Database/database.php";

                $sql = "UPDATE users
                        SET reset_token_hash = ?,
                            reset_token_expires_at = ?
                        WHERE email = ?";

                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("sss", $token_hash, $expiry, $email);
                $stmt->execute();

                if ($mysqli->affected_rows) {

                    $mail = require __DIR__ . "/../User-auth/Mailer/mailer.php";
                   

                    $mail->setFrom("noreply@example.com");
                    $mail->addAddress($email);
                    $mail->Subject = "Password Reset";

                    $mail->Body = "Click <a href='http://localhost/Shekinah.web/User-auth/reset-password.php?token=$token'>here</a> to reset your password.";

                    try {
                        $mail->send();
                        
                    
                    echo "<div class='err'><div class='success'>A reset link has been sent to your email address.</div></div>";
                        unset($_POST['email']);
                    } catch (Exception $e) {
                        echo "<script>
                       
                        console.log(" . htmlspecialchars($mail->ErrorInfo) . ");
                    </script>";
                    echo "<div class='err'><div class='success'>Message could not be sent.</div></div>";
                    }
                } else {
                    if (strlen($email) !== 0) {
                        echo "<script>
                        displayToastNotification(', 'fa-check', '#27ae60', 'slide-in-slide-out');
                    </script>";
                    echo "<div class='err'><div class='success'>No account found with that email.</div></div>";
                    }
                }
            }
        }
        ?>

        <form action="" method="post" class="form">
            <div id="inputs">
                <input type="email" id="email" name="email" placeholder="Enter your Email"
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required
                    autocomplete="false">
            </div>
            <input type="submit" name="send" value="Send" class="send">
        </form>
    </div>
    <?php
    require_once __DIR__ . '/../Assets/Html_footer.php';
    ?>
</body>

</html>