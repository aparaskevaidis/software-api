<?php

namespace App\Http\Controllers;

use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SoftwareController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function updateSoftware(Request $request)
    {

        $license = $request->get('licence');
        $software = $request->get('software');
        $version = $request->get('version');

        if (empty($license) || empty($software) || empty($version)) {
            $responseData = [
                'code' => '0',
                'valid' => false,
                'msg' => 'No License Found'
            ];

            return Response::json($responseData);
        }

        $updateService = new UpdateService();

        $responseData = $updateService->updateSoftware($license, $software, $version);
        ob_clean();
        //dd(Response::download($responseData['path'], null, $responseData['headers']));
        return Response::download($responseData['path'], $responseData['name'], $responseData['headers'], 'attachment');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkForSoftwareUpdates(Request $request)
    {

        $license = $request->get('licence');
        $software = $request->get('software');
        $version = $request->get('version');

        if (empty($license) || empty($software)) {
            $responseData = [
                'code' => '0',
                'valid' => false,
                'msg' => 'No License Found'
            ];

            return Response::json($responseData);
        }

        $updateService = new UpdateService();

        $license = $updateService->checkLicence($license, $software);

        if ($license) {
            $check = $updateService->checkForUpdates($software, $version);

            if ($check['updates']) {

                $responseData = [
                    'code' => '1',
                    'updates' => true,
                    'url' => route('software/download', ['licence' => $license, 'software' => $software, 'version' => $version]),
                    'msg' => 'Updates Available'
                ];
            } else {
                $responseData = [
                    'code' => '2',
                    'updates' => false,
                    'url' => '',
                    'msg' => 'No Updates Available'
                ];
            }

        } else {
            $responseData = [
                'code' => '0',
                'valid' => false,
                'msg' => 'No License Found'
            ];
        }

        return Response::json($responseData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateLicence(Request $request)
    {
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


        if ($check) {
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
