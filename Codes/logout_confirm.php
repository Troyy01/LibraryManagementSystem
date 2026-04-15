<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirm Logout</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f1f4f9;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            background: #ffffff;
            width: 420px;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .box h2 {
            margin-bottom: 25px;
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        /* Buttons container */
        .btn-group {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 20px;
        }

        /* Yes button */
        .yes {
            flex: 1;
            padding: 12px;
            background: #d9534f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s ease-in-out;
        }

        .yes:hover {
            background: #c9302c;
        }

        /* No button */
        .no {
            flex: 1;
            padding: 12px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s ease-in-out;
        }

        .no:hover {
            background: #5a6268;
        }

        a {
            text-decoration: none;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="box">
        <h2>Are you sure you want to log out?</h2>

        <div class="btn-group">
            <form action="logout.php" method="POST" style="margin:0; width:100%;">
                <button type="submit" class="yes">Yes, Logout</button>
            </form>

            <a href="index.php">
                <button class="no">Cancel</button>
            </a>
        </div>
    </div>

</body>
</html>
