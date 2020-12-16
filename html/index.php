<?php
require '../vendor/autoload.php';
require_once 'HTML/Table.php';

const DOCROOT = '/var/www/';

$dbmanager = new dbManager(DOCROOT . 'railerdb.sqlite3');

// Prepare a HTML table.
$table = new HTML_Table();
$attrs = array('width' => '340');
$table->setAttributes($attrs);
//$table->setHeaderContents(0, 0, '');

$crawls = $dbmanager->getTimeStamps();
$numIndex = 0;
$storedSizes = $storedStatus = Array();
foreach ($crawls as $index=>$timestamp) {
    // Get site crawl results for each timestamp.
    $resultsByTimestamp = $dbmanager->getRestultsbyDate($timestamp);
    foreach ($resultsByTimestamp as $listOfSites) {
        if ($numIndex == 0) {
            $table->addCol(range(0,sizeof($listOfSites)));
            $table->setHeaderContents(0, 0, 'Site ID');
        }

        $arraySizes = $arrayCodes = Array();
        $arraySizes[] = $arrayCodes[] = $timestamp;
        foreach ($listOfSites as $siteid=>$site) {
            $arraySizes[] = $site['size'];
            $arrayCodes[] = $site['statusCode'];

            // Store data for later checks.
            $storedSizes[$index][$siteid] = $site['size'];
            $storedStatus[$index][$siteid] = $site['statusCode'];
//            echo 'current size' . $storedSizes[$index-1][$siteid];
//            echo '<br>prev size' . $site['size'];
//            echo 'index::: ' . $siteid;

            if (($storedSizes[$index-1][$siteid] != $site['size']) && $index > 0) {
//                echo ' <b>DIFFERENCE FOUND, MAKE THIS TABLE RED</b> index: ' . $index;
                $rowAttrs = array('bgcolor' => 'orange');
                $markRedCell = $siteid + 1;
                $siteidMarked = $index;
            }
        }

        $table->addCol($arraySizes);
        $table->addCol($arrayCodes);

        // TODO: Change color of rows to help identifying different timestamps.
        $numIndex++;
    }
}

//echo 'marked:: ' . $markRedCell;
if ($markRedCell != 0) {
//    $table->setCellAttributes($siteidMarked,$markRedCell, $rowAttrs);
    $table->setRowAttributes($siteidMarked, $rowAttrs);
}

echo 'Status codes: <br><br>';
$statusCodes = $dbmanager->getStatusCodes();
foreach ($statusCodes as $code) {
    echo 'code : ' . $code . '<br>';
    $statusResults = $dbmanager->getStatsByStatus($code);
    foreach ($statusResults as $timestamp => $statusResult) {
        echo '<b>Crawl: </b>' . $timestamp;
        echo ' <b>Status:</b> ' . $statusResult . '<br>';
    }

    echo '<br><br>';

}




$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
// Let's display the table.
echo $table->toHtml();

echo '<br><br>';
echo '<br><br>';
echo 'Stored sizes: <br>';
print_r($storedSizes);
echo '<br>';
echo 'Stored statuses: <br>';
print_r($storedStatus);

echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';
echo '<br><br>';


//echo '<br><br>All sites:';
//$results = $dbmanager->readDB();
//foreach ($results as $row) {
////    foreach ($row as $elem) {
////        print_r($elem);
//    echo 'timestamp: ' . $row['timestamp'];
//    echo "<br>";
//    echo 'site_id: ' . $row['site_id'];
//    echo "<br>";
//    echo 'url: ' . $row['url'];
//    echo "<br>";
//    echo 'size: ' . $row['size'];
//    echo "<br>";
//    echo 'statusCode: ' . $row['statusCode'];
//    echo "<br><br>";
////    }
//}

?>
