<?php

$count = 0;
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the product key
    $productKey = $_POST['product_key'];
while ($count <= 10) {

    if ($count < 3) {
           // Simulate checking the product key (replace with your actual logic)
    if ($productKey === 'chaintechhub') {
        // Successful installation logic here
        header("Location: https://www.chaintechhub.com/eduport/");
        exit; // Ensure no further code is executed
    } else {
        $errorMessage = "Invalid product key. Please try again.";
    }
    } else {
      header("Location: https://www.chaintechhub.com");
        exit; 
    }
    
  
    $count++;
}
   
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPORT Installation</title>
    <link rel="icon" href="./edut.jpg"> <!-- Update with your app icon path -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
        }
        .purchase-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .logo {
            max-width: 80px; /* Adjust the size of the logo as needed */
            margin-bottom: 5px;
        }

        .purchase-btn a {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .purchase-btn a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
      
       <h1>EduPORT Installation</h1>
    <h2>License Agreement</h2>
    <p>
        By installing this software, you agree to the terms and conditions outlined in the license agreement.
        Please review the agreement before proceeding.
    </p>

    <h2>Enter Product Key</h2>
    <?php if (isset($errorMessage)): ?>
        <p class="error"><?php echo $errorMessage; ?></p>
        <div class="purchase-btn">
            <p>Need a product key? <br><a href="https://www.chaintechhub.com">Purchase Here</a></p>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="product_key" required placeholder="Enter your product key" style="width: 100%; padding: 10px; margin-bottom: 10px;">
        <button type="submit" style="padding: 10px; width: 100%;">Install</button>
    </form>

    <div class="footer">
        <h2>Developer Information</h2>
        <p>Developed by: Chain Tech Hub</p>
        <p>Contact: your.hello@chaintechhub.com</p>
        <p>Version: 1.0.0</p>
    </div>
     <img  src="./edut.jpg" alt="EduPORT Logo" class="logo icon"> <!-- Update with your logo path -->
   
</div>

</body>
</html>
