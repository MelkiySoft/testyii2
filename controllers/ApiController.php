<?php

namespace app\controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use app\models\TwittUser;

class ApiController extends AppController {

    public function actionIndex()
    {
        return '';
    }

    public function sendError ($answer)
    {
        echo json_encode(['error' => $answer]);
        die;
    }

    public function getSha($id, $user = '')
    {
        return sha1($id . $user);
    }

    public function verificationId($id)
    {
        if (strlen($id) != 32) {
            $this->sendError('wrong id');
        }
    }

    public function verificationSecret($id, $secret, $user = '')
    {
        if ($secret !== $this->getSha($id, $user)) {
            $this->sendError('access denied');
        }
    }

    public function verificationMissingParameters($id, $secret, $user = '')
    {
        if($id == '' || $secret == '' || $user == '') {
            $this->sendError('missing parameter');
        }
    }

    /**
     * create a file /config/twitterConnect.php and add an array there with your data to connect to the Twitter api
     *
     * @param $userFromTwitter
     * @param $countTwits
     * @return array|object
     */
    public function getTwitsUser($userFromTwitter, $countTwits)
    {
        //$url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=twitterapi&count=2";

        $tConnect = require (__DIR__ . '/../config/twitterConnect.php');

        $connection = new TwitterOAuth(
            $tConnect['consumer_key'],
            $tConnect['consumer_secret'],
            $tConnect['access_token'],
            $tConnect['access_token_secret']
        );

        //$content = $connection->get("account/verify_credentials"); //!!!!!
        //$content = $connection->get("statuses/user_timeline", ["screen_name" => "BarackObama", "count" => 3]); //!!!!!
        $content = $connection->get("statuses/user_timeline", ["screen_name" => $userFromTwitter, "count" => $countTwits]);

        return $content;
    }

    /**
     * will add the user to the database
     * example: http://test1.yii2.loc/api/add?id=12341234123412341234123412341234&user=MichelleObama&secret=61bf3f8f325db59d2a2500ded72e5de0e609760e
     *
     * @param string $id
     * @param string $user
     * @param string $secret
     */
    public function actionAdd($id = '', $user = '', $secret = '')
    {
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

    /**
     * temporary method. Can be deleted
     */
    public function actionHelpwithsha1($id = '', $user = '', $secret = '') {
        $this->verificationId($id);
        return
            'Add:<br>' .
            'id:' . $id . '<br>' .
            'user:' . $user . '<br>' .
            'secret:' . $secret . '<br>' .
            'sha1:' . $this->getSha($id, $user) . '<br>';
    }

    /**
     * will show tweets from users from the database
     * example: http://test1.yii2.loc/api/feed?id=12341234123412341234123412341234&secret=e4fdc00365cc7b0b700907dded89c981fb0587cb
     *
     * @param string $id
     * @param string $secret
     */
    public function actionFeed($id = '', $secret = '')
    {
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
//var_dump($value);
                //проход по хештегам
                foreach ($value->entities->hashtags as $key2 => $item) {
                    //var_dump($item->text);
                    $hashtag[] = $item->text;
                }
                $response['feed'][] = ['user' => $twittUser->name, 'tweet' => $value->text, 'hashtag' => $hashtag];
            }
        }
//die;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * will remove the user from the database
     * example: http://test1.yii2.loc/api/remove?id=12341234123412341234123412341234&user=BarackObama&secret=247e7cd10d6b1e7912f5d0ea24401df1b2e371a6
     *
     * @param string $id
     * @param string $user
     * @param string $secret
     */
    public function actionRemove($id = '', $user = '', $secret = '')
    {

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