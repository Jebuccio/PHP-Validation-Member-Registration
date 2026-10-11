<?php
/**
 * validation.php
 * Regular-expression rules for the Member Registration activity.
 *
 * Each rule has:
 *   label   - human-friendly field name
 *   pattern - the regular expression the input must match   <-- YOU WRITE THESE
 *   hint    - the format shown to the user when the input is wrong
 *
 * Tip: test each pattern at https://regex101.com (choose the PCRE2 / PHP flavor)
 *      before pasting it here.
 */

const RULES = [
    'first_name' => [
        'label'   => 'First Name',
        // TODO 1: Starts with a letter; then letters, spaces, periods, apostrophes or hyphens; 2-50 characters total.
        //         Must accept names like "Ñiña" and "Ma. Clara". Hint: \p{L} matches any letter but needs the /u flag.
        'pattern' => '/^(?=.{2,50}$)\p{L}[\p{L} .\'-]*$/u',
        'hint'    => 'Letters only (spaces, periods, apostrophes and hyphens allowed), 2-50 characters. e.g. Juan, Ma. Clara, Dela Cruz',
    ],
    'last_name' => [
        'label'   => 'Last Name',
        // TODO 2: Same rule as first_name.
        'pattern' => '/^(?=.{2,50}$)\p{L}[\p{L} .\'-]*$/u',
        'hint'    => 'Letters only (spaces, periods, apostrophes and hyphens allowed), 2-50 characters. e.g. Dela Cruz, O\'Neil',
    ],
    'email' => [
        'label'   => 'Email Address',
        // TODO 3: local-part @ domain . top-level-domain (TLD is at least 2 letters).
        //         Must accept juan.delacruz@usjr.edu.ph; must reject juan@ and juan@usjr
        'pattern' => '/^[^@\s]+@[^@\s]+\.[a-zA-Z]{2,}$/',
        'hint'    => 'A valid email address. e.g. juan.delacruz@usjr.edu.ph',
    ],
    'mobile' => [
        'label'   => 'Mobile Number',
        // TODO 4: Philippine mobile number: 09 followed by 9 digits, OR +639 followed by 9 digits.
        //         Remember: + is a special character in regex.
        'pattern' => '/^(09\d{9}|\+639\d{9})$/',
        'hint'    => 'Philippine mobile number: 09XXXXXXXXX or +639XXXXXXXXX. e.g. 09171234567',
    ],
    'birthdate' => [
        'label'   => 'Birthdate',
        // TODO 5: YYYY-MM-DD. Year 1900-2099, month 01-12, day 01-31.
        //         (Whether the date really exists, e.g. 2025-02-30, is checked later with checkdate().)
        'pattern' => '/^(19\d{2}|20\{2})-(0[0-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/',
        'hint'    => 'Format YYYY-MM-DD. e.g. 2004-08-15',
    ],
    'zip_code' => [
        'label'   => 'ZIP Code',
        // TODO 6: Exactly 4 digits.
        'pattern' => '/^\d{4}$/',
        'hint'    => 'Exactly 4 digits. e.g. 6000',
    ],
    'username' => [
        'label'   => 'Username',
        // TODO 7: Starts with a letter; then letters, digits or underscore; 6-15 characters TOTAL.
        'pattern' => '/^[A-Za-z][A-Za-z0-9_]{5-14}$/',
        'hint'    => '6-15 characters, must start with a letter; letters, digits and underscore only. e.g. juan_dc2004',
    ],
    'password' => [
        'label'   => 'Password',
        // TODO 8: At least 8 characters, no spaces, and contains at least one lowercase letter,
        //         one uppercase letter, one digit and one special (non-alphanumeric) character.
        //         Hint: use lookaheads, e.g. (?=.*[a-z]) means "somewhere ahead there is a lowercase letter".
        'pattern' => '/^(?=\S{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).$/',
        'hint'    => 'At least 8 characters with an uppercase letter, a lowercase letter, a digit and a special character; no spaces.',
    ],
];

/** Photo upload rules */
const PHOTO_MAX_BYTES  = 2 * 1024 * 1024;   // 2 MB
// TODO 9: The ORIGINAL file name must end in .jpg, .jpeg or .png (any letter case).
//         Allowed characters before the extension: letters, digits, underscore, hyphen, period, space.
const PHOTO_NAME_PATTERN = '/^.+\.(jpg|jpeg|png)$/i';
const PHOTO_MIME_TYPES   = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

/**
 * Validates the text fields against RULES.
 * Returns an array of error messages keyed by field name (empty = all valid).
 */
function validate_fields(array $input): array
{
    $errors = [];
    

    foreach (RULES as $field => $rule) {
        $value = trim($input[$field]) ?? '';

        if ($value === ''){
            $errors[$field] = "{$rule['label']} is required.";
        }elseif (!preg_match($rule['pattern'], $value)) {
            $errors[$field] = "Invalid {$rule['label']}.{$rule['hint']}";

        }

        // TODO 10: If $value is empty      -> $errors[$field] = "{$rule['label']} is required.";
        //          Else if it does NOT match $rule['pattern'] (use preg_match)
        //                                  -> $errors[$field] = "Invalid {$rule['label']}. {$rule['hint']}";

    

    // Regex checks the SHAPE of the date; checkdate() checks it actually exists (rejects 2025-02-30).
    $parts = explode('-', $input['birthdate'] ?? '');
    if (!isset($errors['birthdate']) && count($parts) === 3) {
        [$y, $m, $d] = array_map('intval', $parts);
        if (!checkdate($m, $d, $y)) {
            $errors['birthdate'] = 'Birthdate is not a real calendar date.';
        } elseif (new DateTime($input['birthdate']) > new DateTime('today')) {
            $errors['birthdate'] = 'Birthdate cannot be in the future.';
        }
    }

    // TODO 11: If confirm_password is not exactly equal to password
    //          -> $errors['confirm_password'] = 'Passwords do not match.';

    if(
        isset($input['password'], $input['confirm_password']) &&
        $input['password'] !== $input ['confirm_password']
    ) {
        $errors['confirm_password'] = 'Password do not match';
    }


    return $errors;
}
}

/**
 * Validates the uploaded photo ($_FILES['photo']).
 * Returns an error message, or null if the upload is acceptable.
 */
function validate_photo(?array $file): ?string
{
   
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return 'A membership photo is required.';
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return 'Photo is too large. Maximum size is 2 MB.';
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'The photo could not be uploaded. Please try again.';
    }
    if ($file['size'] > PHOTO_MAX_BYTES ) {
        return 'Photo is too large. Maximum size is 2 MB';
    }
    if (!preg_match(PHOTO_NAME_PATTERN, $file ['name'])){
        return 'Photo must be a .jpg, .jpeg or .png file (file name: letters, digits, spaces, - _ . only).';
    }
    // TODO 12: If $file['size'] is greater than PHOTO_MAX_BYTES
    //          -> return 'Photo is too large. Maximum size is 2 MB.';
    


    // TODO 13: If $file['name'] does NOT match PHOTO_NAME_PATTERN
    //          


    // Never trust the extension alone - check the real content type.
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!array_key_exists($mime, PHOTO_MIME_TYPES) || getimagesize($file['tmp_name']) === false) {
        return 'The uploaded file is not a valid JPG or PNG image.';
    }

    return null;
}
