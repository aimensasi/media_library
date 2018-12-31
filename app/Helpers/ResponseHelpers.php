<?php


  if (!function_exists('respond')) {
    function respond($data, $status_code = 200, $headers = []){
      return response()->json($data, $status_code, $headers);
    }
  }

  if (!function_exists('success')) {
    function success($message = "", $status_code = 200){
      return respond($message, $status_code);
    }
  }

	if (!function_exists('validation_error')) {
    function validation_error($message = "", $status_code = 422){
      return respond(['message' => $message], $status_code);
    }
  }


	if (!function_exists('no_content')) {
    function no_content($message = "", $status_code = 204){
      return respond($message, $status_code);
    }
  }


  if (!function_exists('error')) {
    function error($message = "Internal Server Error", $status_code = 500){
      return respond($message, $status_code);
    }
  }

	if (!function_exists('not_found')) {
    function not_found($message = "Not Found", $status_code = 404){
      return respond($message, $status_code);
    }
  }

  if (!function_exists('form_error')) {
    function form_error_respond($messages, $status_code = 422){
      $messagesList = [];

      foreach ($messages->toArray() as $atrr => $msg) {
        $messagesList[] = [
          'field' => $atrr,
          'message' => $msg[0]
        ];
      }
      return error($messagesList, $status_code);
    }
  }
