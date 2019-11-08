<?php
/**
 *
 * User: ThangDang
 * Date: 10/1/18
 * Time: 12:36
 *
 */

namespace tas\social\components;


use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Yii;

class GoogleDialogFlow
{
    private $projectId = 'thangdvcom';
    
    /**
     * @param        $text
     * @param        $sessionId
     * @param string $languageCode
     *
     * @return string
     * @throws \Google\ApiCore\ApiException
     * @throws \Google\ApiCore\ValidationException
     */
    public function detect_intent_texts($text, $sessionId, $languageCode = 'en-US')
    {
        // new session
        $test           = array('credentials' => __DIR__ . '/auth.json');
        $sessionsClient = new SessionsClient($test);
        $session        = $sessionsClient->sessionName($this->projectId, $sessionId ?: uniqid('', true));
        Yii::debug('Session path: ' . $session);
        
        // create text input
        $textInput = new TextInput();
        $textInput->setText($text);
        $textInput->setLanguageCode($languageCode);
        
        // create query input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);
        
        // get response and relevant info
        $response    = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        // $queryText      = $queryResult->getQueryText();
        // $intent         = $queryResult->getIntent();
        // $displayName    = $intent->getDisplayName();
        // $confidence     = $queryResult->getIntentDetectionConfidence();
        $fulfilmentText = $queryResult->getFulfillmentText();
        
        // output relevant info
        // print(str_repeat('=', 20) . PHP_EOL);
        // printf('Query text: %s' . PHP_EOL, $queryText);
        // printf('Detected intent: %s (confidence: %f)' . PHP_EOL, $displayName,
        //     $confidence);
        // print(PHP_EOL);
        // printf('Fulfilment text: %s' . PHP_EOL, $fulfilmentText);
        Yii::debug('Fulfilment text: ' . $fulfilmentText);
        
        $sessionsClient->close();
        return $fulfilmentText;
    }
}