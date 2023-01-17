<?php


namespace app\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\Url;

class ApiController extends \yii\rest\ActiveController
{
    protected const RESPONSE_BAD_REQUEST 			= 400;
    protected const RESPONSE_UNAUTHORIZED 			= 401;
    protected const RESPONSE_FORBIDDEN 				= 403;
    protected const RESPONSE_NOT_FOUND 				= 404;
    protected const RESPONSE_UNPROCESSABLE_ENTITY 	= 422;
	protected const SYSTEM_VERSION 					= 0.111;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['localhost'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];


		unset($behaviors['authenticator']);
		$behaviors['authenticator'] = [
			'class'       => CompositeAuth::className(),
			'authMethods' => [
				HttpBearerAuth::className(),
				QueryParamAuth::className()
			],
		];
		$behaviors['contentNegotiator'] = [
                'class'   => \yii\filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => yii\web\Response::FORMAT_JSON,
                ],
            ];
		return $behaviors;
	}
	
    public function actions()
    {
		$actions = parent::actions();
		unset($actions['index']);
		unset($actions['create']);
		unset($actions['delete']);
		unset($actions['update']);
		unset($actions['view']);
		$actions['error']=['class'=>'yii\web\ErrorAction'];
		return $actions;
    }
	
	protected function verbs()
	{
		return [
			'index' => ['GET', 'HEAD', 'OPTIONS'],
			'view' => ['GET', 'HEAD', 'OPTIONS'],
			'create' => ['POST', 'OPTIONS'],
			'update' => ['PUT', 'PATCH','POST', 'OPTIONS'],
			'delete' => ['DELETE', 'OPTIONS','GET'],
		];
	}
	
	public function beforeAction($action)
	{
		$headers = Yii::$app->request->headers;
		if (isset($headers['authorization']))
		{
			$headers['authorization']='Bearer '.$headers['authorization'];
		}
		return parent::beforeAction($action);
	}
	
	public function getCurrentAction()
	{
		return Yii::$app->controller->id.'/'.$this->action->id;
	}
	
	public function response($data=[],$errors=[],$code=200,$extra=[])
	{
		if ($errors)
		{
			$code=406;
		}
		
		Yii::$app->response->statusCode = $code;	
		
		$arrErrors=[];
		foreach ($errors as $k=>$err)
		{
			if (is_array($err))
			{
				$arrErrors[]=$err[0];
			}
			else
				$arrErrors[]=$err;
		}
		
		$returnBody=[
            'data' 			=> $data,
            'baseUrl' 		=> Url::base(true),
            'errors' 		=> $arrErrors,
            'code' 			=> $code,
            'systemVersion' => self::SYSTEM_VERSION
        ];
		
		if ($extra)
		{
			foreach ($extra as $k=>$v)
			{
				$returnBody[$k]=$v;
			}
		}
		
		if (is_countable($data))
		{
			$returnBody['meta']=['totalCount'=>count($data)];
		}
		return $returnBody;
	}
}