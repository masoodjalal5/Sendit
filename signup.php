<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sendit - Sign Up</title>
    <script src="js/script.js"></script>
    <link href='styles/style.css' rel='stylesheet'>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <h3>Send it</h3>
        <h6>From any where</h6>
    </header>
    <section class="hero"> 

        <form action="php/signup_function.php" method="POST">
            <div class="mb-3">
                <label for="exampleInputName1" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="exampleInputName1" name="nameField">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="emailField">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="passwordField">
            </div>
            <button type="submit" class="btn btn-primary" name="signUpButton">Sign Up</button>
            <div id="emailHelp" class="form-text" style="text-align: center;">Already have an account? <a href="login.php">Log In Here</a></div>
        </form>
        
    </section>
    <footer>
        <p>&copy; 2024 sendit. All Rights Reserved.</p>
    </footer>

</body>
</html>
