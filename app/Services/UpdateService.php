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
use ZipArchive;

class UpdateService
{
    /**
     * Download Update
     * @param $licence
     * @param $software
     * @param $version
     * @return array
     */
    public function updateSoftware($licence, $software, $version)
    {
        $dataArray = [];
        $validation = $this->checkLicence($licence, $software);
        $checkUpdates = $this->checkForUpdates($software, $version);

        if ($validation && $checkUpdates) {
            $dirName = public_path() . '/uploads/packs/1.1';
            $tempPath = public_path() . '/temp';
            // Choose a name for the archive.
            $zipFileName = 'myzip.zip';

            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );

            // Create "MyCoolName.zip" file in public directory of project.
            $zip = new ZipArchive;

            if ( $zip->open( public_path() . '/' . $zipFileName, ZipArchive::CREATE ) === true ) {
                // Copy all the files from the folder and place them in the archive.
                foreach (glob($dirName . '/*') as $fileName) {
                    $file = basename($fileName);
                    $zip->addFile($fileName, $file);
                }

                $zip->close();

            }

            $dataArray = [
                'headers' => $headers,
                'name' => $zipFileName,
                'path' => $tempPath
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
     * @return bool
     */
    protected function checkForUpdates($software, $version)
    {
        $updates = false;
        $dataSoftware = CompanySoftware::all()->where('id', $software);

        $dataSoftwareArray = $dataSoftware->toArray();

        if (empty($dataSoftwareArray)) {
            return false;
        }

        $newVersion = $dataSoftware->get(0)->version;

        if ($newVersion > $version) {
            $updates = true;
        }

        return $updates;
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
}
