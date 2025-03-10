<?php
session_start();
include "db_conn.php";

if ( isset($_POST['uname']) && isset($_POST['password']) && isset($_POST['confirm_password']) 
   && isset($_POST['name']) && isset($_POST['number']) ){

    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);

    $con_pass = validate($_POST['confirm_password']);
    $name = validate($_POST['name']);
    $number = validate($_POST['number']);

    $user_data = 'uname='. $unmae. '&name='.$name;


    if (empty($uname)) {
        header("Location: signup.php?error=User Name is required&$user_data");
        exit();
    }else if (empty($pass)) {
        header("Location: signup.php?error=Password is required&$user_data");
        exit();
    }else if(empty($con_pass)){
        header("Location: signup.php?error=Confirm Password is required&$user_data");
        exit();
    }else if(empty($name)){
        header("Location: signup.php?error=Password is required&$user_data");
        exit();
    }else if(empty($number)){
        header("Location: signup.php?error=Number is required&$user_data");
        exit();
    }else if ($pass != $con_pass) {
        header("Location: signup.php?error=The confirmation password does not match&$user_data");
        exit();
    }

    else{
        
        $sql = "SELECT * FROM users WHERE user_name ='$uname'";
        $result = mysqli_query($conn,$sql);

        if (mysqli_num_rows($result) > 0) {
            header("Location: signup.php?error=The username is taken try another&$user_data");
            exit(); 
        }else{
            $sql2 = "INSERT INTO users(user_name, password, name, number) VALUES ('$uname','$pass','$name','$number')";
            $result2 = mysqli_query($conn,$sql2);
            if ($result2) {
                header("Location: home.php?success=Your account has been created successfully&$user_data");
            exit(); 
            }else {
                header("Location: signup.php?error=Unknown error occured&$user_data");
            exit(); 
            }
        }
    }

}else{    
    header("Location: signup.php");
    exit();
}


