<?php
include_once('../../../include/conectdb.php');
$q = trim(strtolower($_GET["term"]));
if (!$q) return;
$q = str_replace(' ', '', $q);
$q = str_replace('-', '', $q);
//$qq = "SELECT cmId, imatch FROM (SELECT cmId, ctree AS imatch FROM company WHERE ctree LIKE '$q%' OR REPLACE(ctree, ' ','') LIKE '$q%' OR REPLACE(ctree, '-','') LIKE '$q%' ORDER BY ctree ASC) AS imatch";
$qq = "SELECT vanId,van_no AS imatch FROM van WHERE van_no LIKE '$q%' ";
$r = mysqli_query($dbc,$qq);
//echo $qq;
$items = array();
if($r && mysqli_num_rows($r)>0)
{
	while($row = mysqli_fetch_assoc($r))
	{
		 $matched = $row['imatch'];
		 $items[$row['vanId']] = $matched;
	}
}
/*
$items = array(
"Deepak Tokas"=>"Tringa solitaria",
"Pawan Tokas"=>"Tringa solitaria",
"Shalini Tokas"=>"Larus heuglini"
);*/

function array_to_json( $array ){

    if( !is_array( $array ) ){
        return false;
    }

    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
    if( $associative ){

        $construct = array();
        foreach( $array as $key => $value ){

            // We first copy each key/value pair into a staging array,
            // formatting each key and value properly as we go.

            // Format the key:
            if( is_numeric($key) ){
                $key = "key_$key";
            }
            $key = "\"".addslashes($key)."\"";

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "\"".addslashes($value)."\"";
            }

            // Add to staging array:
            $construct[] = "$key: $value";
        }

        // Then we collapse the staging array into the JSON form:
        $result = "{ " . implode( ", ", $construct ) . " }";

    } else { // If the array is a vector (not associative):

        $construct = array();
        foreach( $array as $value ){

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = $value;
        }

        // Then we collapse the staging array into the JSON form:
        $result = "[ " . implode( ", ", $construct ) . " ]";
    }

    return $result;
}

$result = array();
foreach ($items as $key=>$value) {
	if (true) {
		array_push($result, array("id"=>$key, "label"=>$value, "value" => strip_tags($value)));
	}
	if (count($result) > 11)
		break;
}
echo array_to_json($result);

?>