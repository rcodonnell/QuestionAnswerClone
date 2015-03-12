<?php
require_once 'includes/constants.php';
require_once 'classes/User.php';
$user = New User();
$user->confirm_User();

$link = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME)  or
  die('There was a problem connecting to the database');

function retrieve_Username($link, $id) {
$userQuery = "SELECT * FROM user
          WHERE id = $id";
$userResult = $link->query($userQuery)
  or die($link->error);
while($row = $userResult->fetch_assoc()) {
  $user = $row['username'];
}
return $user;
} // End of retrieve_Username


$query = "SELECT * FROM question
          WHERE id ='" . $_GET['question_id'] . "'";

$result = $link->query($query)
  or die($link->error);

  while($row = $result->fetch_assoc()) {
    $question_id = $row['id'];
    $question_title = $row['title'];
    $question_body = $row['body'];
    $asker = retrieve_Username($link ,$row['ownerid']);
    $creationdate = $row['creationdate'];

$question_Table = '';
$question_Table .=<<<EOD
    <tr><td><strong>Title: </strong>$question_title</td></tr>
    <tr><td><strong>Body: </strong>$question_body</td></tr>
    <tr><td><strong>User: </strong>$asker</td></tr>
    <tr><td><strong>Date: </strong>$creationdate</td></tr>
EOD;
}

$query3 = "SELECT * FROM answer
          WHERE parentid ='" . $_GET['question_id'] . "'";

$result3 = $link->query($query3)
  or die($link->error);

function Insert_Answer($ans, $question_id) {
  $link = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME)  or
    die('There was a problem connecting to the database');

  $ownerID = $_SESSION['user_key'];

  $query = "INSERT INTO answer (parentid, body, ownerid)
            VALUES ('$question_id', '$ans', '$ownerID')";

  if(mysqli_query($link, $query)) {
    return true;
  } else {
    return false;
    echo "ERROR: Could not able to execute $query. " . mysqli_error($link);
  }
}

if($_POST && !empty($_POST['answer'])) {
  $response = Insert_Answer($_POST['answer'], $question_id);
  header("Location: displayQuestion.php?question_id=$question_id");
}

?>

<!DOCTYPE html>
<html>
<head>

  <link href="http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="js/script.js"></script>
  </head>

<body>
    <p> Welcome "<?php echo $_SESSION['user_id'];?>!"
    <a href="login.php?status=loggedout">Log Out</a> </p>
    <p>
      <a href="index.php">Home</a>
    </p>
      <h2>Question</h2>
      <table><?php echo $question_Table?></table>
      <h2>Answers</h2>
      <?php while($row = $result3->fetch_assoc()): ?>
        <hr>
    <div class="item" data-postid="<?php echo $row['id'] ?>" data-score="<?php echo $row['score'] ?>">
      <p><?php echo $row['id'] ?></p>
      <div class="vote-span"><!-- voting-->
        <div class="vote" data-action="up" title="Vote up">
          <i class="icon-chevron-up"></i>
        </div><!--vote up-->
        <div class="vote-score"><?php echo $row['score'] ?></div>
        <div class="vote" data-action="down" title="Vote down">
          <i class="icon-chevron-down"></i>
        </div><!--vote down-->
      </div>

      <div class="post"><!-- post data -->
        <p><?php echo $row['body'] ?></p>
      </div>
    </div><!--item-->
    <?php endwhile?>
      <h2>Post and answer to the Question</h2>
      <form method="post" action="">
        <div>
          <label for="answer">Answer Question</label>
          <textarea type="text" name="answer" value="" id="answer" placeholder="answer"></textarea>
        </div>
        <div>
        <input type="submit" value="Submit">
        </div>
      </form>
</body>
</html>
