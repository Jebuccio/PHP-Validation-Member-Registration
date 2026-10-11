<?php
/**
 * register.php
 * Processes the registration form:
 *   1. Validates every text field with a regular expression
 *   2. Validates and saves the uploaded photo
 *   3. On error  -> sends the user back to index.php with messages
 *      On success -> stores the member in the session and redirects to profile.php
 *
 * The redirect after a successful POST (Post/Redirect/Get) prevents the
 * form from being re-submitted when the user refreshes the profile page.
 */
session_start();
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/includes/validation.php';
require_once 'includes/helpers.php';


// Only accept form submissions.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Collect and trim the text inputs. Passwords are NOT trimmed.
$fields = ['first_name', 'last_name', 'email', 'mobile', 'birthdate', 'zip_code', 'username'];
$input  = [];
foreach ($fields as $field) {
    $input[$field] = trim($_POST[$field] ?? '');
}
$input['password']         = $_POST['password'] ?? '';
$input['confirm_password'] = $_POST['confirm_password'] ?? '';

// Step 1 + 2: validate.
$errors     = validate_fields($input);
$photoError = validate_photo($_FILES['photo'] ?? null);
if ($photoError !== null) {
    $errors['photo'] = $photoError;
}

// Step 3a: errors -> back to the form (without the passwords).
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = array_intersect_key($input, array_flip($fields));
    header('Location: index.php');
    exit;
}

// Save the photo with a NEW, unique name so users cannot overwrite each
// other's files or upload something like "../index.php".
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$mime      = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['photo']['tmp_name']);
$extension = PHOTO_MIME_TYPES[$mime];

// TODO 14: Build a unique file name such as  member_3f9a1c0b7d2e4f61.jpg
//          Hint: 'member_' . bin2hex(random_bytes(8)) . '.' . $extension
$photoName = 'member_' . bin2hex(random_bytes(8)); . '.' . $extension;

// TODO 15: Move the temporary upload into the uploads folder with move_uploaded_file().
//          Source: $_FILES['photo']['tmp_name']   Destination: $uploadDir . $photoName
//          If it fails, set $_SESSION['errors'] = ['photo' => 'The photo could not be saved on the server.'];
//          restore $_SESSION['old'] (same as above), redirect to index.php and exit.

if (!move_uploaded_file(
    $_FILES['photo']['tmp_name'],
    $uploadDir . $photoName
)) {
    $_SESSION['errors'] = [
        'photo' => 'The photo could not be saved on the server.'
    ];

    $_SESSION['old'] = array_intersect_key(
        $input,
        array_flip($fields)
    );

    header('Location: index.php');
    exit;
}



// Step 3b: success. Keep only what the profile page needs (NEVER the password).
$_SESSION['member'] = [
    'member_no'     => 'MBR-' . date('Y') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
    'first_name'    => $input['first_name'],
    // TODO 16: Add last_name, email, mobile, birthdate, zip_code and username from $input.
    'last_name'     => $input['last_name'],
    'email'         => $input['email'],
    'mobile'        => $input['mobile'],
    'birthdate'     => $input['birthdate'],
    'zip_code'      => $input['zip_code'],
    'username'      => $input['username'],
    'photo'         => 'uploads/' . $photoName,
    'registered_at' => date('F j, Y g:i A'),
];

header('Location: profile.php');
exit;
