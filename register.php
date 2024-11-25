<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
    /* General styles */
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(45deg, #ff6363, #ff4cae);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 800px;
            display: flex;
            overflow: hidden;
        }

        .form-container {
            width: 60%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        .form-container input {
            background-color: #f0f0f0;
            border: none;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            width: 100%;
            outline: none;
            font-size: 14px;
        }

        .form-container button {
            margin-top: 10px;
            padding: 10px 30px;
            background-color: #512da8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background-color: #3d1b8c;
        }

        .form-container .sign-up-button {
            background-color: #ff416c;
            margin-top: 10px;
        }

        .form-container .sign-up-button:hover {
            background-color: #e03a5c;
        }

        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .overlay-container {
            width: 40%;
            background: linear-gradient(45deg, #ff416c, #ff1414);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .overlay-container h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .overlay-container p {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Register</h1>
            <form action="register.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
                <?php if (!empty($error)): ?>
                    <div class="error"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>
            </form>
        </div>
        <div class="overlay-container">
            <h1>Welcome!</h1>
            <p>Fill in your details to create an account and start your journey with us.</p>
        </div>
    </div>
</body>
</html>
