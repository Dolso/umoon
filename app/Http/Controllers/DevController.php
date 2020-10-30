<?php

namespace App\Http\Controllers;

use App\Service\Controller\WorkWithClient;
use App\Jobs\AfterResponseMessageAddHisoryJob;
use Illuminate\Http\Request;

class DevController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request, $key_bot, WorkWithClient $work_with_client)
    {
        $data = $request->input();

        $response = $work_with_client->sendMessageOrReturnConfirmationToken($data, $key_bot);

        return response($response);
    }
}