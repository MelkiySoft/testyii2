<?php

namespace app\controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use app\models\TwittUser;

class ApiController extends AppController {

    public function actionIndex() {
        return '';
    }

    public function sendError ($answer) {
        echo json_encode(['error' => $answer]);
        die;
    }

    public function getSha($id, $user = '') {
        return sha1($id . $user);
    }

    public function verificationId($id) {
        if (strlen($id) != 32) {
            $this->sendError('wrong id');
        }
    }

    public function verificationSecret($id, $secret, $user = '') {
        if ($secret !== $this->getSha($id, $user)) {
            $this->sendError('access denied');
        }
    }

    public function verificationMissingParameters($id, $secret, $user = '') {
        if($id == '' || $secret == '' || $user == '') {
            $this->sendError('missing parameter');
        }
    }

    public function getTwitsUser($userFromTwitter, $countTwits) {
        //$url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=twitterapi&count=2";

        $access_token = '994491498430332928-BDi9RtESYAGKGTEu9VbJaVybAb8phH6';
        $access_token_secret = 'DEJrBXYueSKQ1WVUnrNr9cUCSwrtzJcGgEda4PLTAOrCP';
        $consumer_key = 'Eg73HgyncYoD5fIkb5RejON1B';
        $consumer_secret = 'JtQyUe8Jqu4dJNdRJ6eJfxxqRMUfdI9XphExMc217iZMZWUU3H';
        //define('CONSUMER_KEY', $consumer_key);
        //define('CONSUMER_SECRET', $consumer_secret);

        $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
        //$content = $connection->get("account/verify_credentials"); //!!!!!
        //$content = $connection->get("statuses/user_timeline", ["screen_name" => "BarackObama", "count" => 3]); //!!!!!
        $content = $connection->get("statuses/user_timeline", ["screen_name" => $userFromTwitter, "count" => $countTwits]);

        return $content;
    }

    public function actionAdd($id = '', $user = '', $secret = '') {

        $this->verificationMissingParameters($id, $secret, $user);
        $this->verificationId($id);
        $this->verificationSecret($id, $secret, $user);


        if (TwittUser::find()->where(['name' => $user])->one() != NULL) {
            $this->sendError('already exists in DB');
        }

        $existTwitUser = !isset($this->getTwitsUser($user, 1)->errors);

        if ($existTwitUser) {
            $twitUser = new TwittUser();
            $twitUser->name = $user;
            $twitUser->save();
        } else {
            $this->sendError('internal error');
        }
    }

    public function actionHelpwithsha1($id = '', $user = '', $secret = '') {
        $this->verificationId($id);
        return
            'Add:<br>' .
            'id:' . $id . '<br>' .
            'user:' . $user . '<br>' .
            'secret:' . $secret . '<br>' .
            'sha1:' . $this->getSha($id, $user) . '<br>';
    }

    public function actionFeed($id = '', $secret = '') {

        $this->verificationMissingParameters($id, $secret, true);
        $this->verificationId($id);
        $this->verificationSecret($id, $secret);

        $twittUsers = TwittUser::find()->all();
        if (count($twittUsers) == 0) {
            die;
        }
        //var_dump($twittUsers);
        $response = ['feed' => []];
        foreach ($twittUsers as $key => $twittUser) {
            $twittUser->date_view = date('Y-m-d H:i:s',time());
            $twittUser->update();
            $tmp = $this->getTwitsUser($twittUser->name, 5);

            //проход по твитам
            foreach ($tmp as $value) {
                $hashtag = [];

                //проход по хештегам
                foreach ($value->entities->hashtags as $key2 => $item) {
                    //var_dump($item->text);
                    $hashtag[] = $item->text;
                }
                $response['feed'][] = ['user' => $twittUser->name, 'tweet' => $value->text, 'hashtag' => $hashtag];
            }
        }

        header('Content-type: application/json; charset=utf-8');
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function actionRemove($id = '', $user = '', $secret = '') {

        $this->verificationMissingParameters($id, $secret, $user);
        $this->verificationId($id);
        $this->verificationSecret($id, $secret, $user);

        $twittUser = TwittUser::find()->where(['name' => $user])->one();
        if ($twittUser == NULL) {
            $this->sendError('User was not in the DB');
        } else {
            $twittUser->delete();
        }
    }
}