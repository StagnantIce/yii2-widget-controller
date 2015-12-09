<?
class TestWidget extends stagnantice\yii2\Widget
{
    public $message;
    public function actionStart() {
        if ($this->message == 'end') {
            $this->redirect('end');
        } else {
            $this->result = [
               'message' => 'end'
            ]
        }
    }

    public function actionEnd() {
        echo "End";
    }

    public function init() {
        parent::init();
        $this->message = 'start';
        $this->result = [
            'message' => $this->message
        ];
    }
}
