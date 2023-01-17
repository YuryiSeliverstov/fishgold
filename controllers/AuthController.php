<?php
namespace app\controllers;

use app\components\ModelHelper;
use app\models\LoginForm;
use app\models\Users;
use app\models\UsersTokens;
use Yii;

class AuthController extends ApiController
{
	public const 
		MESSAGE_SEND_CODE			=	'Код подтверждения отправлен на Ваш E-Mail',
		MESSAGE_NOT_FOUND			=	'Пользователь не найден',
		MESSAGE_WRONG_CREDENTIALS	=	'Пользователь не найден либо пароль не верный либо бог его знает что ещё)',
		MESSAGE_REGISTER_SUCCESS	=	'Регистрация завершена, на Ваш E-Mail отправлен код подтверждения',
		MESSAGE_PASSWORD_SET		=	'Новый пароль установлен',
		MESSAGE_ACTIVATION_SUCCESS	=	'Ваш аккаунт активирован';
	
	public $modelClass = 'app\models\Users';
	
	private array $noAuthRoutes=
	[
		'register',
		'activate',
		'login',
		'restore-password',
		'set-password'
	];
	
	
	
	public function behaviors(): array
	{
		$behaviors = parent::behaviors();
		$behaviors['authenticator']['except'] = $this->noAuthRoutes;
		return $behaviors;
	}
	
	/**
	 * @SWG\Post(path="/auth/register",
	 *     tags={"Авторизация"},
	 *     summary="Регистрация",
	 *     description="",
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "email",
	 *        description = "E-Mail",
	 *        required = true,
	 *        type = "string",
	 *        default = "test@mail.com",
	 *     ),
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "password",
	 *        description = "Пароль",
	 *        required = true,
	 *        type = "string",
	 *        default = "123123",
	 *     ),	
	 *     @SWG\Response(
	 *         response = 200,
	 *         description = "true",
	 *     ),	 
	 *     @SWG\Response(
	 *         response = 406,
	 *         description = "false",
	 *     )
	 * )
	 *
	 */
	public function actionRegister(): array
	{
		$data = Yii::$app->request->getBodyParams();
		try 
		{
			$user=new Users();
			$user->load($data,'');
			$user->generateEmailCode();
			$user->setPassword($data['password']);
			if ($user->save())
			{
				return $this->response([self::MESSAGE_REGISTER_SUCCESS,$user->emailCode]);
			}
			return $this->response([],$user->errors);
		}
		catch (\Exception $e) 
		{
			return $this->response([], [$e->getMessage()]);
		}
	}
	
	/**
	 * @SWG\Post(path="/auth/activate",
	 *     tags={"Авторизация"},
	 *     summary="Активация",
	 *     description="",
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "email",
	 *        description = "E-Mail",
	 *        required = true,
	 *        type = "string",
	 *        default = "test@mail.com",
	 *     ),
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "emailCode",
	 *        description = "Код активации",
	 *        required = true,
	 *        type = "string"
	 *     ),	
	 *     @SWG\Response(
	 *         response = 200,
	 *         description = "true",
	 *     ),	 
	 *     @SWG\Response(
	 *         response = 406,
	 *         description = "false",
	 *     )
	 * )
	 *
	 */
	public function actionActivate(): array
	{
		$data = Yii::$app->request->getBodyParams();
		try 
		{
			if ($user=Users::findOne([
					'email'		=>	$data['email'] ?? '',
					'emailCode'	=>	$data['emailCode'] ?? ''
				]))
			{
				$user->updateAttributes(['active'=>1,'emailCode'=>null]);
				return $this->response([self::MESSAGE_ACTIVATION_SUCCESS]);
			}
			return $this->response([],[self::MESSAGE_WRONG_CREDENTIALS]);
		}
		catch (\Exception $e) 
		{
			return $this->response([], [$e->getMessage()]);
		}
	}
	
	/**
	 * @SWG\Post(path="/auth/login",
	 *     tags={"Авторизация"},
	 *     summary="Логин",
	 *     description="",
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "email",
	 *        description = "E-Mail",
	 *        required = true,
	 *        type = "string",
	 *        default = "test@mail.com"	 
	 *     ),
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "password",
	 *        description = "Пароль",
	 *        required = true,
	 *        type = "string"
	 *     ),
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "fcmToken",
	 *        description = "FireBase token",
	 *        required = true,
	 *        type = "string",
	 *        default = "asdasd1231232asda"	 	 
	 *     ),	
	 *     @SWG\Response(
	 *         response = 200,
	 *         description = "true",
	 *     ),	 
	 *     @SWG\Response(
	 *         response = 406,
	 *         description = "false",
	 *     )
	 * )
	 *
	 */
	public function actionLogin(): array
	{
		$data = Yii::$app->request->getBodyParams();
		
		$loginModel=new LoginForm();
		
		if (!$loginModel->load($data,'') || !$loginModel->validate())
		{
			return $this->response([],$loginModel->errors);
		}
		
		$email			=	$data['email'];
		$passwordHash	=	Users::hashPassword($data['password']);
		
		try 
		{
			if (!$user=Users::findOne(
					[
						'email'		=>	$email,
						'password'	=>	$passwordHash,
						'active'	=>	1,
						'deleted'	=>	0
					]
				)
			)
			{
				return $this->response([],[self::MESSAGE_WRONG_CREDENTIALS]);
			}
			
			$userToken			=	new UsersTokens();
			$userToken->userId	=	$user->id;
			if ($userToken->save())
			{
				$user->updateAttributes([
					'loggedAt'	=>	time(),
					'emailCode'	=>	null,
					'fcmToken'	=>	$data['fcmToken']
				]);
				return $this->response(['token'=>$userToken->token]);
			}
			else
			{
				return $this->response([],$userToken->errors);
			}
		}
		catch (\Exception $e) 
		{
			return $this->response([], [$e->getMessage()]);
		}
	}
	
	/**
	 * @SWG\Get(path="/auth/restore-password",
	 *     tags={"Авторизация"},
	 *     summary="Восстановить пароль",
	 *     description="",
	 *     @SWG\Parameter(
	 *		  in ="query",
	 *        name = "email",
	 *        description = "E-Mail",
	 *        required = true,
	 *        type = "string",
	 *        default = "test@mail.com"	 
	 *     ),
	 *     @SWG\Response(
	 *         response = 401,
	 *         description = "false",
	 *     )
	 * )
	 *
	 */
	public function actionRestorePassword(string $email): array
	{
		try 
		{
			if (!$user=Users::findOne(['email'=>$email]))
			{
				return $this->response([],[self::MESSAGE_NOT_FOUND]);
			}
			
			$user->generateEmailCode();
			$user->save();
			
			return $this->response([self::MESSAGE_SEND_CODE,$user->emailCode]);
		}
		catch (\Exception $e) 
		{
			return $this->response([], [$e->getMessage()]);
		}
	}
	
	/**
	 * @SWG\Post(path="/auth/set-password",
	 *     tags={"Авторизация"},
	 *     summary="Установить новый пароль",
	 *     description="",
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "email",
	 *        description = "E-Mail",
	 *        required = true,
	 *        type = "string"
	 *     ),
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "emailCode",
	 *        description = "Код E-Mail",
	 *        required = true,
	 *        type = "string",
	 *        default = "test@mail.com"	 
	 *     ),
	 *     @SWG\Parameter(
	 *		  in ="formData",
	 *        name = "password",
	 *        description = "Пароль",
	 *        required = true,
	 *        type = "string"
	 *     ),		 
	 *     @SWG\Response(
	 *         response = 401,
	 *         description = "false",
	 *     )
	 * )
	 *
	 */
	public function actionSetPassword(): array
	{
		$data = Yii::$app->request->getBodyParams();
		try 
		{
			if (!$user=Users::findOne(['email'=>$data['email'] ?? '','emailCode'=>$data['emailCode'] ?? '']))
			{
				return $this->response([],[self::MESSAGE_NOT_FOUND]);
			}
			
			$user->setPassword($data['password'] ?? '');
			$user->save();
			
			return $this->response([self::MESSAGE_PASSWORD_SET]);
		}
		catch (\Exception $e) 
		{
			return $this->response([], [$e->getMessage()]);
		}
	}
}