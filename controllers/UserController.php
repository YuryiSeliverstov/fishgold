<?php
namespace app\controllers;

use Yii;

class UserController extends ApiController
{
	public $modelClass = 'app\models\Users';
	
	private array $noAuthRoutes=
	[
		
	];
	
	
	
	public function behaviors(): array
	{
		$behaviors = parent::behaviors();
		$behaviors['authenticator']['except'] = $this->noAuthRoutes;
		return $behaviors;
	}
	
	/**
	 * @SWG\Get(path="/user/profile",
	 *     security = {{"BearerAuthentication":{}}},
	 *     tags={"ЛК"},
	 *     summary="Получить профиль",
	 *     description="",
	 *     @SWG\Response(
	 *         response = 200,
	 *         description = "true",
	 *         @SWG\Schema(ref="#/definitions/Users"),
	 *     ),	 
	 *     @SWG\Response(
	 *         response = 406,
	 *         description = "false",
	 *     )
	 * )
	 *
	 */
	 /**
     * @SWG\Post(
     *     path="/user/profile",
     *     tags={"ЛК"},
     *     summary="Обновить профиль",
     *     security = {{"BearerAuthentication":{}}},
     *     @SWG\Parameter(
     *          in="formData",
     *          name="nickName",
     *          description="Ник",
     *          required=false,
     *          type="string",
     *      ),	 
     *     @SWG\Parameter(
     *          in="formData",
     *          name="firstName",
     *          description="Имя",
     *          required=false,
     *          type="string",
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="lastName",
     *          description="Фамилия",
     *          required=false,
     *          type="string",
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="secondName",
     *          description="Отчество",
     *          required=false,
     *          type="string",
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="gender",
     *          description="Пол",
     *          required=false,
     *          type="string",
	 *          enum={"М", "Ж"}, 
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="dateOfBirth",
     *          description="Дата рождения",
     *          required=false,
     *          type="string",
     *      ),	 
     *     @SWG\Response(
     *          response=200,
     *          description="ОК",
     *          @SWG\Schema(ref="#/definitions/Users"),
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Ошибка авторизации",
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Ошибка валидации",
     *      ),
     * )
     */
	public function actionProfile(): array
	{
		if (Yii::$app->request->isGet)
		{
			return $this->response(Yii::$app->user->identity);
		}
		
		if (Yii::$app->request->isPost)
		{
			$user=Yii::$app->user->identity;
			$data = Yii::$app->request->getBodyParams();
			if ($user->load($data,'') && $user->save())
			{
				return $this->response($user);
			}
			return $this->response([],$user->errors);
		}
		return $this->response([],['SOMETHING_WRONG']);
	}
}