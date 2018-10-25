<?php

namespace App\Http\Controllers;

use App\CompanySoftware;
use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SoftwareController extends Controller
{

    public function index(Request $request) {
//        $license = $request->get('licence');
//        $software = $request->get('software');
//
//        $updateService = new UpdateService();
//
//        $check = $updateService->checkLicence($license, $software);
//
//        return response()->json(null, 204);
    }

    public function validateLicence(Request $request) {
        $license = $request->get('licence');
        $software = $request->get('software');

        if (empty($license) || empty($software)) {
            $responseData = [
                'code' => '0',
                'valid' => false,
                'msg' => 'No License Found'
            ];

            return Response::json($responseData);
        }

        $updateService = new UpdateService();

        $check = $updateService->checkLicence($license, $software);


        if($check) {
            $responseData = [
              'code' => '1',
              'valid' => true,
              'msg' => 'Software is Licenced'
            ];
        } else {
            $responseData = [
                'code' => '0',
                'valid' => false,
                'msg' => 'Invalid Licence Key'
            ];
        }
        return Response::json($responseData);
    }
}
