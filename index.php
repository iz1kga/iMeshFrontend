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
    <head>
        <?PHP
            include("common/header.php");
       ?>
    </head>
    <body>
        <div class="container-fluid h-100">
            <div class="row h-100">
                <?PHP include("common/menu.php"); ?>
                <?PHP include("pages/".$page.".php"); ?>
            </div>
        </div>
    </body>
</html>
