<?php
session_start();
require_once 'db.php';
require_once 'user.php';


// Get id From url for edit form
if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if($id > 0){
        $sqlInsert = 'SELECT * FROM users WHERE  id = :id';
        $result = $connection->prepare($sqlInsert);
        $user = $result->execute(array(':id' => $id));
        if($user === true){
          $user =  $result->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User', array('name', 'age', 'address', 'tax', 'salary'));
          $user = array_shift($user);
          $_SESSION['user'] = $user;
          $_SESSION['id'] = $id;
        }
    }
}
//delete
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if($id > 0) {
        $sqlDelete = 'DELETE FROM users WHERE id = :id';
        $result = $connection->prepare($sqlDelete);
        $delteUser = $result->execute(array(':id' => $id));
        if($delteUser === true){
            $message = 'User is Deleted';
            header('Location: http://localhost/php');
        }
    }


}

$message = 'Welcom to PDO Course';

//Insert || Update Information To Database
if (isset($_POST['submit'])) {

    $name    = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $age     = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $tax     = filter_input(INPUT_POST, 'tax', FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
    $salary  = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

    $params = array(
        ':name'      => $name,
        ':age'      => $age,
        ':address'  => $address,
        ':tax'      => $tax,
        ':salary'   => $salary


    );


    if(isset($_SESSION['user'])){
        $sqlInsert = 'UPDATE users SET name = :name, age = :age, address = :address, tax = :tax, salary = :salary WHERE  id = :id';
        $params[':id'] = $_SESSION['id'];
    }else{
        $sqlInsert = 'INSERT INTO users SET name = :name, age = :age, address = :address, tax = :tax, salary = :salary';
    }

    $stmtInsert = $connection->prepare($sqlInsert);

    if ($stmtInsert->execute($params) === true)
    {
        $message = 'Great.. ' . $name . ' Is successfully Saved';
        session_unset();
        session_destroy();
    }
}


//Reading Information from database
$sqlRead = 'SELECT * FROM users';
$statment = $connection->query($sqlRead);
$result = $statment->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User', array('name', 'age', 'address', 'tax', 'salary'));
$result = (is_array($result) && !empty($result)) ? $result : false;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
</head>


    <body>

        <h3 class="message"><?= $message; ?></h3>

        <div>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                <input type="text" name="name" placeholder="Your name.." value="<?= isset($user) ? $user->name : ''; ?>">
                <input type="number" name="age" placeholder="Your age.." value="<?= isset($user) ? $user->age : ''; ?>">
                <input type="text" name="address" placeholder="Your address.."  value="<?= isset($user) ? $user->address : ''; ?>">
                <input type="number" step="0.01" min="1" max="10" name="tax" placeholder="Your tax.."  value="<?= isset($user) ? $user->tax : ''; ?>">
                <input type="number" min="4000" max="10000" name="salary" placeholder="salary"  value="<?= isset($user) ? $user->salary : ''; ?>">


                <input type="submit" name="submit" value="<?= isset($user) ? 'Edit' : 'Save'; ?>">
            </form>

            <table style="width:100%">
                <tr>
                    <th>name</th>
                    <th>age</th>
                    <th>address</th>
                    <th>tax</th>
                    <th>salary</th>
                    <th>control</th>
                </tr>
                <?php
                    if(false !== $result){
                        foreach ($result as $user){ ?>
                                <tr>
                                     <td><?= $user->name ?></td>
                                     <td><?= $user->age ?></td>
                                     <td><?= $user->address ?></td>
                                     <td><?= $user->tax ?></td>
                                     <td><?= $user->salaryCalc() ?></td>
                                     <td>
                                         <a href="?action=edit&id=<?= $user->id; ?>"> <i class="fa fa-edit"></i> </a>
                                         <a href="?action=delete&id=<?= $user->id; ?>" onclick="if (!confirm('Are you sure you want delete <?= $user->name; ?>')) return false;"> <i class="fa fa-trash"></i> </a>
                                     </td>
                                </tr>
                     <?php   }
                    }else{ ?>
                        <td colspan="6"> <p>Your Table is Empty now</p> </td>
                  <?php  }
                ?>
            </table>

        </div>

    </body>
</html>


