<?php include_once 'dbh.inc.php'?>
<?php
    // default settings 
    // $dflt_keysArr = Array();
    // $dflt_keysArr[] = "booksTitle";
    // $dflt_keysArr[] = "booksISBN";
    // $dflt_keysArr[] = "booksAuthor";
    // $dflt_keysArr[] = "booksCategory";
    // $dflt_keysArr[] = "booksYear";
    // $dflt_keysArr[] = "booksPublisher";
    // $dflt_keysArr[] = "booksShelf";
    // $dflt_keysArr[] = "booksLanguage";
    // $dflt_keys = "(" .implode(", ", $dflt_keysArr). ")";

    // $dflt_value = "";
    // $dflt_order = "booksYear";
    // $dflt_index = 1;
    // $dflt_loadData = 10;

    // could use more sanitize and error handling (what if the get attribute isn't present)
    // mysqli_escape_string();
    // $value = $_GET["value"] == '' ? $dflt_value : $_GET["value"];
    // $order = $_GET["orderBy"] == ''? $dflt_order :  $_GET["orderBy"];
    // $index = $_GET["index"] == '' ? $dflt_index : $_GET["index"];
    // $loadData = $_GET["dR"] == '' ? $dflt_loadData : $_GET["dR"];
    $keysArr = json_decode($_GET["searchKeys"]);
    $value = $_GET["value"];
    $order = $_GET["orderBy"];
    $index = $_GET["index"];
    $loadData = $_GET["dR"];
    
    $index = (int) $index;
    $loadData = (int) $loadData;
    $rangeStart = ($index - 1) * $loadData;

    $condition = "WHERE ";
    for ($x = 0; $x < count($keysArr); $x++) {
        if ($x) {
            $condition .= " OR ";
        }
        $condition .= $keysArr[$x] ." LIKE '%$value%'";
    }

    $totalResult = 0;
    $querySet = Array();
	
    $sql = "SELECT count(distinct booksISBN, booksTitle) AS num FROM books $condition;";
    if ($result = mysqli_query($conn, $sql)) {
        $row = mysqli_fetch_assoc($result);
        $totalResult = (int) $row["num"];
    } 
    
    $sql = "SELECT distinct booksISBN AS isbn, booksTitle AS title, booksDescription AS detail, booksCoverImage AS bookCover, booksQuantity, booksLoaned FROM books $condition ORDER BY $order ASC LIMIT $loadData OFFSET $rangeStart;";
    if ($result = mysqli_query($conn, $sql)) {
        while($row = mysqli_fetch_assoc($result)) {
    
            if ((int)$row["booksQuantity"] - (int)$row["booksLoaned"] > 0) {
                $row += array('available' => true);
            } else {
                $row += array('available' => false);
            }
            unset($row["booksQuantity"]);
            unset($row["booksLoaned"]);
            $querySet[] = $row;
        }
    }

    $rangeEnd = 0;
    if ($totalResult) {
        $rangeStart = $rangeStart + 1;
        $rangeEnd = $rangeStart + $loadData - 1;
        if ($rangeEnd > $totalResult) {
            $rangeEnd = $totalResult;
        }
    }

    $container = Array();
    $container[] = $totalResult;
    $container[] = $rangeStart;
    $container[] = $rangeEnd;
    $container[] = $querySet;
    echo json_encode($container);
?>