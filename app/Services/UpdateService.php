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
use App\Software;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Request;

class UpdateService
{
    public function updateSoftware($licence, $software, $version)
    {

        $validation = $this->checkLicence($licence, $software);

        if ($validation) {
            $dataSoftware = CompanySoftware::with('licences')->where('id', $software)->get();


        }
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
     */
    protected function checkForUpdates($software, $version)
    {
        $dataSoftware = CompanySoftware::all()->where('id', $software);

        $newVersion = $dataSoftware->get(0)->version;


        dd($newVersion);
    }

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