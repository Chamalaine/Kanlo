<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
<header>
    <?php require_once 'layout/menu.php'; ?>
</header>

<main>
    <h4>Dashboard</h4>

    <?php var_dump($_SESSION) ?>


    <form action="http://localhost/kanlo/home/addboard" method="post">
        <input name="title" type="text" id="title" value="Titre">
        <input name="description" type="text" id="description" value="Description">
        <input name="id" type="hidden" value="<?php echo $_SESSION['id']; ?>">
        <input type="submit" value="Envoyer">
    </form>
</main>

<?php require_once 'layout/footer.php'; ?>
</body>
</html>
