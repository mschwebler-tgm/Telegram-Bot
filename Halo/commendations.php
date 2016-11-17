<?php

require_once "../vendor/autoload.php";

//////////////////////////////////////
//// ------- BOT HANDLING ------- ////
//////////////////////////////////////

$token = '283876032:AAGAAKtoSd_SBOlFJ2jNslAHqjGeZ9EEne0';
$bot = new \TelegramBot\Api\BotApi($token);

// get subscriptions (not only groups => false)
$subIDs = getSubs($bot, false);

// get message to send
$message = getCommendations();

// send message to all subs
if ($message != "") {
    foreach ($subIDs as $subID) {
        $bot->sendMessage($subID, $message, 'html');
    }
}






/////////////////////////////////////
//// -------- FUNCTIONS -------- ////
/////////////////////////////////////

function getCommendations() {
	$message = "";

	// get html data
	$link = "https://www.halowaypoint.com/en-us/games/halo-5-guardians/xbox-one/commendations/7e7e9e23-546e-4d1c-a4a1-2df467ad57ac/spartan-companies/achilles%20armor%20assembly";
	$html = file_get_contents($link);

	// regex
	$pattern_title = '/\<p class="text--large"\>[^<]*/';
	$pattern_xp = '/\<p class="numeric--small xp"\>[^<]*/';
	$pattern_level = '/\<p class="text--smallest"\>[^<]*/';

	// apply regex and save into vars "titels" and "xps"
	preg_match_all($pattern_title, $html, $titles);
	preg_match_all($pattern_xp, $html, $xps);
	preg_match_all($pattern_level, $html, $levels);

	$titles = $titles[0];
	$xps = $xps[0];
	$levels = $levels[0];

	// remove first two indexes
	unset($titles[0]);
	unset($titles[1]);
	// re-index
	$titles = array_values($titles);

	// same with levels
	unset($levels[0]);
	unset($levels[1]);
	unset($levels[2]);
	unset($levels[3]);
	unset($levels[4]);
	// re-index
	$levels = array_values($levels);

	// iterate through array
	for ($i = 0; $i < count($titles); $i++) {
	  $title = explode(">", $titles[$i])[1];
	  $xp = explode(">", $xps[$i])[1];
	  $level = explode(">", $levels[$i])[1];
	  if (!(explode("/",$xp)[0] == explode("/",$xp)[1])) {
	    $message =  $message .
       		 "<b>" . $title . "</b>\n" . 
	         $level . "\n" . 
	         $xp    . "\n\n";
	  }
	}

	return $message;
}

function getSubs($bot, $onlyGroups)
{
    $subIDs = array();
    foreach ($bot->getUpdates() as $noob) {
        $id = $noob->getMessage()->getChat()->getId();
        if (!(in_array($id, $subIDs))) {
            if ($onlyGroups) {
                if ($noob->getMessage()->getChat()->getType() == "group") {
                    array_push($subIDs, $id);
                }
            } else {
                array_push($subIDs, $id);
            }
        }
    }

    return $subIDs;
}

?>