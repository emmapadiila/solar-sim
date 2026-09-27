<?php
require __DIR__ . '/../../../src/bootstrap.php';

require_method('POST');

Auth::logout();
json_response(['success' => true]);
