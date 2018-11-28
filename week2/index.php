<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt18_week2', 'ddwt18','ddwt18');

/* Count number of series */
$nbr_series = count_series($db);

/* Count number of users */
$nbr_users = count_users($db);

/* Set right column */
$right_column = use_template('cards');

/* Set template for navigation */
$navigation_template = Array(
    0 => Array(
        'name' => 'Home',
        'url' => '/DDWT18/week2/' ),
    1 => Array(
        'name' => 'Overview',
        'url' => '/DDWT18/week2/overview/'),
    2 => Array(
        'name' => 'Add',
        'url' => '/DDWT18/week2/add/'),
    3 => Array(
        'name' => 'My Account',
        'url' => '/DDWT18/week2/myaccount/' ),
    4 => Array(
        'name' => 'Register',
        'url' => '/DDWT18/week2/register/')
);


/* Landing page */
if (new_route('/DDWT18/week2/', 'get')) {
    /* Page info */
    $page_title = $navigation_template[0]['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Home' => na('/DDWT18/week2/', True)
    ]);
    $navigation = get_navigation($navigation_template, '0');

    /* Page content */
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT18/week2/overview/', 'get')) {
    /* Page info */
    $page_title = $navigation_template[1]['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview', True)
    ]);
    $navigation = get_navigation($navigation_template, '1');

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table(get_series($db), $db);

    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT18/week2/serie/', 'get')) {
    /* Get series from db */
    $serie_id = $_GET['serie_id'];
    $user_id = get_user_id();
    $serie_info = get_serieinfo($db, $serie_id);
    if ($serie_info['user'] == $user_id) {
        $display_buttons = True;
    }
    else {
        $display_buttons = False;
    }

    /* Page info */
    $page_title = $serie_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview/', False),
        $serie_info['name'] => na('/DDWT18/week2/serie/?serie_id='.$serie_id, True)
    ]);
    $navigation = get_navigation($navigation_template, '1');

    /* Page content */
    $added_by = implode(" ", get_name($db, $serie_info['user'])); #implode to print values of the array
    $page_subtitle = sprintf("Information about %s", $serie_info['name']);
    $page_content = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];

    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT18/week2/add/', 'get')) {
    /* Check if user logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Add Series' => na('/DDWT18/week2/new/', True)
    ]);
    $navigation = get_navigation($navigation_template, '2');

    /* Page content */
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT18/week2/add/';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT18/week2/add/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Add serie to database */
    $feedback = add_serie($db, $_POST);
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT18/week2/add/?error_msg=%s',
        json_encode($feedback)));
}

/* Edit serie GET */
elseif (new_route('/DDWT18/week2/edit/', 'get')) {
    /* Check if user logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Get serie info from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        sprintf("Edit Series %s", $serie_info['name']) => na('/DDWT18/week2/new/', True)
    ]);
    $navigation = get_navigation($navigation_template, '1');

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $serie_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT18/week2/edit/';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT18/week2/edit/', 'post')) {
    /* Check if user logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Edit serie into db */
    $feedback = add_serie($db, $_POST);

    /* Redirect to edit GET route */
    redirect(sprintf('/DDWT18/week2/serie/?error_msg=%s',
        json_encode($feedback)));

    /* Choose Template */
    include use_template('serie');
}

/* Remove serie */
elseif (new_route('/DDWT18/week2/remove/', 'post')) {
    /* Check if user logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Remove serie in database */
    $serie_id = $_POST['serie_id'];
    $feedback = remove_serie($db, $serie_id);
    $error_msg = get_error_delete($feedback);

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview', True)
    ]);
    $navigation = get_navigation($navigation_template, '1');

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table(get_series($db), $db);

    /* Choose Template */
    include use_template('main');
}

/* My account get route */
elseif (new_route('/DDWT18/week2/myaccount/', 'get')) {
    /* Check if user logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Page info */
    $page_title = 'My Account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'My account' => na('/DDWT18/week2/myaccount/', True)
    ]);
    $navigation = get_navigation($navigation_template, '3');

    /* Page content */
    $user = implode(' ', get_name($db, $_SESSION['user_id']));
    $page_subtitle = 'Your account';
    $page_content = 'This page contains information about your account.';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    include use_template('account');
}

/* My account POST */
elseif (new_route('/DDWT18/week2/myaccount/', 'post')) {
    /* Register user */
    $feedback = register_user($db, $_POST);
    /* Redirect to homepage */
    redirect(sprintf('/DDWT18/week2/myaccount/?error_msg=%s',
        json_encode($feedback)));
}

/* Register GET */
elseif (new_route('/DDWT18/week2/register/', 'get')){
    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Register' => na('/DDWT18/week2/register/', True)
    ]);
    $navigation = get_navigation($navigation_template, '4');

    /* Page content */
    $page_subtitle = 'Register on Series Overview!';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('register');
}

/* Register POST */
elseif (new_route('/DDWT18/week2/register/', 'post')) {
    /* Register user */
    $error_msg = register_user($db, $_POST);
    /* Redirect to homepage */
    redirect(sprintf('/DDWT18/week2/register/?error_msg=%s',
        json_encode($error_msg)));
}

/* Login GET */
elseif (new_route('/DDWT18/week2/login/', 'get')){
    /* Check if user logged in */
    if ( check_login() ) {
        redirect('/DDWT18/week2/myaccount/');
    }
    /* Page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Login' => na('/DDWT18/week2/login/', True)
    ]);
    $navigation = get_navigation($navigation_template, 0);

    /* Page content */
    $page_subtitle = 'Use your username and password to login';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('login');
}

/* Login POST */
elseif (new_route('/DDWT18/week2/login/', 'post')){
    /* Login user */
    $feedback = login_user($db, $_POST);
    /* Redirect to homepage */
    redirect(sprintf('/DDWT18/week2/login/?error_msg=%s', json_encode($feedback)));
}

/* Log out GET */
elseif (new_route('/DDWT18/week2/logout/', 'get')) {
    /* Get error msg from POST route */
    $feedback = logout_user();
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']); }
    redirect(sprintf('/DDWT18/week2/', json_encode($feedback)));
}

else {
    http_response_code(404);
}