<?php 
require_once('includes/dice_coefficient.inc.php');
require_once('includes/fileutil.inc.php');

if(isset($_POST['message'])) {
    $message = strtolower($_POST['message']);
    $questionsAndAnswers = json_decode(FileUtil::readFile('./includes/chatbot.inc.json', 1024), true);
    $questions = array_keys($questionsAndAnswers);
    $scores = DiceCoefficient::computeSimilarity2($message, $questions);
    array_sort_scores($scores);

    $response = array();
    $score = $scores[0]['score'];
    $content = $scores[0]['content'];

    if(($score * 100) >= 15)
        $response['message'] = $questionsAndAnswers[$content];
    else
        $response['message'] = 'Sorry, I currently don\'t know the answer to your question';
    
    echo json_encode($response);
}

function array_sort_scores(array &$array) {
    for($i = 0; $i < count($array); $i++) {
        for($x = 0; $x < count($array) - $i - 1; $x++) {
            if($array[$x]['score'] < $array[$x + 1]['score']) {
                $temp = $array[$x];
                $array[$x] = $array[$x + 1];
                $array[$x + 1] = $temp;
            }
        }
    }
}

?>