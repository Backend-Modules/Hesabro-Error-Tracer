<?php

namespace hesabro\errorlog\controllers;

use hesabro\errorlog\models\MGTarget;
use hesabro\errorlog\models\MGTargetSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MongoTargetController implements the CRUD actions for MGTarget model.
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/index'],
                            'actions' => ['index']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/category'],
                            'actions' => ['category']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/archive'],
                            'actions' => ['archive']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/expand'],
                            'actions' => ['expand']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/view'],
                            'actions' => ['view']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/delete'],
                            'actions' => ['delete']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/delete-all'],
                            'actions' => ['delete-all']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/delete-all-permanently'],
                            'actions' => ['delete-all-permanently']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/delete-selected'],
                            'actions' => ['delete-selected']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['errorlog/default/delete-http'],
                            'actions' => ['delete-http']
                        ]
                    ]
            ]
        ]);
    }

    /**
     * @param int $type
     * @return string
     */
    public function actionIndex($type = MGTarget::ERROR_EXCEPTION)
    {
        $searchModel = new MGTargetSearch();
        $searchModel->type = $type;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'findType' => 'active'
        ]);
    }

    /**
     * @param int $type
     * @return string
     */
    public function actionCategory($type = MGTarget::ERROR_EXCEPTION)
    {
        $searchModel = new MGTargetSearch();
        $dataProvider = $searchModel->searchCategory(Yii::$app->request->queryParams);

        return $this->render('category', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'findType' => 'index'
        ]);
    }

    /**
     * Lists all MGTarget models.
     * @return mixed
     */
    public function actionArchive()
    {
        $searchModel = new MGTargetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'archive');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'findType' => 'archive'
        ]);
    }

    /**
     * Displays a single MGTarget model.
     */
    public function actionExpand($type = null)
    {
        $id = unserialize(Yii::$app->request->post('expandRowKey'));
        $model = $this->findModel((string)$id, $type);
        return $this->renderPartial('_index', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single MGTarget model.
     * @param integer $_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $findType = 'active')
    {
        return $this->renderAjax('_index', [
            'model' => $this->findModel($id, $findType),
        ]);
    }

    /**
     * Deletes an existing MGTarget model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $response = ['success' => false, 'data' => ''];
        $model = $this->findModel($id);
        ;
        try {
            $flag = $model->softDelete();
            if ($flag) {
                $response['msg'] = \hesabro\errorlog\Module::t('module', 'Item Deleted');
                $response['success'] = true;

            } else {
                $response['msg'] = Yii::t("app", 'Error In Save Info');
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            throw $e;
        }

        return json_encode($response);
    }

    public function actionDeleteAll($type, $application = null, $category = null)
    {
        $response = ['success' => false, 'data' => '', 'msg' => "یافت نشد."];
        $deleteCondition = ['status' => MGTarget::STATUS_ACTIVE, 'type' => (int)$type];
        if ($application) {
            $deleteCondition['application'] = $application;
        }
        if ($category) {
            $deleteCondition['category'] = $category;
        }

        try {
            MGTarget::UpdateAll(['status' => MGTarget::STATUS_DELETED], $deleteCondition);
            $response['msg'] = \hesabro\errorlog\Module::t('module', 'Item Deleted');
            $response['success'] = true;
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            throw $e;
        }

        return json_encode($response);
    }

    public function actionDeleteAllPermanently()
    {
        $response = ['success' => false, 'data' => '', 'msg' => "یافت نشد."];

        try {
            MGTarget::deleteAll([
                'AND',
                ['status' => MGTarget::STATUS_DELETED],
                ['<=', 'log_time', strtotime("-90 days", time())]
            ]);
            $response['msg'] = \hesabro\errorlog\Module::t('module', 'Item Deleted');
            $response['success'] = true;
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            throw $e;
        }

        return json_encode($response);
    }

    public function actionDeleteSelected($selectedIds)
    {
        foreach (explode(',', $selectedIds) as $selectedId) {
            $selectedId = @unserialize($selectedId) === false ? $selectedId : ((string)unserialize($selectedId));
            $model = $this->findModel($selectedId);
            if ($model->softDelete() === false)
                Yii::$app->getSession()->setFlash('warning', \hesabro\errorlog\Module::t('module', "Can't delete this log!"));
        }

        return $this->asJson([
            'status' => true,
            'message' => Yii::t("app", "Item Deleted")
        ]);
    }

    public function actionDeleteHttp()
    {
        $response = ['success' => false, 'data' => '', 'msg' => "یافت نشد."];
        $deleteCondition = [
            'status' => MGTarget::STATUS_DELETED,
            'type' => (int)MGTarget::HTTP_EXCEPTION,
            'category' => ['yii\web\HttpException:400', 'yii\web\HttpException:401'],
            'client_id' => (int)7,
            'application' => 'api'
        ];

        try {
            MGTarget::deleteAll($deleteCondition);
            $response['msg'] = \hesabro\errorlog\Module::t('module', 'Item Deleted');
            $response['success'] = true;
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            throw $e;
        }

        return json_encode($response);
    }

    /**
     * Finds the MGTarget model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return MGTarget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $findType = 'active')
    {
        if (($model = MGTarget::find($findType)->byId($id)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\hesabro\errorlog\Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
