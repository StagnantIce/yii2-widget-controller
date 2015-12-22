# yii2-widget-controller
Yii2 widget action provider controller

If you want separate action for your widgets without controllers, wellcome!

Usage:

1) Extend your widgets from "Widget" class.

2) Add actions methods and use $widget->url() and $widget->redirect() methods in view and widget class instead yii/helpers/Url.

3) Create widget view file "template.php", js and css files "script.js" and "style.css" in widget or template folder.

