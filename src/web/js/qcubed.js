$.fn.extend({
	wait: function(time, type) {
		time = time || 1000;
		type = type || "fx";
		return this.queue(type, function() {
			var self = this;
			setTimeout(function() {
				$(self).dequeue();
			}, time);
		});
	}
});

/*
 * Queued Ajax requests.
 * A new Ajax request won't be started until the previous queued
 * request has finished.
 */
$.ajaxQueue = function(o){
	 $.ajax( o );
};


/*
 * Synced Ajax requests.
 * The Ajax request will happen as soon as you call this method, but
 * the callbacks (success/error/complete) won't fire until all previous
 * synced requests have been completed.
 */
$.ajaxSync = function(o){
	var fn = $.ajaxSync.fn, data = $.ajaxSync.data, pos = fn.length;

	fn[ pos ] = {
		error: o.error,
		success: o.success,
		complete: o.complete,
		done: false
	};

	data[ pos ] = {
		error: [],
		success: [],
		complete: []
	};

	o.error = function(){ data[ pos ].error = arguments; };
	o.success = function(){ data[ pos ].success = arguments; };
	o.complete = function(){
		data[ pos ].complete = arguments;
		fn[ pos ].done = true;

		if ( pos == 0 || !fn[ pos-1 ] )
			for ( var i = pos; i < fn.length && fn[i].done; i++ ) {
				if ( fn[i].error ) fn[i].error.apply( $j, data[i].error );
				if ( fn[i].success ) fn[i].success.apply( $j, data[i].success );
				if ( fn[i].complete ) fn[i].complete.apply( $j, data[i].complete );

				fn[i] = null;
				data[i] = null;
			}
	};

	return $.ajax(o);
};

$.ajaxSync.fn = [];
$.ajaxSync.data = [];

///////////////////////////////////////////////////
// The QCubed Object is used for everything in Qcodo
///////////////////////////////////////////////////
	var qcubed = {

		recordControlModification: function (strControlId, strProperty, strNewValue) {
			if (!qcubed.controlModifications[strControlId])
				qcubed.controlModifications[strControlId] = new Object;
			qcubed.controlModifications[strControlId][strProperty] = strNewValue;
		},

		postBack: function(strForm, strControl, strEvent, mixParameter) {
			var strForm = $("#Qform__FormId").attr("value");
			var objForm = $('#' + strForm);

			if (mixParameter && (typeof mixParameter !== "string")) {
				mixParameter = $.param({ "Qform__FormParameter" : mixParameter });
				objForm.append('<input type="hidden" name="Qform__FormParameterType" value="obj">');
			}
			
			$('#Qform__FormControl').val(strControl);
			$('#Qform__FormEvent').val(strEvent);
			$('#Qform__FormParameter').val(mixParameter);
			$('#Qform__FormCallType').val("Server");
			$('#Qform__FormUpdates').val(this.formUpdates());
			$('#Qform__FormCheckableControls').val(this.formCheckableControls(strForm, "Server"));

			// have $j trigger the submit event (so it can catch all submit events)
			objForm.trigger("submit");
		},

		formUpdates: function() {
			var strToReturn = "";
			for (var strControlId in qcubed.controlModifications)
				for (var strProperty in qcubed.controlModifications[strControlId])
					strToReturn += strControlId + " " + strProperty + " " + qcubed.controlModifications[strControlId][strProperty] + "\n";
			qcubed.controlModifications = new Object;
			return strToReturn;
		},

		formCheckableControls: function(strForm, strCallType) {

			// Select the QCubed Form
			var objFormElements = $('#' + strForm).find('input,select,textarea');
			var strToReturn = "";

			objFormElements.each(function(i) {
				if ((($(this).attr("type") == "checkbox") ||
					 ($(this).attr("type") == "radio")) &&
					((strCallType == "Ajax") ||
					(!$(this).attr("disabled")))) {

					var strControlId = $(this).attr("id");

					// RadioButtonList or CheckBoxList
					if (strControlId.indexOf('_') >= 0) {
						if (strControlId.indexOf('_0') >= 0)
							strToReturn += " " + strControlId.substring(0, strControlId.length - 2);

					// Standard Radio or Checkbox
					} else {
						strToReturn += " " + strControlId;
					}
				}
			});

			if (strToReturn.length > 0)
				return strToReturn.substring(1);
			else
				return "";
		},

		postAjax: function(strForm, strControl, strEvent, mixParameter, strWaitIconControlId) {

			var objForm = $('#' + strForm);
			var strFormAction = objForm.attr("action");
			var objFormElements = $('#' + strForm).find('input,select,textarea');
			var strPostData = '';

			if (mixParameter && (typeof mixParameter !== "string")) {
				strPostData = $.param({ "Qform__FormParameter" : mixParameter });
				objFormElements = objFormElements.not("#Qform__FormParameter");
			} else {
				$('#Qform__FormParameter').val(mixParameter);
			}
			
			$('#Qform__FormControl').val(strControl);
			$('#Qform__FormEvent').val(strEvent);
			$('#Qform__FormCallType').val("Ajax");
			$('#Qform__FormUpdates').val(this.formUpdates());
			$('#Qform__FormCheckableControls').val(this.formCheckableControls(strForm, "Ajax"));

			objFormElements.each(function () {
				var strType = $(this).attr("type");
				if (strType == undefined) strType = this.type;
				var strControlId = $(this).attr("id");
				switch (strType) {
					case "checkbox":
					case "radio":
						if ($(this).attr("checked")) {
							var strTestName;
							var bracketIndex = $(this).attr("name").indexOf('[');
							
							if (bracketIndex > 0) {
								strTestName = $(this).attr("name").substring (0, bracketIndex) + '_';
							} else {
								strTestName = $(this).attr("name") + "_";
							}
							
							if (strControlId.substring(0, strTestName.length) == strTestName)
								// CheckboxList or RadioButtonList
								strPostData += "&" + $(this).attr("name") + "=" + strControlId.substring(strTestName.length);
							else
								strPostData += "&" + strControlId + "=" + $(this).val();
						};
						break;

					case "select-multiple":
						var blnOneSelected = false;
						$(this).find(':selected').each (function (i) {
							strPostData += "&" + $(this).parents("select").attr("name") + "=";
							strPostData += $(this).val();
						});
						break;

          case "textarea":
						strPostData += "&" + strControlId + "=";

						// $(this).val() gagal mengambil data pada tinymce
						var strPostValue = jQuery(this).val();
						if (strPostValue) {
							strPostValue = strPostValue.replace(/\%/g, "%25");
							strPostValue = strPostValue.replace(/&/g, escape('&'));
							strPostValue = strPostValue.replace(/\+/g, "%2B");
						}
						strPostData += strPostValue;
						break;

					default:
						strPostData += "&" + strControlId + "=";

						// For Internationalization -- we must escape the element's value properly
						var strPostValue = $(this).val();
						if (strPostValue) {
							strPostValue = strPostValue.replace(/\%/g, "%25");
							strPostValue = strPostValue.replace(/&/g, escape('&'));
							strPostValue = strPostValue.replace(/\+/g, "%2B");
						}
						strPostData += strPostValue;
						break;
				}
			});

			if (strWaitIconControlId) {
				this.objAjaxWaitIcon = this.getWrapper(strWaitIconControlId);
				if (this.objAjaxWaitIcon)
					this.objAjaxWaitIcon.style.display = 'inline';
			};
			$.ajaxQueue({
				url: strFormAction,
				type: "POST",
				data: strPostData,
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					if (XMLHttpRequest.status != 0 || XMLHttpRequest.responseText.length > 0) {
						alert("An error occurred during AJAX Response parsing.\r\n\r\nThe error response will appear in a new popup.");
						var objErrorWindow = window.open('about:blank', 'qcodo_error','menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=1000,height=700,left=50,top=50');
						objErrorWindow.focus();
						objErrorWindow.document.write(XMLHttpRequest.responseText);
						return;
					}
				},
				success: function (xml) {
					$(xml).find('control').each(function() {
						var strControlId = '#' + $(this).attr("id");
						var strControlHtml = $(this).text();

						if (strControlId == "#Qform__FormState") {
							$(strControlId).val(strControlHtml);
						} else {
							$(strControlId + "_ctl").html(strControlHtml);
						}
					});
					var strCommands = [];
					$(xml).find('command').each(function() {
						strCommands.push($(this).text());
					});
					eval(strCommands.join(''));
					
					// cari flashmessages container set data dari xml
          var flashmessages = $(xml).find('flashmessages').text();
          if(flashmessages.length > 0){
            $('#flash').html(flashmessages);
            // flashing untuk flash messages yg berasal dari ajax action
            $('#flash').fadeIn(750);
            // close flash messages yg berasal dari ajax action
            $(".close").on("click", function(e){
              $(this).parent().fadeOut(750);
            });
            $(window).scrollTop(60);
            blink('#flash');
          }
          
          if($("table.datagrid").length>0) {
            // enable tree table setelah adanya ajax
            $("table.datagrid").find("tbody tr").each(function() {
              $(this).attr("data-level",$(this).find(".level").attr("data-level"));
            });
            $("table.datagrid").treeTable({
              ignoreClickOn: "input, a, img",
              collapsedByDefault: false
            });
          }
          
          if($('table.footable').length>0) {
            // enable footable after ajax
            $('table.footable').footable();
          }
					
					if (qcubed.objAjaxWaitIcon)
						$(qcubed.objAjaxWaitIcon).hide();
				}
			});

		},

		initialize: function() {



		////////////////////////////////
		// Browser-related functionality
		////////////////////////////////

			this.loadJavaScriptFile = function(strScript, objCallback) {
				if (strScript.indexOf("/") == 0) {
					strScript = qc.baseDir + strScript;
				} else if (strScript.indexOf("http") != 0) {
					strScript = qc.jsAssets + "/" + strScript;
				}
				$.ajax({
					url: strScript,
					success: objCallback,
					dataType: "script",
					cache: true
				});
			};

			this.loadStyleSheetFile = function(strStyleSheetFile, strMediaType) {
				if (strStyleSheetFile.indexOf("/") == 0) {
					strStyleSheetFile = qc.baseDir + strStyleSheetFile;
				} else if (strStyleSheetFile.indexOf("http") != 0) {
					strStyleSheetFile = qc.cssAssets + "/" + strStyleSheetFile;
				}

				$('head').append('<link rel="stylesheet" href="' + strStyleSheetFile + '" type="text/css" />');

			};




		/////////////////////////////
		// QForm-related functionality
		/////////////////////////////

			this.wrappers = new Array();


		}
	};

	///////////////////////////////
	// Timers-related functionality
	///////////////////////////////

		qcubed._objTimers = new Object();

		qcubed.clearTimeout = function(strTimerId) {
			if (qcubed._objTimers[strTimerId]) {
				clearTimeout(qcubed._objTimers[strTimerId]);
				qcubed._objTimers[strTimerId] = null;
			};
		};

		qcubed.setTimeout = function(strTimerId, strAction, intDelay) {
			qcubed.clearTimeout(strTimerId);
			qcubed._objTimers[strTimerId] = setTimeout(strAction, intDelay);
		};



	/////////////////////////////////////
	// Event Object-related functionality
	/////////////////////////////////////

		// You may still use this function but be advised
		// we no longer use it in core.  All event terminations
		// and event bubbling are handled through jQuery.
		// see http://trac.qcu.be/projects/qcubed/ticket/681
		// @deprecated
		qcubed.terminateEvent = function(objEvent) {
			objEvent = qcubed.handleEvent(objEvent);

			if (objEvent) {
				// Stop Propogation
				if (objEvent.preventDefault)
					objEvent.preventDefault();
				if (objEvent.stopPropagation)
					objEvent.stopPropagation();
				objEvent.cancelBubble = true;
				objEvent.returnValue = false;
			};

			return false;
		};


////////////////////////////////
// Qcodo Shortcut and Initialize
////////////////////////////////
	// Make sure we set $.noConflict() to $j

	var qc = qcubed;
	qc.initialize();

	qc.pB = qcubed.postBack;
	qc.pA = qcubed.postAjax;
