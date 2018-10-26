<?php
/**
 * Created by PhpStorm.
 * User: pelfe
 * Date: 25/10/2018
 * Time: 11:37 πμ
 */

namespace App\Services;


use App\CompanySoftware;
use App\Licences;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use ZanySoft\Zip\Zip;

class UpdateService
{
    /**
     * Download Update
     * @param $licence
     * @param $software
     * @param $version
     * @return array
     * @throws \Exception
     */
    public function updateSoftware($licence, $software, $version)
    {
        $dataArray = [];
        $validation = $this->checkLicence($licence, $software);
        $checkUpdates = $this->checkForUpdates($software, $version);

        if ($validation && $checkUpdates['updates']) {
            $dirName = public_path() . '/uploads/' . $checkUpdates['version'];
            $tempPath = public_path() . '/temp/';
            // Choose a name for the archive.
            $zipFileName = $checkUpdates['title'].'_'.$checkUpdates['version'].'.zip';
            File::delete($tempPath.'/'.$zipFileName);
            $zip = Zip::create($tempPath . $zipFileName);

            $zip->add($dirName);
            $zip->close();

            $headers = array(
                'Content-Type' => 'application/octet-stream',
                'Content-Transfer-Encoding'=> 'Binary',
                'Content-Length' => filesize($tempPath.$zipFileName)
            );

            $dataArray = [
                'headers' => $headers,
                'name' => $zipFileName,
                'path' => $tempPath . $zipFileName
            ];
        }

        return $dataArray;
    }

    /**
     * Checks If Entered License is Valid
     * @param $licence
     * @param $software
     * @return bool
     */
    public function checkLicence($licence, $software)
    {

        $dataLicences = Licences::all()->where('software_id', $software);

        return $this->licenceValidation($dataLicences, $licence);
    }

    /**
     * Check For Updates
     * @param $software
     * @param $version
     * @return array
     */
    public function checkForUpdates($software, $version)
    {
        $updates = false;
        $dataSoftware = CompanySoftware::all()->where('id', $software);

        $dataSoftwareArray = $dataSoftware->toArray();

        if (empty($dataSoftwareArray)) {
            return [
                'updates' => false,
                'version' => '',
                'title' => ''
            ];
        }

        $newVersion = $dataSoftware->get(0)->version;
        $title = $dataSoftware->get(0)->title;
        if ($newVersion > $version) {
            $updates = true;
        }

        return [
            'updates' => $updates,
            'version' => $newVersion,
            'title' => $title
        ];
    }

    /**
     * Validate Licence
     * @param Collection $licences
     * @param $licence
     * @return bool
     */
    private function licenceValidation(Collection $licences, $licence)
    {
        $licenced = false;
        $dateTime = '';

        $dataLicencesArray = $licences->toArray();
        $valid = false;

        if (empty($dataLicencesArray)) {
            return false;
        }

        foreach ($licences as $key => $value) {
            if ($licence == $value->value) {
                $dateTime = $value->updated_at;
                $licenced = true;
            }
        }

        $timestamp = Carbon::parse($dateTime)->timestamp;
        $yearTimestamp = strtotime('+1 year', $timestamp);
        $todayTimestamp = Carbon::parse(date('Y-m-d H:i:s'))->timestamp;

        if ($todayTimestamp < $yearTimestamp && $licenced) {

            $valid = true;
        }

        return $valid;
    }

    private function createZip($files = array(), $destination = '', $overwrite = false)
    {


    }

}
