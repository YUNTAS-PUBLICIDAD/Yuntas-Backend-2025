<?php

namespace App\Http\Controllers\Whatsapp;

use App\Application\Services\Template\TemplateService;
use App\Application\Support\TemplateVariableBuilder;
use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\Lead;
// use App\Models\WhatsappPopup;
// use App\Models\Product;
// use App\Models\WhatsappProducto;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsappPopupController extends Controller
{
}
