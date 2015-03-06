<?php

 /**
 * This class is used to embed FullCalendar JQuery Plugin to my Yii2 Projects
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 *
 */

namespace net\frenzel\textareaautosize;

use Yii;
use yii\base\Model;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\base\Widget as elWidget;

class yii2textareaautosize extends elWidget
{

    /**
    * @var array options the HTML attributes (name-value pairs) for the field container tag.
    * The values will be HTML-encoded using [[Html::encode()]].
    * If a value is null, the corresponding attribute will not be rendered.
    */
    public $options = [
        'class' => 'fullcalendar',
    ];

    /**
     * @var array clientOptions the HTML attributes for the widget container tag.
     */
    public $clientOptions = [
    ];

    /**
     * internal marker for the name of the plugin
     * @var string
     */
    private $_pluginName = 'textareaautosize';

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        //checks for the element id
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
         //checks for the element id
        if (!isset($this->options['class'])) {
            $this->options['class'] = 'fullcalendar';
        }

        parent::init();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {   
        $this->options['data-plugin-name'] = $this->_pluginName;

        if (!isset($this->options['class'])) {
            $this->options['class'] = 'fullcalendar';
        }
        
        echo Html::beginTag('div', $this->options) . "\n";
            echo Html::beginTag('div',['class'=>'fc-loading','style' => 'display:none;']);
                echo Html::encode($this->loading);
            echo Html::endTag('div')."\n";
        echo Html::endTag('div')."\n";
        $this->registerPlugin();
    }

    /**
    * Registers the FullCalendar javascript assets and builds the requiered js  for the widget and the related events
    */
    protected function registerPlugin()
    {        
        $id = $this->options['id'];
        $view = $this->getView();

        /** @var \yii\web\AssetBundle $assetClass */
        $assets = CoreAsset::register($view);

        if (isset($this->options['lang'])) 
        {
            $assets->language = $this->options['lang'];
        }        
        
        if ($this->googleCalendar) 
        {
            $asset->googleCalendar = $this->googleCalendar;
        }

        $js = array();

        if($this->ajaxEvents != NULL){
            $this->clientOptions['events'] = $this->ajaxEvents;
        }

        if(is_array($this->header))
        {
            $this->clientOptions['header'] = $this->header;
        }

        $cleanOptions = $this->getClientOptions();
        $js[] = "$('#$id').fullCalendar($cleanOptions);";

        //lets check if we have an event for the calendar...
        if(count($this->events)>0)
        {
            foreach($this->events AS $event)
            {
                $jsonEvent = Json::encode($event);
                $isSticky = $this->stickyEvents;
                $js[] = "$('#$id').fullCalendar('renderEvent',$jsonEvent,$isSticky);";
            }
        }
        
        $view->registerJs(implode("\n", $js),View::POS_READY);
    }

    /**
     * @return array the options for the text field
     */
    protected function getClientOptions()
    {
        $id = $this->options['id'];
        $options['loading'] = new JsExpression("function(isLoading, view ) {
                $('#{$id}').find('.fc-loading').toggle(isLoading);
        }");
        $options = array_merge($options, $this->clientOptions);
        return Json::encode($options);
    }

}