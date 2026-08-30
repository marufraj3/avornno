<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LicenseController extends Controller
{
    public function licenseInfo()
    {
        $domain = preg_replace('/^www\./', '', request()->getHost());

        $licenseData = [
            'status' => 'valid',
            'domain' => $domain,
            'message' => 'This installation does not phone home. License is managed locally — no domain, IP, or license key is sent to a third-party server.',
        ];

        return view('backEnd.license.info', compact('licenseData'));
    }
}
