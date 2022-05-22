"use strict";

// Class Definition
var KTLogin = function () {
	var _login;

	var _showForm = function (form) {
		var cls = 'login-' + form + '-on';
		var form = 'kt_login_' + form + '_form';

		_login.removeClass('login-forgot-on');
		_login.removeClass('login-signin-on');
		_login.removeClass('login-signup-on');

		_login.addClass(cls);

		KTUtil.animateClass(KTUtil.getById(form), 'animate__animated animate__backInUp');
	}

	var _handleSignInForm = function () {
		var validation;

		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validation = FormValidation.formValidation(
			KTUtil.getById('kt_login_signin_form'),
			{
				fields: {
					email: {
						validators: {
							notEmpty: {
								message: 'Email address is required'
							}
						}
					},
					password: {
						validators: {
							notEmpty: {
								message: 'Password is required'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					submitButton: new FormValidation.plugins.SubmitButton(),
					//defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		);

		$('#kt_login_signin_submit').on('click', function (e) {
			e.preventDefault();
			validation.validate().then(function (status) {
				if (status == 'Valid') {
					var formData = $("#kt_login_signin_form").serialize();
					$.ajax({
						url: "scripts/login.php",
						type: "post",
						data: formData,
						success: function (response) {
							swal.fire({
								text: "You have successfully logged in. You are being redirected ...",
								icon: "success",
								buttonsStyling: false,
								confirmButtonText: "OK",
								customClass: {
									confirmButton: "btn font-weight-bold btn-light-primary"
								}
							}).then(function () {
								KTUtil.scrollTop();
							});
							setTimeout(function () {
								window.location.reload();
							}, 2000);
						},
						error: function (response) {
							swal.fire({
								text: response.responseText,
								icon: "error",
								buttonsStyling: false,
								confirmButtonText: "OK",
								customClass: {
									confirmButton: "btn font-weight-bold btn-light-primary"
								}
							}).then(function () {
								KTUtil.scrollTop();
							});
						}
					});
				} else {
					swal.fire({
						text: "Sorry, several errors apparently have been encountered. Please try again.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "OK",
						customClass: {
							confirmButton: "btn font-weight-bold btn-light-primary"
						}
					}).then(function () {
						KTUtil.scrollTop();
					});
				}
			});
		});

		// Handle forgot button
		$('#kt_login_forgot').on('click', function (e) {
			e.preventDefault();
			_showForm('forgot');
		});
	}

	var _handleForgotForm = function (e) {
		var validation;
		validation = FormValidation.formValidation(
			KTUtil.getById('kt_login_forgot_form'),
			{
				fields: {
					email: {
						validators: {
							notEmpty: {
								message: 'Enter your e-mail address.'
							},
							emailAddress: {
								message: 'Email is not a valid.'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		);

		// Handle submit button
		$('#kt_login_forgot_submit').on('click', function (e) {
			e.preventDefault();

			validation.validate().then(function (status) {
				if (status == 'Valid') {
					// Submit form
					KTUtil.scrollTop();
				} else {
					swal.fire({
						text: "Sorry, several errors apparently have been encountered. Please try again.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "OK",
						customClass: {
							confirmButton: "btn font-weight-bold btn-light-primary"
						}
					}).then(function () {
						KTUtil.scrollTop();
					});
				}
			});
		});

		// Handle cancel button
		$('#kt_login_forgot_cancel').on('click', function (e) {
			e.preventDefault();

			_showForm('signin');
		});
	}

	// Public Functions
	return {
		// public functions
		init: function () {
			_login = $('#kt_login');

			_handleSignInForm();
			_handleForgotForm();
		}
	};
}();

// Class Initialization
jQuery(document).ready(function () {
	KTLogin.init();
});
