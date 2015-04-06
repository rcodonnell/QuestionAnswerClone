<?php
session_start();
include('config.php');

$link = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME)  or
  die('There was a problem connecting to the database');

$query = mysqli_query($link, "
  SELECT *
  FROM user,avatar
  WHERE user.id = '".$_GET['user_id']."'
  AND user.avatar_id = avatar.id
  LIMIT 1");

$quesquery = mysqli_query($link, "
  SELECT *
  FROM question
  WHERE ownerid = '".$_GET['user_id']."'");

$scorequery = mysqli_query($link, "
  select sum(score) totalscore
  from (select score from question
  where ownerid = '".$_GET['user_id']."'
  union all
  select score from answer
  where ownerid = '".$_GET['user_id']."') tb");
while($row = mysqli_fetch_array($scorequery)) {
  $score = $row['totalscore'];
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>QuestionAnswer</title>
    <!-- Compiled and minified CSS -->
  <link rel="stylesheet" href="css/materialize.css">
  <link rel="stylesheet" href="css/style.css">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.95.3/js/materialize.min.js"></script>
  <script src="js/search.js"></script>
</head>

<body>
  <nav class="lighten-1" role="navigation">
    <div class="container">
      <div class="nav-wrapper"><a id="logo-container" href="#" class="brand-logo">Welcome "<?php echo $_SESSION['user_id'];?>!"</a>
        <form class="right" id="searchform" method="post">
            <input type="text" name="search_query" id="search_query" size="24" placeholder="Who are you looking for?"/>
        </form>
        <ul class="right">
          <li><a href="login.php?status=loggedout">Log Out</a></li>
        </ul>
        <ul class="right">
          <li><a href="askQuestion.php">Ask a question</a></li>
        </ul>
        <ul class="right">
          <li><a href="index.php">Home</a></li>
        </ul>

        <ul id="nav-mobile" class="side-nav">
          <li><a href="#">Navbar Link</a></li>
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
      </div>
    </div>
  </nav>

  <div class="container">
  <div id="display_results"></div>
    <h2>User</h2>
      <?php while($row = mysqli_fetch_array($query)): ?>
      <h4><?php echo $row['username'] . " " . $score; ?></h4>
      <h4>Avatar</h4><?php $src = "avatars/".$row['filename'];
      echo "<img src=$src> "?>
      <?php endwhile?>
    <?php if($_SESSION['user_key'] === $_GET['user_id']): ?>
      <h4>Upload new avatar</h4>
      <form enctype="multipart/form-data" action="fileupload.php" method="post">
      <input type="hidden" name="MAX_FILE_SIZE" value="30000">
      File: <input name="userfile" type="file">
      <input type="submit" value="Upload!">
      </form>
    <?php endif; ?>
    <h2>Asked Questions</h2>
      <?php while($row = mysqli_fetch_array($quesquery)): ?>
      <div class="question">
        <p><?php
        $url = 'displayQuestion.php?question_id=' . $row['id'];
        $site_title = $row['title'];
        echo "<a href=$url>$site_title</a>" ?></p>
      </div>
    <?php endwhile?>
    <?php mysqli_close($link);?>
  </div>
</body>
</html>
