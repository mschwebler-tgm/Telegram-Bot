<?php

require_once "vendor/autoload.php";
use Telegram\Bot\Api;

$message = "";
foreach (getLinks() as $link) {
break;
  $message = $message . getContent($link, $message);
  break;
}

// Link
$token = 'bot271222864:AAF0lHLfYlI0gHEnNXHbq7r15pjC3h0uuc4';
$chat = 73892561;
$parse_mode = 'markdown';
$message = "*Test*";


#try {
  $bot = new \TelegramBot\Api\BotApi($token);
  $bot->sendMessage(
    $chat, 
    $message
  );
#} catch (\TelegramBot\Api\Exception $e) {
#  echo $e->getMessage();
#}



// Send POST
#$ch = curl_init();
#curl_setopt($ch,CURLOPT_URL, $url);
#$result = curl_exec($ch);
#curl_close($ch);

function getLinks()
{
  $ch = curl_init("https://www.smitegame.com/category/events/");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
  #echo "executing curl...\n";
  $content = curl_exec($ch);
  #echo "download finished\n";
  curl_close($ch);

  #echo "applying regex...\n";
  $pattern = '/<a class="thumbnail" href="https:\/\/www.smitegame.com\/[^"]*"/';
  $matches = array();
  preg_match_all($pattern, $content, $matches);

  $links = array();

  //iterate each line
  foreach ($matches[0] as $event) {
    $arr = explode('"', $event);
    array_push($links, $arr[3]);
  }

  #var_dump($links);

  return $links;
}

function getContent($link) {
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $content = curl_exec($ch);
  curl_close($ch);

  # get title
  $matches = array();
  $pattern = '/<title>[^<]+/';
  preg_match($pattern, $content, $matches);
  #var_dump($matches);
  $title = get_string_between($matches[0],'>','|');

  # get entry content (description)
  $matches = array();
  $pattern = '/<div id="" class="entry-content ">[^($)*]+/';
  preg_match($pattern, $content, $matches);


  $totalDescription = get_string_between($content, "<div id=\"\" class=\"\">", "</div>");
  $description = strip_tags(get_string_between($totalDescription, "<p>", "</p>"));
  $date = strip_tags(get_string_between($totalDescription, "<hr />", "</em>"));

  $message = "";
  #$message = $message . "Link: ".$link."\n";
  $message = $message . "*".$title."*\n";
  $message = $message . "".$description."\n";
  $message = $message . "".$date."---------------------------\n\n\n";

  return $message;
}

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

  $sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('John', 'Doe', 'john@example.com')";

  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();

  /*
   * CREATE TABLE events (
   *  id int PRIMARY KEY AUTO_INCREMENT,
   *  ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP PRIMARY KEY,
   *
   * )
   *
   *
   *
   *
   */
}


?>