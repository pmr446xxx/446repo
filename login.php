<?php

session_start();

require_once 'includes/db.php';

if (isset($_SESSION['operator'])) {
    header("Location: index.php");
    exit;
}

$errors = [];

$login = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($login == "") {
        $errors[] = "Podaj nazwę operatora lub e-mail.";
    }

    if ($password == "") {
        $errors[] = "Podaj hasło.";
    }

    if (count($errors) == 0) {

        // Sprawdzenie użytkownika w tabeli operators
        $stmt = $pdo->prepare("
            SELECT *
            FROM operators
            WHERE operator = ?
            LIMIT 1
        ");

        $stmt->execute([
            $login
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {

            $errors[] = "Nie znaleziono użytkownika.";

        } else {

            if (password_verify($password, $user["password"])) {

                $_SESSION["operator"] = $user["operator"];
                
                // Jeśli to admin, przejdź do panelu admin
                if ($user["operator"] === "admin") {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit;

            } else {

                $errors[] = "Niepoprawne hasło.";

            }
        }
    }
}
include 'includes/header.php';
include 'includes/navbar.php';

?>

<div class="container-fluid mt-4">

<div class="row justify-content-center">

<div class="col-lg-5">

<div class="panel">

<div class="panel-title">

<i class="fa-solid fa-right-to-bracket"></i>

Logowanie

</div>

<div class="panel-body">

<?php

if (count($errors) > 0) {

    echo '<div class="alert alert-danger">';

    foreach ($errors as $e) {
        echo $e . "<br>";
    }

    echo '</div>';
}

?>

<form method="post">

<div class="mb-3">

<label class="form-label">

Nazwa operatora

</label>

<input
type="text"
name="login"
class="form-control"
required
autocomplete="username"
value="<?= htmlspecialchars($login) ?>">

</div>

<div class="mb-4">

<label class="form-label">

Hasło

</label>

<input
type="password"
name="password"
class="form-control"
required
autocomplete="current-password">

</div>

<div class="d-grid">

<button
type="submit"
class="btn btn-danger btn-lg">

<i class="fa-solid fa-right-to-bracket"></i>

Zaloguj się

</button>

</div>

<div class="text-center mt-4">

Nie masz jeszcze konta?

<br><br>

<a
href="register.php"
class="btn btn-outline-light">

<i class="fa-solid fa-user-plus"></i>

Załóż konto

</a>

</div>

</form>
</div>

</div>

</div>

</div>

</div>

<?php include 'includes/footer.php'; ?>
