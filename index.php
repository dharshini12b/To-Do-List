<?php
$db_host = '127.0.0.1';
$db_user = "root";
$db_pass = "";
$db_name = "todolist";
$msg = "";
$classs = "";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if($conn->connect_error){
    $msg = "<p>Conntection Failed " . $conn->connect_error . "</p>";
    $classs = "error visible";
}
else{
    if(isset($_POST["add_task"])){
        $task = $_POST["task"];
        $stmt = $conn->prepare("SELECT task FROM TASK WHERE task = ?");
        $stmt->bind_param("s",$task);
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->num_rows>0){
            $msg = "<p>Task Already Exists</p>";
            $classs = "error visible";
        }
        else{
            $insert_stmt = $conn->prepare("INSERT INTO Task (task) VALUES (?)");
            $insert_stmt->bind_param("s",$task);
            if($insert_stmt->execute()){
                   $msg = "<p>Task Added Successfully</p>";
                   $classs = "success visible";
                   //header("Location:index.php");
            }
            else{
                $msg = "<p>Try Again</p>";
                $classs = "error visible";
            }
        }
    }
    if(isset($_GET["complete"])){
        $id = $_GET["complete"];

        $conn->query("DELETE FROM Task WHERE id = $id");
        header("Location:index.php");
    }

    $result = $conn->query("SELECT * FROM Task WHERE status = 'Pending' order by id desc");
   
    /*$result->execute();
    $result->store_result();
    if($result->num_rows<1){
        $msg = "<p>No Task Exists.</p>";
        $class = "<p>error visible</p>";
    }*/
    $conn->close();
}
?>


<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ToDo List</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container">
            <h1>To Do List</h1>
            <form class="main_box" action="index.php" method="post">
                <label for="task" id="task">Task:</label>
                <input type="text" name="task" placeholder="Add your Task" required>
                <button class="add_task" name="add_task">Add</button>
               <div class="php_display <?php echo $classs; ?>"> <?php echo $msg; ?> </div>
                  
               <p class="encourage"> <?php if($result->num_rows>0){
                    echo $result->num_rows . " more to go, happiness loading✨......";
                    } 
                    else{
                        echo "🚀 Nothing on your list — start adding dreams to chase!";
                    }?>
                </p>
            <ol type="9">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="group">
                    <li> <?php echo $row["Task"]; ?>
                
                       <button class="action"><a href="index.php?complete=<?php echo $row["id"]; ?>">complete</a></button>
                    
                    </li>
                    </div>
                <?php endwhile; ?>
            </ol> 
            
               
            </form>
        </div>
    </body>
</html>