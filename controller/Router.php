<?php
class Routere
{
    public static function handle($path = '/')
    {
        /*for just testing
        $currentMethod = $_SERVER['REQUEST_METHOD'];
        $currentUri = $_SERVER['REQUEST_URI'];
         echo $currentUri;*/

        $path = '/' . ltrim($path, '/');
        $root = '/Graduation/views/index';
        $id = null; // Initialize $id here

        if (strpos($path, '/Graduation/views/cart_display?remove=') !== false) {
            $pattern = '/\/Graduation\/views\/(cart_display(?:\?remove=)?)(\d*)/';
        } else {

            $pattern = '/\/Graduation\/views\/(product|editproduct|deleteproduct|edituser|deleteuser|makeuser|makeadmin|editorder|editreview|vieworder|cancelorder|changepictures)\?id=(\d+)/';
        }
        if (preg_match($pattern, $path, $matches)) {
            // Extract the 'id' value from the matched URL
            $action = $matches[1];
            $id = $matches[2];

            // echo "id = " . $id;
        }
        //  echo $path;
        //  echo "id = " .$id;
        require "config.php";
        session_start();


        if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
            $result = mysqli_query($conn, " SELECT p.*, u.* FROM permissions p JOIN users u ON p.user_id = u.id WHERE p.guest = '1' ");
            $row = mysqli_fetch_assoc($result);
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["id"];
        }



        if ($path === $root) {
            require '../views/homepage.php';
            exit();
        }
         elseif ($path === '/Graduation/views/lab?id=' . $id) {
            require '../views/lab.php';
            exit();
         }
            else {

            require '../views/404.php';
            exit();
        }
    }
}