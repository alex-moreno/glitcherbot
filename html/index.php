<?php
require '../vendor/autoload.php';
require_once 'HTML/Table.php';

const DOCROOT = '/var/www/';

$dbmanager = new dbManager(DOCROOT . 'railerdb.sqlite3');

// Prepare a HTML table.
$attrs = array('width' => '340');
$table = new HTML_Table();
$table->setAttributes($attrs);
//$table->setHeaderContents(0, 0, '');

$crawls = $dbmanager->getTimeStamps();
$numIndex = 0;
foreach ($crawls as $index=>$timestamp) {
    // Get site crawl results for each timestamp.
    $resultsByTimestamp = $dbmanager->getRestultsbyDate($timestamp);
    foreach ($resultsByTimestamp as $index=>$listOfSites) {
        if ($numIndex == 0) {
            $table->addCol(range(0,sizeof($listOfSites)));
            $table->setHeaderContents(0, 0, 'Site ID');
        }
        $arraySizes = Array();
        $arraySizes[] = $timestamp;
        foreach ($listOfSites as $site) {
            $arraySizes[] = $site['size'];

        }

        $numIndex++;
        $table->addCol($arraySizes);
//        $table->setHeaderContents(0, $numIndex, $timestamp);
    }
    $numIndex++;
}

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
// Let's display the table.
echo $table->toHtml();

echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';


echo '<br><br>All sites:';
$results = $dbmanager->readDB();
foreach ($results as $row) {
//    foreach ($row as $elem) {
//        print_r($elem);
    echo 'timestamp: ' . $row['timestamp'];
    echo "<br>";
    echo 'site_id: ' . $row['site_id'];
    echo "<br>";
    echo 'url: ' . $row['url'];
    echo "<br>";
    echo 'size: ' . $row['size'];
    echo "<br>";
    echo 'statusCode: ' . $row['statusCode'];
    echo "<br><br>";
//    }
}

?>
