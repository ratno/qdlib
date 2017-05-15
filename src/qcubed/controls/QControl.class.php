<?php

/**
 * QControl.class.php contains QControl Class
 * @package Controls
 */

/**
 * QControl is the user overridable Base-Class for all Controls.
 *
 * This class is intended to be modified.  Please place any custom modifications to QControl in the file.
 * The RenderWithName function provided here is a basic rendering.  Feel free to make your own modifcations.
 * Please note: All custom render methods should start with a RenderHelper call and end with a RenderOutput call.
 *
 * @package Controls
 */
abstract class QControl extends QControlBase
{
    /**
     * By default, wrappers are turned on for all controls. Wrappers create an extra <div> tag around
     * QControls, and were historically used to help manipulate QControls, and to group a name and error
     * message with a control. However, they can at times get in the way. Now that we are using jQuery to
     * manipulate controls, they are not needed as much, but they are still useful for grouping names and
     * error messages with a control. If you want to turn global wrappers off and rather set a wrapper for
     * individual controls, uncomment the line below.
     */
    //protected $blnUseWrapper = false;

    /**
     * Renders the control with an attached name
     *
     * This will call {@link QControlBase::GetControlHtml()} for the bulk of the work, but will add layout html as well.  It will include
     * the rendering of the Controls' name label, any errors or warnings, instructions, and html before/after (if specified).
     * As this is the parent class of all controls, this method defines how ALL controls will render when rendered with a name.
     * If you need certain controls to display differently, override this function in that control's class.
     *
     * @param boolean $blnDisplayOutput true to send to display buffer, false to just return then html
     * @return string HTML of rendered Control
     */
    public function RenderWithName($blnDisplayOutput = true)
    {
        ////////////////////
        // Call RenderHelper
        $this->RenderHelper(func_get_args(), __FUNCTION__);
        ////////////////////

        $strDataRel = '';
        $strWrapperAttributes = '';
        if (!$this->blnUseWrapper) {
            //there is no wrapper --> add the special attribute data-rel to the name control
            $strDataRel = sprintf('data-rel="#%s"', $this->strControlId);
            $strWrapperAttributes = 'data-hasrel="1"';
        }

        // Custom Render Functionality Here
        // Because this example RenderWithName will render a block-based element (e.g. a DIV), let's ensure
        // that IsBlockElement is set to true
        $this->blnIsBlockElement = true;

        // Render the Control's Dressing
        $strToReturn = '<div class="form_item" ' . $strDataRel . '>';

        // Render the Left side
        $strLabelClass = "form-name";
        if ($this->blnRequired) {
            $strLabelClass .= ' required';
        }
        if (!$this->blnEnabled) {
            $strLabelClass .= ' disabled';
        }

        if ($this->strInstructions) {
//      $strInstructions = '<br/><span class="instructions">' . $this->strInstructions . '</span>';
            $strInstructions = ' <span class="hint--right hint--rounded hint--info" data-hint="' . $this->strInstructions . '"><i class="icon-question-sign"></i></span>';
        } else {
            $strInstructions = '';
        }

        $strToReturn .= sprintf('<div class="%s"><label for="%s">%s</label>%s</div>', $strLabelClass, $this->strControlId, $this->strName, $strInstructions);

        // Render the Right side
        if ($this->strValidationError) {
            $strMessage = sprintf('<p><code><b>error:</b> %s</code></p>', $this->strValidationError);
            QPage::setFlashMessages("error|" . $this->strValidationError);
        } else if ($this->strWarning) {
            $strMessage = sprintf('<p><code><b>warning:</b> %s</code></p>', $this->strWarning);
//        QPage::setFlashMessages("warning|". $this->strWarning);
        } else {
            $strMessage = '';
        }

        try {
            $strToReturn .= sprintf('<div class="form-field">%s%s%s%s</div>', $this->strHtmlBefore, $this->GetControlHtml(), $this->strHtmlAfter, $strMessage);
        } catch (QCallerException $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $strToReturn .= '</div>';

        ////////////////////////////////////////////
        // Call RenderOutput, Returning its Contents
        return $this->RenderOutput($strToReturn, $blnDisplayOutput, false, $strWrapperAttributes);
        ////////////////////////////////////////////
    }

    /**
     * Renders the control with an error
     *
     * This will call {@link QControlBase::GetControlHtml()} for the bulk of the work, but will add layout html as well.  It will include
     * the rendering of the Controls' name label, any errors or warnings, instructions, and html before/after (if specified).
     * As this is the parent class of all controls, this method defines how ALL controls will render when rendered with a name.
     * If you need certain controls to display differently, override this function in that control's class.
     *
     * @param boolean $blnDisplayOutput true to send to display buffer, false to just return then html
     * @return string HTML of rendered Control
     */
    public function RenderWithError($blnDisplayOutput = true)
    {
        ////////////////////
        // Call RenderHelper
        $this->RenderHelper(func_get_args(), __FUNCTION__);
        ////////////////////

        $strDataRel = '';
        $strWrapperAttributes = '';
        if (!$this->blnUseWrapper) {
            //there is no wrapper --> add the special attribute data-rel to the name control
            $strDataRel = sprintf('data-rel="#%s"', $this->strControlId);
            $strWrapperAttributes = 'data-hasrel="1"';
        }

        // Custom Render Functionality Here
        // Because this example RenderWithName will render a block-based element (e.g. a DIV), let's ensure
        // that IsBlockElement is set to true
        $this->blnIsBlockElement = true;

        // Render the Control's Dressing
        $strToReturn = '<div ' . $strDataRel . '>';

        // Render the Right side
        if ($this->strValidationError) {
            $strMessage = sprintf('<p><code><b>error:</b> %s</code></p>', $this->strValidationError);
            QPage::setFlashMessages("error|" . $this->strValidationError);
        } else if ($this->strWarning) {
            $strMessage = sprintf('<p><code><b>warning:</b> %s</code></p>', $this->strWarning);
//        QPage::setFlashMessages("warning|". $this->strWarning);
        } else {
            $strMessage = '';
        }

        try {
            $strToReturn .= sprintf('<div>%s%s%s%s</div>', $this->strHtmlBefore, $this->GetControlHtml(), $this->strHtmlAfter, $strMessage);
        } catch (QCallerException $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $strToReturn .= '</div>';

        ////////////////////////////////////////////
        // Call RenderOutput, Returning its Contents
        return $this->RenderOutput($strToReturn, $blnDisplayOutput, false, $strWrapperAttributes);
        ////////////////////////////////////////////
    }

}

?>
