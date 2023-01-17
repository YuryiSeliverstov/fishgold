<?php
namespace app\controllers;

use app\models\Products;
use app\models\ProductsStorages;
use app\models\ProductsShops;
use Yii;

class ProductsController extends ApiController
{
	public $modelClass = 'app\models\Products';
	
	private array $noAuthRoutes=[];
	
	public function behaviors(): array
	{
		$behaviors = parent::behaviors();
		$behaviors['authenticator']['except'] = $this->noAuthRoutes;
		return $behaviors;
	}
	
	/**
	 * @SWG\Get(path="/products",
	 *     security = {{"BearerAuthentication":{}}},
	 *     tags={"Товары"},
	 *     summary="Получить список товаров",
	 *     description="",
	 *     @SWG\Parameter(
     *          in="query",
     *          name="name",
     *          description="Наименование",
     *          required=false,
     *          type="string"
     *      ),
	 *     @SWG\Parameter(
     *          in="query",
     *          name="storageId",
     *          description="id склада",
     *          required=false,
     *          type="integer"
     *      ),		 
	 *     @SWG\Parameter(
     *          in="query",
     *          name="shopId",
     *          description="id магазина",
     *          required=false,
     *          type="integer"
     *      ),		 
	 *     @SWG\Parameter(
     *          in="query",
     *          name="offset",
     *          description="offset",
     *          required=false,
     *          type="integer",
	 *          default="0",
     *      ),	 
	 *     @SWG\Parameter(
     *          in="query",
     *          name="limit",
     *          description="limit",
     *          required=false,
     *          type="integer",
	 *          default="30",
     *      ),	 
	 
	 *     @SWG\Response(
	 *         response = 200,
	 *         description = "true",
	 *         @SWG\Schema(ref="#/definitions/Products"),
	 *     ),	 
	 *     @SWG\Response(
	 *         response = 406,
	 *         description = "false",
	 *     )
	 * )
	 *
	 */
	public function actionIndex(string $name='',int $storageId=0, int $shopId=0, int $offset=0,int $limit=30): array
	{
		$q=Products::find();
		
		if ($name)
		{
			$q
				->where(['LIKE',Products::tableName().'.name',$name]);
		}
		
		if ($storageId)
		{
			$pStorages=ProductsStorages::tableName();
			$q
				->leftJoin($pStorages, $pStorages . '.productId='.Products::tableName().'.id')
				->andWhere([$pStorages . '.storageId' => $storageId]);
		}
		
		if ($shopId)
		{
			$pShops=ProductsShops::tableName();
			$q
				->leftJoin($pShops, $pShops . '.productId='.Products::tableName().'.id')
				->andWhere([$pShops . '.shopId' => $shopId]);
		}
		return $this->response($q->offset($offset)->limit($limit)->all());
	}
	
	/**
	 * @SWG\Get(path="/products/generate",
	 *     security = {{"BearerAuthentication":{}}},
	 *     tags={"Товары"},
	 *     summary="Создать N товаров",
	 *     description="",
	 *     @SWG\Parameter(
     *          in="query",
     *          name="count",
     *          description="кол-во",
     *          required=true,
     *          type="integer",
	 *          default="20",
     *      ),	 
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
	public function actionGenerate($count=20): array
	{
		Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0; TRUNCATE '.Products::tableName().';SET FOREIGN_KEY_CHECKS = 1;')->execute();
		
		$names=[
			'Колбаса из динозавра',
			'Сыр из молока',
			'Сыр из пальмового масла',
			'Натуральная вода из под крана',
			'Виски недельной выдержки',
			'Красная икра из жира мертвой дворняги',
			'Бензин 95ый',
			'Ношпа',
			'Фенозепам',
			'Ксанакс от Паши Техника',
			'Маска для лица из глаза ящерицы',
			'Жареные гвозди',
			'Биг мак',
			'Смалл мак',
			'Колбаса по мексикански',
			'Вареные камни',
			'Вафли с витамином Ю',
			'Гематоген',
			'Оскорбиновая кислота',
			'Чипсы для похудения',
			'Бифилайф',
			'Крем депиляторный',
			'Презервативы "Знай наших"',
			'Витамины Центрум',
			'Витамины Витрум',
			'Успокоин',
			'Джет',
			'Рад-Хэ',
			'Психо',
			'Ментаны',
			'Баффаут',
			'Мед-Хэ',
			'Супер стимулятор',
			'Психо-винт',
			'Похуин',
			'Хулинам',
			'Центропиздин',
			'Отъебин',
			'Зеленка',
			'Звездочка от комаров',
			'Пирацетам',
			'Аддиктол'
		];
		
		$values=[
			'мл',
			'гр',
			'л',
			'кг',
			'м3',
			'шт'
		];
		
		for ($i=0;$i<$count;$i++)
		{
			$product=new Products();
			$r=rand(0,count($names)-1);
			$v=rand(0,count($values)-1);
			$product->name=$names[$r].' '.rand(100,999).' '.$values[$v];
			$product->packQuantity=rand(0,99);
			$product->save();
		}
		return $this->response(Products::find()->limit($count)->all());
	}
}