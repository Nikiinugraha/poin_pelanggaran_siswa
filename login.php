<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="process/login_process.php" method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username">
        <br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
</body>
</html>