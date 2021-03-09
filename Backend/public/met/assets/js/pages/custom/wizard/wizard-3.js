"use strict";

// Class definition
var KTWizard3 = function () {
	// Base elements
	var _wizardEl;
	var _formEl;
	var _wizard;
	var _validations = [];

	// Private functions
	var initWizard = function () {
		// Initialize form wizard
		_wizard = new KTWizard(_wizardEl, {
			startStep: 1, // initial active step number
			clickableSteps: true  // allow step clicking
		});

		// Validation before going to next page
		_wizard.on('beforeNext', function (wizard) {
			// Don't go to the next step yet
			_wizard.stop();

			// Validate form
			var validator = _validations[wizard.getStep() - 1]; // get validator for currnt step
			validator.validate().then(function (status) {
				if (status == 'Valid') {
					_wizard.goNext();
					KTUtil.scrollTop();
				} else {
					Swal.fire({
						text: "Sorry, looks like there are some errors detected, please try again.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, got it!",
						customClass: {
							confirmButton: "btn font-weight-bold btn-light"
						}
					}).then(function () {
						KTUtil.scrollTop();
					});
				}
			});
		});

		// Change event
		_wizard.on('change', function (wizard) {
			KTUtil.scrollTop();
		});
	}

	var initValidation = function () {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		// Step 1
		_validations.push(FormValidation.formValidation(
			_formEl,
			{
				fields: {
					title: {
						validators: {
							notEmpty: {
								message: 'Title is required'
							}
						}
					},
					start_date: {
						validators: {
							notEmpty: {
								message: 'Date is required'
							},
							date: {
                      format: 'YYYY-MM-DD',
                      message: 'The value is not a valid date',
                }
						}
					},
					'teachers[]': {
						validators: {
							notEmpty: {
								message: 'Teacher is required'
							},
						}
					},

					description: {
						validators: {
							notEmpty: {
								message: 'description is required'
							}
						}
					},

				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		));

		// Step 2
		_validations.push(FormValidation.formValidation(
			_formEl,
			{
				fields: {
					plan_id: {
						validators: {
							notEmpty: {
								message: 'please choose plan'
							}
						}
					},
					price: {
						validators: {
							checkPrice: {
								message: 'price field value is required and must be number'
							}
						}
					},
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		));

		// Step 3
		_validations.push(FormValidation.formValidation(
			_formEl,
			{
				fields: {
					image: {
						validators: {
							notEmpty: {
								message: 'Course Image Is required'
							}
						}
					},
					media_type: {
						validators: {
							checkType: {
								message: 'Course Video Is required'
							}
						}
					},
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		));

		// Step 4
		_validations.push(FormValidation.formValidation(
			_formEl,
			{
				fields: {

				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		));
	}

	return {
		// public functions
		init: function () {
			_wizardEl = KTUtil.getById('kt_wizard_v3');
			_formEl = KTUtil.getById('kt_form');

			initWizard();
			initValidation();
		}
	};
}();

jQuery(document).ready(function () {

	const price_validate = function(){
		return {
			validate: function(input){
					const value = input.value;

					// if (!$.isNumeric(value)) {
					// 	return {
					// 				valid: false,
					// 		};
					// }


					var from = Number($('.amount_from').text());
					var to = Number($('.amount_to').text());


					if(from != '0' && to != '0' && value == '' && !$.isNumeric(value)){
						//$('.price').attr('disapled',false);
						return {
									valid: false,
							};
					}

					if (from == '0' && to == '0') {
						$('.price').val('');
						$('.price').attr('disapled',true);
						return {
									valid: true,
							};
					}

					if (value < from || value > to) {
							return {
										valid: false,
								};
					}
					// if ((from != '0' && value > from) || value < to) {
					// 	return {
					// 				valid: false,
					// 		};
					// }

			}
		};
	}

	const video_type_validate = function(){
		return {
			validate: function(input){
					const value = input.value;
					var video_url = $('.video').val();
					var video_uploaded = $('.js-input-intro').val();

					if (value == '') {
							$('.video').hide();
							$('.video_file').hide();
							return {
									valid: false,
							};
					}

					if (value != 'upload') {
						$('.video').show();
						$('.video_file').hide();
					}else{
						$('.video').hide();
						$('.video_file').show();
					}

					if (value == 'upload' && video_uploaded == '') {
						return {
								valid: false,
						};
					}else if (value != 'upload' && video_url == '') {
						return {
								valid: false,
						};
					}else {
						return {
								valid: true,
						};
					}
			}
		};
	}


	FormValidation.validators.checkPrice = price_validate;
	FormValidation.validators.checkType = video_type_validate;

	KTWizard3.init();
});
