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

    $country=trim($_POST["country"] ?? "");
    $city=trim($_POST["city"] ?? "");

    $grid=trim($_POST["grid"] ?? "");
    $radio=trim($_POST["radio"] ?? "");
    $antenna=trim($_POST["antenna"] ?? "");
    $station_type=trim($_POST["station_type"] ?? "");
    $about=trim($_POST["about"] ?? "");

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

    if($country=="")
        $errors[]="Podaj kraj.";

    if($city=="")
        $errors[]="Podaj miejscowość.";

    $stmt=$pdo->prepare("SELECT id FROM users WHERE operator=?");
    $stmt->execute([$operator]);

    if($stmt->fetch())
        $errors[]="Taki operator już istnieje.";

    $stmt=$pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);

    if($stmt->fetch())
        $errors[]="Taki e-mail już istnieje.";

    if(count($errors)==0)
    {

        $password=password_hash($password,PASSWORD_DEFAULT);

        $stmt=$pdo->prepare("

INSERT INTO users

(

operator,
email,
password,
country,
city,
grid,
radio,
antenna,
station_type,
about

)

VALUES

(

?,
?,
?,
?,
?,
?,
?,
?,
?,
?

)

");

        $stmt->execute([

            $operator,
            $email,
            $password,
            $country,
            $city,
            $grid,
            $radio,
            $antenna,
            $station_type,
            $about

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
value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

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
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Powtórz hasło *

</label>

<input
type="password"
name="password2"
class="form-control"
required>

</div>

</div>


<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Kraj *

</label>

<input
type="text"
name="country"
class="form-control"
required
value="<?= htmlspecialchars($_POST['country'] ?? 'Polska') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Miejscowość *

</label>

<input
type="text"
name="city"
class="form-control"
required
placeholder="np. Elbląg"
value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">

</div>

</div>


<div class="mb-3">

<label class="form-label">

Grid (opcjonalnie)

</label>

<input
type="text"
name="grid"
class="form-control"
placeholder="np. JO94HM"
value="<?= htmlspecialchars($_POST['grid'] ?? '') ?>">

</div>


<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Radio

</label>

<input
type="text"
name="radio"
class="form-control"
placeholder="np. MOTOROLA T82"
value="<?= htmlspecialchars($_POST['radio'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Antena

</label>

<input
type="text"
name="antenna"
class="form-control"
placeholder="np. FABRYCZNA"
value="<?= htmlspecialchars($_POST['antenna'] ?? '') ?>">

</div>

</div>


<div class="mb-3">

<label class="form-label">

Rodzaj stacji

</label>

<select
name="station_type"
class="form-select">

<option value="">-- wybierz --</option>

<option value="Ręczna">Ręczna</option>

<option value="Mobil">Mobil</option>

<option value="Baza">Baza</option>

</select>

</div>


<div class="mb-3">

<label class="form-label">

Opis operatora

</label>

<textarea
name="about"
class="form-control"
rows="5"
placeholder="Napisz kilka słów o sobie..."><?= htmlspecialchars($_POST['about'] ?? '') ?></textarea>

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