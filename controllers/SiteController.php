<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class SiteController extends Controller
{
	public function actionError()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return ['error'=>'wrong action'];
	}
}
