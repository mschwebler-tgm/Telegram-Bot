<?php

require_once "vendor/autoload.php";


// get events
$message = "Test";
foreach (getLinks() as $link) {
  break;
  $message = $message . getContent($link, $message);
}

// parameters
$token = '271222864:AAF0lHLfYlI0gHEnNXHbq7r15pjC3h0uuc4';
$chatId = 73892561;
$parse_mode = 'markdown';
$messageText = "*Test*";

// create bot
$bot = new \TelegramBot\Api\BotApi($token);

// get subscriptions
foreach($bot->getUpdates() as $noob) {
  #$vars = array get_object_vars($noob);
  var_dump ($noob->getMessage()->getFrom()->getFirstName());
}

// send message
$bot->sendMessage($chatId, $message, $parse_mode);

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


  // put links into array
  $links = array();
  foreach ($matches[0] as $event) {
    $arr = explode('"', $event);
    array_push($links, $arr[3]);
  }

  // return array
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

function db()
{
  $servername = "localhost";
  $username = "root";
  $password = "raspberry";
  $dbname = "myDB";

// Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "INSERT INTO MyGuests (firstname, lastname, email) VALUES ('John', 'Doe', 'john@example.com')";

  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}


?>