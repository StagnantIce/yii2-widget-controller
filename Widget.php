<?
namespace stagnantice\yii2;

use yii\base\Widget as YiiWidget;
use yii\helpers\Url;
use Yii;

class Widget extends YiiWidget
{
    public $template = 'template';
    public $result = [];
    public $path;
    public $shortPath;
    public $getParams = [];
    public $actionName = 'action';

    protected $actionId;

    private function findViewPath() {
        $paths = [];

        // check in site directory
        $path = Yii::getAlias('@app/views/widgets/'. $this->path . '/');
        if (file_exists($path . '/' . $this->template . '.php')) {
            return $path;
        }
        $paths[] = $path;

        // check in template directory
        /*$path = Yii::getAlias('@template/views/widgets/' . $this->path . '/');
        if (file_exists($path . '/' . $this->template . '.php')) {
            return  $path;
        }
        $paths[] = $path;*/

        // check in widget directory
        $class = new \ReflectionClass($this);
        $path = dirname(dirname($class->getFileName())) .'/views/' .  $this->shortPath . '/';

        if (file_exists($path . '/' . $this->template . '.php')) {
            return $path;
        }
        $paths[] = $path;

        throw new \Exception("View file '{$this->template}.php' for widget '{$this->getId()}' not found in: " . implode(', ', $paths));
    }

    public function getViewPath() {
        return $this->findViewPath();
    }

    public function registerAssets() {
        $viewPath = $this->getViewPath();
        if (file_exists($viewPath . 'script.js')) {
            $arr = Yii::$app->assetManager->publish($viewPath . 'script.js');
            $this->view->registerJsFile($arr[1]);
        }
        if (file_exists($viewPath . 'style.css')) {
            $arr = Yii::$app->assetManager->publish($viewPath . 'style.css');
            $this->view->registerCssFile($arr[1]);
        }
    }
    
    public function init()
    {
        parent::init();
        $class = explode('\\', static::className());
        $this->path = implode('.', $class));
        $this->shortPath = strtolower(end($class));
        $this->registerAssets();
        $this->getParams = Yii::$app->request->get($this->id, []);
    }

    public function getRoute($params) {
        $urlParams = [];
        $params = (array)$params;
        if (!$params) {
            $urlParams[$this->getId()] = null;
        } else if (isset($params[0])) {
            $url = $params[0];
            unset($params[0]);
            $urlParams[$this->getId()] = $params;
            $urlParams[$this->getId()][$this->actionName] = $url;
        }
        return Url::current($urlParams);
    }

    public function redirect($params = []) {
        return Yii::$app->getResponse()->redirect($this->getRoute($params));
    }

    public function to($params = []) {
        return $this->getRoute($params);
    }

    public function run()
    {
        if (isset($this->getParams[$this->actionName])) {
            $action = 'action'.ucfirst($this->getParams[$this->actionName]);
            unset($this->getParams[$this->actionName]);
            $f = new \ReflectionMethod($this, $action);
            $params = [];
            foreach ($f->getParameters() as $param) {
                if (array_key_exists($param->name, $this->getParams)) {
                    $params[] = $this->getParams[$param->name];
                    unset($this->getParams[$param->name]);
                }
            }
            call_user_func_array([$this, $action], $params);
        }
        return $this->render($this->template, ['result' => $this->result, 'widget' => $this, 'widgetClass' => get_class($this)]);
    }
}
