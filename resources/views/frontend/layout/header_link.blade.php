<!DOCTYPE html>
<html>

<head>
    <title>{{ __('label.site_title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset("/admin/images/favicon.png") }}" type="image/x-icon">
	<!-- Bootstrap -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('frontend/css/jasny-bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Validation-Engine/2.6.4/validationEngine.jquery.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">

    <link href="{{ asset('frontend/css/owl.carousel.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('frontend/css/owl.theme.default.min.css') }}" rel="stylesheet" media="screen">

	<link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet" media="screen">

	<script src="https://code.jquery.com/jquery.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.core.min.js"></script>
</head>
