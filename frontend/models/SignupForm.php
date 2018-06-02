<?php
namespace frontend\models;

use common\components\event\EmailEvent;
use common\components\helper\SignHelper;
use common\models\startdata\UrlCheck;
use lbmzorx\components\behavior\LimitLogin;
use lbmzorx\components\behavior\RsaAttribute;
use yii\base\Exception;
use yii\base\Model;
use common\models\user\User;
use Yii;
use yii\helpers\Url;
use yii\helpers\VarDumper;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $confirm_password;
    public $verifyCode;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\user\User', 'message' =>\yii::t('app','This username has already been taken.')],
            ['username', 'string', 'min' => 2, 'max' => 50],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\user\User', 'message' => \yii::t('app','This email address has already been taken.')],

            [['password','confirm_password'], 'required'],
            [['password','confirm_password'], 'string', 'min' => 8,'max'=>25],
            [ 'password','match','pattern'=>'/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,25}$/','message'=>\yii::t('app','Password must include number,lowercase letters,and uppercase letters')],
            ['password','compare','compareAttribute'=>'password','operator'=>'===',],

            ['verifyCode','captcha'],
        ];
    }

    public function behaviors()
    {
        return [
            'bs_rsa'=>[
                'class'=>RsaAttribute::className(),
                'rsaAtAttributes'=>['password','confirm_password'],
            ],
            'check_login'=>[
                'class'=>LimitLogin::className(),
                'attribute'=>'password',
            ],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateSecretKey();
        $user->status=User::STATUS_WAITING_ACTIVE;

        if( $user->save()&&$this->sendEmail($user)){
            \yii::$app->session->setFlash('success',\yii::t('app','Success signup, the activation email has been sent to you email {email},checkout and click it.',['email'=>$user->email]));
            return $user;
        }
        return null;
    }

    public function attributeLabels()
    {
        return [
            'username'=>\yii::t('app','Username'),
            'email'=>\yii::t('app','Email'),
            'password'=>\yii::t('app','Password'),
            'confirm_password'=>\yii::t('app','Confirm Password'),
            'verifyCode'=>\yii::t('app','Verify Code'),
        ]; // TODO: Change the autogenerated stub
    }

    /**
     * Sends an email with a link, for resetting the password.
     * @var $user User
     * @return bool whether the email was send
     */
    public function sendEmail($user)
    {
        /* @var $user User */
        if (!$user) {
            return false;
        }
        $emailEvent=new EmailEvent();
        $this->trigger(EmailEvent::EVENT_BEFORE_EMAIL,$emailEvent);
        $linkParams=SignHelper::signSecretKey(['type'=>'user-signup','expire'=>strtotime('+7 day')],$user->getAuthKey(),true,true);
        if($linkParams!=false){
            array_unshift($linkParams,'site/active-account');
            $link=Url::to($linkParams,true);
            $urlCheck=new UrlCheck();
            $urlCheck->setScenario('create');
            $urlCheck->url=$link;
            $urlCheck->md5=$linkParams['sign'];
            $urlCheck->user_id=$user->id;
            $urlCheck->expire_time=strtotime('+7 day');
            $urlCheck->status=UrlCheck::STATUS_WAITING;
            $urlCheck->auth_key=$user->getAuthKey();
            if($urlCheck->save()){
                $mail= Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'signup-html', 'text' => 'signup-text'],
                        [
                            'user' => $user,
                            'link'=>$link,
                            'expire'=>$urlCheck->expire_time,
                        ]
                    )
                    ->setFrom(Yii::$app->params['supportEmail'])
                    ->setTo($this->email)
                    ->setSubject(\yii::t('app','Welcome signup {name} click link and activate you account!',[
                        'name'=>\yii::t('app',Yii::$app->name),
                    ]))
                    ->send();
                if($mail){
                    $this->trigger(EmailEvent::EVENT_SUCCESS_EMAIL,$emailEvent);
                }
                return $mail;
            }
        }
        $this->addError('email',Yii::t('app','Can\'t create email for activate you account , please checkout you email !'));
        return false;
    }
}
