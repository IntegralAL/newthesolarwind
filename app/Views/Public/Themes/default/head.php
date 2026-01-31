<?
// Создатель CMS/Framework SolarWind: Ларионов Андрей Николаевич
// По поводу приобретения платной версии системы обращаться:
// Телефон для связи: 8-913-134-09-42
// Email: IntegralAL@mail.ru

use App\mobiledetect\Mobile_Detect;
use App\Models;
use App\Models\Class_AI;
use App\Models\Class_BD;
use App\Models\Class_Communicate;
use App\Models\Class_Config;
use App\Models\Class_Files;
use App\Models\Class_Math;
use App\Models\Class_paggination;
use App\Models\Class_String;
use App\Models\Components;
use App\Models\Components\Combobox;

$ConfigConnect = new Class_Config();      // 1
$Config = new Class_Config();             // 2
$BD = new Class_BD();                     // 3
$Communicate = new Class_Communicate();     // 5
$Combobox = new Combobox();               // 6
$Detect = new Mobile_Detect();            // 7
$StrClass = new Class_String();           // 8
$Files = new Class_Files();               // 9
$AI = new Class_AI();                     // 10
$Math = new Class_Math();                 // 11
$Paggination_ = new Class_Paggination();   // 13

$RUS_text['Creator'] = 'Ларионов Андрей';
$RUS_text['Copyright'] = 'integral@mail.ru';
?>