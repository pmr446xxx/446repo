<?php

session_start();

require_once 'includes/db.php';

if(isset($_SESSION['operator']))
{
    header("Location: index.php");
    exit;
}

$errors=[];

if($_SERVER["REQUEST_METHOD"]=="POST")
{

    $operator=trim($_POST["operator"] ?? "");
    $email=trim($_POST["email"] ?? "");
    $password=$_POST["password"] ?? "";
    $password2=$_POST["password2"] ?? "";

    if($operator=="")
        $errors[]="Podaj nazwę operatora.";

    if(strlen($operator)<3)
        $errors[]="Nazwa operatora jest za krótka.";

    if($email=="")
        $errors[]="Podaj adres e-mail.";

    if(!filter_var($email,FILTER_VALIDATE_EMAIL))
        $errors[]="Niepoprawny adres e-mail.";

    if(strlen($password)<6)
        $errors[]="Hasło musi mieć minimum 6 znaków.";

    if($password!=$password2)
        $errors[]="Hasła nie są identyczne.";

    $stmt=$pdo->prepare("SELECT operator FROM operators WHERE operator=?");
    $stmt->execute([$operator]);

    if($stmt->fetch())
        $errors[]="Taki operator już istnieje.";

    $stmt=$pdo->prepare("SELECT operator FROM operators WHERE email=?");
    $stmt->execute([$email]);

    if($stmt->fetch())
        $errors[]="Taki e-mail już istnieje.";

    if(count($errors)==0)
    {

        $password=password_hash($password,PASSWORD_DEFAULT);

        $stmt=$pdo->prepare("

INSERT INTO operators

(

operator,
email,
password,
created_at

)

VALUES

(

?,
?,
?,
NOW()

)

");

        $stmt->execute([

            $operator,
            $email,
            $password

        ]);

        $_SESSION["operator"]=$operator;

        header("Location:index.php");

        exit;

    }

}

include 'includes/header.php';
include 'includes/navbar.php';

?>

<div class="container-fluid mt-4">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="panel">

<div class="panel-title">

<i class="fa-solid fa-user-plus"></i>

Rejestracja operatora

</div>

<div class="panel-body">

<?php

if(count($errors)>0)
{

echo '<div class="alert alert-danger">';

foreach($errors as $e)
{
    echo $e."<br>";
}

echo '</div>';

}

?><form method="post">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Nazwa operatora *

</label>

<input
type="text"
name="operator"
class="form-control"
required
value="<?= htmlspecialchars($_POST['operator'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Adres e-mail *

</label>

<input
type="email"
name="email"
class="form-control"
required
value="">

</div>

</div>


<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Hasło *

</label>

<input
type="password"
name="password"
class="form-control"
required
value="">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Powtórz hasło *

</label>

<input
type="password"
name="password2"
class="form-control"
required
value="">

</div>

</div>


<div class="form-check mb-4">

<input
class="form-check-input"
type="checkbox"
required>

<label class="form-check-label">

Akceptuję regulamin serwisu.

</label>

</div><div class="d-grid">

<button
type="submit"
class="btn btn-danger btn-lg">

<i class="fa-solid fa-user-plus"></i>

Utwórz konto

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php

include 'includes/footer.php';

?>
