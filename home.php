<?php
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
?>

<!DOCTYPE html>
<html>
    <head>
        <title>HOME</title>
        <link rel ="stylesheet" type="text/css" href="menu_style.css">
    </head>
    <body>
        <div class="menu-container">
            <h2>Main Menu</h2>
            
            
            <a href="create_budget.php" class="menu-button">Create Budget</a>
            
            
            <a href="deduct_budget.php" class="menu-button">Deduct from Budget</a>

            <a href="view_budget.php" class="menu-button">View Budget</a>
            
            
            <form action="logout.php" method="POST">
                <button type="submit" class="menu-button">Log Out</button>
            </form>
        </div>
    </body>
</html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
