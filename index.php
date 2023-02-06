<?PHP
    include('config.php');
    if ($_GET["page"]) {
        $page = $_GET["page"];
    }
    else {
        $page = "home";
    }


?>

<!DOCTYPE html>
<html lang="en">
<?PHP
    include("common/header.php");
?>

<body>

<?PHP
    include("pages/".$page.".php");
?>


</body>
</html>
