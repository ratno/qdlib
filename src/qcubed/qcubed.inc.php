<?php
/* Unless otherwise specified, all files in the QCubed Development Framework
 * are under the following copyright and licensing policies:
 *
 * QCubed Development Framework for PHP
 * http://www.qcu.be
 *
 * The QCubed Development Framework is distributed by the QCubed Project
 * under the terms of The MIT License.  More information can be found at
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright (c) 2001 - 2009, Quasidea Development, LLC; QCubed Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

// Versioning Information
define('QDF_VERSION_NUMBER_ONLY', '1.100');
define('QDF_VERSION', QDF_VERSION_NUMBER_ONLY . ' Development Release (QDF ' . QDF_VERSION_NUMBER_ONLY . ')');

define('__JQUERY_CORE_VERSION__', '1.4.4');
define('__JQUERY_UI_VERSION__', '1.8.6');

// Preload Required Framework Classes
require_once('framework/DisableMagicQuotes.inc.php');
require_once('controls/base/_enumerations.inc.php');
require_once('framework/QBaseClass.class.php');
require_once('framework/QExceptions.class.php');
require_once('framework/QType.class.php');
require_once('framework/QApplicationBase.class.php');

// Setup the Error Handler
require_once('error.inc.php');

// Start Output Buffering
function __ob_callback($strBuffer)
{
    return QApplication::OutputPage($strBuffer);
}

ob_start('__ob_callback');

// Preload Other Framework Classes
require_once('framework/QDatabaseBase.class.php');
require_once('database/QPdoDatabase.class.php');
if (version_compare(PHP_VERSION, '5.2.0', '<'))
    // Use the Legacy (Pre-5.2.0) QDateTime class
    require_once('framework/QDateTime.legacy.class.php');
else
    // Use the New QDateTime class (which extends PHP DateTime)
    require_once('framework/QDateTime.class.php');

// Define Classes to be Preloaded on QApplication::Initialize()
QApplicationBase::$PreloadedClassFile['qcontrolbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QControlBase.class.php';
QApplicationBase::$PreloadedClassFile['qcontrol'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QControl.class.php';
QApplicationBase::$PreloadedClassFile['qpage'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QPage.class.php';
QApplicationBase::$PreloadedClassFile['qformbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QFormBase.class.php';
QApplicationBase::$PreloadedClassFile['qform'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QForm.class.php';
QApplicationBase::$PreloadedClassFile['_actions'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/_actions.inc.php';
QApplicationBase::$PreloadedClassFile['_effect_actions'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/_effect_actions.inc.php';
QApplicationBase::$PreloadedClassFile['_events'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/_events.inc.php';
QApplicationBase::$PreloadedClassFile['qq'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QQuery.class.php';

// Define ClassFile Locations for FormState Handlers
QApplicationBase::$ClassFile['qformstatehandler'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/qform_state_handlers/QFormStateHandler.class.php';
QApplicationBase::$ClassFile['qsessionformstatehandler'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/qform_state_handlers/QSessionFormStateHandler.class.php';
QApplicationBase::$ClassFile['qfileformstatehandler'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/qform_state_handlers/QFileFormStateHandler.class.php';

// Define ClassFile Locations for Framework Classes
QApplicationBase::$ClassFile['qrssfeed'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QRssFeed.class.php';
QApplicationBase::$ClassFile['qrssimage'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QRssFeed.class.php';
QApplicationBase::$ClassFile['qrsscategory'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QRssFeed.class.php';
QApplicationBase::$ClassFile['qrssitem'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QRssFeed.class.php';
QApplicationBase::$ClassFile['qemailserver'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QEmailServer.class.php';
QApplicationBase::$ClassFile['qemailmessage'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QEmailServer.class.php';
QApplicationBase::$ClassFile['qmimetype'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QMimeType.class.php';
QApplicationBase::$ClassFile['qdatetime'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QDateTime.class.php';
QApplicationBase::$ClassFile['qstring'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QString.class.php';
QApplicationBase::$ClassFile['qstack'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QStack.class.php';
QApplicationBase::$ClassFile['qcryptography'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QCryptography.class.php';
QApplicationBase::$ClassFile['qsoapservice'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QSoapService.class.php';
QApplicationBase::$ClassFile['qi18n'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QI18n.class.php';
QApplicationBase::$ClassFile['qtranslationbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QTranslationBase.class.php';
QApplicationBase::$ClassFile['qtranslationpoparser'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QTranslationPoParser.class.php';

QApplicationBase::$ClassFile['qqueryexpansion'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QQueryExpansion.class.php';
QApplicationBase::$ClassFile['qconvertnotation'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QConvertNotation.class.php';
QApplicationBase::$ClassFile['qfolder'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QFolder.class.php';
QApplicationBase::$ClassFile['qfile'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QFile.class.php';
QApplicationBase::$ClassFile['qarchive'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QArchive.class.php';
QApplicationBase::$ClassFile['qlexer'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QLexer.class.php';
QApplicationBase::$ClassFile['qregex'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QRegex.class.php';
QApplicationBase::$ClassFile['qtimer'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QTimer.class.php';

QApplicationBase::$ClassFile['qinstallationvalidator'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QInstallationValidator.class.php';

QApplicationBase::$ClassFile['qplugin'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QPluginInterface.class.php';
QApplicationBase::$ClassFile['qpluginconfigparser'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QPluginConfigParser.class.php';
QApplicationBase::$ClassFile['qplugininstallerbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QPluginInstallerBase.class.php';
QApplicationBase::$ClassFile['qplugininstaller'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QPluginInstaller.class.php';
QApplicationBase::$ClassFile['qpluginuninstaller'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QPluginUninstaller.class.php';

QApplicationBase::$ClassFile['qcache'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QCache.class.php';
QApplicationBase::$ClassFile['qdatetimespan'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QDateTimeSpan.class.php';

// Cache providers
QApplicationBase::$ClassFile['qabstractcacheprovider'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QAbstractCacheProvider.class.php';
QApplicationBase::$ClassFile['qcacheprovidermemcache'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QCacheProviderMemcache.class.php';
QApplicationBase::$ClassFile['qcacheproviderlocalmemory'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QCacheProviderLocalMemory.class.php';
QApplicationBase::$ClassFile['qcacheprovidernocache'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QCacheProviderNoCache.class.php';
QApplicationBase::$ClassFile['qmultilevelcacheprovider'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QMultiLevelCacheProvider.class.php';
QApplicationBase::$ClassFile['qdbbackedsessionhandler'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QDbBackedSessionHandler.class.php';

// Define ClassFile Locations for Qform Classes
QApplicationBase::$ClassFile['qfontfamily'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QFontFamily.class.php';

QApplicationBase::$ClassFile['qcalendar'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QCalendar.class.php';
QApplicationBase::$ClassFile['qdatetimepicker'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDateTimePicker.class.php';
QApplicationBase::$ClassFile['qdatetimetextbox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDateTimeTextBox.class.php';

QApplicationBase::$ClassFile['qcheckbox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QCheckBox.class.php';
QApplicationBase::$ClassFile['qfilecontrol'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QFileControl.class.php';
QApplicationBase::$ClassFile['qradiobutton'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QRadioButton.class.php';

QApplicationBase::$ClassFile['qblockcontrol'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QBlockControl.class.php';
QApplicationBase::$ClassFile['qlabel'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QLabel.class.php';
QApplicationBase::$ClassFile['qpanel'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QPanel.class.php';
QApplicationBase::$ClassFile['qcontrolproxy'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QControlProxy.class.php';
QApplicationBase::$ClassFile['qdialogbox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDialogBox.class.php';

QApplicationBase::$ClassFile['qcontrollabel'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QControlLabel.class.php';

QApplicationBase::$ClassFile['qactioncontrol'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QActionControl.class.php';
QApplicationBase::$ClassFile['qbuttonbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QButtonBase.class.php';
QApplicationBase::$ClassFile['qbutton'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QButton.class.php';
QApplicationBase::$ClassFile['qimagebutton'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QImageButton.class.php';
QApplicationBase::$ClassFile['qlinkbutton'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QLinkButton.class.php';

QApplicationBase::$ClassFile['qlistcontrol'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QListControl.class.php';
QApplicationBase::$ClassFile['qlistitem'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QListItem.class.php';
QApplicationBase::$ClassFile['qlistboxbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QListBoxBase.class.php';
QApplicationBase::$ClassFile['qlistbox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QListBox.class.php';
QApplicationBase::$ClassFile['qlistitemstyle'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QListItemStyle.class.php';
QApplicationBase::$ClassFile['qcheckboxlist'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QCheckBoxList.class.php';
QApplicationBase::$ClassFile['qradiobuttonlist'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QRadioButtonList.class.php';
QApplicationBase::$ClassFile['qtreenav'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QTreeNav.class.php';
QApplicationBase::$ClassFile['qtreenavitem'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QTreeNavItem.class.php';

QApplicationBase::$ClassFile['qtextboxbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QTextBoxBase.class.php';
QApplicationBase::$ClassFile['qtextbox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QTextBox.class.php';
QApplicationBase::$ClassFile['qnumerictextbox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QNumericTextBox.class.php';
QApplicationBase::$ClassFile['qfloattextbox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QFloatTextBox.class.php';
QApplicationBase::$ClassFile['qintegertextbox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QIntegerTextBox.class.php';
QApplicationBase::$ClassFile['qwritebox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QWriteBox.class.php';

QApplicationBase::$ClassFile['qpaginatedcontrol'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QPaginatedControl.class.php';
QApplicationBase::$ClassFile['qpaginatorbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QPaginatorBase.class.php';
QApplicationBase::$ClassFile['qpaginator'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QPaginator.class.php';

QApplicationBase::$ClassFile['qdatagridbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDataGridBase.class.php';
QApplicationBase::$ClassFile['qdatagridcolumn'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDataGridColumn.class.php';
QApplicationBase::$ClassFile['qcheckboxcolumn'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QCheckBoxColumn.class.php';
QApplicationBase::$ClassFile['qdatagridrowstyle'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDataGridRowStyle.class.php';
QApplicationBase::$ClassFile['qdatagrid'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QDataGrid.class.php';

QApplicationBase::$ClassFile['qsimpletablebase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QSimpleTableBase.class.php';
QApplicationBase::$ClassFile['qabstractsimpletablecolumn'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QSimpleTableColumn.class.php';
QApplicationBase::$ClassFile['qsimpletablepropertycolumn'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QSimpleTableColumn.class.php';
QApplicationBase::$ClassFile['qsimpletableindexedcolumn'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QSimpleTableColumn.class.php';
QApplicationBase::$ClassFile['qsimpletableclosurecolumn'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QSimpleTableColumn.class.php';
QApplicationBase::$ClassFile['qsimpletable'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QSimpleTable.class.php';

QApplicationBase::$ClassFile['qdatarepeater'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDataRepeater.class.php';

QApplicationBase::$ClassFile['qwaiticon'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QWaitIcon.class.php';
QApplicationBase::$ClassFile['qcontrolgrouping'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QControlGrouping.class.php';
QApplicationBase::$ClassFile['qdropzonegrouping'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDropZoneGrouping.class.php';

QApplicationBase::$ClassFile['qsamplecontrol'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QSampleControl.class.php';

// jQuery controls
QApplicationBase::$ClassFile['qdraggablebase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDraggableBase.class.php';
QApplicationBase::$ClassFile['qdroppablebase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDroppableBase.class.php';
QApplicationBase::$ClassFile['qresizablebase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QResizableBase.class.php';
QApplicationBase::$ClassFile['qselectablebase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QSelectableBase.class.php';
QApplicationBase::$ClassFile['qsortablebase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QSortableBase.class.php';
QApplicationBase::$ClassFile['qaccordionbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QAccordionBase.class.php';
QApplicationBase::$ClassFile['qautocompletebase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QAutocompleteBase.class.php';
QApplicationBase::$ClassFile['qjqbuttonbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QJqButtonBase.class.php';
QApplicationBase::$ClassFile['qdatepickerbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDatepickerBase.class.php';
QApplicationBase::$ClassFile['qdatepickerboxbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDatepickerBoxBase.class.php';
QApplicationBase::$ClassFile['qdialogbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QDialogBase.class.php';
QApplicationBase::$ClassFile['qprogressbarbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QProgressbarBase.class.php';
QApplicationBase::$ClassFile['qsliderbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QSliderBase.class.php';
QApplicationBase::$ClassFile['qtabsbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QTabsBase.class.php';
QApplicationBase::$ClassFile['qanytimeboxbase'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/base/QAnyTimeBoxBase.class.php';

QApplicationBase::$ClassFile['qdraggable'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QDraggable.class.php';
QApplicationBase::$ClassFile['qdroppable'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QDroppable.class.php';
QApplicationBase::$ClassFile['qresizable'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QResizable.class.php';
QApplicationBase::$ClassFile['qselectable'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QSelectable.class.php';
QApplicationBase::$ClassFile['qsortable'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QSortable.class.php';
QApplicationBase::$ClassFile['qaccordion'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QAccordion.class.php';
QApplicationBase::$ClassFile['qautocomplete'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QAutocomplete.class.php';
QApplicationBase::$ClassFile['qjqbutton'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QJqButton.class.php';
QApplicationBase::$ClassFile['qdatepicker'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QDatepicker.class.php';
QApplicationBase::$ClassFile['qdatepickerBox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QDatepickerBox.class.php';
QApplicationBase::$ClassFile['qdialog'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QDialog.class.php';
QApplicationBase::$ClassFile['qprogressbar'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QProgressbar.class.php';
QApplicationBase::$ClassFile['qslider'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QSlider.class.php';
QApplicationBase::$ClassFile['qtabs'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QTabs.class.php';
QApplicationBase::$ClassFile['qanytimebox'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QAnyTimeBox.class.php';

QApplicationBase::$ClassFile['qjsclosure'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/JavaScriptHelper.class.php';
QApplicationBase::$ClassFile['javascripthelper'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/JavaScriptHelper.class.php';
QApplicationBase::$ClassFile['qnoscriptajaxaction'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/JavaScriptHelper.class.php';
QApplicationBase::$ClassFile['qjstimer'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QJsTimer.class.php';

QApplicationBase::$ClassFile['qfirebug'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QFirebug.class.php';
QApplicationBase::$ClassFile['qrequest'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QRequest.class.php';
QApplicationBase::$ClassFile['qr'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QRequest.class.php';

QApplicationBase::$ClassFile['qtinymce'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/controls/QTinyMCE.class.php';

QApplicationBase::$ClassFile['js'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/js.class.php';
QApplicationBase::$ClassFile['datastream'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/DataStream.class.php';
QApplicationBase::$ClassFile['qerror'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QError.class.php';
QApplicationBase::$ClassFile['qapi'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QApi.class.php';
QApplicationBase::$ClassFile['qapidispatcher'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QApiDispatcher.class.php';
QApplicationBase::$ClassFile['qcss'] = __BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/framework/QCss.class.php';

include(__BASEPATH__ . '/vendor/ratno/qdlib/src/qcubed/_jq_paths.inc.php');