<?php
$host = "localhost";
$db = "pdo";
$user1 = "root";
$pass = "";
try
{
    $dsn = "mysql:host=" . $host . ";dbname=" . $db;
    $con = new PDO($dsn, $user1, $pass);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
}
catch(PDOException $error)
{
    echo "Connection Failed " . $error->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" >

<!-- JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>
<body>
<?php
if (isset($_REQUEST['search']))
{
    $id = $_REQUEST['id'];
    $searchQuery = "SELECT * FROM user WHERE id= :id";
    $searchQueryRun = $con->prepare($searchQuery);
    $searchResultExec = $searchQueryRun->execute(array(
        ":id" => $id
    ));
    if ($searchResultExec)
    {
        if ($searchQueryRun->rowCount() > 0)
        {
            foreach ($searchQueryRun as $row)
            {
                $id = $row->id;
                $user = $row->user;
                $pass = $row->pass;
            }
        }
        else
        {
            $error = 'No Id Found';
        }
    }
    else
    {
        $error = 'No Search is not Run';
    }
}
$user = "";

//INSERT
function filter($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (isset($_REQUEST['insert']))
{
    $user = filter($_REQUEST['user']);
    $pass = filter($_REQUEST['pass']);

    if (!(empty($user) && empty($pass)))
    {
        if (preg_match("/^[a-zA-Z ]*$/", $user))
        {
            $insertQuery = "INSERT INTO user(user,pass) VALUES(:u,:p)";
            $insertQueryRun = $con->prepare($insertQuery);
            $InsertResult = $insertQueryRun->execute(array(
                ':u' => $user,
                ':p' => $pass
            ));
            if ($InsertResult)
            {
                header("location:single.php");
            }
        }
        else
        {
            $error = "Name Must be Character";
        }
    }
    else
    {
        $error = "All fields blank";
    }
}

//Update
if (isset($_REQUEST['update']))
{
    $id = filter($_REQUEST['id']);
    $user = filter($_REQUEST['user']);
    $pass = filter($_REQUEST['pass']);
    if (!empty($id))
    {
        if (!(empty($user) || empty($pass)))
        {
            if (preg_match("/^[a-zA-Z ]*$/", $user))
            {
                $updateQuery = "UPDATE user SET user=:u,pass=:p where id=:id";
                $updateQueryRun = $con->prepare($updateQuery);
                $updateResult = $updateQueryRun->execute(array(
                    ':u' => $user,
                    ':p' => $pass,
                    ':id' => $id
                ));
            }
            else
            {
                $error = "User Should be in String";
            }
        }
        else
        {
            $error = "All field Required";
        }
    }
    else
    {
        $error = "Choose ID for Update the field";
    }
}

//delete
if (isset($_REQUEST['delete']))
{
    $id = $_REQUEST['id'];
    if (!empty($id))
    {
        $deleteQuery = "DELETE FROM user where id=:id";
        $deleteQueryRun = $con->prepare($deleteQuery);
        $deleteResult = $deleteQueryRun->execute(array(
            ':id' => $id
        ));
    }
    else
    {
        $error = "Please enter ID for Delete the Data";
    }
}

?>


<div class="container col-6 border border-muted pt-5 mt-5">
	<center class="display-4">CRUD PDO</center>
	<div class="alert alert-danger"><?php if (isset($error))
{
    echo $error;
} ?></div>
<form action="" method="">
   <div class="form-group clearfix">
		<label>Enter Your ID For Edit :</label>
		 <input type="text" name="id" class="form-control float-right" 
		  value="<?php if (isset($_REQUEST['search']))
{
    echo $id;
} ?>" placeholder="ID" maxlength="2" style="width:70%">
	</div>
	<div class="form-group">
			<input type="text" name="user" value="<?php echo $user ?>" class="form-control" placeholder="name">
	</div>
	<div class="form-group">
			<input type="text" name="pass" value="<?php echo $pass ?>" class="form-control" placeholder="pass">
	</div>
	<div class="form-group">
	<button type="submit" name="insert" class="btn btn-outline-success">Insert</button>
    <button type="submit" name="display" class="btn btn-outline-info">Display</button>
    <button type="submit" name="search" class="btn btn-outline-primary">Search</button>
    <button type="submit" name="update" class="btn btn-outline-primary">Update</button>
    <button type="submit" name="delete" class="btn btn-outline-danger">Delete</button>
    </div>
</form>
<?php
//#############Display on Table#############//
if (isset($_REQUEST["display"]))
{
    $fetchQuery = "SELECT * FROM user";
    $fetchQueryRun = $con->query($fetchQuery);
?>
 <table class="table">
 	<thead>
 		<tr>
 			<th>S.NO</th>
 			<th>NAME</th>
 			<th>PASS</th>
 		</tr>
 	</thead>
 	<tbody>
 		<?php while ($row = $fetchQueryRun->fetch(PDO::FETCH_ASSOC)): ?>
 		<tr>
 			<tr>
 				<td><?php echo $row['id'] ?></td>
 				<td><?php echo $row['user'] ?></td>
 				<td><?php echo $row['pass'] ?></td>
 			</tr>
 		</tr>
 	<?php
    endwhile; ?>
 	</tbody>
 </table>
  <?php
}
?>
</div>
</body>
</html>
