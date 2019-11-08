<?php
/**
 *
 * User: ThangDang
 * Date: 7/27/18
 * Time: 16:45
 *
 */

namespace tas\social\models\config;


use app\models\WebConfiguration;
use Yii;
use yii\base\Model;

/**
 *
 * @property string $type
 */
class ConfigModel extends Model
{
    public function getType()
    {
        return '';
    }
    
    public function init()
    {
        parent::init();
        $object_vars = get_object_vars($this);
        if (count($object_vars)) {
            $keys = array_keys($object_vars);
            foreach ($keys as $key) {
                $val = $this->getConfig($key);
                if (!empty($val)) {
                    $this->{$key} = $val;
                }
            }
        }
    }
    
    private function getConfig($key)
    {
        $cache     = Yii::$app->cache;
        $cache_key = 'config_' . $this->type . '_' . $key;
        if (($result = $cache->get($cache_key)) == false) {
            $result = WebConfiguration::findOne([
                'name' => $key,
                'type' => $this->getType(),
            ]);
            $cache->set($cache_key, $result);
        }
        
        return $result ? $result->value : '';
    }
    
    private function saveConfig($key, $val)
    {
        $result = WebConfiguration::findOne([
            'name' => $key,
            'type' => $this->getType(),
        ]);
        if ($result == null) {
            $result        = new WebConfiguration();
            $result->type  = $this->getType();
            $result->name  = $key;
            $result->title = $this->getAttributeLabel($key);
        }
        $result->value = $val;
        if ($result->save()) {
            $cache     = Yii::$app->cache;
            $cache_key = 'config_' . $this->type . '_' . $key;
            $cache->delete($cache_key);
            return true;
        }
        return false;
    }
    
    /**
     * Update click to call config
     *
     * @return true|false
     */
    public function update()
    {
        if ($this->validate()) {
            $result      = true;
            $object_vars = get_object_vars($this);
            foreach ($object_vars as $key => $val) {
                $result &= $this->saveConfig($key, $val);
            }
            
            return $result;
        }
        return false;
    }
}