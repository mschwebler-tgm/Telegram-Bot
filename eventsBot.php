<?php

require_once "vendor/autoload.php";


// get events
$message = "";
foreach (getLinks() as $link) {
  $message = $message . getContent($link, $message);
}

// parameters
$token = '271222864:AAF0lHLfYlI0gHEnNXHbq7r15pjC3h0uuc4';
$chatId = 73892561;

// create bot
$bot = new \TelegramBot\Api\BotApi($token);

// get subscriptions
foreach($bot->getUpdates() as $noob) {
  #var_dump ($noob->getMessage()->getFrom()->getFirstName());
}

// send message
if ($message != "") {
  $bot->sendMessage($chatId, $message, 'markdown');
}

/**
 * This function returns all links to current events in an array
 */
function getLinks()
{
  // get html data
  $ch = curl_init("https://www.smitegame.com/category/events/");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
  $content = curl_exec($ch);
  curl_close($ch);

  // get all links using regex
  $pattern = '/<a class="thumbnail" href="https:\/\/www.smitegame.com\/[^"]*"/';
  $matches = array();
  preg_match_all($pattern, $content, $matches);


  // check for new links
  $links = array();
  $conn = connectDB();
  $sql = "SELECT link FROM events";
  $result = (mysqli_query($conn, $sql));
  $dbLinks = array();
  // save links to array
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      array_push($dbLinks, $row["link"]);
    }
  }

  foreach ($matches[0] as $event) {
    //get link
    $arr = explode('"', $event);
    $link = $arr[3];

    // match current link with links in database
    if (!(in_array($link, $dbLinks))) {
      // if not in database: add to links + save to database
      array_push($links, $link);
      $sql = "INSERT into events VALUES('" . $link . "')";
      if ($conn->query($sql) === TRUE) {
        // New record created successfully
      } else {
        // Error
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
    }
  }
  closeDB($conn);

  // return array to be sent
  return $links;
}

/**
 * Returns the content of a single event (Title, Description and Date)
 */
function getContent($link) {
  // get html data from a single event
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $content = curl_exec($ch);
  curl_close($ch);

  // get title
  $matches = array();
  $pattern = '/<title>[^<]+/';
  preg_match($pattern, $content, $matches);
  $title = get_string_between($matches[0],'>','|');

  // get entry content (description)
  $matches = array();
  $pattern = '/<div id="" class="entry-content ">[^($)*]+/';
  preg_match($pattern, $content, $matches);

  // seperate title, description and date
  $totalDescription = get_string_between($content, "<div id=\"\" class=\"\">", "</div>");
  $description = strip_tags(get_string_between($totalDescription, "<p>", "</p>"));
  $date = strip_tags(get_string_between($totalDescription, "<hr />", "</em>"));

  $message = "";
  #$message = $message . "Link: ".$link."\n";
  $message = $message . "*".$title."*\n";
  $message = $message . "".$description."\n";
  $message = $message . "".$date."\n\n\n";

  return $message;
}

/**
 * function that returns a String between two other strings/characters
 * Input: "Hello huge World!"
 * Usage: get_string_between($input, 'Hello ', ' World!'){
 * Output: huge
 */
function get_string_between($string, $start, $end){
  $string = ' ' . $string;
  $ini = strpos($string, $start);
  if ($ini == 0) return '';
  $ini += strlen($start);
  $len = strpos($string, $end, $ini) - $ini;
  return substr($string, $ini, $len);
}

function connectDB()
{
  $servername = "localhost";
  $username = "root";
  $password = "raspberry";
  $dbname = "smiteBot";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  return $conn;
}





function closeDB($conn) {
  $conn->close();
}


?>