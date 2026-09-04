<?php
namespace App\Http\Controllers\Email;

use App\Application\Services\Template\TemplateService;
use App\Http\Controllers\Controller;
use App\Jobs\SendTemplateEmailJob;
use Illuminate\Http\Request;
use App\Jobs\SendProductEmailJob;
use Illuminate\Support\Facades\Mail;
use App\Mail\InicioMailing;
use App\Mail\ProductosMailing;
use App\Models\Lead;
use App\Models\EmailMessage;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isNull;

class EmailPopupController extends Controller
{

}
