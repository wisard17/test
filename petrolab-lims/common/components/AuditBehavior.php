<?php
namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use common\models\AuditLog;

/**
 * Automatically records audit trails for changes on ActiveRecord models.
 */
class AuditBehavior extends Behavior
{
    public $actions = [
        ActiveRecord::EVENT_AFTER_INSERT => 'insert',
        ActiveRecord::EVENT_AFTER_UPDATE => 'update',
        ActiveRecord::EVENT_AFTER_DELETE => 'delete',
    ];

    public function events()
    {
        return array_keys($this->actions);
    }

    public function afterInsert($event)
    {
        $this->writeLog($event->changedAttributes, $this->owner->getAttributes(), 'insert');
    }

    public function afterUpdate($event)
    {
        $this->writeLog($event->changedAttributes, $this->owner->getAttributes(), 'update');
    }

    public function afterDelete($event)
    {
        $this->writeLog($this->owner->getOldAttributes(), [], 'delete');
    }

    private function writeLog($before, $after, $action)
    {
        $log = new AuditLog();
        $log->user_id = Yii::$app->user && !Yii::$app->user->isGuest ? Yii::$app->user->id : null;
        $log->action = $action;
        $log->model = get_class($this->owner);
        $log->model_pk = (string) $this->owner->getPrimaryKey();
        $log->before_data = $before ?: null;
        $log->after_data = $after ?: null;
        $log->created_at = time();
        $log->save(false);
    }
}
