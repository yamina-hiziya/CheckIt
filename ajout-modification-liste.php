<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/templates/header.php';
require_once 'lib/pdo.php';
require_once 'lib/category.php';
require_once 'lib/list.php';

if (!isUserConnected()) {
    //redirection vers la page de login
    header('Location: login.php');
    exit;
}

$categories = getCategories($pdo);

$errorsList = [];
$errorsListItem = [];
$messagesList = [];

$list = [
    'title' => '',
    'category_id' => '',
];

//le formulaire d'ajout/modif de liste a été envoyé
if (isset($_POST['saveList'])) {
    if (!empty($_POST['title'])) {
        $id = null;
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
        }
        $res = saveList($pdo, $_POST['title'], (int) $_SESSION['user']['id'], (int) $_POST['category_id'], $id);

        if ($res) {
            if (!$id) {
                $messagesList[] = "La liste a bien été mise à jour";
            } else {
                header('Location: ajout-modification-liste.php?id=' . $res);
            }
        } else {
            // erreur
            $errorsList[] = "La liste n'a pas pu être enregistrée";
        }
    } else {
        //erreur
        $errorsList[] = "Le titre est obligatoire";
    }
}
//le formulaire d'ajout d'items a été envoyé
if (isset($_POST['saveListItem'])) {
    if (!empty($_POST['name'])) {
        //sauvegarder
        $item_id = (isset($_POST['item_id'])) ? (int)$_POST['item_id'] : null;
        $status = isset($_POST['status']) ? true : false;
        $res = saveListItem($pdo, $_POST['name'], (int)$_GET['id'], $status, $item_id);
        if ($res) {
            $messagesList[] = "L'item a bien été enregistré";
        }
    } else {
        //erreur
        $errorsListItem[] = "Le nom de l'item est obligatoire";
    }
}
if (isset($_GET['action']) && isset($_GET['item_id'])) {
    if ($_GET['action'] === 'deleteListItem') {
        $res = deleteListItemById($pdo, (int)$_GET['item_id']);
        header('Location: ajout-modification-liste.php?id=' . (int)$_GET['id']);
        exit;
    }
    if ($_GET['action'] === 'updateStatusListItem') {
        $res = updateListItemStatus($pdo, (int)$_GET['item_id'], (bool)$_GET['status']);
        if (isset($_GET['redirect']) && $_GET['redirect'] === 'list') {
            header('Location: mes-listes.php');
        } else {
            header('Location: ajout-modification-liste.php?id=' . $_GET['id']);
        }
    }
}


$editMode = false;
if (isset($_GET['id'])) {
    // Récupérer les données de la liste à modifier
    $list = getListById($pdo, (int)$_GET['id']);
    $editMode = true;
    // Récupérer les items de la liste
    $items = getListItems($pdo, (int)$_GET['id']);
}
?>

<div class="container col-xxl-8">
    <h1>Liste</h1>
    <?php foreach ($errorsList as $error) { ?>
        <div class="alert alert-danger">
            <?= $error; ?>
        </div>
    <?php } ?>
    <?php foreach ($messagesList as $message) { ?>
        <div class="alert alert-success">
            <?= $message; ?>
        </div>
    <?php } ?>
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button <?= ($editMode) ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded=<?= ($editMode ? 'false' : 'true') ?> aria-controls="collapseOne">
                    <?= ($editMode ? $list['title'] : 'Ajouter une liste') ?>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse <?= ($editMode) ? '' : 'show' ?>" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <form action="ajout-modification-liste.php" method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" value="<?= $list['title'] ?>" class="form-control" id="title" name="title">
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Catégorie</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <?php foreach ($categories as $category) { ?>
                                    <option <?= (($category['id'] === $list['category_id']) ? 'selected="selected"' : '') ?>
                                        value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="submit" class="btn btn-primary" name="saveList" value="Enregistrer">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <?php if (!$editMode) {  ?>
            <div class="alert alert-warning">
                Aprés avoir enregistrée, vous pourrez ajouter des items.
            </div>
        <?php } else {  ?>
            <h2 class="border-top pt-3">Items</h2>
            <?php foreach ($errorsListItem as $error) { ?>
                <div class="alert alert-danger">
                    <?= $error; ?>
                </div>
            <?php } ?>

            <form method="post" class="d-flex">
                <input type="checkbox" name="status" id="status" autocomplete="off">
                <input type="text" name="name" id="name" placeholder=" Ajouter un items" class="form-control mx-2" autocomplete="off" required>
                <input type="submit" name="saveListItem" class="btn btn-primary" value="Enregistrer">
            </form>


            <div class="row m-4 border rounded p-2">
                <?php foreach ($items as $item) { ?>
                    <div class="accordion mb-2">
                        <div class="accordion-item" id="accordeon-parent-<?= $item['id'] ?>">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-item-<?= $item['id'] ?>" aria-expanded='false' aria-expanded="false" aria-controls="collapseOne">
                                    <a class="me-2" href="?id=<?= $_GET['id'] ?>&action=updateStatusListItem&item_id=<?= $item['id'] ?>&status=<?= !$item['status'] ?>"><i class="bi bi-check-circle<?= ($item['status'] ? '-fill' : '') ?>  ?><?= ($item['status'] ? '-fill' : '') ?>"></i></a>
                                    <?= $item['name'] ?>
                                </button>
                            </h2>
                            <div id="collapse-item-<?= $item['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordion-parent-<?= $item['id'] ?>">
                                <div class="accordion-body">
                                    <form action="ajout-modification-liste.php?id=<?= $list['id'] ?>" method="post">
                                        <div class="mb-3 d-flex">
                                            <input type="checkbox" name="status" id="status-<?= $item['id'] ?>"
                                                <?= ($item['status']) ? 'checked' : '' ?> class="me-2">
                                            <input type="text" value="<?= $item['name'] ?>" class="form-control" name="name">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <input type="submit" class="btn btn-primary" name="saveListItem" value="Enregistrer">
                                        </div>
                                    </form>
                                    <a class="btn btn-primary" href="?id=<?= $_GET['id'] ?>&action=deleteListItem&item_id=<?= $item['id'] ?>" onclick="return confirm('Etes-vous sûr de vouloir supprimer cette item ?')"><i class="bi bi-trash"></i> Supprimer</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>


<?php require_once __DIR__ . '/templates/footer.php'; ?>