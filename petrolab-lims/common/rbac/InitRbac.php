<?php
namespace common\rbac;

use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;

class InitRbac extends Controller
{
    public function actionIndex()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $roles = ['ADMIN', 'LAB_MANAGER', 'ANALYST', 'CUSTOMER_SERVICE', 'CLIENT'];
        foreach ($roles as $roleName) {
            $role = $auth->createRole($roleName);
            $auth->add($role);
        }

        $permissions = [
            'manageMasterData', 'assignSample', 'inputResult', 'reviewResult', 'issueCoa', 'viewOwnData'
        ];
        foreach ($permissions as $permName) {
            $perm = $auth->createPermission($permName);
            $auth->add($perm);
        }

        $auth->addChild($auth->getRole('ADMIN'), $auth->getPermission('manageMasterData'));
        $auth->addChild($auth->getRole('ADMIN'), $auth->getPermission('assignSample'));
        $auth->addChild($auth->getRole('ADMIN'), $auth->getPermission('inputResult'));
        $auth->addChild($auth->getRole('ADMIN'), $auth->getPermission('reviewResult'));
        $auth->addChild($auth->getRole('ADMIN'), $auth->getPermission('issueCoa'));

        $auth->addChild($auth->getRole('LAB_MANAGER'), $auth->getPermission('assignSample'));
        $auth->addChild($auth->getRole('LAB_MANAGER'), $auth->getPermission('reviewResult'));
        $auth->addChild($auth->getRole('LAB_MANAGER'), $auth->getPermission('issueCoa'));

        $auth->addChild($auth->getRole('ANALYST'), $auth->getPermission('inputResult'));
        $auth->addChild($auth->getRole('CUSTOMER_SERVICE'), $auth->getPermission('assignSample'));
        $auth->addChild($auth->getRole('CLIENT'), $auth->getPermission('viewOwnData'));

        $this->stdout("RBAC initialized.\n");
    }
}
