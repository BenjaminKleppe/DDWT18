<?php
/**
 * Controller
 * User: benjaminkleppe
 * Date: 03-12-18
 * Time: 14:30
 */

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Connect to database */
$db = connect_db('localhost', 'ddwt18_week3', 'ddwt18', 'ddwt18');

/* Set the credentials */
$cred = set_cred('ddwt18', 'ddwt18');

/* Create Router instance */
$router = new \Bramus\Router\Router();

/* Check if user logged in, in order to GET/POST/PUT/DELETE */
$router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){
    // Validate authentication
    if (!check_cred($cred)) {
        echo 'Authentication required.';
        http_response_code(401);
        exit();
    }
    echo "Succesfully authenticated";
});

$router->mount('/api', function() use ($router, $db){
    /* change content-type to json */
    http_content_type('application/json');

    /* GET for reading all series */
    $router->get('/series', function() use($db) {
        // Retrieve and output information
        $series = json_encode(get_series($db));
        echo $series;
    });

    /* GET for reading individual series */
    $router->get('/series/(\d+)', function($id) use($db) {
        // Retrieve and output information
        $serie_info = json_encode(get_serieinfo($db, $id));
        echo $serie_info;
    });

    /* DELETE for individual series */
    $router->delete('/series/(\d+)', function($id) use($db) {
        // Retrieve and output information
        $removed = json_encode(remove_serie($db, $id));
        echo $removed;
    });

    /* POST for adding series */
    $router->post('/series', function() use ($db) {
        // Add series with POST information
        $added = add_serie($db, $_POST);
        $added_json = json_encode($added);
        echo $added_json;
    });

    /* Update for individual series */
    $router->put('/series/(\d+)', function($id) use($db) {
        // Fake $_PUT
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        $serie_info = $_PUT + ["serie_id" => $id];
        $updated = update_serie($db, $serie_info);
        $updated_json = json_encode($updated);
        echo $updated_json;
    });
});

/* Return valid error message when the page is not found */
$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    // ... do something special here
    echo 'This page does not exist, please check your website url or try an other page.';
});

/* Run the router */
$router->run();
