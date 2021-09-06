<?php

namespace app\controllers;

use app\controllers\base\BaseController;
use app\models\factory\Prize\type\PrizeItem;
use app\models\form\PrizeDeliveryForm;
use app\models\StaffNotification;
use app\services\GameService;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\form\UserLoginForm;

class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['access' => "array", 'verbs' => "array"])]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['error' => "string[]", 'captcha' => "array"])]
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays game page.
     *
     * @return string - view string
     * @throws Exception - if something wrong with game service
     */
    public function actionIndex(): string
    {
        if (Yii::$app->user->isGuest) {
            return $this->render('index');
        }
        $gameService = new GameService();
        if (!$gameService->canPlay()) {
            throw new NotFoundHttpException(Yii::t('app', 'You already got your prize!'));
        }
        if ($this->request->isPost) {
            if ($this->request->post('play')) {

                $prize = $gameService->getPrize();
                return $this->render('win', compact('prize'));

            } elseif ($this->request->post('replay')) {

                $gameService->releaseCurrentPrize();
                $prize = $gameService->getPrize();
                return $this->render('win', compact('prize'));

            } elseif ($this->request->post('accept')) {

                $prize = $gameService->acceptCurrentPrize();
                return $this->render('accept', compact('prize'));

            }

        }
        return $this->render('game');
    }

    /**
     * Handle provided delivery address
     * @return string
     */
    public function actionPrizeItemDelivery(): string
    {
        $gameService = new GameService();

        /**
         * @var PrizeItem $prize
         */
        $prize = $gameService->getCurrentPrize();
        if (!$prize instanceof PrizeItem) {
            $this->goHome();
        }

        $addressForm = new PrizeDeliveryForm();
        $addressForm->load(Yii::$app->request->post());
        if ($addressForm->validate()) {
            $staffNotification = new StaffNotification([
                'message_template' => StaffNotification::TEMPLATE_PRIZE_ITEM,
                'user_id' => Yii::$app->user->id,
                'data' => [
                    'prize id' => $prize->item->id,
                    'prize title' => $prize->item->title,
                    'delivery address' => implode(', ', $addressForm->attributes())
                ]
            ]);
            $staffNotification->save(false);
        }

        return $this->render('accept', compact('prize', 'addressForm'));
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new UserLoginForm();
        if ($model->load($this->request->post())) {
            if ($model->login())
                return $this->redirect(Url::home());
            else {
                $model->password = '';
            }
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
