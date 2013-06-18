(function($, undefined) {
	"use strict";
	
	window.BLOG_APP = {
		
		global: {
			sf_namespace: 'Hasheado\\BlogBundle\\Controller\\Admin\\',
		},
		/*
		 * The common functionalities this will be executed in all pages.
		 */
		common : {
			/*
			 * This is executed First of all.
			 */
			init : function() {
				
				/* Modal delete confirmation */
				$('#modal-delete-confirmation').bind('show', function() {
				    var _this = $(this);
				    _this.find('.close, .secondary').on('click', function(e){
				    	_this.modal('hide');
				    });
				})
				.modal({ backdrop: true, show: false });

				$('.confirm-delete').on('click', function(e) {
				    e.preventDefault();
				    
				    var href = $(this).attr('href'),
				    	modal = $('#modal-delete-confirmation');
				    
				    modal.find('.danger').attr('href', href);
				    modal.modal('show');
				});
				/* END Modal delete confirmation */

				/* Collapse div's content */
				$('.btn-minimize').click(function(e){
					e.preventDefault();
					var $target = $(this).parent().parent().next('.box-content');
					
					if($target.is(':visible'))
						$('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
					else
						$('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
					
					$target.slideToggle();
				});
				/* END Collapse div's content */

				/* Chosen plugin - reference to jquery.chosen.min.js */
				var dropDown = $('.chzn-select');
				dropDown.chosen({});

				//Extend chosen to add non-existing tag
				dropDown.parent().find('.chzn-container .search-field input[type=text]').keydown( function (evt) {
					var stroke, _ref, target, list;
		            // get keycode
		            stroke = (_ref = evt.which) != null ? _ref : evt.keyCode;
		            target = $(evt.target);
		            list = target.parents('.chzn-container').find('.chzn-choices li.search-choice > span').map(function () { return $(this).text(); }).get();
           			//with "Tab" and "Enter" execute
           			if (stroke === 9 || stroke === 13) {
           				var value = $.trim(target.val());
			            // if the option does not exists
			            if ($.inArray(value,list) < 0) {
			                var option = $('<option>');
			                option.text(value).val(value).appendTo(dropDown);
			                option.attr('selected','selected');
			                // add the option and set as selected
			            }
			            // trigger the update event
			            dropDown.trigger("liszt:updated");
			            return false;
           			}
 				});
				/* END Chosen plugin */

			},

			/*
			 * This is executed after all functionalities.
			 */
			finalize : function() {
				
			}
		},

		/*
		 * Content Module
		 */
		some_controller : {

			'init' : function() {
				//This code will be executed in all pages of this module
			},

			/*
			 * Some comment here
			 */
			'some_action' : function() {
				
			}
		}
	};

	var UTIL = {
		exec : function(controller, action) {
			var ns = BLOG_APP,
                action = (action === undefined ) ? "init" : action;

			if (controller !== "" && ns[controller] && typeof ns[controller][action] == "function") {
				ns[controller][action]();
			}
		},

		init : function() {
			
			var main_container = $('#content'),
                data_controller = main_container.data("controller"),
                c_array, a_helper, controller, action;

            a_helper = data_controller.replace(window.BLOG_APP.global.sf_namespace, '');
            c_array = a_helper.split('::');
            controller = c_array[0].replace('Controller', '');
            action = c_array[1].replace('Action', '');

			UTIL.exec("common");
			UTIL.exec(controller);
			UTIL.exec(controller, action);
			UTIL.exec("common", "finalize");
		}
	};

	$(document).ready(UTIL.init);

})(jQuery);