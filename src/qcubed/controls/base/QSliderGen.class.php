<?php

/**
 * QSliderGen File
 *
 * The abstract QSliderGen class defined here is
 * code-generated and contains options, events and methods scraped from the
 * JQuery UI documentation Web site. It is not generated by the typical
 * codegen process, but rather is generated periodically by the core QCubed
 * team and checked in. However, the code to generate this file is
 * in the assets/_core/php/_devetools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the JQuery UI site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference. Note that this is very low-level code, and does not always
 * update QCubed state variables. See the QSliderBase
 * file, which contains code to interface between this generated file and QCubed.
 *
 * Because subsequent re-code generations will overwrite any changes to this
 * file, you should leave this file unaltered to prevent yourself from losing
 * any information or code changes.  All customizations should be done by
 * overriding existing or implementing new methods, properties and variables
 * in the QSlider class file.
 *
 */
/* Custom event classes for this control */

/**
 * Triggered after the user slides a handle, if the value has changed; or if
 *        the value is changed programmatically via the <a><code>value</code></a>
 *        method.<ul><li><strong>event</strong> Type: <a>Event</a> </li>
 *        <li><strong>ui</strong> Type: <a>Object</a>
 *        <ul><li><strong>handle</strong> Type: <a>jQuery</a> The jQuery object
 *        representing the handle that was changed.</li> <li><strong>value</strong>
 *        Type: <a>Number</a> The current value of the slider.</li></ul></li></ul>
 */
class QSlider_ChangeEvent extends QJqUiEvent
{

    const EventName = 'slidechange';

}

/**
 * Triggered when the slider is created.<ul><li><strong>event</strong> Type:
 *        <a>Event</a> </li> <li><strong>ui</strong> Type: <a>Object</a>
 *        </li></ul><p><em>Note: The <code>ui</code> object is empty but included for
 *        consistency with other events.</em></p>
 */
class QSlider_CreateEvent extends QJqUiEvent
{

    const EventName = 'slidecreate';

}

/**
 * Triggered on every mouse move during slide. The value provided in the event
 *        as <code>ui.value</code> represents the value that the handle will have as
 *        a result of the current movement. Canceling the event will prevent the
 *        handle from moving and the handle will continue to have its previous
 *        value.<ul><li><strong>event</strong> Type: <a>Event</a> </li>
 *        <li><strong>ui</strong> Type: <a>Object</a>
 *        <ul><li><strong>handle</strong> Type: <a>jQuery</a> The jQuery object
 *        representing the handle being moved.</li> <li><strong>value</strong> Type:
 *        <a>Number</a> The value that the handle will move to if the event is not
 *        canceled.</li> <li><strong>values</strong> Type: <a>Array</a> An array of
 *        the current values of a multi-handled slider.</li></ul></li></ul>
 */
class QSlider_SlideEvent extends QJqUiEvent
{

    const EventName = 'slide';

}

/**
 * Triggered when the user starts sliding.<ul><li><strong>event</strong> Type:
 *        <a>Event</a> </li> <li><strong>ui</strong> Type: <a>Object</a>
 *        <ul><li><strong>handle</strong> Type: <a>jQuery</a> The jQuery object
 *        representing the handle being moved.</li> <li><strong>value</strong> Type:
 *        <a>Number</a> The current value of the slider.</li></ul></li></ul>
 */
class QSlider_StartEvent extends QJqUiEvent
{

    const EventName = 'slidestart';

}

/**
 * Triggered after the user slides a handle.<ul><li><strong>event</strong>
 *        Type: <a>Event</a> </li> <li><strong>ui</strong> Type: <a>Object</a>
 *        <ul><li><strong>handle</strong> Type: <a>jQuery</a> The jQuery object
 *        representing the handle that was moved.</li> <li><strong>value</strong>
 *        Type: <a>Number</a> The current value of the slider.</li></ul></li></ul>
 */
class QSlider_StopEvent extends QJqUiEvent
{

    const EventName = 'slidestop';

}

/* Custom "property" event classes for this control */

/**
 * Generated QSliderGen class.
 *
 * This is the QSliderGen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the QSliderBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see QSliderBase
 * @package Controls\Base
 * @property mixed $Animate Whether to slide the handle smoothly when the user clicks on the slider
 *        track. Also accepts any valid <a>animation duration</a>.<strong>Multiple
 *        types supported:</strong><ul><li><strong>Boolean</strong>: When set to
 *        <code>true</code>, the handle will animate with the default duration.</li>
 *        <li><strong>String</strong>: The name of a speed, such as
 *        <code>"fast"</code> or <code>"slow"</code>.</li>
 *        <li><strong>Number</strong>: The duration of the animation, in
 *        milliseconds.</li></ul>
 * @property boolean $Disabled Disables the slider if set to <code>true</code>.
 * @property integer $Max The maximum value of the slider.
 * @property integer $Min The minimum value of the slider.
 * @property string $Orientation Determines whether the slider handles move horizontally (min on left, max
 *        on right) or vertically (min on bottom, max on top). Possible values:
 *        <code>"horizontal"</code>, <code>"vertical"</code>.
 * @property mixed $Range Whether the slider represents a range.<strong>Multiple types
 *        supported:</strong><ul><li><strong>Boolean</strong>: If set to
 *        <code>true</code>, the slider will detect if you have two handles and
 *        create a stylable range element between these two.</li>
 *        <li><strong>String</strong>: Either <code>"min"</code> or
 *        <code>"max"</code>. A min range goes from the slider min to one handle. A
 *        max range goes from one handle to the slider max.</li></ul>
 * @property integer $Step Determines the size or amount of each interval or step the slider takes
 *        between the min and max. The full specified value range of the slider (max
 *        - min) should be evenly divisible by the step.
 * @property integer $Value Determines the value of the slider, if there's only one handle. If there is
 *        more than one handle, determines the value of the first handle.
 * @property array $Values This option can be used to specify multiple handles. If the
 *        <a><code>range</code></a> option is set to <code>true</code>, the length of
 *        <code>values</code> should be 2.
 */
class QSliderGen extends QPanel
{

    protected $strJavaScripts = __JQUERY_EFFECTS__;
    protected $strStyleSheets = __JQUERY_CSS__;

    /** @var mixed */
    protected $mixAnimate = null;

    /** @var boolean */
    protected $blnDisabled = null;

    /** @var integer */
    protected $intMax = null;

    /** @var integer */
    protected $intMin;

    /** @var string */
    protected $strOrientation = null;

    /** @var mixed */
    protected $mixRange = null;

    /** @var integer */
    protected $intStep = null;

    /** @var integer */
    protected $intValue;

    /** @var array */
    protected $arrValues = null;

    public function GetEndScript()
    {
        $str = '';
        if ($this->getJqControlId() !== $this->ControlId) {
            // #845: if the element receiving the jQuery UI events is different than this control
            // we need to clean-up the previously attached event handlers, so that they are not duplicated
            // during the next ajax update which replaces this control.
            $str = sprintf('jQuery("#%s").off(); ', $this->getJqControlId());
        }
        return $str . $this->GetControlJavaScript() . '; ' . parent::GetEndScript();
    }

    public function GetControlJavaScript()
    {
        return sprintf('jQuery("#%s").%s({%s})', $this->getJqControlId(), $this->getJqSetupFunction(), $this->makeJqOptions());
    }

    public function getJqSetupFunction()
    {
        return 'slider';
    }

    protected function makeJqOptions()
    {
        $strJqOptions = '';
        $strJqOptions .= $this->makeJsProperty('Animate', 'animate');
        $strJqOptions .= $this->makeJsProperty('Disabled', 'disabled');
        $strJqOptions .= $this->makeJsProperty('Max', 'max');
        $strJqOptions .= $this->makeJsProperty('Min', 'min');
        $strJqOptions .= $this->makeJsProperty('Orientation', 'orientation');
        $strJqOptions .= $this->makeJsProperty('Range', 'range');
        $strJqOptions .= $this->makeJsProperty('Step', 'step');
        $strJqOptions .= $this->makeJsProperty('Value', 'value');
        $strJqOptions .= $this->makeJsProperty('Values', 'values');
        if ($strJqOptions)
            $strJqOptions = substr($strJqOptions, 0, -2);
        return $strJqOptions;
    }

    protected function makeJsProperty($strProp, $strKey)
    {
        $objValue = $this->$strProp;
        if (null === $objValue) {
            return '';
        }

        return $strKey . ': ' . JavaScriptHelper::toJsObject($objValue) . ', ';
    }

    /**
     * Removes the slider functionality completely. This will return the element
     * back to its pre-init state.<ul><li>This method does not accept any
     * arguments.</li></ul>
     */
    public function Destroy()
    {
        $this->CallJqUiMethod("destroy");
    }

    /**
     * Call a JQuery UI Method on the object.
     *
     * A helper function to call a jQuery UI Method. Takes variable number of arguments.
     *
     * @param string $strMethodName the method name to call
     * @internal param $mixed [optional] $mixParam1
     * @internal param $mixed [optional] $mixParam2
     */
    protected function CallJqUiMethod($strMethodName /* , ... */)
    {
        $args = func_get_args();

        $strArgs = JavaScriptHelper::toJsObject($args);
        $strJs = sprintf('jQuery("#%s").%s(%s)', $this->getJqControlId(), $this->getJqSetupFunction(), substr($strArgs, 1, strlen($strArgs) - 2)); // params without brackets
        QApplication::ExecuteJavaScript($strJs);
    }

    /**
     * Disables the slider.<ul><li>This method does not accept any
     * arguments.</li></ul>
     */
    public function Disable()
    {
        $this->CallJqUiMethod("disable");
    }

    /**
     * Enables the slider.<ul><li>This method does not accept any
     * arguments.</li></ul>
     */
    public function Enable()
    {
        $this->CallJqUiMethod("enable");
    }

    /**
     * Gets the value currently associated with the specified
     * <code>optionName</code>.<ul><li><strong>optionName</strong> Type:
     * <a>String</a> The name of the option to get.</li></ul>
     * @param $optionName
     */
    public function Option($optionName)
    {
        $this->CallJqUiMethod("option", $optionName);
    }

    /**
     * Gets an object containing key/value pairs representing the current slider
     * options hash.<ul><li>This method does not accept any arguments.</li></ul>
     */
    public function Option1()
    {
        $this->CallJqUiMethod("option");
    }

    /**
     * Sets the value of the slider option associated with the specified
     * <code>optionName</code>.<ul><li><strong>optionName</strong> Type:
     * <a>String</a> The name of the option to set.</li>
     * <li><strong>value</strong> Type: <a>Object</a> A value to set for the
     * option.</li></ul>
     * @param $optionName
     * @param $value
     */
    public function Option2($optionName, $value)
    {
        $this->CallJqUiMethod("option", $optionName, $value);
    }

    /**
     * Sets one or more options for the slider.<ul><li><strong>options</strong>
     * Type: <a>Object</a> A map of option-value pairs to set.</li></ul>
     * @param $options
     */
    public function Option3($options)
    {
        $this->CallJqUiMethod("option", $options);
    }

    /**
     * Get the value of the slider.<ul><li>This method does not accept any
     * arguments.</li></ul>
     */
    public function Value()
    {
        $this->CallJqUiMethod("value");
    }

    /**
     * Set the value of the slider.<ul><li><strong>value</strong> Type:
     * <a>Number</a> The value to set.</li></ul>
     * @param $value
     */
    public function Value1($value)
    {
        $this->CallJqUiMethod("value", $value);
    }

    /**
     * Get the value for all handles.<ul><li>This method does not accept any
     * arguments.</li></ul>
     */
    public function Values()
    {
        $this->CallJqUiMethod("values");
    }

    /**
     * Get the value for the specified handle.<ul><li><strong>index</strong> Type:
     * <a>Integer</a> The zero-based index of the handle.</li></ul>
     * @param $index
     */
    public function Values1($index)
    {
        $this->CallJqUiMethod("values", $index);
    }

    /**
     * Set the value for the specified handle.<ul><li><strong>index</strong> Type:
     * <a>Integer</a> The zero-based index of the handle.</li>
     * <li><strong>value</strong> Type: <a>Number</a> The value to set.</li></ul>
     * @param $index
     * @param $value
     */
    public function Values2($index, $value)
    {
        $this->CallJqUiMethod("values", $index, $value);
    }

    /**
     * Set the value for all handles.<ul><li><strong>values</strong> Type:
     * <a>Array</a> The values to set.</li></ul>
     * @param $values
     */
    public function Values3($values)
    {
        $this->CallJqUiMethod("values", $values);
    }

    public function __get($strName)
    {
        switch ($strName) {
            case 'Animate':
                return $this->mixAnimate;
            case 'Disabled':
                return $this->blnDisabled;
            case 'Max':
                return $this->intMax;
            case 'Min':
                return $this->intMin;
            case 'Orientation':
                return $this->strOrientation;
            case 'Range':
                return $this->mixRange;
            case 'Step':
                return $this->intStep;
            case 'Value':
                return $this->intValue;
            case 'Values':
                return $this->arrValues;
            default:
                try {
                    return parent::__get($strName);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'Animate':
                $this->mixAnimate = $mixValue;

                if ($this->Rendered) {
                    $this->CallJqUiMethod('option', 'animate', $mixValue);
                }
                break;

            case 'Disabled':
                try {
                    $this->blnDisabled = QType::Cast($mixValue, QType::Boolean);
                    if ($this->Rendered) {
                        $this->CallJqUiMethod('option', 'disabled', $this->blnDisabled);
                    }
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Max':
                try {
                    $this->intMax = QType::Cast($mixValue, QType::Integer);
                    if ($this->Rendered) {
                        $this->CallJqUiMethod('option', 'max', $this->intMax);
                    }
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Min':
                try {
                    $this->intMin = QType::Cast($mixValue, QType::Integer);
                    if ($this->Rendered) {
                        $this->CallJqUiMethod('option', 'min', $this->intMin);
                    }
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Orientation':
                try {
                    $this->strOrientation = QType::Cast($mixValue, QType::String);
                    if ($this->Rendered) {
                        $this->CallJqUiMethod('option', 'orientation', $this->strOrientation);
                    }
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Range':
                $this->mixRange = $mixValue;

                if ($this->Rendered) {
                    $this->CallJqUiMethod('option', 'range', $mixValue);
                }
                break;

            case 'Step':
                try {
                    $this->intStep = QType::Cast($mixValue, QType::Integer);
                    if ($this->Rendered) {
                        $this->CallJqUiMethod('option', 'step', $this->intStep);
                    }
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Value':
                try {
                    $this->intValue = QType::Cast($mixValue, QType::Integer);
                    if ($this->Rendered) {
                        $this->CallJqUiMethod('option', 'value', $this->intValue);
                    }
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Values':
                try {
                    $this->arrValues = QType::Cast($mixValue, QType::ArrayType);
                    if ($this->Rendered) {
                        $this->CallJqUiMethod('option', 'values', $this->arrValues);
                    }
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }


            case 'Enabled':
                $this->Disabled = !$mixValue; // Tie in standard QCubed functionality
                parent::__set($strName, $mixValue);
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

}

?>
