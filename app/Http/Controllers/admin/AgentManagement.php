<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminBaseController;

class AgentManagement extends AdminBaseController
{
    public function listMasterAgent()
    {
        echo 'you are able to watch master agent list';

    }
    public function addMasterAgent()
    {
        echo 'You are able to add master agent';
    }
    public function modifyMasterAgent(Request $request)
    {
        echo 'You can edit a master agent';
    }
}
