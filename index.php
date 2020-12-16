<?php
require('database.php');
require('accounts_db.php');
require('questions_db.php');

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'show_login';
    }
}

switch ($action) {
    case 'show_login': {
        include('login.php');
        break;
    }

    case 'validate_login': {
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');
        if ($email == NULL || $password == NULL) {
            $error = 'Email and Password not included';
            include('error.php');
        } else {
            $userId = validate_login($email, $password);
            if ($userId == false) {
                header("Location: .?action=display_registration");
            } else {
                header("Location: .?action=display_questions&userId=$userId");
            }
        }

        break;
    }

    case 'display_registration': {
        include('registration.php');
        break;
    }

    case 'submit_registration': {
        $firstName = filter_input(INPUT_POST, 'firstName');
        $lastName = filter_input(INPUT_POST, 'lastName');
        $birthday = filter_input(INPUT_POST, 'birthday');
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');
        if(strlen($password) < 8) {
            echo 'Password should be at least 8 characters';
        }
        if (empty($firstName)){
            echo 'First name is empty'; }
        echo "<br>";
        if (empty($lastName)){
            echo 'Last name is empty'; }
        echo "<br>";
        if (empty($birthday)){
            echo 'Birthday is empty'; }
        echo "<br>";
        if (empty($email)){
            echo 'Email is empty'; }
        echo "<br>";
        if (strpos($email, '@') == false ) {
            echo 'Email must contain an @ character';
            echo "<br>";
        }
        register_user($firstName, $lastName, $birthday, $email, $password);
        header("Location: .?action=show_login");
        break;
    }

    case 'display_questions': {
        $userId = filter_input(INPUT_GET, 'userId');
        $listType = filter_input(INPUT_GET, 'listType');
        if ($userId == NULL || $userId < 0) {
            header('Location: .?action=display_login');
        } else {
            $questions = get_users_questions($userId);
            $questions = ($listType === 'all') ? get_all_questions() : get_users_questions($userId);
            include('display_questions.php');
        }
        break;
    }

    case 'display_question_form': {
        $userId = filter_input(INPUT_GET, 'userId');
        if ($userId == NULL || $userId < 0) {
            header("Location: .?action=display_login");
        } else {
            include('question_form.php');
        }
        break;
    }

    case 'submit_question': {
        $userId = filter_input(INPUT_POST, 'userId');
        $title = filter_input(INPUT_POST, 'title');
        $body = filter_input(INPUT_POST, 'body');
        $skills = filter_input(INPUT_POST, 'skills');
        if ($userId == NULL || $title == NULL || $body == NULL || $skills == NULL) {
            $error = 'All fields are required';
            include('error.php');
        } else {
            create_question($title, $body, $skills, $userId);
            header("Location: .?action=display_questions&userId=$userId");
        }
        break;
    }

    case 'delete_question': {
        $questionId = filter_input(INPUT_POST, 'questionId');
        $userId = filter_input(INPUT_POST, 'userId');
        if ($questionId == NULL || $userId == NULL) {
            $error = 'Please enter your information';
            include('error.php');
        } else {
            delete_question($questionId);
            header("Location: .?action=display_questions&userId=$userId");
        }
        break;
    }
    case 'edit_question': {
        $questionId = filter_input(INPUT_POST, 'questionId');
        $userId = filter_input(INPUT_POST, 'userId');
        if ($questionId == NULL || $userId = NULL) {
            $error = 'Please enter your information';
            include('error.php');
        } else {
            $questions = get_question($questionId);
            $actionString = 'update_question';
            include('question_form.php');
        }
        break;
    }

    case 'display_edit_question': {
        $userId = filter_input(INPUT_POST,'userId');
        $title = filter_input(INPUT_POST, 'title');
        $body = filter_input(INPUT_POST, 'body');
        $skills = filter_input(INPUT_POST, 'skills');
        if ($userId == NULL || $title == NULL || $body == NULL ||$skills == NULL) {
            $error = 'All fields are required';
            include('error.php');
        } else {
            display_edit_question($title, $body, $skills, $userId);
            header("Location: .?action=display_questions&userId=$userId");
        }

        break;
    }

    case 'view_question': {
        $questionId = filter_input(INPUT_POST, 'questionId');
        $userId = filter_input(INPUT_POST, 'userId');
        if ($questionId == NULL || $userId == NULL) {
            $error = 'All fields are required';
            include('error.php');
        } else {
            $questions = get_question($questionId);
            include('display_questions.php');
        }

        break;
    }

    default: {
        $error = 'Unknown Action';
        include('error.php');
    }
}

?>
