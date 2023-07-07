<?php
/**
 * Template Name: ESLN RAQ Servie
 *
 *  by Zack Spalding @ Southeastern
 */

?>

<?php get_header(); ?>

<script>
    function toggleResponse(id) {
        var shortResponseElement = document.getElementById('response_' + id);
        var fullResponseElement = document.getElementById('full_response_' + id);

        if (shortResponseElement.style.display === 'none') {
            shortResponseElement.style.display = 'inline';
            fullResponseElement.style.display = 'none';
        } else {
            shortResponseElement.style.display = 'none';
            fullResponseElement.style.display = 'inline';
        }
    }
</script>

<div id="main-content"<?php highend_main_content_style(); ?>>

	<div class="container">

<?php
$jsonData = file_get_contents('https://wnylrc.org/ask-the-lawyer/raqsta');
$data = json_decode($jsonData, true);

// Number of results per page
$resultsPerPage = 5;

// Current page
$currentPage = isset($_GET['my_raqpage']) ? $_GET['my_raqpage'] : 1;

// Filtered results based on search query
$searchResults = array_filter($data, function ($item) {
    $searchQuery = isset($_GET['my_raqsearch']) ? strtolower($_GET['my_raqsearch']) : '';
    return strpos(strtolower($item['topic']), $searchQuery) !== false
        || strpos(strtolower($item['question']), $searchQuery) !== false
        || strpos(strtolower($item['answer']), $searchQuery) !== false;
});

// Total number of results
$totalResults = count($searchResults);

// Calculate pagination variables
$totalPages = ceil($totalResults / $resultsPerPage);
$startIndex = ($currentPage - 1) * $resultsPerPage;
$endIndex = $startIndex + $resultsPerPage;

// Paginated results
$paginatedResults = array_slice($searchResults, $startIndex, $resultsPerPage);

// Handle search form submission
if (isset($_GET['search'])) {
    $searchQuery = isset($_GET['my_raqsearch']) ? $_GET['my_raqsearch'] : '';
    $searchQuery = urlencode($searchQuery);
    header("Location: ?my_raqsearch=$searchQuery");
    exit();
}

// Handle reset form submission
if (isset($_GET['reset'])) {
    header("Location: ?");
    exit();
}

// Display search form
echo '<form method="get" action="">';
echo 'Search: <input type="text" name="my_raqsearch" value="' . (isset($_GET['my_raqsearch']) ? $_GET['my_raqsearch'] : '') . '">';
echo '<input type="submit" name="search" value="Search">';
echo '<input type="submit" name="reset" value="Reset">';
echo '</form>';

// Display total results
echo '<p>Total results: ' . $totalResults . '</p>';

// Display results
echo '<table>';
foreach ($paginatedResults as $result) {
    echo '<tr><td><h2>Topic:<b> ' . $result['topic'] . '</h2></b></td></tr>';
    echo '<tr><td><h2>Question: </h2>' . $result['question'] . '</td></tr>';

    $response = $result['answer'];
    $isLongResponse = strlen($response) > 1500;

    echo '<tr><td><h2>Attorney Response: </h2>';

    if ($isLongResponse) {
        $shortResponse = substr($response, 0, 1500);
        echo '<div class="read-more" id="response_' . $result['id'] . '">' . $shortResponse . '...<a class="eslnReadMore" href="#" onclick="toggleResponse(' . $result['id'] . '); return false;" id="toggl
e_' . $result['id'] . '">Read More</a></div>';
        echo '<div class="read-less" id="full_response_' . $result['id'] . '" style="display:none">' . $response . '...<a class="eslnReadLess" href="#" onclick="toggleResponse(' . $result['id'] . '); ret
urn false;">Read Less</a></div>';
    } else {
        echo $response;
    }

    echo '</td></tr>';
}

echo '</table>';

// Display pagination links
echo '<br>';
if ($totalPages > 1) {
    echo 'Page: ';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="?my_raqpage=' . $i . '&my_raqsearch=' . (isset($_GET['my_raqsearch']) ? $_GET['my_raqsearch'] : '') . '">' . $i . '</a> ';
    }
}
?>



	</div><!-- END .container -->

</div><!-- END #main-content -->

<?php get_footer(); ?>
