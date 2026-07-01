<?php

namespace App\Support;

use App\Models\CustomerVerification;

class VerificationDocumentData
{
    public static function verification(CustomerVerification $verification): array
    {
        $data = $verification->toArray();

        unset($data['government_id_path'], $data['driving_license_path']);

        $data['documents'] = [
            'government_id' => self::document($verification, 'government-id'),
            'driving_license' => self::document($verification, 'driving-license'),
        ];

        return $data;
    }

    public static function document(CustomerVerification $verification, string $document): array
    {
        $path = match ($document) {
            'government-id' => $verification->government_id_path,
            'driving-license' => $verification->driving_license_path,
            default => null,
        };

        $fileName = $path ? basename($path) : null;
        $fileType = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : null;

        return [
            'available' => (bool) $path,
            'url' => $path ? route('customer.verification.document', [$verification->id, $document]) : null,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ];
    }
}
