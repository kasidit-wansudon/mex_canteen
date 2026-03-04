<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

date_default_timezone_set('America/Mexico_City');

define('LARAVEL_START', microtime(true));
define('TIMESTAMP', date('Y-m-d H:i:s'));
define('LIMIT_DEFAULT', 20);

define('DB_CATTY', 'mysql_catty');
define('DB_WMS', 'mysql_wms');

define('WMS_DB', 'warehouse');
define('CATTY_DB', 'mexico_hr');
define('HR_DB', 'mexico_backoffice');

define('SUPERADMINPASSWORD', 'c10d28ed5df84c0054183a8a980b0f3d'); // AdminHQ@***@##@2026
define('SUPERADMINITPASSWORD', '000362747ed5629a118975ec5bc694ed'); // AdminIT@***@##@2026


define('SUPERPASSWORD', 'ca5e772ce54c4849a19c9176fbb4a19d'); //AdminMX@HQ

define('SUPERTRAININGPASSWORD', 'ca5e772ce54c4849a19c9176fbb4a19d'); //AdminMXHR@12345
define('MARIANA_SYSTEM', 'https://sellout.jumbo-mx.com/');


//group of create staff
define('FOR_STAFF_BACKOFFICE', '1');
define('FOR_STAFF_AREA', '2');
define('IS_STAFF_BACKOFFICE', 'backoffice');
define('IS_STAFF_AREA', 'area');

//step of create staff
define('BASIC_INFORMATION', '1');
define('EDU_INFORMATION', '2');
define('WORK_INFORMATION', '3');
define('SALARY_INFORMATION', '4');
define('AREA_INFORMATION', '5');
define('OTHER_INFORMATION', '6');
define('HIRING_DOCUMENT', '7');
define('AGENT_INFORMATION', '8');

//status for check account user
define('ENABLED', 1); //or can use check working status
define('SUSPEND', 2);
define('DISABLED', 0); //or can use check resign status

//staff group 
define('ADMINISTRATOR', 1);
define('HR', 2);
define('NORMAL_STAFF', 3); //default
define('AREA_ASSISTANT', 4);
define('DIRECTOR', 5);
//province id
define('HQ', 49);

//status use for check complete
define('COMPLETE', '1');
define('NOT_COMPLETE', '0');

//status use for check hr check
define('CHECKED', 1);
define('NOT_CHECKED', 0);

//status use for check re-active
define('COMPLETED_RE_ACTIVE', 2);

//status use for check approve
define('APPROVED', 1);
define('REJECTED', 2);
define('WAIT_APPROVE', 3);

define('FLAG_APPROVED', 'Approved');
define('FLAG_REJECTED', 'Rejected');
define('FLAG_PENDING', 'Pending');
define('REPORT_TYPE_TRANSFER', 2);
define('REPORT_TYPE_ASIGNED', 1);
define('ASM_TYPE_PROVINCE', 3);
define('ASM_TYPE_AREA', 2);

//status use for check process on approve
define('ON_PROCESSING', 1);
define('END_PROCESSING', 2);

define('DRAFTED', 'Draft');

//Max Hiring Document upload
define('MAX_FILES_UPLOAD', 12);

//department fix
define('HR_DEPARTMENT', 5);

//position fix
define('PRESIDENT', 65);
define('PC_POSITION', 95);
define('OBSPC_POSITION', 243);
define('HUNTER', 93);
define('HUNTER_LEADER', 92);
define('SUPERVISOR', 94);
define('REGIONAL_DIRECTOR', 88);
define('TRAINER', 96);
define('AREA_ASSISTANT_POSITION', 90);
define('SALE_DIRECTOR', 91);
define('HR_REGIONAL_ASSISTANT', 306);

//step for approval new staff on email
define('STEP_RD', 1);
define('STEP_SALE_DIRECTOR', 2);
define('STEP_PRESIDENT', 3);
define('STEP_HR', 4);

//type of approval on staff_approve
define('NEW_STAFF', 1);
define('RESIGN_STAFF', 2);
define('RECOVER_STAFF', 3);
define('TRANFER_STAFF', 4);
define('EDIT_STAFF', 4);

//type of edit approve for area assistant
define('EDIT_NEED_APPROVE', 1);
define('EDIT_WITHOUT_APPROVE', 2);

//type of attendance for work
define('FIX_SCHEDULE', 1);
define('WORK_SHIFT', 2);
define('FREEWORK', 3);

//staff group id from Catty System 
define('PC_GROUP_ID', 4);
define('Non_OPPO_GROUP_ID', 47);
define('PC_PartTime_GROUP_ID', 49);
define('KAM_STORE_ID', 48); // group name SUB Director Group
define('TRAINING_GROUP_ID', 17);
define('SALE_HUNTER_GROUP_ID', 51);
define('SALE_FARMER_GROUP_ID', 43);
define('RD_ID', 28);
define('RGM_GROUP_ID', 5);
#define('HUNTER_READER_GROUP_ID', 42);
define('AREA_ASSISTANT_ID', 50);
define('TRAINING_TEAM_ID', 17);
define('BACKOFFICE_ID', 46);
define('KAM_ID', 41);
define('HUNTER_LEADER_GROUP_ID', 42);
define('TRADE_MARKETING_HQ_ID', 57);

// Report Type use in Live Demo Function
define('TRANSFER_TYPE_PTP', 1);
define('TRANSFER_TYPE_STS', 2);

define('Assign_task_report_type', 1);
define('Transfer_ptp_report_type', 2); // transfer staff to staff
define('Transfer_sts_report_type', 3); // transfer store to store
define('Return_stock_report_type', 4);
define('Check_stock_report_type', 5);

define('REPORT_TYPE_WAITING', 1);
define('REPORT_TYPE_APPROVE', 2);
define('REPORT_TYPE_REJECT', 3);

// live demo approve center status task
define('APPROVE_C_PENDING', 1);
define('APPROVE_C_SUCCESS', 2);
define('APPROVE_C_REJECT', 3);
define('APPROVE_ISSUE_COMPLETE', 3);


define('SUCCESS', 'SUCCESS');
define('FAIL', 'FAIL');

define('ASSIGN', 'ASSIGN');
define('TRANSFERPTP', 'TRANSFERPTP');
define('TRANSFERSTS', 'TRANSFERSTS');
define('CHECK_STOCK', 'CHECK_STOCK');
define('RETURN_IMEI', 'RETURN_IMEI');
define('REPORT', 'REPORT');

define('STATUS_WAITING', 1);
define('STATUS_APPROVE', 2);
define('STATUS_REJECT', 3);

define('LD_HQ', 1);
define('LD_RD', 2);
define('LD_AS', 3);
define('LD_SALE', 4);

define('REPORT_PROCESS_PC', 1);
define('REPORT_PROCESS_AS', 2);
define('REPORT_PROCESS_RD', 3);


// condition groupmanagement 
define('CATTY_GROUP_CON', 20);
define('WMS_GROUP_CON', 21);

//Sync Oway HR

define('OWAY_API_LINK', 'https://hr.oppo-oway.com/api/front/');
define('OWAY_API_HEADER_TOKEN', 'ZkqczbfwCHQksPlONZsLQat4hm2vrPqrprZQF9RqT5TEtAt5yQcuPPSjMf43WPWM');
define('API_KEY_MEX', 'eNjKxUfH3jAAhutZdMYpydZKIc8wUi4e');

//dingTalk status
define('CONDITION_LEAVE_MONTH', 1);
define('CONDITION_LEAVE_YEAR', 2);
define('CONDITION_LEAVE_TIME', 3);


/* Ali mail API */
define('ALI_MAIL_GET_TOKEN_API_URL', 'https://alimail-cn.aliyuncs.com/oauth2/v2.0/token');
define('ALI_MAIL_DELETE_API_URL', 'https://alimail-cn.aliyuncs.com/v2/users/');
define('ALI_MAIL_CLIENT_ID', 'ygLCnbw2E07uyLfA');
define('ALI_MAIL_CLIENT_SECRET', 'r3uDB7vfi0CxMa6Wsr2VeSU8tbFwkgFAWOUy0Wa8J3DJPMxv352XRMEf7bLeP5j0');

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

#fix session path
session_save_path(__DIR__ . '/../storage/framework/sessions');

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
