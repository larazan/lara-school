<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class DynamicMimes implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = Arr::get(request()->all(), str_replace('.file', '.type', $attribute));
        $rowNumber = isset($matches[1]) ? (int)$matches[1] + 1 : null;

        if ($type === 'video_upload') {
            // return in_array($value->getMimeType(), ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-flv', 'video/webm']);
            $fail("{$rowNumber} Row Video Uploads File must be a video file (mp4, avi, mov, flv).");
        }

        if ($type === 'file_upload') {
            // return in_array($value->getMimeType(), ['text/plain', 'application/pdf', 'image/jpeg', 'image/png']);
            $fail("{$rowNumber} Row File Uploads File must be a file (txt, pdf, jpeg, png).");
        }

    }
}
