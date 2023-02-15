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
<div class="container h-100">
    <div class="row h-100">
        <?PHP include("common/menu.php"); ?>
        <?PHP include("pages/".$page.".php"); ?>
     </div>
</div>
</body>
</html>
